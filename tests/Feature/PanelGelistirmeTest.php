<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Widgets\CokSatanlar;
use App\Filament\Widgets\KritikStok;
use App\Filament\Widgets\SatisGrafigi;
use App\Filament\Widgets\SonSiparisler;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\TopluUrunIslemleri;
use App\Services\VariantMatrix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Panel geliştirmeleri: toplu ürün işlemleri, sipariş detay sayfası,
 * pano bileşenleri, kategori ağacı, yazdırılabilir fiş.
 */
class PanelGelistirmeTest extends TestCase
{
    use RefreshDatabase;

    private User $yonetici;

    protected function setUp(): void
    {
        parent::setUp();

        $this->yonetici = User::factory()->create(['role' => 'admin']);
    }

    private function urun(string $ad, float $fiyat, int $stok = 10, ?float $eski = null): Product
    {
        $urun = Product::create(['name' => $ad, 'is_active' => true]);
        $urun->variants()->create(['sku' => 'SKU-'.$urun->id, 'price' => $fiyat, 'compare_at_price' => $eski, 'stock' => $stok]);
        app(VariantMatrix::class)->refreshProduct($urun->fresh());

        return $urun->fresh();
    }

    private function siparis(array $alanlar = []): Order
    {
        $urun = $this->urun('Fiesta Tava', 1330);

        $order = Order::create(array_merge([
            'number' => 'SCH-TEST-0001', 'status' => 'paid', 'payment_status' => 'paid', 'payment_provider' => 'paytr',
            'customer_name' => 'Mehmet Kaya', 'customer_email' => 'mehmet@example.test', 'customer_phone' => '05480000000',
            'shipping_address' => ['name' => 'Mehmet Kaya', 'line1' => 'Düzyol Sok. 10', 'district' => 'Gönyeli', 'city' => 'Lefkoşa'],
            'subtotal' => 2660, 'grand_total' => 2660, 'paid_at' => now(),
        ], $alanlar));

        OrderItem::create(['order_id' => $order->id, 'product_id' => $urun->id, 'product_variant_id' => $urun->variants()->value('id'),
            'name' => 'Fiesta Tava', 'sku' => 'FST-28', 'unit_price' => 1330, 'quantity' => 2, 'line_total' => 2660]);

        return $order->fresh('items');
    }

    // --- Toplu ürün işlemleri ---

    public function test_yuzde_zam_ve_yuvarlama(): void
    {
        $a = $this->urun('A', 1000);
        $b = $this->urun('B', 1234.56);

        $n = app(TopluUrunIslemleri::class)->fiyatDegistir(collect([$a, $b]), 'yuzde', 10, 'on');

        $this->assertSame(2, $n);
        $this->assertSame('1100.00', $a->variants()->value('price'));
        $this->assertSame('1360.00', $b->variants()->value('price')); // 1358,02 → 10'a
        $this->assertSame('1100.00', $a->fresh()->min_price, 'Ürün önbelleği tazelenmeli');
    }

    public function test_tutar_dususu_eski_fiyati_anlamsizsa_temizler(): void
    {
        $a = $this->urun('A', 900, eski: 950);

        app(TopluUrunIslemleri::class)->fiyatDegistir(collect([$a]), 'tutar', 100, 'yok');

        $v = $a->variants()->first();
        $this->assertSame('1000.00', $v->price);
        $this->assertNull($v->compare_at_price, 'Eski fiyat yeni fiyatın altında kaldıysa indirim kalkar');
    }

    public function test_kampanya_indirimi_birikmez_ve_kaldirilir(): void
    {
        $a = $this->urun('A', 1000);
        $islem = app(TopluUrunIslemleri::class);

        $islem->indirimUygula(collect([$a]), 20, 'tam');
        $islem->indirimUygula(collect([$a]), 20, 'tam'); // ikinci kez: hâlâ eski fiyattan %20

        $v = $a->variants()->first();
        $this->assertSame('800.00', $v->price);
        $this->assertSame('1000.00', $v->compare_at_price);

        $islem->indirimKaldir(collect([$a]));
        $v->refresh();
        $this->assertSame('1000.00', $v->price);
        $this->assertNull($v->compare_at_price);
    }

    public function test_normal_fiyat_satis_fiyatinin_iki_kati_olur(): void
    {
        $a = $this->urun('A', 1450);
        $b = $this->urun('B', 800, eski: 1000);   // zaten indirimli: eski fiyat değişir
        $c = $this->urun('C', 0);                 // fiyatsız: atlanır

        $n = app(TopluUrunIslemleri::class)->normalFiyatiKatla(collect([$a, $b, $c]), 2);

        $this->assertSame(2, $n);
        $this->assertSame('1450.00', $a->variants()->value('price'), 'Satış fiyatı değişmemeli');
        $this->assertSame('2900.00', $a->variants()->value('compare_at_price'));
        $this->assertSame('1600.00', $b->variants()->value('compare_at_price'));
        $this->assertNull($c->variants()->value('compare_at_price'));
        $this->assertSame(50, Product::kartIcin()->find($a->id)->indirim_orani);
    }

    public function test_stok_ayarlama_rezervin_altina_inmez(): void
    {
        $a = $this->urun('A', 100, stok: 3);
        $a->variants()->update(['reserved' => 2]);

        app(TopluUrunIslemleri::class)->stokAyarla(collect([$a->fresh()]), 0);
        $this->assertSame(2, $a->variants()->value('stock'));

        app(TopluUrunIslemleri::class)->stokAyarla(collect([$a->fresh()]), 10);
        $this->assertSame(10, $a->variants()->value('stock'));
    }

