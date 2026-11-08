<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteAyarlari;
use App\Http\Middleware\BakimPerdesi;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class YapimVeFiyatTest extends TestCase
{
    use RefreshDatabase;

    private Product $urun;

    protected function setUp(): void
    {
        parent::setUp();
        config(['shop.bakim_modu' => false, 'shop.fiyat.gizle' => false, 'shop.fiyat.uyelere_goster' => false, 'shop.yapim.pin' => '']);

        $this->urun = Product::create(['name' => 'Düdüklü Tencere', 'is_active' => true, 'is_featured' => true]);
        ProductVariant::create(['product_id' => $this->urun->id, 'sku' => 'SCH-1', 'price' => 1234.50, 'compare_at_price' => 2469, 'stock' => 5, 'is_active' => true]);
        $this->urun->refresh();
    }

    // --- Yapım aşaması ---

    public function test_yapim_sayfasi_panelden_girilen_baslik_ve_metni_gosterir(): void
    {
        config(['shop.bakim_modu' => true, 'shop.yapim.baslik' => 'Yeni mağazamız geliyor', 'shop.yapim.metin' => 'Kasım sonunda açılıyoruz.']);

        $this->get('/')->assertStatus(503)
            ->assertSee('Yeni mağazamız geliyor')
            ->assertSee('Kasım sonunda açılıyoruz.')
            ->assertDontSee('Önizleme kodu'); // PIN yoksa form yok
    }

    public function test_dogru_pin_cihazda_siteyi_acar_yanlis_pin_acmaz(): void
    {
        config(['shop.bakim_modu' => true, 'shop.yapim.pin' => '2468']);

        $this->get('/koleksiyonlar')->assertStatus(503)->assertSee('Önizleme kodu');

        $this->from('/koleksiyonlar')->post('/onizleme-pin', ['pin' => '1111', 'to' => '/koleksiyonlar'])
            ->assertRedirect('/koleksiyonlar')
            ->assertCookieMissing(BakimPerdesi::CEREZ);

        $yanit = $this->post('/onizleme-pin', ['pin' => '2468', 'to' => '/koleksiyonlar'])
            ->assertRedirect('/koleksiyonlar')
            ->assertCookie(BakimPerdesi::CEREZ);

        $cerez = $yanit->getCookie(BakimPerdesi::CEREZ, false)->getValue();
        $this->withUnencryptedCookie(BakimPerdesi::CEREZ, $cerez)
            ->get('/')->assertOk()->assertSee('Önizleme — site henüz ziyaretçilere kapalı');
    }

    public function test_pin_degisince_eski_cerez_gecersiz(): void
    {
        config(['shop.bakim_modu' => true, 'shop.yapim.pin' => '2468']);
        $eski = BakimPerdesi::jeton('2468');
        config(['shop.yapim.pin' => '9999']);

        $this->withUnencryptedCookie(BakimPerdesi::CEREZ, $eski)->get('/')->assertStatus(503);
    }

    public function test_pin_formu_dis_adrese_yonlendirmez(): void
    {
        config(['shop.bakim_modu' => true, 'shop.yapim.pin' => '2468']);

        $this->post('/onizleme-pin', ['pin' => '2468', 'to' => '//kotu.example/x'])->assertRedirect('/');
    }

    public function test_panelden_yapim_ayarlari_kaydedilir(): void
    {
        $yonetici = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($yonetici)->test(SiteAyarlari::class)
            ->fillForm(['bakim_modu' => true, 'yapim_pin' => '12', 'fiyat_gizle' => true])
            ->call('save')
            ->assertHasFormErrors(['yapim_pin']);

        Livewire::actingAs($yonetici)->test(SiteAyarlari::class)
            ->fillForm(['bakim_modu' => true, 'yapim_pin' => '2468', 'yapim_baslik' => 'Az kaldı', 'fiyat_gizle' => true, 'fiyat_metin' => 'Fiyat için arayın'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(config('shop.bakim_modu'));
        $this->assertSame('2468', config('shop.yapim.pin'));
        $this->assertSame('Az kaldı', config('shop.yapim.baslik'));
        $this->assertTrue(config('shop.fiyat.gizle'));
        $this->assertSame('Fiyat için arayın', config('shop.fiyat.metin'));
    }

    // --- Fiyatları gizle ---

    public function test_fiyat_gizliyken_vitrinde_fiyat_ve_sepet_yok(): void
    {
        config(['shop.fiyat.gizle' => true]);

        $this->get('/koleksiyonlar')->assertOk()
            ->assertDontSee('1.234,50')
            ->assertDontSee('-%50')
            ->assertDontSee('Sepete Ekle')
            ->assertSee('Fiyat bilgisi için bize ulaşın')
            ->assertSee('Fiyat sor')
            ->assertDontSee('Fiyat: düşükten yükseğe');

        $this->get('/urun/'.$this->urun->slug)->assertOk()
            ->assertDontSee('1.234,50')
            ->assertDontSee('1234.5')          // varyant JSON'u
            ->assertDontSee('"price"', false)  // JSON-LD teklifi
            ->assertDontSee('sepet-formu', false)
            ->assertSee('Fiyat sor')
            ->assertSee('wa.me', false);
    }

    public function test_fiyat_gizliyken_sepet_odeme_ve_xml_kapali(): void
    {
        config(['shop.fiyat.gizle' => true]);
        $v = $this->urun->variants->first();

        $this->post('/sepet/ekle', ['variant_id' => $v->id, 'quantity' => 1])->assertRedirect(route('home'));
        $this->get('/sepet')->assertRedirect(route('home'));
        $this->get('/odeme')->assertRedirect(route('home'));
        $this->get('/xml/urunler.xml')->assertNotFound();
        $this->assertSame([], session('cart', []));
    }

    public function test_fiyat_gizliyken_adres_satirindaki_fiyat_suzgeci_yok_sayilir(): void
    {
        config(['shop.fiyat.gizle' => true]);

        // 1234,50 TL'lik ürün, süzgeç uygulansaydı listeden düşerdi
        $this->get('/koleksiyonlar?fiyat_min=5000')->assertOk()->assertSee('Düdüklü Tencere');
    }

    public function test_uyelere_goster_aciksa_giris_yapan_musteri_fiyati_gorur(): void
    {
        config(['shop.fiyat.gizle' => true, 'shop.fiyat.uyelere_goster' => true]);
        $musteri = User::factory()->create(['role' => 'customer']);

        $this->get('/urun/'.$this->urun->slug)->assertDontSee('1.234,50');
        $this->actingAs($musteri)->get('/urun/'.$this->urun->slug)->assertSee('1.234,50')->assertSee('sepet-formu', false);
    }

    public function test_yonetici_fiyat_gizliyken_de_gorur_ve_uyarilir(): void
    {
        config(['shop.fiyat.gizle' => true]);
        $yonetici = User::factory()->create(['role' => 'admin']);

        $this->actingAs($yonetici)->get('/urun/'.$this->urun->slug)
            ->assertSee('1.234,50')
            ->assertSee('Fiyatlar GİZLİ', false);
    }

    public function test_fiyat_acikken_her_sey_normal(): void
    {
        $this->get('/urun/'.$this->urun->slug)->assertOk()
            ->assertSee('1.234,50')
            ->assertSee('sepet-formu', false)
            ->assertDontSee('Fiyat sor');
        $this->get('/xml/urunler.xml')->assertOk();
    }
}
