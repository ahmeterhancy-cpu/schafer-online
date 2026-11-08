<?php

namespace App\Http\Middleware;

use App\Support\Fiyat;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fiyatlar gizliyken sepet ve ödeme kapalı: fiyatı görünmeyen ürün satılamaz.
 * Doğrudan adrese gelen ya da eski bir formu gönderen ziyaretçi ana sayfaya döner.
 */
class SatisKapali
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Fiyat::gizli()) {
            return redirect()->route('home')->with('bilgi', 'Online satış şu an kapalı. '.Fiyat::metin().'.');
        }

        return $next($request);
    }
}