    public function test_gecersiz_degerler_reddedilir(): void
    {
        $a = $this->urun('A', 100);

        $this->expectException(InvalidArgumentException::class);
        app(TopluUrunIslemleri::class)->indirimUygula(collect([$a]), 100);
    }

    // --- Ürün listesi ---

    public function test_urun_listesi_kategori_agaciyla_suzulur_ve_sku_ile_aranir(): void
    {
        $mutfak = Category::create(['name' => 'Mutfak']);
        $tencere = Category::create(['name' => 'Tencere', 'parent_id' => $mutfak->id]);
        $tekstil = Category::create(['name' => 'Tekstil']);

        $a = $this->urun('Düdüklü', 4560);
        $a->categories()->attach($tencere);
        $b = $this->urun('Havlu', 400);
        $b->categories()->attach($tekstil);

        Livewire::actingAs($this->yonetici)->test(ListProducts::class)
            ->filterTable('kategori', $mutfak->id)
            ->assertCanSeeTableRecords([$a])
            ->assertCanNotSeeTableRecords([$b]);

        Livewire::actingAs($this->yonetici)->test(ListProducts::class)
            ->searchTable('SKU-'.$b->id)
            ->assertCanSeeTableRecords([$b])
            ->assertCanNotSeeTableRecords([$a]);
    }

    public function test_toplu_kategoriye_ekleme_ve_satistan_kaldirma(): void
    {
        $kat = Category::create(['name' => 'Kampanya']);
        $a = $this->urun('A', 100);
        $b = $this->urun('B', 200);

        Livewire::actingAs($this->yonetici)->test(ListProducts::class)
            ->callTableBulkAction('kategoriEkle', [$a, $b], ['islem' => 'ekle', 'kategoriler' => [$kat->id]])
            ->assertHasNoTableBulkActionErrors()
            ->callTableBulkAction('satistanKaldir', [$a]);

        $this->assertSame(2, $kat->products()->count());
        $this->assertFalse($a->fresh()->is_active);
        $this->assertTrue($b->fresh()->is_active);
    }

    // --- Sipariş detayı ve fiş ---

    public function test_siparis_detayinda_kalemler_ve_musteri_gorunur(): void
    {
        $order = $this->siparis();

        $this->actingAs($this->yonetici)
            ->get('/admin/orders/'.$order->id)
            ->assertOk()
            ->assertSee('Fiesta Tava')
            ->assertSee('FST-28')
            ->assertSee('Mehmet Kaya')
            ->assertSee('Gönyeli', false)
            ->assertSee('Kargoya ver');

        Livewire::actingAs($this->yonetici)->test(ViewOrder::class, ['record' => $order->id])->assertOk();
    }

    public function test_elle_siparis_olusturma_yok(): void
    {
        $this->actingAs($this->yonetici)->get('/admin/orders/create')->assertNotFound();
    }

    public function test_fis_yalniz_panel_kullanicisina_acik(): void
    {
        $order = $this->siparis(['payment_provider' => 'kapida', 'payment_status' => 'pending', 'status' => 'preparing']);

        $this->actingAs($this->yonetici)->get(route('admin.siparis.fis', $order))
            ->assertOk()
            ->assertSee('Fiesta Tava')
            ->assertSee('KAPIDA TAHSİL EDİLECEK', false);

        $musteri = User::factory()->create(['role' => 'customer']);
        $this->actingAs($musteri)->get(route('admin.siparis.fis', $order))->assertForbidden();

        auth()->logout();
        $this->get(route('admin.siparis.fis', $order))->assertRedirect();
    }

    // --- Pano ---

    public function test_pano_bilesenleri_acilir(): void
    {
        $this->siparis();
        $this->urun('Biten Ürün', 100, stok: 0);

        foreach ([SatisGrafigi::class, SonSiparisler::class, CokSatanlar::class] as $w) {
            Livewire::actingAs($this->yonetici)->test($w)->assertOk();
        }

        Livewire::actingAs($this->yonetici)->test(SonSiparisler::class)->assertSee('SCH-TEST-0001');
        Livewire::actingAs($this->yonetici)->test(CokSatanlar::class)->assertSee('Fiesta Tava');
        Livewire::actingAs($this->yonetici)->test(KritikStok::class)->assertSee('Biten Ürün');
    }

    public function test_satis_grafigi_personele_gorunmez(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));
        $this->assertFalse(SatisGrafigi::canView());

        $this->actingAs($this->yonetici);
        $this->assertTrue(SatisGrafigi::canView());
    }

    // --- Kategoriler ---

    public function test_kategori_listesi_agac_sirasiyla_ve_satir_ici_ana_sayfa_anahtari(): void
    {
        $ust = Category::create(['name' => 'Mutfak', 'position' => 1]);
        $alt = Category::create(['name' => 'Pişirme', 'parent_id' => $ust->id]);

        Livewire::actingAs($this->yonetici)->test(ListCategories::class)
            ->assertCanSeeTableRecords([$ust, $alt], inOrder: true)
            ->call('updateTableColumnState', 'is_featured', (string) $alt->id, true);

        $this->assertTrue($alt->fresh()->is_featured);
    }
}
