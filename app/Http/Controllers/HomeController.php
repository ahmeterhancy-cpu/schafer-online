<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

/**
 * Ana sayfa — schaferkibris.com bölüm sırası:
 * slayt → "#SCHAFER #KEŞFET" trend kategoriler → kategori başına ürün
 * bölümleri (Sofra Grubu, Ev Tekstili, Kahve Makineleri, Tencereler…) →
 * kampanyadaki ürünler.
 *
 * Ürün bölümleri panelde "Ana sayfada göster" işaretli kategorilerden gelir
 * (Kategoriler → is_featured), sırası kategori sırasıdır. Her bölüm gerçek
 * veriden beslenir; ürünü olmayan bölüm çizilmez.
 */
class HomeController extends Controller
{
    public function __invoke()
    {
        $aktif = fn () => Product::query()->where('is_active', true)->where('total_stock', '>', 0)->kartIcin();

        // Trend kategoriler: görselli alt kategoriler, en çok ürünü olan 8'i
        $trend = Category::query()
            ->whereNotNull('parent_id')
            ->where('is_active', true)
            ->whereNotNull('image')
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->filter(fn ($k) => $k->products_count > 0)
            ->sortByDesc('products_count')
            ->take(8)
            ->values();

        $bolumler = Category::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('position')
            ->get()
            ->map(function (Category $kat) use ($aktif) {
                $urunler = $aktif()
                    ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $kat->altAgacIdleri()))
                    ->orderByDesc('is_featured')
                    ->orderBy('position')
                    ->orderByDesc('created_at')
                    ->limit(8)
                    ->get();

                return ['kategori' => $kat, 'urunler' => $urunler];
            })
            ->filter(fn ($b) => $b['urunler']->isNotEmpty())
            ->values();

        $kampanyalar = $aktif()
            ->whereHas('variants', fn ($v) => $v->where('is_active', true)
                ->whereNotNull('compare_at_price')
                ->whereColumn('compare_at_price', '>', 'price'))
            ->orderByDesc('is_featured')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        // Slayt yoksa kahraman alanında öne çıkan ürün görselleri kullanılır
        $vitrinUrunleri = Product::query()
            ->where('is_active', true)
            ->whereNotNull('hero_image')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get(['id', 'name', 'slug', 'hero_image']);

        $banner = fn (string $yer) => rescue(fn () => Banner::yayinda($yer)->get(), collect(), false);

        return view('vitrin.anasayfa', [
            'bannerSlayt' => $banner('slayt'),
            'bannerAfis' => $banner('afis')->take(3),
            'bannerGenis' => $banner('genis')->take(2),
            'trend' => $trend,
            'bolumler' => $bolumler,
            'kampanyalar' => $kampanyalar,
            'vitrinUrunleri' => $vitrinUrunleri,
        ]);
    }
}
