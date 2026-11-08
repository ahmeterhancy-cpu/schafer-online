# Schafer Kıbrıs — Durum

Son güncelleme: 2026-11-07 · **419 test** geçiyor

schaferkibris.com (WooCommerce + Basel teması) yerine yönetim panelli yeni
online mağaza. Laravel 13 + Filament 5, Blade, sıfır çalışma zamanı kütüphanesi.
Altyapı Zeys Fashion House projesinden (canlıda kanıtlanmış) devralındı ve
Schafer'e / KKTC'ye uyarlandı.

Yerel: `http://127.0.0.1:8190` · panel `/admin` · giriş bilgisi
`storage/app/yerel-yonetici.txt` (git'e girmez).

```bash
C:/php/php.exe -S 127.0.0.1:8190 -t public server.php
```

---

## Katalog

- `php artisan woo:ice-aktar` — eski sitenin kataloğunu alır. Kaynak
  `storage/app/woo/` (git'e girmez): `kategoriler.json`, `urunler.json`
  (WooCommerce yedeği), `gorsel/{wooId}-{sıra}.webp`. Tekrar çalıştırılabilir
  (eşleşme `products.woo_id`); var olan ürünün görseli yeniden kopyalanmaz.
- İlk aktarım: **1.486 ürün (606 yayında, 880 taslak)**, 49 kategori (3 seviye),
  **1.703 görsel** (1200 px WebP, 115 MB). 90 üründe SKU boş/tekrarlı olduğu için
  `WC-{wooId}` verildi. Yayındaki ürünlerin hepsi görselli, fiyatlı ve stoklu.
- Schafer ürünleri **tek tip** (seçeneksiz): her ürünün seçeneksiz tek varyantı
  var. Kartta doğrudan "Sepete Ekle". Renk/ölçü seçenekli ürün de tanımlanabilir
  (özellik kütüphanesi: Ölçü, Renk).
- Eski adresler 301 ile yönlenir: `/product/{slug}` → `/urun/…`,
  `/product-category/…/{slug}` → `/kategori/…`, `/hesabim`, `/teslimat-kargo`,
  `/iade-politikasi` vb.

## Tasarım

Referans schaferkibris.com: siyah üst şerit ("DESTEK HATTI"), bej başlık
(#C09E80) + ortada logo, altında büyük harfli kategori menüsü ve geniş açılır
menü (alt + torun kategoriler), "#SCHAFER #KEŞFET" başlıklı bölümler, ortalı
ürün kartı, kırmızı alt bilgi. Lato + Poppins (derlemede kendi sunucumuza alınır).

Bilerek farklı olanlar:
- Bej üstünde **koyu yazı** (referansta beyazdı, 2.5:1 okunmuyordu).
- Kırmızı vurgu #D21C22 (logo #EB1C22 beyaz yazıyla 4.47:1, sınırın altında).
- Ana sayfa ürün bölümleri panelde **"Ana sayfada göster"** işaretli
  kategorilerden gelir (şimdilik Sofra Grubu, Pişirme, Kahve Makineleri,
  Ev Tekstili). Tükenen ürün ana sayfada gösterilmez.
- Slayt: panelden (Vitrin → Slayt ve Afişler). Girilmemişse öne çıkan ürün
  görselleriyle marka alanı çizilir. Eski sitenin slayt görselleri sunucuda yok
  (404), lifestyle fotoğraf gerekiyor.
- İstek listesi / karşılaştır yok (altyapısı yok).

## KKTC uyarlamaları

- Saat dilimi `Asia/Famagusta`, adres Gönyeli/Lefkoşa, telefonlar config'te.
- Kasa: "Şehir" (KKTC'nin 6 ilçesi öneri listesi) + "Bölge / köy";
  kimlik no isteğe bağlı ve **yalnız biçim** kontrolü (T.C. sağlama algoritması
  KKTC numaralarını reddederdi); vergi no 5–15 karakter. e-Arşiv ifadeleri kalktı.
- Yasal metinler KKTC Tüketicinin Korunması Yasası / Kişisel Verilerin
  Korunması Yasası'na göre **taslak** — hukukçu onayı gerekiyor.
- Eski sitenin bilgi sayfaları başka bir mağazadan ("mytrendsonline.com")
  kopyaydı, taşınmadı; yerine Schafer'e özgü Hakkımızda / Üyelik / Alışveriş /
  Teslimat / Güvenli Alışveriş sayfaları yazıldı (panelden düzenlenebilir).

## Ödeme yöntemleri

Kasada üç yöntem; her biri Site Ayarları → "Ödeme yöntemleri"nden açılıp
kapatılır (`App\Support\OdemeYontemleri`, anahtar = `orders.payment_provider`).

| Yöntem | Sipariş anında | Sonra |
|---|---|---|
| Kart (`paytr`) | stok rezerve, PayTR sayfası | ödeme bildirimi → ödendi, stok düşer, e-posta |
| Havale (`havale`) | stok rezerve, durum "Ödeme bekleniyor", IBAN'lı e-posta + sipariş sayfası | panelde **"Ödeme alındı"** → ödendi, stok düşer. N gün (varsayılan 3) içinde onaylanmazsa `schafer:rezerv-temizle` iptal eder |
| Kapıda (`kapida`) | stok DÜŞER, durum "Hazırlanıyor", e-posta | ödenmeden kargolanır; **"Teslim edildi"** = tahsil edildi (ödendi) |

- Havale, panele en az bir hesap ("Banka | Hesap sahibi | IBAN") girilmeden
  kasada görünmez. **Gerçek IBAN henüz girilmedi.**
- Kapıda ödeme bedeli (`orders.payment_fee`) tanımlanabilir, varsayılan 0;
  toplama dahil, sipariş özetinde ve e-postada ayrı satır.
- PayTR iadesi yalnız kartla ödenmiş siparişte; havale/kapıdada "Elle iade ettim".
- Kartın 90 dk "takılı rezerv" kuralı havaleye dokunmaz; panodaki "Takılı rezerv"
  havaleyi saymaz, ayrı "Havale bekleyen" kartı var.
- Sipariş numarası öneki `SCH-` (Zeys'ten kalan `ZEY-` düzeltildi).

## Yönetim paneli

- **Pano**: yapılacak işler kartları (kargolanacak, havale bekleyen, iade,
  stoğu biten; takılı rezerv yalnız sorun varken), satış grafiği (7/30/90 gün,
  12 ay; ciro + adet + ortalama sepet — yalnız yönetici), son siparişler,
  30 günün çok satanları, kritik stok listesi.
- **Ürünler**: kategori ağacıyla süzgeç (alt kategoriler dahil), ad/SKU/barkod
  araması, tek tip üründe **satır içi fiyat ve stok**, satışta/öne çıkan
  anahtarları, süzgeçler (stok, indirimli, görselsiz, kategorisiz).
  **Toplu işlemler** (`App\Services\TopluUrunIslemleri`): satışa al/kaldır,
  öne çıkar, kategoriye ekle/çıkar/değiştir, % ya da TL fiyat değişimi
  (yuvarlamalı), kampanya indirimi (birikmez) / indirimi kaldır, stoğu
  ayarla (rezervin altına inmez).
- **Siparişler**: yeni detay sayfası (kalemler, tutarlar, müşteri/adres,
  ödeme, teslimat, fatura, notlar) + üstte sıradaki adım düğmesi; işlemler
  `Orders\SiparisIslemleri`'nde tek yerde (liste + detay). Yazdırılabilir
  sipariş fişi `/admin/siparis/{id}/fis` (kapıda tahsil tutarı, imza alanı).
  Elle sipariş oluşturma kaldırıldı (işlevsiz boş formdu). Menü rozeti =
  bekleyen iş.
- **Kategoriler**: ağaç sırası, satıştaki ürün sayısı (tıklayınca ürün
  listesi süzülür), satır içi "Vitrinde" / "Ana sayfada bölüm" / sıra.
- **Müşteriler** (Satış menüsü): sipariş vermiş üyeler, toplam harcama,
  son sipariş; üye sayfasında sipariş geçmişi.
- Genel arama (Ctrl+K): ürün adı/SKU, sipariş no/müşteri/telefon.
- Filament "ürün Oluştur" yerine "Yeni ürün".

## Düzeltilen hatalar (devralınan altyapıda)

- Seçeneksiz ürün panelde kaydedilince tek varyantı siliniyordu (ürün
  satılamaz oluyordu). `UrunVaryantlari::esitle` artık tek tip ürünü korur,
  yoksa başlangıç fiyat/stoğuyla oluşturur. Gerileme testleri eklendi.
- Site Ayarları'ndaki telefon alanı parantezli numarayı reddediyordu; numara
  biçimi `+90 392 223 55 56` yapıldı.

## XML (2026-11-08)

- **Dışa**: `/xml/urunler.xml` — Google Merchant RSS 2.0 (`g:` alanları). Meta katalog,
  Akakçe/Cimri ve bayiler de okur. Yayındaki her aktif varyant bir `<item>`; indirimdeyse
  `g:price` = eski fiyat, `g:sale_price` = satış fiyatı; stok bitince `out_of_stock`.
  30 dk önbellek; panelden kapatılabilir.
- **İçeri**: Panel → Ayarlar → XML. Tedarikçi adresi, eşleşme (SKU/barkod), fiyat/stok
  güncelleme, yeni ürünü TASLAK açma, saatlik otomatik. Etiket adları boşsa tahmin edilir
  (`App\Services\Xml\XmlIceAktarim::ADAYLAR`). "Önizle" hiçbir şeye yazmaz.
  XML'de eski fiyat yoksa mevcut indirim oranı korunur. Son rapor
  `storage/app/private/xml/son-aktarim.json`. Komut: `php artisan schafer:xml-ice-aktar [--kuru] [--kaynak=]`.
- Otomatik çalışma için cron (`schedule:run`) şart — bkz. DEPLOY.md.

## Bekleyenler (müşteriden)

1. **Ödeme**: PayTR anahtarları (kart; şu an test benzetimi) ve havale için
   banka hesap(lar)ı / IBAN. Kapıda ödeme bedeli alınacak mı?
2. Firma unvanı, vergi no (yasal metinler ve fatura).
3. ~~Teslimat ücreti / eşiği~~ — KARARLAŞTI (2026-11-08): 250 TL, 2.500 TL üzeri ücretsiz.
4. Slayt için lifestyle fotoğraflar, Instagram hesabı.
5. Yasal metinlerin KKTC'de bir hukukçuya okutulması.
6. Hosting / alan adı taşıma planı (DEPLOY.md cPanel tarifi geçerli).
