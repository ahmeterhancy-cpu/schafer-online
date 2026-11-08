<?php

namespace App\Http\Controllers;

use App\Services\Xml\UrunBeslemesi;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * /xml/urunler.xml — Google Merchant, Meta katalog ve fiyat karşılaştırma
 * siteleri için ürün beslemesi. 30 dk önbellekli: bu servisler dosyayı
 * sık çeker, her istekte 1500 ürünü yeniden kurmaya gerek yok.
 */
class XmlBeslemeController extends Controller
{
    public const ONBELLEK = 'xml_urun_beslemesi';

    public function __invoke(UrunBeslemesi $besleme): Response
    {
        // Fiyatlar gizliyken besleme de kapalı: Google/Meta fiyatsız ürün kabul etmez
        abort_unless(config('shop.xml.disa_acik') && ! config('shop.fiyat.gizle'), 404);

        $xml = Cache::remember(self::ONBELLEK, now()->addMinutes(30), fn () => $besleme->uret());

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
