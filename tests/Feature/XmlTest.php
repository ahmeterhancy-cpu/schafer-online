<?php

namespace Tests\Feature;

use App\Filament\Pages\XmlAyarlari;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Xml\XmlIceAktarim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class XmlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
        Cache::flush();
        config([
            'shop.xml.ice_eslesme' => 'sku',
            'shop.xml.ice_fiyat' => true,
            'shop.xml.ice_stok' => true,
            'shop.xml.ice_yeni_urun' => false,
        ]);
    }

    private function urun(string $ad, string $sku, float $fiyat, ?float $eski = null, int $stok = 5, bool $aktif = true, ?string $barkod = null): Product
    {
        $u = Product::create(['name' => $ad, 'is_active' => $aktif, 'description' => '<p>Güzel <b>tencere</b></p>']);
        ProductVariant::create(['product_id' => $u->id, 'sku' => $sku, 'barcode' => $barkod, 'price' => $fiyat, 'compare_at_price' => $eski, 'stock' => $stok, 'is_active' => true]);

        return $u->fresh();
    }

    // --- Dışa besleme ---

    public function test_besleme_yayindaki_urunleri_google_bicimiyle_verir(): void
    {
        $kat = Category::create(['name' => 'Mutfak', 'slug' => 'mutfak', 'is_active' => true]);
        $alt = Category::create(['name' => 'Tencere', 'slug' => 'tencere', 'parent_id' => $kat->id, 'is_active' => true]);
        $u = $this->urun('Düdüklü Tencere', 'SCH-1', 1000, 2000, 3, barkod: '8690000000017');
        $u->categories()->attach($alt);
        $this->urun('Taslak Ürün', 'SCH-2', 500, aktif: false);
        $this->urun('Biten Ürün', 'SCH-3', 300, stok: 0);

        $yanit = $this->get('/xml/urunler.xml')->assertOk();
        $this->assertStringContainsString('application/xml', $yanit->headers->get('Content-Type'));

        $xml = simplexml_load_string($yanit->getContent());
        $ogeler = $xml->channel->item;
        $this->assertCount(2, $ogeler);

        $g = $ogeler[0]->children('http://base.google.com/ns/1.0');
        $this->assertSame('SCH-1', (string) $g->id);
        $this->assertSame('2000.00 TRY', (string) $g->price);
        $this->assertSame('1000.00 TRY', (string) $g->sale_price);
        $this->assertSame('in_stock', (string) $g->availability);
        $this->assertSame('8690000000017', (string) $g->gtin);
        $this->assertSame('Mutfak > Tencere', (string) $g->product_type);
        $this->assertSame('Güzel tencere', (string) $g->description);

        $g3 = $ogeler[1]->children('http://base.google.com/ns/1.0');
        $this->assertSame('out_of_stock', (string) $g3->availability);
        $this->assertSame('300.00 TRY', (string) $g3->price);
        $this->assertSame('no', (string) $g3->identifier_exists);
    }

    public function test_besleme_kapatilabilir(): void
    {
        config(['shop.xml.disa_acik' => false]);
        $this->get('/xml/urunler.xml')->assertNotFound();
    }

    // --- İçeri aktarım ---

    private function tedarikciXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Urunler>
  <Urun>
    <StokKodu>sch-1</StokKodu>
    <UrunAdi>Düdüklü Tencere</UrunAdi>
    <SatisFiyati>1.250,50</SatisFiyati>
    <Stok>12</Stok>
  </Urun>
  <Urun>
    <StokKodu>SCH-2</StokKodu>
    <UrunAdi>Tava</UrunAdi>
    <SatisFiyati>400</SatisFiyati>
    <Stok>5</Stok>
  </Urun>
  <Urun>
    <StokKodu>YOK-9</StokKodu>
    <UrunAdi>Yeni Çaydanlık</UrunAdi>
    <SatisFiyati>800</SatisFiyati>
    <Stok>4</Stok>
    <Resimler><Resim>https://tedarikci.test/caydanlik.png</Resim></Resimler>
  </Urun>
  <Urun>
    <UrunAdi>Kodsuz</UrunAdi>
    <SatisFiyati>10</SatisFiyati>
  </Urun>
