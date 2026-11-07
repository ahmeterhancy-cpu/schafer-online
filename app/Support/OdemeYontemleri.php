<?php

namespace App\Support;

use App\Services\Payments\PayTrGateway;

/**
 * Kasada sunulan ödeme yöntemleri.
 *
 *   paytr  — kredi/banka kartı (PayTR iframe)
 *   havale — havale/EFT: sipariş alınır, stok ayrılır, ödeme panelden onaylanır
 *   kapida — kapıda ödeme: sipariş hemen onaylanır, teslimatta tahsil edilir
 *
 * Anahtarlar orders.payment_provider değerleriyle AYNI.
 * Açık/kapalı ve metinler Site Ayarları'ndan (config('shop.odeme.*')).
 */
class OdemeYontemleri
{
    public const ADLAR = [
        'paytr' => 'Kredi / banka kartı',
        'havale' => 'Havale / EFT',
        'kapida' => 'Kapıda ödeme',
    ];

    /** Çevrim dışı yöntemler: ödeme sipariş anında alınmaz. */
    public const CEVRIMDISI = ['havale', 'kapida'];

    /**
     * Kasada gösterilecek yöntemler, sıralı.
     *
     * @return array<string, array{ad: string, aciklama: string, ek_ucret: float}>
     */
    public static function etkinler(): array
    {
        $o = config('shop.odeme');
        $liste = [];

        // Kart: açık VE (PayTR tanımlı YA DA yerel/test ortamında benzetim)
        if (($o['kart'] ?? true) && (app(PayTrGateway::class)->isConfigured() || app()->environment(['local', 'testing']))) {
            $liste['paytr'] = [
                'ad' => self::ADLAR['paytr'],
                'aciklama' => 'Güvenli ödeme sayfasında 3D Secure ile. Taksit seçenekleri ödeme adımında görünür.',
                'ek_ucret' => 0.0,
            ];
        }

        // Havale: hesap bilgisi girilmeden sunulmaz (müşteri nereye ödeyeceğini bilmeli)
        if (($o['havale'] ?? false) && filled($o['havale_hesaplari'] ?? null)) {
            $liste['havale'] = [
                'ad' => self::ADLAR['havale'],
                'aciklama' => 'Sipariş sonrası banka bilgileri gösterilir. Açıklamaya sipariş numaranızı yazın; '
                    .self::havaleSureGun().' gün içinde ödenmeyen sipariş iptal olur.',
                'ek_ucret' => 0.0,
            ];
        }

        if ($o['kapida'] ?? false) {
            $liste['kapida'] = [
                'ad' => self::ADLAR['kapida'],
                'aciklama' => (string) ($o['kapida_aciklama'] ?? '') ?: 'Ürünü teslim alırken ödersiniz.',
                'ek_ucret' => self::kapidaEkUcret(),
            ];
        }

        return $liste;
    }

    public static function etkinMi(string $yontem): bool
    {
        return array_key_exists($yontem, self::etkinler());
    }

    public static function ekUcret(string $yontem): float
    {
        return self::etkinler()[$yontem]['ek_ucret'] ?? 0.0;
    }

    public static function ad(?string $yontem): string
    {
        return self::ADLAR[$yontem ?? 'paytr'] ?? (string) $yontem;
    }

    public static function cevrimdisi(?string $yontem): bool
    {
        return in_array($yontem, self::CEVRIMDISI, true);
    }

    public static function havaleSureGun(): int
    {
        return max(1, (int) config('shop.odeme.havale_sure_gun', 3));
    }

    public static function kapidaEkUcret(): float
    {
        return max(0.0, round((float) config('shop.odeme.kapida_ek_ucret', 0), 2));
    }

    /**
     * Panelde satır satır girilen hesaplar: "Banka | Hesap sahibi | IBAN".
     *
     * @return list<array{banka: string, sahip: string, iban: string}>
     */
    public static function havaleHesaplari(): array
    {
        $satirlar = preg_split('/\R/', (string) config('shop.odeme.havale_hesaplari', '')) ?: [];

        return collect($satirlar)
            ->map(fn ($s) => array_map('trim', explode('|', $s)))
            ->filter(fn ($p) => filled($p[0] ?? null))
            ->map(fn ($p) => [
                'banka' => $p[0],
                'sahip' => $p[1] ?? (string) config('shop.satici.unvan'),
                'iban' => $p[2] ?? '',
            ])
            ->values()
            ->all();
    }
}
