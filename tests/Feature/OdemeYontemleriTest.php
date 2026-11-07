<?php

namespace Tests\Feature;

use App\Mail\OrderPlaced;
use App\Models\LegalDocument;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart;
use App\Services\CevrimdisiOdeme;
use App\Services\OrderShipping;
use App\Services\PaymentRefunds;
use App\Services\VariantMatrix;
use App\Support\OdemeYontemleri;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

/**
 * Havale/EFT ve kapıda ödeme (kartın yanında).
 */
class OdemeYontemleriTest extends TestCase
{
    use RefreshDatabase;

    private ProductVariant $varyant;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        config([
            'shop.odeme.kart' => true,
            'shop.odeme.havale' => true,
            'shop.odeme.kapida' => true,
            'shop.odeme.havale_hesaplari' => "Örnek Bankası | Schafer Ltd. | TR00 0001 0002 0003 0004 0005 06\nİkinci Banka | Schafer Ltd. | TR11 2222",
            'shop.odeme.havale_sure_gun' => 3,
            'shop.odeme.kapida_ek_ucret' => 0,
            'shop.kargo.ucret' => 150,
            'shop.kargo.ucretsiz_esigi' => 2500,
        ]);

        LegalDocument::publish('mesafeli-satis', 'Mesafeli Satış Sözleşmesi', 'Metin');

        $urun = Product::create(['name' => 'Düdüklü Tencere', 'is_active' => true]);
        $this->varyant = $urun->variants()->create(['sku' => 'SCHTEK', 'price' => 4560, 'stock' => 5]);
        app(VariantMatrix::class)->refreshProduct($urun->fresh());

