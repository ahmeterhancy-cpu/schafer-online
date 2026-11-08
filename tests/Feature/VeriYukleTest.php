<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VeriYukleTest extends TestCase
{
    use RefreshDatabase;

    private function dosya(string $sql): string
    {
        $yol = tempnam(sys_get_temp_dir(), 'veri').'.sql';
        file_put_contents($yol, $sql);

        return $yol;
    }

    public function test_dosya_yoksa_sessizce_atlar(): void
    {
        $this->artisan('schafer:veri-yukle', ['dosya' => '/yok/boyle-bir.sql'])->assertSuccessful();
    }

    public function test_dokumu_yukler_ve_dosyayi_yeniden_adlandirir(): void
    {
        $yol = $this->dosya("INSERT INTO settings (key, value) VALUES ('instagram', 'deneme');");

        $this->artisan('schafer:veri-yukle', ['dosya' => $yol])->assertSuccessful();

        $this->assertSame('deneme', DB::table('settings')->where('key', 'instagram')->value('value'));
        $this->assertFileDoesNotExist($yol);
        $this->assertNotEmpty(glob($yol.'.yuklendi-*'));
    }

    public function test_siparis_varsa_reddeder(): void
    {
        Order::forceCreate([
            'number' => 'SCH-1', 'customer_name' => 'A', 'customer_email' => 'a@example.test', 'customer_phone' => '1',
            'shipping_address' => [], 'subtotal' => 1, 'shipping_total' => 0, 'discount_total' => 0, 'grand_total' => 1,
        ]);
        $yol = $this->dosya("INSERT INTO settings (key, value) VALUES ('instagram', 'deneme');");

        $this->artisan('schafer:veri-yukle', ['dosya' => $yol])->assertFailed();

        $this->assertFileExists($yol);
        $this->assertNull(DB::table('settings')->where('key', 'instagram')->value('value'));
    }
}
