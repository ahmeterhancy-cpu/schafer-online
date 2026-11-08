<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use ZipArchive;

class IlkKurulumTest extends TestCase
{
    use RefreshDatabase;

    private string $kok;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kok = sys_get_temp_dir().'/ilk-kurulum-'.uniqid();
        mkdir($this->kok.'/public', 0777, true);
        $this->app->useStoragePath($this->kok);
        config([
            'shop.kurulum.gorsel_url' => 'https://ornek.test/gorseller.zip',
            'shop.kurulum.veri_url' => 'https://ornek.test/ilk-veri.sql',
            'shop.kurulum.giris_dosyasi' => $this->kok.'/yonetici-giris.txt',
            'shop.satici.eposta' => 'info@ornek.test',
        ]);
    }

    private function zip(): string
    {
        $yol = $this->kok.'/kaynak.zip';
        $z = new ZipArchive;
        $z->open($yol, ZipArchive::CREATE);
        $z->addFromString('urunler/tencere-1.webp', 'gorsel-verisi');
        $z->close();

        return (string) file_get_contents($yol);
    }

    public function test_bos_kurulumda_gorsel_veri_ve_yonetici_kurulur(): void
    {
        Http::fake([
            'ornek.test/gorseller.zip' => Http::response($this->zip()),
            'ornek.test/ilk-veri.sql' => Http::response("INSERT INTO products (name, slug, is_active, created_at, updated_at) VALUES ('Tencere', 'tencere', 1, '2026-01-01', '2026-01-01');"),
        ]);

        $this->artisan('schafer:ilk-kurulum')->assertSuccessful();

        $this->assertFileExists($this->kok.'/app/public/urunler/tencere-1.webp');
        $this->assertSame(1, Product::count());
        $yonetici = User::where('role', 'admin')->first();
        $this->assertSame('info@ornek.test', $yonetici->email);
        $giris = (string) file_get_contents($this->kok.'/yonetici-giris.txt');
        preg_match('/Parola = (\S+)/', $giris, $m);
        $this->assertTrue(Hash::check($m[1], $yonetici->password));
    }

    public function test_kurulu_sistemde_hicbir_sey_yapmaz(): void
    {
        mkdir($this->kok.'/app/public/urunler', 0777, true);
        Product::create(['name' => 'Var olan', 'is_active' => true]);
        User::factory()->create(['role' => 'admin', 'email' => 'eski@ornek.test']);
        Http::fake();

        $this->artisan('schafer:ilk-kurulum')->assertSuccessful();

        Http::assertNothingSent();
        $this->assertFileDoesNotExist($this->kok.'/yonetici-giris.txt');
        $this->assertSame(1, Product::count());
    }

    public function test_indirme_hatasi_basarisiz_doner(): void
    {
        Http::fake(['*' => Http::response('', 404)]);

        $this->artisan('schafer:ilk-kurulum')->assertFailed();
        $this->assertSame(0, DB::table('products')->count());
    }

    public function test_sunucuya_elle_yuklenmis_dosyalari_kullanir(): void
    {
        file_put_contents($this->kok.'/elle.zip', $this->zip());
        file_put_contents($this->kok.'/elle.sql', "INSERT INTO products (name, slug, is_active, created_at, updated_at) VALUES ('Tava', 'tava', 1, '2026-01-01', '2026-01-01');");
        config(['shop.kurulum.gorsel_url' => $this->kok.'/elle.zip', 'shop.kurulum.veri_url' => $this->kok.'/elle.sql']);
        Http::fake();

        $this->artisan('schafer:ilk-kurulum')->assertSuccessful();

        Http::assertNothingSent();
        $this->assertFileExists($this->kok.'/app/public/urunler/tencere-1.webp');
        $this->assertSame('Tava', Product::value('name'));
    }
}