        app(Cart::class)->add($this->varyant, 1);
    }

    private function form(string $yontem, array $ek = []): array
    {
        return array_merge([
            'ad' => 'Mehmet Kaya',
            'eposta' => 'mehmet@example.test',
            'telefon' => '05488888888',
            'adres' => 'Düzyol Sok. 10',
            'ilce' => 'Gönyeli',
            'il' => 'Lefkoşa',
            'sozlesme_onay' => '1',
            'fatura_tipi' => 'bireysel',
            'odeme_yontemi' => $yontem,
            'sozlesme_surum' => LegalDocument::current('mesafeli-satis')?->version,
            'onbilgi_surum' => LegalDocument::current('on-bilgilendirme')?->version,
        ], $ek);
    }

    public function test_kasada_uc_yontem_gorunur(): void
    {
        $this->get('/odeme')->assertOk()
            ->assertSee('value="paytr"', false)
            ->assertSee('value="havale"', false)
            ->assertSee('value="kapida"', false);
    }

    public function test_hesap_girilmeden_havale_sunulmaz(): void
    {
        config(['shop.odeme.havale_hesaplari' => '']);

        $this->assertFalse(OdemeYontemleri::etkinMi('havale'));
        $this->get('/odeme')->assertOk()->assertDontSee('value="havale"', false);
    }

    public function test_kapali_yontemle_siparis_verilemez(): void
    {
        config(['shop.odeme.kapida' => false]);

        $this->post('/odeme', $this->form('kapida'))->assertSessionHasErrors('odeme_yontemi');
        $this->assertSame(0, Order::count());
    }

    public function test_havale_siparisi_alinir_stok_ayrilir_iban_gosterilir(): void
    {
        $yanit = $this->post('/odeme', $this->form('havale'));

        $order = Order::firstOrFail();
        $yanit->assertRedirect();
        $this->assertStringContainsString('/siparis/'.$order->number, $yanit->headers->get('Location'));

        $this->assertSame('havale', $order->payment_provider);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('pending', $order->status);
        $this->assertSame('reserved', $order->stock_state);
        $this->assertSame(1, $this->varyant->fresh()->reserved);
        $this->assertSame(5, $this->varyant->fresh()->stock);

        // Talimatlı onay e-postası sipariş anında
        Mail::assertSent(OrderPlaced::class, fn ($m) => $m->hasTo('mehmet@example.test'));

        $this->followRedirects($yanit)->assertOk()
            ->assertSee('Havale / EFT ile ödeme', false)
            ->assertSee('TR00 0001 0002 0003 0004 0005 06')
            ->assertSee($order->number);

        $this->assertTrue(app(Cart::class)->isEmpty());
    }

    public function test_havale_onaylaninca_stok_duser_eposta_tekrarlanmaz(): void
    {
        $this->post('/odeme', $this->form('havale'));
        $order = Order::firstOrFail();

        app(CevrimdisiOdeme::class)->havaleOnayla($order, 'yonetici@example.test');

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('paid', $order->status);
        $this->assertSame('committed', $order->stock_state);
        $this->assertSame(4, $this->varyant->fresh()->stock);
        $this->assertSame(0, $this->varyant->fresh()->reserved);

        // Müşteriye ve (varsa) mağazaya; onayda ikinci kez gitmemeli
        Mail::assertSent(OrderPlaced::class, 1);
    }

    public function test_suresi_dolan_havale_iptal_edilir_kart_kurali_dokunmaz(): void
    {
        $this->post('/odeme', $this->form('havale'));
        $order = Order::firstOrFail();

        // 2 saat sonra: kartın 90 dk kuralı havaleye DOKUNMAMALI
        $this->travel(2)->hours();
        Artisan::call('schafer:rezerv-temizle');
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('reserved', $order->fresh()->stock_state);

        // 3 günden sonra iptal, stok serbest
        $this->travel(3)->days();
        Artisan::call('schafer:rezerv-temizle');

        $order->refresh();
        $this->assertSame('cancelled', $order->status);
        $this->assertSame('none', $order->stock_state);
        $this->assertSame(0, $this->varyant->fresh()->reserved);
    }

    public function test_kapida_odeme_bedeli_toplama_eklenir(): void
    {
        config(['shop.odeme.kapida_ek_ucret' => 50]);

        $this->get('/odeme')->assertOk()->assertSee('Kapıda ödeme bedeli', false);

        $this->post('/odeme', $this->form('kapida'))->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('50.00', $order->payment_fee);
        // 4560 ürün + 0 kargo (eşik üstü) + 50
        $this->assertSame('4610.00', $order->grand_total);
    }

    public function test_kapida_siparis_hemen_onaylanir_teslimde_odenir(): void
    {
        $this->post('/odeme', $this->form('kapida'));
        $order = Order::firstOrFail();

        $this->assertSame('kapida', $order->payment_provider);
        $this->assertSame('preparing', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('committed', $order->stock_state);
        $this->assertSame(4, $this->varyant->fresh()->stock);

        $kargo = app(OrderShipping::class);
        $kargo->markShipped($order->fresh(), 'Kurye', 'K-1');
        $this->assertSame('shipped', $order->fresh()->status);

        $kargo->markDelivered($order->fresh());

        $order->refresh();
        $this->assertSame('delivered', $order->status, 'Ödeme onayı teslim durumunu geri almamalı');
        $this->assertSame('paid', $order->payment_status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(4, $this->varyant->fresh()->stock, 'Stok iki kez düşmemeli');
        Mail::assertSent(OrderPlaced::class, 1);
    }

    public function test_odenmemis_kart_siparisi_hala_kargolanamaz(): void
    {
        $this->post('/odeme', $this->form('paytr'));
        $order = Order::firstOrFail();

        $this->assertSame('paytr', $order->payment_provider);

        $this->expectException(RuntimeException::class);
        app(OrderShipping::class)->markShipped($order, 'Kurye', 'K-1');
    }

    public function test_havale_siparisine_paytr_iadesi_denenemez(): void
    {
        $this->post('/odeme', $this->form('havale'));
        $order = Order::firstOrFail();
        app(CevrimdisiOdeme::class)->havaleOnayla($order);

        $this->assertFalse(PaymentRefunds::kartlaOdendi($order->fresh()));

        $this->expectException(RuntimeException::class);
        app(PaymentRefunds::class)->refundOrder($order->fresh());
    }

    public function test_yontem_gonderilmezse_kart_varsayilir(): void
    {
        $form = $this->form('paytr');
        unset($form['odeme_yontemi']);

        $this->post('/odeme', $form)->assertSessionHasNoErrors();
        $this->assertSame('paytr', Order::firstOrFail()->payment_provider);
    }
}
