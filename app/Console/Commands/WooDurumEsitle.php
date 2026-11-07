<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * WooCommerce ürün dışa aktarımı (CSV) ile yayın/taslak durumunu eşitler.
 *
 *   php artisan woo:durum-esitle dosya.csv            → yalnız rapor (değişiklik YOK)
 *   php artisan woo:durum-esitle dosya.csv --uygula   → uygular
 *
 * WooCommerce "Ürünler → Dışa aktar" CSV'si: Türkçe ya da İngilizce başlık.
 * "Yayımlanmış" / "Published": 1 = yayında, 0 = taslak, -1 = özel (taslak sayılır).
 *
 * Eşleşme sırası: Woo kimliği (products.woo_id) → SKU → birebir ürün adı.
 * CSV'de olmayan ürünlere DOKUNULMAZ, raporda listelenir.
 * Her çalıştırmada storage/app/woo/durum-raporu-*.csv yazılır.
 */
class WooDurumEsitle extends Command
{
    protected $signature = 'woo:durum-esitle {dosya : WooCommerce ürün CSV dosyası} {--uygula : Değişiklikleri kaydet (verilmezse yalnız rapor)}';

    protected $description = 'WooCommerce CSV dışa aktarımına göre ürünleri yayına alır / taslağa çeker';

    /** Başlık eşanlamlıları — Woo dil ayarına göre değişir */
    private const BASLIKLAR = [
        'id' => ['id', 'kimlik'],
        'tur' => ['type', 'tür', 'tur'],
        'sku' => ['sku', 'stok kodu (sku)', 'stok kodu'],
        'ad' => ['name', 'isim', 'ad', 'ürün adı'],
        'yayin' => ['published', 'yayımlanmış', 'yayınlanmış', 'yayımlandı', 'yayınlandı'],
    ];

    public function handle(): int
    {
        $dosya = $this->argument('dosya');

        if (! is_file($dosya)) {
            $this->error("Dosya bulunamadı: {$dosya}");

            return self::FAILURE;
        }

        [$satirlar, $hata] = $this->oku($dosya);

        if ($hata) {
            $this->error($hata);

            return self::FAILURE;
        }

        // Eşleştirme dizinleri
        $wooId = Product::whereNotNull('woo_id')->pluck('id', 'woo_id')->all();
        $skuDizin = ProductVariant::query()->pluck('product_id', 'sku')
            ->mapWithKeys(fn ($pid, $sku) => [Str::upper(trim($sku)) => $pid])->all();
        $baseSku = Product::whereNotNull('base_sku')->pluck('id', 'base_sku')
            ->mapWithKeys(fn ($pid, $sku) => [Str::upper(trim($sku)) => $pid])->all();
        $adDizin = Product::pluck('id', 'name')->mapWithKeys(fn ($pid, $ad) => [$this->norm($ad) => $pid])->all();
        $aktif = Product::pluck('is_active', 'id')->all();
        $adlar = Product::pluck('name', 'id')->all();

        $yayinaAl = [];
        $taslagaCek = [];
        $ayni = 0;
        $eslesmeyen = [];
        $gorulen = [];
        $rapor = [['islem', 'yerel_id', 'woo_id', 'sku', 'ad', 'eslesme']];

        foreach ($satirlar as $s) {
            if (in_array($this->kucuk($s['tur'] ?? ''), ['variation', 'varyasyon'], true)) {
                continue;
            }

            [$pid, $nasil] = match (true) {
                isset($wooId[(int) ($s['id'] ?? 0)]) => [$wooId[(int) $s['id']], 'woo_id'],
                filled($s['sku'] ?? null) && isset($skuDizin[Str::upper(trim($s['sku']))]) => [$skuDizin[Str::upper(trim($s['sku']))], 'sku'],
                filled($s['sku'] ?? null) && isset($baseSku[Str::upper(trim($s['sku']))]) => [$baseSku[Str::upper(trim($s['sku']))], 'sku'],
                isset($adDizin[$this->norm($s['ad'] ?? '')]) => [$adDizin[$this->norm($s['ad'] ?? '')], 'ad'],
                default => [null, null],
            };

            if (! $pid) {
                $eslesmeyen[] = $s;
                $rapor[] = ['ESLESMEDI', '', $s['id'] ?? '', $s['sku'] ?? '', $s['ad'] ?? '', ''];

                continue;
            }

            $gorulen[$pid] = true;
            $hedef = trim((string) ($s['yayin'] ?? '')) === '1';

            if ((bool) $aktif[$pid] === $hedef) {
                $ayni++;

                continue;
            }

            if ($hedef) {
                $yayinaAl[$pid] = true;
            } else {
                $taslagaCek[$pid] = true;
            }
            $rapor[] = [$hedef ? 'YAYINA_AL' : 'TASLAGA_CEK', $pid, $s['id'] ?? '', $s['sku'] ?? '', $adlar[$pid], $nasil];
        }

        // Aynı ürün CSV'de iki kez farklı durumla geçerse yayında kalsın (güvenli taraf)
        $taslagaCek = array_diff_key($taslagaCek, $yayinaAl);

        $csvdeYok = array_diff_key($aktif, $gorulen);
        foreach ($csvdeYok as $pid => $a) {
            $rapor[] = ['CSVDE_YOK', $pid, '', '', $adlar[$pid], $a ? 'şu an yayında' : 'şu an taslak'];
        }

        $raporYolu = storage_path('app/woo/durum-raporu-'.now()->format('Ymd-His').'.csv');
        @mkdir(dirname($raporYolu), 0775, true);
        $fh = fopen($raporYolu, 'w');
        fwrite($fh, "\xEF\xBB\xBF");
        foreach ($rapor as $r) {
            fputcsv($fh, $r, ';');
        }
        fclose($fh);

        $this->table(['', 'Adet'], [
            ['CSV satırı (varyasyon hariç)', count($satirlar)],
            ['Zaten doğru durumda', $ayni],
            ['Yayına alınacak', count($yayinaAl)],
            ['Taslağa çekilecek', count($taslagaCek)],
            ['CSV\'de eşleşmeyen satır', count($eslesmeyen)],
            ['Sitede olup CSV\'de olmayan (dokunulmaz)', count($csvdeYok)],
        ]);

        foreach (array_slice($eslesmeyen, 0, 15) as $s) {
            $this->line('  eşleşmedi: '.($s['sku'] ?? '—').' · '.($s['ad'] ?? ''));
        }

        $this->info('Rapor: '.$raporYolu);

        if (! $this->option('uygula')) {
            $this->warn('Yalnız rapor — değişiklik yapılmadı. Uygulamak için --uygula ekleyin.');

            return self::SUCCESS;
        }

        Product::whereKey(array_keys($yayinaAl))->update(['is_active' => true]);
        Product::whereKey(array_keys($taslagaCek))->update(['is_active' => false]);

        $this->info(count($yayinaAl).' ürün yayına alındı, '.count($taslagaCek).' ürün taslağa çekildi.');

        return self::SUCCESS;
    }

