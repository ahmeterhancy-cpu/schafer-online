<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * İlk kurulum: yerelde hazırlanmış SQL dökümünü (şema + veri) canlı veritabanına yükler.
 *
 * Sunucuda terminal yok; .cpanel.yml dosya VARSA bu komutu çalıştırır.
 * Güvenlik kilidi: veritabanında tek bir sipariş bile varsa YÜKLEMEZ —
 * döküm tabloları silip yeniden kurar, canlı siparişler kaybolurdu.
 * Başarılı yüklemeden sonra dosya yeniden adlandırılır; ikinci deploy'da
 * tekrar çalışmaz.
 */
class VeriYukle extends Command
{
    protected $signature = 'schafer:veri-yukle {dosya : SQL dökümünün tam yolu}';

    protected $description = 'İlk kurulum SQL dökümünü yükler (sipariş varsa reddeder)';

    public function handle(): int
    {
        $dosya = (string) $this->argument('dosya');

        if (! is_file($dosya)) {
            $this->line("Dosya yok, yükleme atlandı = {$dosya}");

            return self::SUCCESS;
        }

        if (Schema::hasTable('orders') && DB::table('orders')->exists()) {
            $this->error('REDDEDİLDİ = veritabanında sipariş var; döküm canlı veriyi silerdi.');

            return self::FAILURE;
        }

        try {
            DB::unprepared((string) file_get_contents($dosya));
        } catch (Throwable $e) {
            $this->error('YÜKLEME HATASI = '.$e->getMessage());

            return self::FAILURE;
        }

        $yeni = $dosya.'.yuklendi-'.date('Ymd-His');
        rename($dosya, $yeni);
        $this->info('Yüklendi = '.DB::table('products')->count().' ürün; dosya '.basename($yeni).' olarak saklandı');

        return self::SUCCESS;
    }
}
