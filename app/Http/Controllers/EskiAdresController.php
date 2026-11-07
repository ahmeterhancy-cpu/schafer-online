<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

/**
 * Eski schaferkibris.com (WooCommerce) adreslerini yeni sayfalara 301 ile taşır.
 * Arama motorundaki sıralama ve paylaşılmış bağlantılar boşa gitmesin.
 *
 *   /product/{slug}/                    → /urun/{yeni-slug}
 *   /product-category/.../{slug}/       → /kategori/{yeni-slug}
 *   /shop, /magaza                      → /koleksiyonlar
 *   /iletisim/, /hesabim/...            → karşılıkları
 *
 * Eşleşme woo_slug sütunundan (içe aktarmada kaydedildi); yoksa yeni slug denenir.
 */
class EskiAdresController extends Controller
{
    public function urun(string $slug)
    {
        $slug = urldecode(trim($slug, '/'));
        $urun = Product::where('woo_slug', $slug)->orWhere('slug', $slug)->first(['slug']);

        return $urun
            ? redirect()->route('products.show', $urun->slug, 301)
            : redirect()->route('catalog.search', ['q' => str_replace('-', ' ', $slug)], 302);
    }

    public function kategori(string $yol)
    {
        $parcalar = array_values(array_filter(explode('/', urldecode($yol))));
        $son = end($parcalar) ?: '';

        // Sayfalı eski adres: /product-category/x/page/2/
        if (count($parcalar) >= 3 && $parcalar[count($parcalar) - 2] === 'page') {
            $son = $parcalar[count($parcalar) - 3];
        }

        $kat = Category::where('woo_slug', $son)->orWhere('slug', $son)->first(['slug']);

        return $kat
            ? redirect()->route('catalog.category', $kat->slug, 301)
            : redirect()->route('collections.index', status: 301);
    }
}
