<?php

namespace App\Http\Controllers;

use App\Http\Middleware\BakimPerdesi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Yapım aşaması perdesinde PIN formu. Doğru PIN'i giren tarayıcıya 30 günlük
 * çerez bırakılır; site o cihazda normal açılır.
 */
class YapimController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate(['pin' => ['required', 'string', 'max:20']]);

        // Yalnız site içi göreli adrese dön (açık yönlendirme olmasın)
        $hedef = (string) $request->input('to', '/');
        if (! str_starts_with($hedef, '/') || str_starts_with($hedef, '//')) {
            $hedef = '/';
        }

        $pin = (string) config('shop.yapim.pin');
        if ($pin === '' || ! hash_equals($pin, (string) $request->input('pin'))) {
            return redirect($hedef)->with('pin_hata', 'PIN kodu hatalı.');
        }

        return redirect($hedef)->withCookie(cookie(
            BakimPerdesi::CEREZ,
            BakimPerdesi::jeton($pin),
            60 * 24 * 30,
            httpOnly: true,
            sameSite: 'lax',
        ));
    }
}
