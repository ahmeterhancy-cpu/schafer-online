<?php

namespace App\Services\Xml;

use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\ProductVariant;
use App\Observers\ProductVariantObserver;
use App\Services\VariantMatrix;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Tedarikçi XML'inden fiyat / stok güncelleme (isteğe bağlı yeni ürün).
 *
 * Her tedarikçinin etiket adları farklı; panelde alan adı girilmezse yaygın
 * adlardan tahmin edilir (Google Merchant, Trendyol/N11 bayi XML'leri,
 * Türkçe "urun/fiyat/stok" biçimleri). Ad alanı önekleri (g:price) yok
 * sayılır, büyük/küçük harf fark etmez.
 *
 * Güvenlik kuralları:
 *  - Eşleşmeyen ürün varsayılan olarak ATLANIR; yeni ürün ayarı açıksa
 *    TASLAK (yayında değil) açılır — yanlış XML vitrini dolduramaz.
 *  - Fiyatı 0 ya da okunamayan satır fiyatı değiştirmez.
 *  - Önizleme (kuru çalıştırma) hiçbir şeye yazmaz, aynı raporu üretir.
 */
class XmlIceAktarim
{
    public const RAPOR = 'xml/son-aktarim.json';

    private const AZAMI_BAYT = 60 * 1024 * 1024;

    /** Alan => tahmin sırası (ilk bulunan kazanır). */
    private const ADAYLAR = [
        'urun' => ['item', 'product', 'urun', 'entry', 'offer', 'row', 'record', 'stokkart', 'urunler_urun'],
        'kod' => ['sku', 'stok_kodu', 'stokkodu', 'stock_code', 'stockcode', 'productcode', 'product_code', 'urun_kodu', 'urunkodu', 'model_kodu', 'modelcode', 'kod', 'code', 'mpn', 'model', 'id'],
        'barkod' => ['barcode', 'barkod', 'gtin', 'ean', 'ean13', 'upc'],
        'ad' => ['title', 'name', 'urun_adi', 'urunadi', 'product_name', 'productname', 'baslik', 'ad', 'adi', 'label'],
        'fiyat' => ['sale_price', 'indirimli_fiyat', 'indirimlifiyat', 'satis_fiyati', 'satisfiyati', 'price', 'fiyat', 'price1', 'bayi_fiyati'],
        'eski_fiyat' => ['price', 'regular_price', 'liste_fiyati', 'listefiyati', 'piyasa_fiyati', 'piyasafiyati', 'market_price', 'list_price', 'eski_fiyat'],
        'stok' => ['quantity', 'stock', 'stok', 'stok_adedi', 'stokadedi', 'stock_quantity', 'miktar', 'qty', 'amount', 'adet', 'availability'],
        'gorsel' => ['image_link', 'image', 'images', 'resim', 'resimler', 'gorsel', 'picture', 'pictures', 'photo', 'additional_image_link', 'image1', 'resim1'],
    ];

    public function __construct(private VariantMatrix $matris) {}