    /**
     * @return array{0: list<array<string, string>>, 1: ?string}
     */
    private function oku(string $dosya): array
    {
        $icerik = file_get_contents($dosya);
        $icerik = preg_replace('/^\xEF\xBB\xBF/', '', $icerik);

        $ilkSatir = strtok($icerik, "\n");
        $ayrac = substr_count($ilkSatir, ';') > substr_count($ilkSatir, ',') ? ';' : ',';

        $fh = fopen('php://memory', 'r+');
        fwrite($fh, $icerik);
        rewind($fh);

        $baslik = fgetcsv($fh, 0, $ayrac, '"', '');
        if (! $baslik) {
            return [[], 'CSV başlığı okunamadı.'];
        }

        $sutun = [];
        foreach ($baslik as $i => $b) {
            $b = $this->kucuk(trim($b));
            foreach (self::BASLIKLAR as $anahtar => $esler) {
                if (! isset($sutun[$anahtar]) && in_array($b, $esler, true)) {
                    $sutun[$anahtar] = $i;
                }
            }
        }

        if (! isset($sutun['yayin'])) {
            return [[], 'CSV\'de "Yayımlanmış" / "Published" sütunu yok. Dışa aktarırken tüm sütunları seçin.'];
        }

        if (! isset($sutun['id']) && ! isset($sutun['sku']) && ! isset($sutun['ad'])) {
            return [[], 'CSV\'de ürünü tanıyacak sütun yok (ID, SKU ya da İsim).'];
        }

        $satirlar = [];
        while (($r = fgetcsv($fh, 0, $ayrac, '"', '')) !== false) {
            if ($r === [null] || $r === []) {
                continue;
            }
            $satir = [];
            foreach ($sutun as $anahtar => $i) {
                $satir[$anahtar] = (string) ($r[$i] ?? '');
            }
            $satirlar[] = $satir;
        }
        fclose($fh);

        return [$satirlar, null];
    }

    private function norm(string $ad): string
    {
        $ad = html_entity_decode($ad, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $this->kucuk(trim(preg_replace('/\s+/u', ' ', $ad)));
    }

    /** Türkçe küçük harf: mb_strtolower "İ"yi noktalı "i̇" yapıyor, başlık tanınmıyordu. */
    private function kucuk(string $s): string
    {
        return mb_strtolower(strtr($s, ['İ' => 'i', 'I' => 'ı']), 'UTF-8');
    }
}