</Urunler>
XML;
    }

    public function test_alanlar_tahmin_edilir_ve_turkce_sayi_okunur(): void
    {
        $sonuc = app(XmlIceAktarim::class)->ayristir($this->tedarikciXml());

        $this->assertSame('urun', $sonuc['urun_etiketi']);
        $this->assertSame('stokkodu', $sonuc['alanlar']['kod']);
        $this->assertSame('satisfiyati', $sonuc['alanlar']['fiyat']);
        $this->assertSame(1250.5, $sonuc['satirlar'][0]['fiyat']);
        $this->assertSame(12, $sonuc['satirlar'][0]['stok']);
        $this->assertSame(['https://tedarikci.test/caydanlik.png'], $sonuc['satirlar'][2]['gorseller']);
    }

    public function test_sayi_bicimleri(): void
    {
        $a = app(XmlIceAktarim::class);
        $this->assertSame(1234.56, $a->sayi('1.234,56 TL'));
        $this->assertSame(1234.56, $a->sayi('1,234.56'));
        $this->assertSame(1234.56, $a->sayi('1234.56 TRY'));
        $this->assertSame(99.9, $a->sayi('99,90'));
        $this->assertSame(1234567.0, $a->sayi('1.234.567'));
        $this->assertNull($a->sayi('0'));
        $this->assertNull($a->sayi(''));
    }

    public function test_eslesen_urunlerin_fiyati_ve_stogu_guncellenir_indirim_orani_korunur(): void
    {
        $tencere = $this->urun('Düdüklü Tencere', 'SCH-1', 1000, 2000, 3);
        $tava = $this->urun('Tava', 'SCH-2', 400, null, 5);
        Storage::disk('local')->put('tedarikci.xml', $this->tedarikciXml());

        $rapor = app(XmlIceAktarim::class)->calistir(Storage::disk('local')->path('tedarikci.xml'));

        $this->assertArrayNotHasKey('hata', $rapor);
        $this->assertSame(['toplam' => 4, 'eslesen' => 2, 'guncellenen' => 1, 'degismeyen' => 1, 'bulunamayan' => 1, 'yeni' => 0, 'atlanan' => 1], $rapor['sayac']);

        $v = $tencere->variants()->first();
        $this->assertEquals(1250.50, (float) $v->price);
        $this->assertEquals(2501.00, (float) $v->compare_at_price); // ×2 oranı korundu
        $this->assertSame(12, (int) $v->stock);
        $this->assertEquals(1250.50, (float) $tencere->fresh()->min_price); // ürün önbelleği tazelendi
        $this->assertSame(5, (int) $tava->variants()->first()->stock);

        $this->assertNotNull(XmlIceAktarim::sonRapor());
    }

    public function test_onizleme_hicbir_seyi_degistirmez(): void
    {
        $tencere = $this->urun('Düdüklü Tencere', 'SCH-1', 1000, 2000, 3);
        Storage::disk('local')->put('tedarikci.xml', $this->tedarikciXml());

        $rapor = app(XmlIceAktarim::class)->calistir(Storage::disk('local')->path('tedarikci.xml'), kuru: true);

        $this->assertSame(1, $rapor['sayac']['guncellenen']);
        $this->assertEquals(1000, (float) $tencere->variants()->first()->price);
        $this->assertNull(XmlIceAktarim::sonRapor());
    }

    public function test_fiyat_guncelleme_kapaliysa_yalniz_stok_degisir(): void
    {
        config(['shop.xml.ice_fiyat' => false]);
        $tencere = $this->urun('Düdüklü Tencere', 'SCH-1', 1000, 2000, 3);
        Storage::disk('local')->put('tedarikci.xml', $this->tedarikciXml());

        app(XmlIceAktarim::class)->calistir(Storage::disk('local')->path('tedarikci.xml'));

        $v = $tencere->variants()->first();
        $this->assertEquals(1000, (float) $v->price);
        $this->assertSame(12, (int) $v->stock);
    }

    public function test_yeni_urun_taslak_olarak_gorseliyle_eklenir(): void
    {
        config(['shop.xml.ice_yeni_urun' => true]);
        $png = imagecreate(40, 40); // paletli PNG — boş WebP tuzağı
        imagecolorallocate($png, 200, 30, 30);
        ob_start();
        imagepng($png);
        Http::fake(['tedarikci.test/*' => Http::response(ob_get_clean(), 200, ['Content-Type' => 'image/png'])]);
        Storage::disk('local')->put('tedarikci.xml', $this->tedarikciXml());

        $rapor = app(XmlIceAktarim::class)->calistir(Storage::disk('local')->path('tedarikci.xml'));

        $this->assertSame(3, $rapor['sayac']['yeni']);
        $yeni = Product::where('name', 'Yeni Çaydanlık')->firstOrFail();
        $this->assertFalse($yeni->is_active);
        $this->assertSame('YOK-9', $yeni->variants()->first()->sku);
        $this->assertEquals(800, (float) $yeni->fresh()->min_price);
        $this->assertNotNull($yeni->fresh()->hero_image);
        $this->assertGreaterThan(30, Storage::disk('public')->size($yeni->fresh()->hero_image));
    }

    public function test_bozuk_xml_hata_raporlar(): void
    {
        Storage::disk('local')->put('bozuk.xml', '<urunler><urun>');

        $rapor = app(XmlIceAktarim::class)->calistir(Storage::disk('local')->path('bozuk.xml'));

        $this->assertStringContainsString('XML okunamadı', $rapor['hata']);
    }

    public function test_dis_varlik_okunmaz(): void
    {
        $xml = '<?xml version="1.0"?><!DOCTYPE r [<!ENTITY x SYSTEM "file:///etc/passwd">]><r><item><sku>A</sku><price>5</price><title>&x;</title></item><item><sku>B</sku><price>6</price></item></r>';

        $sonuc = app(XmlIceAktarim::class)->ayristir($xml);

        $this->assertNull($sonuc['satirlar'][0]['ad']);
    }

    public function test_panel_sayfasi_kaydeder_ve_onizler(): void
    {
        $yonetici = User::factory()->create(['role' => 'admin']);
        $this->urun('Düdüklü Tencere', 'SCH-1', 1000, 2000, 3);
        Http::fake(['tedarikci.test/*' => Http::response($this->tedarikciXml(), 200, ['Content-Type' => 'application/xml'])]);

        Livewire::actingAs($yonetici)
            ->test(XmlAyarlari::class)
            ->fillForm(['xml_ice_url' => 'https://tedarikci.test/urunler.xml', 'xml_ice_eslesme' => 'sku'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->callAction('onizle')
            ->assertSet('onizleme.sayac.guncellenen', 1)
            ->assertSee('ÖNİZLEME');

        $this->assertSame('https://tedarikci.test/urunler.xml', config('shop.xml.ice_url'));
        $this->assertEquals(1000, (float) ProductVariant::where('sku', 'SCH-1')->value('price'));
    }

    public function test_personel_xml_sayfasini_goremez(): void
    {
        $personel = User::factory()->create(['role' => 'staff']);

        $this->actingAs($personel)->get('/admin/xml')->assertForbidden();
    }
}
