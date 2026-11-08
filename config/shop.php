<?php

/*
 * Magaza ayarlari.
 *
 * TUZAK: config onbellegi (`artisan optimize`) sonrasi Laravel .env dosyasini
 * HIC yuklemez. config/ disindaki her env() cagrisi null doner. Bu yuzden
 * .env'den okunan her deger BU dosyadan gecmek zorunda; uygulama kodu
 * daima config('shop.*') kullanir, asla env().
 */

return [

    // Kimlik
    'ad' => env('SHOP_AD', 'Schafer Kıbrıs'),

    /*
     * Demo icerigin canliya yuklenmesine acik izin. DemoSeeder canlida
     * kendini kilitler; vitrini gercek urunler gelmeden doldurmak icin
     * .env'ye DEMO_SEED_IZIN=true yazilir ve is bitince KALDIRILIR.
     * Deger config'ten okunur: onbellege alinmis ayarda env() null doner.
     */
    'demo_seed_izin' => (bool) env('DEMO_SEED_IZIN', false),
    'alan_adi' => env('SHOP_ALAN_ADI', 'schaferkibris.com'),

    // Para birimi — satis bolgesi KKTC, fiyatlar TL
    'para_birimi' => 'TRY',
    'para_simgesi' => '₺',

    /*
     * Kargo: tek ucret + ucretsiz esigi.
     *
     * Para her yerde TL cinsinden decimal(10,2) tutulur — varyant fiyati,
     * siparis toplami ve burasi ayni birimde olmak ZORUNDA. Kurus/TL
     * karisimi sessiz 100 kat hatalara yol acar.
     */
    'kargo' => [
        'ucret' => (float) env('SHOP_KARGO_UCRET', 250.00),
        'ucretsiz_esigi' => (float) env('SHOP_KARGO_UCRETSIZ_ESIGI', 2500.00),
        'firma' => env('SHOP_KARGO_FIRMA', ''),
    ],

    /*
     * Odeme yontemleri (bkz. App\Support\OdemeYontemleri).
     * Havale, hesap bilgisi girilmeden kasada gosterilmez.
     * havale_hesaplari: her satir "Banka | Hesap sahibi | IBAN".
     */
    'odeme' => [
        'kart' => (bool) env('SHOP_ODEME_KART', true),
        'havale' => (bool) env('SHOP_ODEME_HAVALE', true),
        'kapida' => (bool) env('SHOP_ODEME_KAPIDA', true),
        'havale_hesaplari' => env('SHOP_HAVALE_HESAPLARI', ''),
        'havale_sure_gun' => (int) env('SHOP_HAVALE_SURE_GUN', 3),
        'kapida_ek_ucret' => (float) env('SHOP_KAPIDA_EK_UCRET', 0),
        'kapida_aciklama' => env('SHOP_KAPIDA_ACIKLAMA', 'Ürünü teslim alırken nakit ya da kartla ödersiniz.'),
    ],

    // Yeni siparis bildiriminin gidecegi magaza adresi (bos ise gonderilmez)
    'siparis_bildirim_epostasi' => env('SHOP_SIPARIS_BILDIRIM', ''),

    // Stok
    'dusuk_stok_esigi' => (int) env('SHOP_DUSUK_STOK_ESIGI', 3),

    // 6502 sayili kanun / Mesafeli Sozlesmeler Yonetmeligi
    'cayma_hakki_gun' => 14,

    /*
     * Yapim asamasi perdesi (bakim_modu acikken). PIN girilen cihaz siteyi
     * normal gorur; baslik/metin bos ise varsayilanlar kullanilir.
     */
    'yapim' => [
        'pin' => env('SHOP_YAPIM_PIN', ''),
        'baslik' => '',
        'metin' => '',
    ],

    /*
     * Fiyatlari gizle. Acikken vitrinde fiyat, indirim ve sepet gorunmez;
     * yerine "fiyat sor" (WhatsApp) cikar. Sepet ve odeme kapanir.
     */
    'fiyat' => [
        'gizle' => (bool) env('SHOP_FIYAT_GIZLE', false),
        'uyelere_goster' => false,
        'metin' => 'Fiyat bilgisi için bize ulaşın',
    ],

    /*
     * XML.
     *  - disa: /xml/urunler.xml (Google Merchant / Meta katalog / fiyat
     *    karsilastirma siteleri ayni bicimi okur).
     *  - ice: tedarikci XML'inden fiyat/stok guncelleme. Alan adlari bos
     *    birakilirsa yaygin etiket adlarindan tahmin edilir.
     */
    'xml' => [
        'disa_acik' => (bool) env('SHOP_XML_DISA_ACIK', true),
        'ice_url' => env('SHOP_XML_ICE_URL', ''),
        'ice_eslesme' => env('SHOP_XML_ICE_ESLESME', 'sku'), // sku | barkod
        'ice_fiyat' => (bool) env('SHOP_XML_ICE_FIYAT', true),
        'ice_stok' => (bool) env('SHOP_XML_ICE_STOK', true),
        'ice_yeni_urun' => (bool) env('SHOP_XML_ICE_YENI', false),
        'ice_otomatik' => (bool) env('SHOP_XML_ICE_OTOMATIK', false),
        'ice_alan_urun' => '',
        'ice_alan_kod' => '',
        'ice_alan_barkod' => '',
        'ice_alan_ad' => '',
        'ice_alan_fiyat' => '',
        'ice_alan_eski_fiyat' => '',
        'ice_alan_stok' => '',
        'ice_alan_gorsel' => '',
    ],

    /*
     * Iletisim kanallari. WhatsApp numarasi yerel bicimde yazilabilir
     * (05551800206); App\Support\Iletisim uluslararasi bicime cevirir.
     * Harita gommesi bos birakilirsa adresten uretilir.
     */
    'iletisim' => [
        'whatsapp' => env('SHOP_WHATSAPP', '+905488477000'),
        'whatsapp_mesaj' => env('SHOP_WHATSAPP_MESAJ', 'Merhaba, Schafer Kıbrıs ürünleri hakkında bilgi almak istiyorum.'),
        /*
         * Harita: once harita_embed (tam gomme adresi), sonra harita_sorgu.
         * TUZAK: ham adres ("... Edirne Life 5 — Edirne/Merkez") Google'da
         * cozulemeyip DUNYA haritasi aciyordu; magaza adiyla sorgu dogru
         * igneyi getiriyor.
         */
        'harita_embed' => env('SHOP_HARITA_EMBED', ''),
        'harita_sorgu' => env('SHOP_HARITA_SORGU', 'Schafer Gönyeli Lefkoşa'),
        'calisma_saatleri' => env('SHOP_CALISMA_SAATLERI', 'Pazartesi - Cumartesi 10.00 - 20.00'),
    ],

    // Sosyal
    'sosyal' => [
        'instagram' => env('SHOP_INSTAGRAM', ''),
    ],

    // Satici bilgileri — yasal metinlerde ve faturada gorunur
    'satici' => [
        'unvan' => env('SHOP_SATICI_UNVAN', ''),
        'adres' => env('SHOP_SATICI_ADRES', 'Düzyol Sokak No:10/B Gönyeli, Lefkoşa — KKTC (Garanti Bankası yanı)'),
        'telefon' => env('SHOP_SATICI_TELEFON', '+90 392 223 55 56'),
        'telefon2' => env('SHOP_SATICI_TELEFON2', '+90 548 847 70 00'),
        'eposta' => env('SHOP_SATICI_EPOSTA', 'info@schaferkibris.com'),
        'mersis' => env('SHOP_SATICI_MERSIS', ''),
        'vergi_dairesi' => env('SHOP_SATICI_VERGI_DAIRESI', ''),
        'vergi_no' => env('SHOP_SATICI_VERGI_NO', ''),
    ],

    // Bakim perdesi
    'bakim_modu' => (bool) env('SHOP_BAKIM_MODU', false),

];