    /**
     * Ayarlardaki adresi (ya da verilen kaynağı) okuyup uygular, raporu saklar.
     *
     * @return array<string, mixed>
     */
    public function calistir(?string $kaynak = null, bool $kuru = false): array
    {
        $kaynak = trim((string) ($kaynak ?? config('shop.xml.ice_url')));
        $rapor = ['tarih' => now()->toIso8601String(), 'kaynak' => $kaynak, 'kuru' => $kuru];

        try {
            if ($kaynak === '') {
                throw new RuntimeException('XML adresi girilmemiş.');
            }
            $ayrik = $this->ayristir($this->oku($kaynak), $this->ayarAlanlari());
            $rapor += ['alanlar' => $ayrik['alanlar'], 'urun_etiketi' => $ayrik['urun_etiketi']];
            $rapor += $this->uygula($ayrik['satirlar'], $kuru);
        } catch (Throwable $e) {
            $rapor['hata'] = $e->getMessage();
        }

        if (! $kuru) {
            Storage::disk('local')->put(self::RAPOR, json_encode($rapor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return $rapor;
    }

    /** @return array<string, mixed>|null */
    public static function sonRapor(): ?array
    {
        $disk = Storage::disk('local');

        return $disk->exists(self::RAPOR) ? json_decode($disk->get(self::RAPOR), true) : null;
    }

    public function oku(string $kaynak): string
    {
        if (preg_match('#^https?://#i', $kaynak)) {
            $yanit = Http::timeout(120)->withHeaders(['Accept' => 'application/xml,text/xml,*/*'])->get($kaynak);
            if (! $yanit->successful()) {
                throw new RuntimeException("XML adresi yanıt vermedi (HTTP {$yanit->status()}).");
            }
            $govde = $yanit->body();
        } elseif (is_file($kaynak)) {
            $govde = (string) file_get_contents($kaynak);
        } else {
            throw new RuntimeException('XML adresi http:// ya da https:// ile başlamalı.');
        }

        if (strlen($govde) > self::AZAMI_BAYT) {
            throw new RuntimeException('XML dosyası çok büyük (60 MB üstü).');
        }

        return $govde;
    }

    /** @return array<string, string> panelde elle girilmiş etiket adları */
    private function ayarAlanlari(): array
    {
        $sonuc = [];
        foreach (array_keys(self::ADAYLAR) as $alan) {
            $deger = trim((string) config("shop.xml.ice_alan_{$alan}"));
            if ($deger !== '') {
                $sonuc[$alan] = $this->anahtar($deger);
            }
        }

        return $sonuc;
    }

    /**
     * @param  array<string, string>  $elle
     * @return array{satirlar: list<array<string, mixed>>, alanlar: array<string, string|null>, urun_etiketi: string}
     */
    public function ayristir(string $xml, array $elle = []): array
    {
        $dom = new DOMDocument;
        $onceki = libxml_use_internal_errors(true);
        // LIBXML_NONET: dış varlık / DTD indirmesi yok.
        $tamam = $dom->loadXML($xml, LIBXML_NONET | LIBXML_NOCDATA | LIBXML_PARSEHUGE | LIBXML_COMPACT);
        $hata = libxml_get_errors()[0] ?? null;
        libxml_clear_errors();
        libxml_use_internal_errors($onceki);
        if (! $tamam) {
            throw new RuntimeException('XML okunamadı'.($hata ? ': '.trim($hata->message).' (satır '.$hata->line.')' : '.'));
        }

        $urunEtiketi = $elle['urun'] ?? $this->urunEtiketiBul($dom);
        $ogeler = [];
        foreach ($dom->getElementsByTagName('*') as $el) {
            if ($this->anahtar($el->localName) === $urunEtiketi) {
                $ogeler[] = $el;
            }
        }
        if ($ogeler === []) {
            throw new RuntimeException("XML'de ürün öğesi bulunamadı (aranan etiket: {$urunEtiketi}).");
        }

        $hamlar = array_map(fn (DOMElement $el) => $this->ogeyiDuzle($el), $ogeler);
        $alanlar = $this->alanlariTahminEt($hamlar, $elle);

        $satirlar = [];
        foreach ($hamlar as $h) {
            $fiyat = $this->sayi($h[$alanlar['fiyat'] ?? ''][0] ?? null);
            $eski = $this->sayi($h[$alanlar['eski_fiyat'] ?? ''][0] ?? null);
            if (! $fiyat && $eski) {
                [$fiyat, $eski] = [$eski, null];
            }
            $satirlar[] = [
                'kod' => $this->metin($h[$alanlar['kod'] ?? ''][0] ?? null),
                'barkod' => $this->metin($h[$alanlar['barkod'] ?? ''][0] ?? null),
                'ad' => $this->metin($h[$alanlar['ad'] ?? ''][0] ?? null),
                'fiyat' => $fiyat,
                'eski_fiyat' => $eski && $fiyat && $eski > $fiyat ? $eski : null,
                'stok' => $this->stok($h[$alanlar['stok'] ?? ''][0] ?? null),
                'gorseller' => array_values(array_unique(array_filter(
                    $alanlar['gorsel'] ? ($h[$alanlar['gorsel']] ?? []) : [],
                    fn ($u) => preg_match('#^https?://#i', (string) $u)
                ))),
            ];
        }

        return ['satirlar' => $satirlar, 'alanlar' => $alanlar, 'urun_etiketi' => $urunEtiketi];
    }

    /**
     * @param  list<array<string, mixed>>  $satirlar
     * @return array<string, mixed>
     */
    public function uygula(array $satirlar, bool $kuru = false): array
    {
        $eslesme = config('shop.xml.ice_eslesme') === 'barkod' ? 'barkod' : 'kod';
        $fiyatGuncelle = (bool) config('shop.xml.ice_fiyat');
        $stokGuncelle = (bool) config('shop.xml.ice_stok');
        $yeniAc = (bool) config('shop.xml.ice_yeni_urun');

        $sayac = ['toplam' => count($satirlar), 'eslesen' => 0, 'guncellenen' => 0, 'degismeyen' => 0, 'bulunamayan' => 0, 'yeni' => 0, 'atlanan' => 0];
        $ornekler = [];
        $bulunamayanlar = [];

        $varyantlar = ProductVariant::query()->get(['id', 'product_id', 'sku', 'barcode', 'price', 'compare_at_price', 'stock']);
        $skuDizin = $varyantlar->filter(fn ($v) => $v->sku)->keyBy(fn ($v) => $this->kodNormal($v->sku));
        $barkodDizin = $varyantlar->filter(fn ($v) => $v->barcode)->keyBy(fn ($v) => $this->kodNormal($v->barcode));

        $dokunulanUrunler = [];
        ProductVariantObserver::$muted = true;

        try {
            foreach ($satirlar as $s) {
                $birincil = $eslesme === 'barkod' ? $s['barkod'] : $s['kod'];
                $ikincil = $eslesme === 'barkod' ? $s['kod'] : $s['barkod'];
                if (! $birincil && ! $ikincil) {
                    $sayac['atlanan']++;

                    continue;
                }

                $v = null;
                if ($birincil) {
                    $v = ($eslesme === 'barkod' ? $barkodDizin : $skuDizin)->get($this->kodNormal($birincil));
                }
                if (! $v && $ikincil) {
                    $v = ($eslesme === 'barkod' ? $skuDizin : $barkodDizin)->get($this->kodNormal($ikincil));
                }

                if (! $v) {
                    if ($yeniAc && $s['ad'] && $s['fiyat'] && ($s['kod'] || $s['barkod'])) {
                        $sayac['yeni']++;
                        if (count($ornekler) < 200) {
                            $ornekler[] = ['tur' => 'yeni', 'kod' => $s['kod'] ?: $s['barkod'], 'ad' => $s['ad'], 'fiyat' => [null, $s['fiyat']], 'stok' => [null, $s['stok']]];
                        }
                        if (! $kuru) {
                            $yeni = $this->yeniUrun($s, $skuDizin);
                            $dokunulanUrunler[$yeni->product_id] = true;
                        }
                    } else {
                        $sayac['bulunamayan']++;
                        if (count($bulunamayanlar) < 200) {
                            $bulunamayanlar[] = ['kod' => $birincil ?: $ikincil, 'ad' => $s['ad']];
                        }
                    }

                    continue;
                }

                $sayac['eslesen']++;
                $degisim = [];

                if ($fiyatGuncelle && $s['fiyat'] && abs((float) $v->price - $s['fiyat']) >= 0.005) {
                    $eskiKarsilastirma = (float) $v->compare_at_price;
                    $yeniKarsilastirma = $s['eski_fiyat']
                        ?: ($eskiKarsilastirma > (float) $v->price && (float) $v->price > 0
                            ? round($s['fiyat'] * $eskiKarsilastirma / (float) $v->price, 2)
                            : null);
                    $degisim['fiyat'] = [(float) $v->price, $s['fiyat']];
                    $degisim['_kayit']['price'] = $s['fiyat'];
                    $degisim['_kayit']['compare_at_price'] = $yeniKarsilastirma;
                }

                if ($stokGuncelle && $s['stok'] !== null && (int) $v->stock !== $s['stok']) {
                    $degisim['stok'] = [(int) $v->stock, $s['stok']];
                    $degisim['_kayit']['stock'] = $s['stok'];
                }

                if (! isset($degisim['_kayit'])) {
                    $sayac['degismeyen']++;

                    continue;
                }

                $sayac['guncellenen']++;
                if (count($ornekler) < 200) {
                    $ornekler[] = ['tur' => 'guncel', 'kod' => $v->sku, 'ad' => $s['ad'], 'fiyat' => $degisim['fiyat'] ?? null, 'stok' => $degisim['stok'] ?? null];
                }
                if (! $kuru) {
                    ProductVariant::whereKey($v->id)->update($degisim['_kayit'] + ['updated_at' => now()]);
                    $dokunulanUrunler[$v->product_id] = true;
                }
            }
        } finally {
            ProductVariantObserver::$muted = false;
        }

        if (! $kuru && $dokunulanUrunler) {
            Product::whereIn('id', array_keys($dokunulanUrunler))->get()
                ->each(fn (Product $u) => $this->matris->refreshProduct($u));
        }

        return ['sayac' => $sayac, 'ornekler' => $ornekler, 'bulunamayanlar' => $bulunamayanlar];
    }

    /** @param  \Illuminate\Support\Collection<string, ProductVariant>  $skuDizin */
    private function yeniUrun(array $s, $skuDizin): ProductVariant
    {
        return DB::transaction(function () use ($s, $skuDizin) {
            $urun = Product::create([
                'name' => Str::limit($s['ad'], 190, ''),
                'slug' => Product::uniqueSlug($s['ad']),
                'base_sku' => $s['kod'],
                'is_active' => false, // taslak: panelden kontrol edilip yayına alınır
            ]);

            $sku = $s['kod'] ?: 'XML-'.$s['barkod'];
            $taban = $sku;
            $i = 2;
            while ($skuDizin->has($this->kodNormal($sku)) || ProductVariant::where('sku', $sku)->exists()) {
                $sku = $taban.'-'.$i++;
            }

            $varyant = ProductVariant::create([
                'product_id' => $urun->id,
                'sku' => $sku,
                'barcode' => $s['barkod'],
                'price' => $s['fiyat'],
                'compare_at_price' => $s['eski_fiyat'],
                'stock' => $s['stok'] ?? 0,
                'is_active' => true,
            ]);
            $skuDizin->put($this->kodNormal($sku), $varyant);

            $yollar = [];
            foreach (array_slice($s['gorseller'], 0, 5) as $n => $url) {
                $yol = $this->gorselIndir($url, 'urunler/'.Str::limit($urun->slug, 80, '').'-x'.($n + 1).'.webp');
                if ($yol) {
                    ProductMedia::create(['product_id' => $urun->id, 'path' => $yol, 'alt' => $urun->name, 'position' => $n]);
                    $yollar[] = $yol;
                }
            }
            if ($yollar) {
                $urun->forceFill(['hero_image' => $yollar[0]])->saveQuietly();
            }

            return $varyant;
        });
    }

    private function gorselIndir(string $url, string $hedef): ?string
    {
        try {
            $yanit = Http::timeout(30)->get($url);
            if (! $yanit->successful() || strlen($yanit->body()) > 10 * 1024 * 1024
                || ! str_starts_with((string) $yanit->header('Content-Type'), 'image/')) {
                return null;
            }
            $im = @imagecreatefromstring($yanit->body());
            if (! $im) {
                return null;
            }
            // Paletli PNG'de imagewebp() boş dosya bırakır — önce gerçek renge çevir.
            if (! imageistruecolor($im)) {
                imagepalettetotruecolor($im);
            }
            ob_start();
            imagewebp($im, null, 85);
            $veri = (string) ob_get_clean();
            imagedestroy($im);
            if (strlen($veri) < 30) { // geçerli en küçük WebP ~40 bayt; 0 bayt = bozuk dönüşüm
                return null;
            }
            Storage::disk('public')->put($hedef, $veri);

            return $hedef;
        } catch (Throwable) {
            return null;
        }
    }

    private function urunEtiketiBul(DOMDocument $dom): string
    {
        $sayim = [];
        foreach ($dom->getElementsByTagName('*') as $el) {
            $cocuk = 0;
            foreach ($el->childNodes as $c) {
                if ($c instanceof DOMElement && ++$cocuk >= 2) {
                    break;
                }
            }
            if ($cocuk >= 2 && $el !== $dom->documentElement) {
                $k = $this->anahtar($el->localName);
                $sayim[$k] = ($sayim[$k] ?? 0) + 1;
            }
        }
        foreach (self::ADAYLAR['urun'] as $aday) {
            if (($sayim[$aday] ?? 0) > 0) {
                return $aday;
            }
        }
        arsort($sayim);

        return (string) array_key_first($sayim) ?: 'item';
    }

    /**
     * Öğeyi "etiket => değerler" haritasına çevirir. Alt öğeler (resimler/resim)
     * ve öğenin kendi nitelikleri de dahil edilir.
     *
     * @return array<string, list<string>>
     */
    private function ogeyiDuzle(DOMElement $el): array
    {
        $h = [];
        foreach ($el->attributes as $a) {
            $h[$this->anahtar($a->localName)][] = trim($a->value);
        }
        $this->cocuklariTopla($el, $h, 0);

        return $h;
    }

    /** @param  array<string, list<string>>  $h */
    private function cocuklariTopla(DOMElement $el, array &$h, int $derinlik): void
    {
        foreach ($el->childNodes as $c) {
            if (! $c instanceof DOMElement) {
                continue;
            }
            $k = $this->anahtar($c->localName);
            $altVar = false;
            foreach ($c->childNodes as $cc) {
                if ($cc instanceof DOMElement) {
                    $altVar = true;
                    break;
                }
            }
            if ($altVar && $derinlik < 2) {
                $this->cocuklariTopla($c, $h, $derinlik + 1);
                // <resimler><resim>..</resim></resimler> → "resimler" anahtarı da dolsun
                foreach ($c->getElementsByTagName('*') as $alt) {
                    if (trim($alt->textContent) !== '' && ! $alt->firstElementChild) {
                        $h[$k][] = trim($alt->textContent);
                    }
                }
            } else {
                $h[$k][] = trim($c->textContent);
            }
            foreach ($c->attributes as $a) {
                if (in_array($this->anahtar($a->localName), ['url', 'src', 'href'], true)) {
                    $h[$k][] = trim($a->value);
                }
            }
        }
    }

    /**
     * @param  list<array<string, list<string>>>  $hamlar
     * @param  array<string, string>  $elle
     * @return array<string, string|null>
     */
    private function alanlariTahminEt(array $hamlar, array $elle): array
    {
        $ornek = array_slice($hamlar, 0, 50);
        $varMi = function (string $k) use ($ornek): bool {
            foreach ($ornek as $h) {
                foreach ($h[$k] ?? [] as $d) {
                    if ($d !== '') {
                        return true;
                    }
                }
            }

            return false;
        };

        $sonuc = [];
        $kullanilan = [];
        foreach (array_keys(self::ADAYLAR) as $alan) {
            if ($alan === 'urun') {
                continue;
            }
            if (isset($elle[$alan])) {
                $sonuc[$alan] = $elle[$alan];
                $kullanilan[] = $elle[$alan];

                continue;
            }
            $sonuc[$alan] = null;
            foreach (self::ADAYLAR[$alan] as $aday) {
                if (! in_array($aday, $kullanilan, true) && $varMi($aday)) {
                    $sonuc[$alan] = $aday;
                    $kullanilan[] = $aday;
                    break;
                }
            }
        }

        return $sonuc;
    }

    private function anahtar(string $ad): string
    {
        $ad = Str::afterLast($ad, ':');

        return Str::lower(Str::ascii(str_replace(['-', ' '], '_', $ad)));
    }

    private function kodNormal(string $kod): string
    {
        return Str::upper(preg_replace('/\s+/', '', $kod));
    }

    private function metin(?string $d): ?string
    {
        $d = trim(html_entity_decode((string) $d, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $d === '' ? null : $d;
    }

    /** "1.234,56 TL", "1234.56 TRY", "1,234.56" → 1234.56 */
    public function sayi(?string $d): ?float
    {
        $d = preg_replace('/[^0-9.,]/', '', (string) $d);
        if ($d === '' || $d === null) {
            return null;
        }
        $nokta = strrpos($d, '.');
        $virgul = strrpos($d, ',');
        if ($nokta !== false && $virgul !== false) {
            $ondalik = $nokta > $virgul ? '.' : ',';
            $binlik = $ondalik === '.' ? ',' : '.';
            $d = str_replace([$binlik, $ondalik], ['', '.'], $d);
        } elseif ($virgul !== false) {
            $d = preg_match('/^\d{1,3}(,\d{3})+$/', $d) ? str_replace(',', '', $d) : str_replace(',', '.', $d);
        } elseif ($nokta !== false && preg_match('/^\d{1,3}(\.\d{3})+$/', $d) && substr_count($d, '.') > 1) {
            $d = str_replace('.', '', $d);
        }
        $s = (float) $d;

        return $s > 0 ? round($s, 2) : null;
    }

    /** Sayı ya da "in stock / out of stock / var / yok". */
    private function stok(?string $d): ?int
    {
        $d = Str::lower(trim((string) $d));
        if ($d === '') {
            return null;
        }
        if (is_numeric(str_replace(',', '.', $d))) {
            return max(0, (int) floor((float) str_replace(',', '.', $d)));
        }
        if (preg_match('/out|yok|tukendi|tükendi|none|false|^0$/u', $d)) {
            return 0;
        }

        return null; // "in stock" gibi miktarsız değer: stoğa dokunma
    }
}
