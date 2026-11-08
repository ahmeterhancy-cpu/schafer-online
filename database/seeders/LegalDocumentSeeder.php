<?php

namespace Database\Seeders;

use App\Models\LegalDocument;
use Illuminate\Database\Seeder;

/**
 * Yasal metin ŞABLONLARI.
 *
 * ⚠️ Bunlar hukukçu onayından geçmiş metinler değildir. Satış bölgesi
 * KKTC: KKTC Tüketicinin Korunması Yasası ve Kişisel Verilerin Korunması
 * Yasası'na göre uyarlanmış TASLAKLARDIR; yayına çıkmadan önce KKTC'de
 * çalışan bir hukukçuya okutulmalıdır.
 *
 * {{satici_*}} yer tutucuları config/shop.php üzerinden doldurulur;
 * .env'deki SHOP_SATICI_* alanları boşken metinler eksik kalır.
 */
class LegalDocumentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->documents() as $slug => $doc) {
            if (LegalDocument::current($slug)) {
                continue; // panelden düzenlenmiş olabilir, üzerine yazma
            }

            LegalDocument::publish($slug, $doc['title'], $this->fill($doc['body']));
        }
    }

    private function fill(string $body): string
    {
        return strtr($body, [
            '{{magaza}}' => config('shop.ad'),
            '{{satici_unvan}}' => config('shop.satici.unvan') ?: '[SATICI UNVANI GİRİLMEDİ]',
            '{{satici_adres}}' => config('shop.satici.adres') ?: '[ADRES GİRİLMEDİ]',
            '{{satici_telefon}}' => config('shop.satici.telefon') ?: '[TELEFON GİRİLMEDİ]',
            '{{satici_eposta}}' => config('shop.satici.eposta') ?: '[E-POSTA GİRİLMEDİ]',
            '{{satici_mersis}}' => config('shop.satici.mersis') ?: '[MERSİS NO GİRİLMEDİ]',
            '{{cayma_gun}}' => (string) config('shop.cayma_hakki_gun'),
            '{{kargo_esik}}' => number_format((float) config('shop.kargo.ucretsiz_esigi'), 2, ',', '.'),
        ]);
    }

    /** @return array<string, array{title:string, body:string}> */
    private function documents(): array
    {
        return [
            'on-bilgilendirme' => [
                'title' => 'Ön Bilgilendirme Formu',
                'body' => <<<'HTML'
<h2>1. Satıcı Bilgileri</h2>
<p>
    Unvan: {{satici_unvan}}<br>
    Adres: {{satici_adres}}<br>
    Telefon: {{satici_telefon}}<br>
    E-posta: {{satici_eposta}}
</p>

<h2>2. Sözleşme Konusu Ürün</h2>
<p>
    Sözleşmenin konusu, ALICI'nın {{magaza}} internet sitesi üzerinden
    elektronik ortamda siparişini verdiği, nitelikleri ve satış fiyatı
    sipariş özetinde belirtilen ürünlerin satışı ve teslimidir.
</p>

<h2>3. Ödeme ve Teslimat</h2>
<p>
    Ürün bedeli, sipariş sırasında seçilen ödeme yöntemiyle tahsil edilir.
    Kart bilgileri ödeme kuruluşunun güvenli sayfasında girilir ve
    {{magaza}} tarafından saklanmaz.
</p>
<p>
    Kargo bedeli sipariş özetinde ayrıca gösterilir.
    {{kargo_esik}} TL ve üzeri siparişlerde kargo ücretsizdir.
</p>

<h2>4. Cayma Hakkı</h2>
<p>
    ALICI, malı teslim aldıktan sonra hiçbir gerekçe göstermeksizin ve cezai şart ödemeksizin sözleşmeden
    cayma hakkına sahiptir. Cayma hakkının kullanıldığına dair bildirim SATICI'ya yöneltilmelidir.
</p>

<h2>5. Cayma Hakkının Kullanılamayacağı Durumlar</h2>
<p>
    İlgili mevzuat uyarınca; ALICI'nın isteği doğrultusunda kişiye özel
    hazırlanan ürünlerde ve tesliminden sonra ambalajı açılmış olup
    iadesi sağlık/hijyen açısından uygun olmayan ürünlerde (gıdayla temas
    eden kullanılmış ürünler, iç ambalajı açılmış tekstil ürünleri vb.)
    cayma hakkı kullanılamaz.
</p>

<h2>6. Uyuşmazlık</h2>
<p>
    Uyuşmazlıklarda KKTC mevzuatı uygulanır; KKTC Ticaret Dairesi
    Tüketiciyi Koruma birimine başvurulabilir, KKTC mahkemeleri
    yetkilidir.
</p>
HTML,
            ],

            'mesafeli-satis' => [
                'title' => 'Mesafeli Satış Sözleşmesi',
                'body' => <<<'HTML'
<h2>Madde 1 — Taraflar</h2>
<p>
    SATICI: {{satici_unvan}}, {{satici_adres}},
    {{satici_telefon}}, {{satici_eposta}}.<br>
    ALICI: Sipariş formunda bilgileri yer alan kişi.
</p>

<h2>Madde 2 — Konu</h2>
<p>
    İşbu sözleşmenin konusu, ALICI'nın {{magaza}} internet sitesinden
    elektronik ortamda sipariş verdiği ürünün satışı ve teslimi ile ilgili
    olarak KKTC Tüketicinin Korunması Yasası ve ilgili mevzuat
    hükümleri uyarınca tarafların hak ve
    yükümlülüklerinin belirlenmesidir.
</p>

<h2>Madde 3 — Sözleşme Konusu Ürün</h2>
<p>
    Ürünün türü, miktarı, marka/modeli, rengi, adedi, satış
    bedeli ve ödeme şekli sipariş özetinde gösterildiği gibidir ve işbu
    sözleşmenin ayrılmaz parçasıdır.
</p>

<h2>Madde 4 — Genel Hükümler</h2>
<p>
    ALICI, sözleşme konusu ürünün temel nitelikleri, satış fiyatı ve
    ödeme şekli ile teslimata ilişkin ön bilgileri okuyup bilgi sahibi
    olduğunu ve elektronik ortamda gerekli teyidi verdiğini kabul eder.
</p>
<p>
    Sözleşme konusu ürün, 30 günlük süreyi aşmamak koşuluyla ALICI'nın
    KKTC içinde belirttiği adrese SATICI'nın kendi aracıyla ya da
    anlaşmalı kurye/kargo aracılığıyla teslim edilir.
</p>

<h2>Madde 5 — Cayma Hakkı</h2>
<p>
    ALICI, malı teslim aldıktan sonra cayma hakkını kullanabilir. Cayma hakkının kullanılması hâlinde ürün,
    faturası ve varsa standart aksesuarlarıyla birlikte eksiksiz ve
    hasarsız olarak iade edilir. Bedel, cayma bildiriminin SATICI'ya
    ulaşmasının ardından ALICI'ya iade edilir.
</p>

<h2>Madde 6 — Değişim</h2>
<p>
    Renk ya da model değişimi taleplerinde ürün, kullanılmamış ve
    ambalajı/aksesuarları eksiksiz olmak kaydıyla değiştirilir. Değişimde
    bedel iadesi yapılmaz; talep edilen yeni ürün gönderilir.
</p>

<h2>Madde 7 — Yetkili Mahkeme</h2>
<p>
    İşbu sözleşmeden doğan uyuşmazlıklarda KKTC mahkemeleri yetkilidir.
</p>
HTML,
            ],


            'kvkk' => [
                'title' => 'Kişisel Verilerin Korunması Aydınlatma Metni',
                'body' => <<<'HTML'
<h2>Veri Sorumlusu</h2>
<p>
    KKTC Kişisel Verilerin Korunması Yasası uyarınca veri sorumlusu
    {{satici_unvan}}'dır.
</p>

<h2>İşlenen Veriler ve Amaç</h2>
<p>
    Ad-soyad, iletişim bilgileri, teslimat ve fatura adresi ile sipariş
    geçmişi; siparişin oluşturulması, ödemenin alınması, kargo teslimatı,
    fatura düzenlenmesi, iade ve değişim süreçlerinin yürütülmesi ve
    yasal saklama yükümlülüklerinin yerine getirilmesi amacıyla işlenir.
</p>

<h2>Aktarım</h2>
<p>
    Veriler; ödeme kuruluşu, teslimatı yapan kurye/kargo firması ve yasal olarak yetkili kamu
    kurumlarıyla, yalnızca ilgili amaçla sınırlı olarak paylaşılır.
    Kart bilgileri tarafımızca görülmez ve saklanmaz.
</p>

<h2>Haklarınız</h2>
<p>
    Yasa kapsamındaki bilgi alma, düzeltme ve silme haklarınız için
    {{satici_eposta}} adresine başvurabilirsiniz.
</p>
HTML,
            ],

            'hakkimizda' => [
                'title' => 'Hakkımızda',
                'body' => <<<'HTML'
<p>
    {{magaza}}, Schafer markasının mutfak, sofra, ev tekstili ve küçük ev
    aletleri ürünlerini Kuzey Kıbrıs'ta müşterileriyle buluşturan mağazadır.
    Gönyeli'deki mağazamızda ve bu sitede yemek takımlarından döküm
    tencerelere, Türk kahvesi makinelerinden nevresim takımlarına yüzlerce
    ürünü inceleyebilirsiniz.
</p>
<h2>Mağazamız</h2>
<p>
    Adres: {{satici_adres}}<br>
    Telefon: {{satici_telefon}}<br>
    E-posta: {{satici_eposta}}
</p>
HTML,
            ],

            'uyelik' => [
                'title' => 'Üyelik',
                'body' => <<<'HTML'
<h2>Nasıl üye olabilirim?</h2>
<p>Sağ üstteki "Giriş / Kayıt" bağlantısından "Üye Ol" formunu doldurmanız yeterlidir. Üyelik ücretsizdir.</p>
<h2>Üye olmadan alışveriş yapabilir miyim?</h2>
<p>Evet. Ödeme sayfasında bilgilerinizi girerek üye olmadan da sipariş verebilirsiniz. Siparişinizi daha sonra "Sipariş Takibi" sayfasından sorgulayabilirsiniz.</p>
<h2>Üyeliğin bana faydası nedir?</h2>
<p>Adreslerinizi kaydeder, geçmiş siparişlerinizi ve iade taleplerinizi tek yerden izlersiniz.</p>
<h2>Parolamı unuttum</h2>
<p>Giriş sayfasındaki "Parolamı unuttum" bağlantısıyla e-posta adresinize sıfırlama bağlantısı gönderebilirsiniz. Sorun yaşarsanız {{satici_telefon}} numarasından bize ulaşın.</p>
HTML,
            ],

            'alisveris' => [
                'title' => 'Alışveriş',
                'body' => <<<'HTML'
<h2>Nasıl sipariş veririm?</h2>
<ol>
    <li>Beğendiğiniz ürünü "Sepete Ekle" ile sepetinize ekleyin.</li>
    <li>Sepetinizden "Ödemeye geç" adımına ilerleyin.</li>
    <li>Teslimat ve fatura bilgilerinizi girin, ödemenizi tamamlayın.</li>
    <li>Sipariş onayı e-posta adresinize gönderilir.</li>
</ol>
<h2>Fiyatlar</h2>
<p>Sitedeki fiyatlar Türk Lirası (₺) cinsindendir ve KDV dahildir. Kampanyalı ürünlerde eski fiyat üstü çizili olarak gösterilir.</p>
<h2>Stok</h2>
<p>Sitede gördüğünüz stok bilgisi anlıktır. Tükenen ürünler için "Gelince haber ver" seçeneğiyle bildirim alabilirsiniz.</p>
HTML,
            ],

            'teslimat' => [
                'title' => 'Teslimat',
                'body' => <<<'HTML'
<p>Siparişleriniz KKTC genelinde belirttiğiniz adrese teslim edilir. Teslimat süresi ve günü sipariş onayından sonra sizinle paylaşılır.</p>
<h2>Teslimat ücreti</h2>
<p>{{kargo_esik}} ₺ ve üzeri siparişlerde teslimat ücretsizdir. Altındaki siparişlerde teslimat ücreti ödeme sayfasında gösterilir.</p>
<h2>Teslim alırken</h2>
<p>Lütfen paketi teslim alırken kontrol edin. Hasarlı paket durumunda teslimatı yapan kişiye tutanak tutturun ve {{satici_telefon}} numarasından bize bilgi verin.</p>
<h2>Mağazadan teslim</h2>
<p>Siparişinizi Gönyeli'deki mağazamızdan da teslim alabilirsiniz; bunun için sipariş notuna yazmanız yeterlidir.</p>
HTML,
            ],

            'guvenli-alisveris' => [
                'title' => 'Güvenli Alışveriş',
                'body' => <<<'HTML'
<p>Sitemizdeki tüm sayfalar 256 bit SSL şifrelemesiyle korunur.</p>
<h2>Kart bilgileriniz</h2>
<p>Ödemeler lisanslı ödeme kuruluşunun güvenli ödeme sayfasında, 3D Secure doğrulamasıyla alınır. Kart bilgileriniz {{magaza}} tarafından görülmez ve saklanmaz.</p>
<h2>Kişisel verileriniz</h2>
<p>Paylaştığınız bilgiler yalnızca siparişinizin hazırlanması ve teslimatı için kullanılır. Ayrıntılar için Kişisel Verilerin Korunması Aydınlatma Metni'ni inceleyebilirsiniz.</p>
HTML,
            ],

            'cerez' => [
                'title' => 'Çerez Politikası',
                'body' => <<<'HTML'
<h2>Kullandığımız Çerezler</h2>
<p>
    {{magaza}} yalnızca sitenin çalışması için zorunlu çerezleri kullanır:
    oturum çerezi (sepetinizin ve girişinizin korunması) ve güvenlik
    çerezi (form güvenliği).
</p>

<h2>Reklam ve İzleme</h2>
<p>
    Üçüncü taraf reklam veya profilleme çerezi kullanılmamaktadır.
    İleride eklenmesi hâlinde bu metin güncellenecek ve onayınız
    alınacaktır.
</p>

<h2>Çerezleri Kapatmak</h2>
<p>
    Tarayıcı ayarlarından çerezleri engelleyebilirsiniz; ancak zorunlu
    çerezler kapatıldığında sepet ve giriş işlevleri çalışmaz.
</p>
HTML,
            ],
        ];
    }
}
