<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\ProductVariant;
use App\Observers\ProductVariantObserver;
use App\Services\VariantMatrix;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Eski schaferkibris.com (WooCommerce) kataloğunu içeri alır.
 *
 * Kaynak dosyalar storage/app/woo altında:
 *   kategoriler.json — /wp-json/wc/v3/products/categories çıktısı
 *   urunler.json     — {"products": [...]} WooCommerce yedeği
 *   gorsel/{wooId}-{sıra}.webp — önceden indirilmiş, 1200 px'e küçültülmüş görseller
 *
 * Tekrar çalıştırılabilir: eşleşme anahtarı Woo kimliği (woo_id). Var olan
 * ürünün adı/fiyatı/stoğu/kategorileri güncellenir, görseller yeniden kopyalanmaz.
 *
 * WooCommerce'teki ürünlerin hepsi "basit ürün"; burada her biri seçeneksiz
 * TEK varyantla tutulur.
 */
class WooIceAktar extends Command
{
    protected $signature = 'woo:ice-aktar {--klasor= : Kaynak klasör (varsayılan storage/app/woo)} {--gorselsiz : Görselleri kopyalama}';

    protected $description = 'Eski WooCommerce kataloğunu (kategori, ürün, görsel) içeri aktarır';

    public function handle(VariantMatrix $matris): int
    {
        $klasor = $this->option('klasor') ?: storage_path('app/woo');
        $katDosya = $klasor.'/kategoriler.json';
        $urunDosya = $klasor.'/urunler.json';

        if (! is_file($katDosya) || ! is_file($urunDosya)) {
            $this->error("Kaynak bulunamadı: {$klasor}");

            return self::FAILURE;
        }

        $kategoriler = json_decode(file_get_contents($katDosya), true);
        $urunler = json_decode(file_get_contents($urunDosya), true)['products'] ?? [];

        $katHarita = $this->kategoriler($kategoriler);
        $this->info(count($katHarita).' kategori hazır.');

        $disk = Storage::disk('public');
        $kullanilanSku = ProductVariant::pluck('product_id', 'sku')->all();
        $sayac = ['yeni' => 0, 'guncel' => 0, 'gorsel' => 0];

        ProductVariantObserver::$muted = true;
        $bar = $this->output->createProgressBar(count($urunler));

        try {
            foreach ($urunler as $w) {
                DB::transaction(function () use ($w, $katHarita, $disk, &$kullanilanSku, &$sayac, $klasor) {
                    $urun = Product::where('woo_id', $w['id'])->first();
                    $yeni = ! $urun;
                    $urun ??= new Product(['woo_id' => $w['id']]);

                    $ad = $this->temizAd($w['name']);
                    $urun->fill([
                        'woo_slug' => urldecode($w['slug']),
                        'name' => $ad,
                        'base_sku' => trim((string) $w['sku']) ?: null,
                        'short_description' => $this->duzMetin($w['short_description']) ?: null,
                        'description' => $this->duzMetin($w['description']) ?: null,
                        'is_active' => $w['status'] === 'publish',
                        'is_featured' => (bool) $w['featured'],
                        'position' => (int) $w['menu_order'],
                    ]);

                    if ($yeni) {
                        $urun->slug = Product::uniqueSlug($this->slug($w['slug'], $ad));
                        $urun->created_at = $w['date_created'] ?? now();
                    }
                    $urun->save();

                    // Kategoriler
                    $katIdleri = collect($w['categories'])->pluck('id')->map(fn ($id) => $katHarita[$id] ?? null)->filter()->unique()->values()->all();
                    $urun->categories()->sync($katIdleri);

                    // Tek varyant: fiyat + indirim + stok
                    $normal = (float) ($w['regular_price'] ?: $w['price'] ?: 0);
                    $indirimli = $w['sale_price'] !== '' ? (float) $w['sale_price'] : null;
                    $fiyat = $indirimli && $indirimli < $normal ? $indirimli : $normal;
                    $eski = $indirimli && $indirimli < $normal ? $normal : null;
                    $stok = $w['manage_stock']
                        ? max(0, (int) $w['stock_quantity'])
                        : ($w['stock_status'] === 'instock' ? 100 : 0);

                    $varyant = $urun->variants()->first() ?? new ProductVariant(['product_id' => $urun->id]);
                    if (! $varyant->exists) {
                        $varyant->sku = $this->benzersizSku($w, $kullanilanSku);
                        $kullanilanSku[$varyant->sku] = $urun->id;
                    }
                    $varyant->fill([
                        'price' => $fiyat,
                        'compare_at_price' => $eski,
                        'stock' => $stok,
                        'is_active' => true,
                    ])->save();

                    // Görseller (yalnız ilk kez)
                    if (! $this->option('gorselsiz') && ! $urun->media()->exists()) {
                        $yollar = [];
                        foreach ($w['images'] as $i => $im) {
                            $kaynak = "{$klasor}/gorsel/{$w['id']}-{$i}.webp";
                            if (! is_file($kaynak) || filesize($kaynak) === 0) {
                                continue;
                            }
                            $hedef = 'urunler/'.Str::limit($urun->slug, 80, '').'-'.($i + 1).'.webp';
                            $disk->put($hedef, file_get_contents($kaynak));
                            ProductMedia::create([
                                'product_id' => $urun->id,
                                'path' => $hedef,
                                'alt' => $ad,
                                'position' => $i,
                            ]);
                            $yollar[] = $hedef;
                            $sayac['gorsel']++;
                        }
                        if ($yollar) {
                            $urun->forceFill(['hero_image' => $yollar[0]])->saveQuietly();
                        }
                    }

                    $sayac[$yeni ? 'yeni' : 'guncel']++;
                });
                $bar->advance();
            }
        } finally {
            ProductVariantObserver::$muted = false;
        }
        $bar->finish();
        $this->newLine();

        // Fiyat aralığı / toplam stok önbelleği — toplu, sonda bir kez
        Product::with('variants')->chunkById(200, function ($urunler) use ($matris) {
            foreach ($urunler as $urun) {
                $matris->refreshProduct($urun);
            }
        });

        // Kategori görseli: içindeki ilk görselli yayındaki ürün
        foreach (Category::whereNull('image')->get() as $kat) {
            $gorsel = Product::where('is_active', true)->whereNotNull('hero_image')
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $this->altAgac($kat)))
                ->orderByDesc('is_featured')->orderBy('position')->value('hero_image');
            if ($gorsel) {
                $kat->forceFill(['image' => $gorsel])->saveQuietly();
            }
        }

        $this->info("Yeni: {$sayac['yeni']} · Güncellenen: {$sayac['guncel']} · Görsel: {$sayac['gorsel']}");

        return self::SUCCESS;
    }

    /** @return array<int,int> Woo kategori kimliği → yerel kimlik */
    private function kategoriler(array $kaynak): array
    {
        $harita = [];
        // Ebeveynler önce gelsin diye derinliğe göre sırala
        $derinlik = function (array $k) use ($kaynak, &$derinlik): int {
            if (! $k['parent']) {
                return 0;
            }
            $ust = collect($kaynak)->firstWhere('id', $k['parent']);

            return $ust ? 1 + $derinlik($ust) : 0;
        };
        $sirali = collect($kaynak)->sortBy(fn ($k) => [$derinlik($k), $k['menu_order'], $k['id']])->values();

        // Menüde görünen sıra: referans sitedeki gibi
        $anaSira = ['sofra-grubu' => 1, 'mutfak' => 2, 'kucuk-ev-aletleri' => 3, 'ev-tekstili' => 4, 'mutfak-aksesuarlari' => 5];

        // Eski sitedeki yazım hataları ve ana sayfada bölüm olarak görünecekler
        $adDuzelt = ['kahvesi-makinesi' => 'Kahve Makineleri'];
        $anaSayfada = ['sofra-grubu', 'ev-tekstili', 'kahvesi-makinesi', 'pisirme'];

        foreach ($sirali as $k) {
            $slug = $this->slug($k['slug'], $this->temizAd($k['name']));
            $kat = Category::firstOrNew(['slug' => $slug]);
            if (! $kat->exists) {
                $kat->is_featured = in_array($slug, $anaSayfada, true);
            }
            $kat->fill([
                'woo_slug' => urldecode($k['slug']),
                'name' => $adDuzelt[$slug] ?? $this->temizAd($k['name']),
                'parent_id' => $k['parent'] ? ($harita[$k['parent']] ?? null) : null,
                'description' => $this->duzMetin($k['description'] ?? '') ?: null,
                'position' => $anaSira[$slug] ?? ((int) $k['menu_order'] + 10),
                'is_active' => true,
            ]);
            $kat->save();
            $harita[$k['id']] = $kat->id;
        }

        return $harita;
    }

    /** @return list<int> */
    private function altAgac(Category $kat): array
    {
        $idler = [$kat->id];
        foreach (Category::where('parent_id', $kat->id)->get() as $alt) {
            $idler = array_merge($idler, $this->altAgac($alt));
        }

        return $idler;
    }

    private function benzersizSku(array $w, array $kullanilan): string
    {
        $sku = Str::upper(trim(preg_replace('/\s+/', ' ', (string) $w['sku'])));
        if ($sku === '' || isset($kullanilan[$sku]) || mb_strlen($sku) > 120) {
            $sku = 'WC-'.$w['id'];
        }

        return $sku;
    }

    private function temizAd(string $ad): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode($ad, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    private function slug(string $wooSlug, string $ad): string
    {
        $s = Str::slug(urldecode($wooSlug));

        return $s !== '' ? Str::limit($s, 150, '') : Str::slug($ad);
    }

    /** WooCommerce HTML açıklamasını satır sonlarını koruyarak düz metne çevirir. */
    private function duzMetin(?string $html): string
    {
        if (! $html) {
            return '';
        }
        $html = preg_replace('~<\s*(br|/p|/div|/li|/h[1-6]|/tr)\s*/?>~i', "\n", $html);
        $html = preg_replace('~<\s*li[^>]*>~i', '• ', $html);
        $metin = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $metin = preg_replace("/[ \t\x{00A0}]+/u", ' ', $metin);
        $metin = preg_replace("/ *\n */", "\n", $metin);
        $metin = preg_replace("/\n{3,}/", "\n\n", $metin);

        return trim($metin);
    }
}
