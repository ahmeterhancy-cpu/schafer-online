<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Throwable;
use ZipArchive;

/**
 * Canlı sunucuda ilk kurulum — sunucuda terminal yok, .cpanel.yml her deploy'da çağırır.
 * Her adım YALNIZ gerektiğinde çalışır; kurulumdan sonraki deploy'larda hiçbir şey yapmaz.
 *
 *  1. Görseller: storage/app/public/urunler yoksa KURULUM_GORSEL_URL'deki zip indirilip açılır.
 *  2. Veri: ürün tablosu yok/boşsa KURULUM_VERI_URL'deki SQL dökümü yüklenir
 *     (schafer:veri-yukle — veritabanında sipariş varsa reddeder).
 *  3. Yönetici: hiç yönetici yoksa SHOP_SATICI_EPOSTA adresiyle oluşturulur; rastgele
 *     parola web'e KAPALI ana dizindeki yonetici-giris.txt dosyasına yazılır
 *     (giriş yapıldıktan sonra parola değiştirilip dosya silinmeli).
 */
class IlkKurulum extends Command
{
    protected $signature = 'schafer:ilk-kurulum';

    protected $description = 'Canlıda ilk kurulum: görseller, veri ve yönetici (yalnız eksikse)';

    public function handle(): int
    {
        $sonuc = self::SUCCESS;

        // 1. Görseller
        $hedef = storage_path('app/public');
        if (is_dir($hedef.'/urunler')) {
            $this->line('Görseller zaten var, atlandı');
        } elseif ($url = config('shop.kurulum.gorsel_url')) {
            $sonuc = $this->gorseller($url, $hedef) ? $sonuc : self::FAILURE;
        } else {
            $this->line('KURULUM_GORSEL_URL yok, görseller atlandı');
        }

        // 2. Veri
        $bos = ! Schema::hasTable('products') || ! DB::table('products')->exists();
        if (! $bos) {
            $this->line('Veri zaten var, atlandı');
        } elseif ($url = config('shop.kurulum.veri_url')) {
            $dosya = storage_path('app/private/ilk-veri.sql');
            if ($this->indir($url, $dosya)) {
                $kod = Artisan::call('schafer:veri-yukle', ['dosya' => $dosya]);
                $this->line(trim(Artisan::output()));
                $sonuc = $kod === 0 ? $sonuc : self::FAILURE;
            } else {
                $sonuc = self::FAILURE;
            }
        } else {
            $this->line('KURULUM_VERI_URL yok, veri atlandı');
        }

        // 3. Yönetici
        if (Schema::hasTable('users') && ! User::where('role', 'admin')->exists()) {
            $this->yonetici();
        }

        return $sonuc;
    }

    private function indir(string $url, string $dosya): bool
    {
        @mkdir(dirname($dosya), 0775, true);

        // Sunucuya elle yüklenmiş dosya (Dosya Yöneticisi) — indirme yok
        if (! preg_match('#^https?://#i', $url)) {
            if (! is_file($url)) {
                $this->error('DOSYA YOK = '.$url);

                return false;
            }
            copy($url, $dosya);
            $this->line('Yerel dosya kullanıldı = '.basename($url));

            return true;
        }

        try {
            $yanit = Http::timeout(600)->withOptions(['sink' => $dosya])->get($url);
        } catch (Throwable $e) {
            $this->error('İNDİRME HATASI = '.$e->getMessage());

            return false;
        }
        // Sahte (test) yanıtlar sink'e yazılmaz — gövdeyi elle yaz
        if ($yanit->successful() && (! is_file($dosya) || filesize($dosya) === 0)) {
            file_put_contents($dosya, $yanit->body());
        }
        if (! $yanit->successful() || ! is_file($dosya) || filesize($dosya) === 0) {
            $this->error('İNDİRME BAŞARISIZ = HTTP '.$yanit->status());
            @unlink($dosya);

            return false;
        }
        $this->line('İndirildi = '.basename($dosya).' ('.round(filesize($dosya) / 1048576, 1).' MB)');

        return true;
    }

    private function gorseller(string $url, string $hedef): bool
    {
        $zip = storage_path('app/private/gorseller.zip');
        if (! $this->indir($url, $zip)) {
            return false;
        }
        $z = new ZipArchive;
        if ($z->open($zip) !== true) {
            $this->error('ZIP AÇILAMADI');

            return false;
        }
        @mkdir($hedef, 0775, true);
        $z->extractTo($hedef);
        $adet = $z->numFiles;
        $z->close();
        @unlink($zip);
        $this->line("Görseller açıldı = {$adet} dosya");

        return true;
    }

    private function yonetici(): void
    {
        $eposta = (string) (config('shop.satici.eposta') ?: 'admin@'.config('shop.alan_adi', 'example.com'));
        $parola = '';
        $havuz = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        for ($i = 0; $i < 18; $i++) {
            $parola .= $havuz[random_int(0, strlen($havuz) - 1)];
        }

        User::updateOrCreate(['email' => $eposta], ['name' => 'Yönetici', 'password' => Hash::make($parola), 'role' => 'admin']);

        // Varsayılan: ana dizin (uygulamanın bir üstü) — web'e kapalı
        $dosya = (string) config('shop.kurulum.giris_dosyasi');
        file_put_contents($dosya, "Panel = ".rtrim((string) config('app.url'), '/')."/admin\nE-posta = {$eposta}\nParola = {$parola}\n\nGiris yaptiktan sonra parolayi degistirin ve BU DOSYAYI SILIN.\n");
        @chmod($dosya, 0600);
        $this->info('Yönetici oluşturuldu = '.$eposta.' (parola '.basename($dosya).' dosyasında)');
    }
}
