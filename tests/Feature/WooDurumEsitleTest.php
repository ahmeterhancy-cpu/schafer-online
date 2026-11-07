<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WooDurumEsitleTest extends TestCase
{
    use RefreshDatabase;

    private string $csv;

    private function urun(string $ad, bool $aktif, ?int $wooId = null, ?string $sku = null): Product
    {
        $u = Product::create(['name' => $ad, 'is_active' => $aktif, 'woo_id' => $wooId]);
        $u->variants()->create(['sku' => $sku ?? 'SKU-'.$u->id, 'price' => 100, 'stock' => 5]);

        return $u;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->csv = tempnam(sys_get_temp_dir(), 'woo').'.csv';
    }

    protected function tearDown(): void
    {
        @unlink($this->csv);
        parent::tearDown();
    }

    private function yaz(string $icerik): void
    {
        file_put_contents($this->csv, "\xEF\xBB\xBF".$icerik);
    }

    public function test_turkce_basliklarla_kimlik_sku_ve_ad_eslesir(): void
    {
        $idIle = $this->urun('Fiesta Tava', false, wooId: 101);
        $skuIle = $this->urun('Havlu', true, sku: '3ST517-34902-EKR01');
        $adIle = $this->urun('Schafer Luis 60 Parça Çatal Takımı', true);
        $ozel = $this->urun('Özel Ürün', true, wooId: 104);
        $csvdeYok = $this->urun('CSV Dışı', true);

        $this->yaz(implode("\n", [
            'Kimlik,Tür,Stok kodu (SKU),İsim,Yayımlanmış',
            '101,simple,,Fiesta Tava,1',
            '999,simple,3ST517-34902-EKR01,Havlu (yeni ad),0',
            '555,simple,,"Schafer  Luis 60 Parça Çatal Takımı",0',
            '104,simple,,Özel Ürün,-1',
            '777,simple,YOK-1,Bilinmeyen Ürün,1',
            '888,variation,X,Varyasyon,0',
        ]));

        // Rapor modu: hiçbir şey değişmez
        $this->artisan('woo:durum-esitle', ['dosya' => $this->csv])->assertSuccessful();
        $this->assertFalse($idIle->fresh()->is_active);
        $this->assertTrue($skuIle->fresh()->is_active);

        $this->artisan('woo:durum-esitle', ['dosya' => $this->csv, '--uygula' => true])->assertSuccessful();

        $this->assertTrue($idIle->fresh()->is_active, 'Woo kimliğiyle yayına alınmalı');
        $this->assertFalse($skuIle->fresh()->is_active, 'SKU ile taslağa çekilmeli');
        $this->assertFalse($adIle->fresh()->is_active, 'Ad (boşluk farkına rağmen) ile eşleşmeli');
        $this->assertFalse($ozel->fresh()->is_active, 'Özel (-1) taslak sayılır');
        $this->assertTrue($csvdeYok->fresh()->is_active, 'CSV\'de olmayana dokunulmaz');
    }

    public function test_ingilizce_baslik_ve_noktali_virgul_ayraci(): void
    {
        $u = $this->urun('Kettle', true, wooId: 5);

        $this->yaz("ID;Type;SKU;Name;Published\n5;simple;;Kettle;0\n");

        $this->artisan('woo:durum-esitle', ['dosya' => $this->csv, '--uygula' => true])->assertSuccessful();

        $this->assertFalse($u->fresh()->is_active);
    }

    public function test_yayin_sutunu_yoksa_hata_verir(): void
    {
        $this->yaz("ID,Name\n1,X\n");

        $this->artisan('woo:durum-esitle', ['dosya' => $this->csv])->assertFailed();
    }
}
