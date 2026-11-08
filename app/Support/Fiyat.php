<?php

namespace App\Support;

/**
 * "Fiyatları gizle" modülü. Vitrin, sepet, ödeme, JSON-LD ve XML beslemesi
 * fiyatı göstermeden önce buraya sorar.
 *
 * Gizliyken:
 *  - Ziyaretçi fiyat, indirim oranı ve sepet görmez; ürünlerde "Fiyat sor"
 *    (WhatsApp) çıkar. Sepet ve ödeme kapalıdır.
 *  - "Üyelere göster" açıksa giriş yapmış müşteri fiyatı görür ve alışveriş yapar.
 *  - Panel kullanıcısı (yönetici/personel) her zaman görür — siteyi kontrol edebilsin.
 */
class Fiyat
{
    public static function gorunur(): bool
    {
        if (! config('shop.fiyat.gizle')) {
            return true;
        }

        $kullanici = auth()->user();
        if (! $kullanici) {
            return false;
        }

        return $kullanici->panelde() || (bool) config('shop.fiyat.uyelere_goster');
    }

    public static function gizli(): bool
    {
        return ! static::gorunur();
    }

    public static function metin(): string
    {
        return trim((string) config('shop.fiyat.metin')) ?: 'Fiyat bilgisi için bize ulaşın';
    }

    /** Ürün için "fiyat sor" bağlantısı: WhatsApp varsa o, yoksa iletişim sayfası. */
    public static function sorBaglantisi(string $urunAdi, ?string $adres = null): string
    {
        $mesaj = 'Merhaba, "'.$urunAdi.'" ürününün fiyatını öğrenmek istiyorum.'.($adres ? ' '.$adres : '');

        return Iletisim::whatsappBaglanti($mesaj) ?? route('contact');
    }
}
