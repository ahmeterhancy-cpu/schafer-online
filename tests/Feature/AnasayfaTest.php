<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\VariantMatrix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnasayfaTest extends TestCase
{
    use RefreshDatabase;

    /** Seçeneksiz (tek tip) ürün — Schafer kataloğundaki gibi. */
    private function tekTipUrun(string $ad, float $fiyat, array $kategoriler = [], bool $aktif = true, int $stok = 5): Product
    {
        $urun = Product::create(['name' => $ad, 'is_active' => $aktif]);
        $urun->variants()->create(['sku' => 'SKU-'.$urun->id, 'price' => $fiyat, 'stock' => $stok]);
        $urun->categories()->sync($kategoriler);
        app(VariantMatrix::class)->refreshProduct($urun->fresh());

        return $urun->fresh();
    }

    public function test_anasayfa_acilir(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Schafer Kıbrıs', false);
    }

    public function test_urun_slayti_ana_sayfa_kategorilerinden_olusur(): void
    {
        $kat = Category::create(['name' => 'Pişirme', 'is_featured' => true]);
        $urun = $this->tekTipUrun('SCHAFER MATRIX DÖKÜM TENCERE SETİ', 13900, [$kat->id]);
        $urun->update(['hero_image' => 'urunler/yok.webp']);
        $urun->variants()->update(['compare_at_price' => 27800]);

        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('hero-slayt', $html);
        $this->assertStringContainsString('-%50', $html);
        $this->assertStringContainsString('Tüm Pişirme', $html);
    }

    public function test_urun_yokken_de_acilir(): void
    {
        // Boş katalog çökmemeli; ürünü olmayan bölüm çizilmez
        $this->get('/')->assertOk()->assertDontSee('Kampanyalı Ürünler', false);
    }

    public function test_ana_sayfada_gosterilen_kategorinin_urunleri_listelenir(): void
    {
        $sofra = Category::create(['name' => 'Sofra Grubu', 'is_featured' => true]);
        $alt = Category::create(['name' => 'Yemek Takımı', 'parent_id' => $sofra->id]);
        $gizliKat = Category::create(['name' => 'Gizli Kategori', 'is_featured' => false]);

        // Alt kategorideki ürün üst kategorinin bölümünde görünür
        $this->tekTipUrun('Fiesta Yemek Takımı', 5900, [$alt->id]);
        $this->tekTipUrun('Pasif Takım', 1000, [$alt->id], aktif: false);
        $this->tekTipUrun('Başka Ürün', 1000, [$gizliKat->id]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Sofra Grubu')
            ->assertSee('Fiesta Yemek Takımı')
            ->assertSee('5.900,00 ₺', false)
            ->assertDontSee('Pasif Takım')
            ->assertDontSee('Başka Ürün');
    }

    public function test_tek_tip_urun_kartta_dogrudan_sepete_eklenir(): void
    {
        $kat = Category::create(['name' => 'Tencereler', 'is_featured' => true]);
        $urun = $this->tekTipUrun('Düdüklü Tencere', 4560, [$kat->id]);
        $varyant = $urun->variants()->first();

        $this->get('/')->assertOk()
            ->assertSee('name="variant_id" value="'.$varyant->id.'"', false)
            ->assertSee('Sepete Ekle', false);

        $this->post('/sepet/ekle', ['variant_id' => $varyant->id])->assertRedirect('/sepet');
        $this->get('/sepet')->assertOk()->assertSee('Düdüklü Tencere');
    }

    public function test_tukenen_urun_ana_sayfa_bolumunde_gorunmez(): void
    {
        $kat = Category::create(['name' => 'Ev Tekstili', 'is_featured' => true]);
        $this->tekTipUrun('Stokta Havlu', 400, [$kat->id]);
        $this->tekTipUrun('Biten Nevresim', 900, [$kat->id], stok: 0);

        $this->get('/')->assertOk()->assertSee('Stokta Havlu')->assertDontSee('Biten Nevresim');
    }

    public function test_kargo_esigi_ust_seritte_gorunur(): void
    {
        config(['shop.kargo.ucretsiz_esigi' => 2500.00]);

        $this->get('/')->assertOk()->assertSee('2.500 TL üzeri ücretsiz teslimat', false);
    }

    public function test_yasal_sayfa_baglantilari_alt_bilgide_var(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Mesafeli Satış Sözleşmesi', false)
            ->assertSee('Gizlilik ve Kişisel Veriler', false)
            ->assertSee('İade Politikası', false)
            ->assertSee('Teslimat', false);
    }

    public function test_magaza_adresi_ve_telefonlari_gorunur(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Gönyeli', false)
            ->assertSee(config('shop.satici.telefon'), false)
            ->assertSee('DESTEK HATTI', false);
    }
}
