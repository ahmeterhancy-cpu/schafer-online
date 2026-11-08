<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Yapım aşaması perdesi — panelden açılıp kapanır (Site Ayarları).
 *
 * Perde açıkken:
 *  - Ziyaretçi 503 + "yapım aşamasında" sayfasını görür (Retry-After ile;
 *    arama motorları siteyi kaldırmaz, geçici sayar).
 *  - Yönetici siteyi NORMAL görür (önizleme) ve panele girebilir.
 *  - Panelde PIN tanımlıysa sayfada PIN formu çıkar; doğru PIN'i giren
 *    cihaz da siteyi normal görür (müşteriye/ekibe göstermek için).
 *    Çerezde PIN değil, uygulama anahtarıyla imzalı özeti durur; PIN
 *    değişince eski çerezlerin hepsi geçersiz olur.
 *  - PayTR bildirimleri ETKİLENMEZ: routes/paytr.php `web` grubunun
 *    dışında, bu ara katman orada hiç çalışmıyor.
 *
 * TUZAK: Filament Livewire'ı rastgele önekle servis ediyor
 * (/livewire-172643c6/update). Geçiş listesinde `livewire*` olmalı;
 * `livewire/*` EŞLEŞMEZ ve perde açıkken panele giriş yapılamaz.
 */
class BakimPerdesi
{
    public const CEREZ = 'sch_onizleme';

    /** Perdeden geçen yollar. */
    private const GECIS = [
        'admin', 'admin/*',
        'livewire*',
        'filament/*',
        'build/*', 'img/*', 'css/*', 'js/*', 'fonts/*', 'storage/*',
        'robots.txt', 'favicon.ico',
        'onizleme-pin',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('shop.bakim_modu')) {
            return $next($request);
        }

        if ($request->is(...self::GECIS)) {
            return $next($request);
        }

        // Yönetici ve PIN girmiş cihaz siteyi olduğu gibi görür
        if ($request->user()?->panelde() || static::onizlemede($request)) {
            return $next($request);
        }

        return response()
            ->view('vitrin.bakim', ['hedef' => '/'.ltrim($request->path(), '/')], 503)
            ->header('Retry-After', '3600')
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'no-store');
    }

    public static function jeton(string $pin): string
    {
        return hash_hmac('sha256', 'onizleme|'.$pin, (string) config('app.key'));
    }

    public static function onizlemede(Request $request): bool
    {
        $pin = (string) config('shop.yapim.pin');
        $cerez = (string) $request->cookie(self::CEREZ);

        return $pin !== '' && $cerez !== '' && hash_equals(static::jeton($pin), $cerez);
    }
}
