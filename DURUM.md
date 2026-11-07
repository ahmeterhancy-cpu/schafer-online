# Schafer Kıbrıs — Durum

Son güncelleme: 2026-11-07 · **395 test** geçiyor

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

## Düzeltilen hatalar (devralınan altyapıda)

- Seçeneksiz ürün panelde kaydedilince tek varyantı siliniyordu (ürün
  satılamaz oluyordu). `UrunVaryantlari::esitle` artık tek tip ürünü korur,
  yoksa başlangıç fiyat/stoğuyla oluşturur. Gerileme testleri eklendi.
- Site Ayarları'ndaki telefon alanı parantezli numarayı reddediyordu; numara
  biçimi `+90 392 223 55 56` yapıldı.

## Bekleyenler (müşteriden)

1. **Ödeme kuruluşu**: altyapı PayTR (test modunda benzetimle çalışıyor).
   KKTC'de hangi sanal POS kullanılacak? Havale/EFT veya kapıda ödeme istenecek mi?
2. Firma unvanı, vergi no (yasal metinler ve fatura).
3. Teslimat ücreti ve ücretsiz teslimat eşiği (şu an 150 TL / 2.500 TL varsayım).
4. Slayt için lifestyle fotoğraflar, Instagram hesabı.
5. Yasal metinlerin KKTC'de bir hukukçuya okutulması.
6. Hosting / alan adı taşıma planı (DEPLOY.md cPanel tarifi geçerli).
