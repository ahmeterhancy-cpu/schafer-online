<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

/**
 * Ürün listelerinin ortak sıralama ve fiyat süzgeci
 * (koleksiyonlar, koleksiyon, kategori, arama).
 *
 * Fiyat süzgeci ve sıralama ürünün ÖNBELLEK fiyatına (min_price) bakar;
 * varyantlara inip her ürün için ayrı hesap yapmaz.
 */
class Katalog
{
    public const SIRALAR = [
        'onerilen' => 'Önerilen',
        'yeni' => 'En yeniler',
        'fiyat-artan' => 'Fiyat: düşükten yükseğe',
        'fiyat-azalan' => 'Fiyat: yüksekten düşüğe',
        'begenilen' => 'En beğenilenler',
        'indirim' => 'İndirimdekiler',
    ];

    /**
     * @template T of Builder|Relation
     *
     * @param  T  $q  sorgu ya da ilişki (koleksiyonun ürünleri)
     * @return T
     */
    /** Ekranda sunulan sıralamalar — fiyatlar gizliyken fiyata dayalılar çıkar. */
    public static function siralar(): array
    {
        return Fiyat::gorunur()
            ? self::SIRALAR
            : array_diff_key(self::SIRALAR, array_flip(['fiyat-artan', 'fiyat-azalan', 'indirim']));
    }

    public static function uygula(Builder|Relation $q, Request $request): Builder|Relation
    {
        // Fiyatlar gizliyken adres satırına yazılan fiyat süzgeci de yok sayılır
        $min = Fiyat::gorunur() ? self::sayi($request->query('fiyat_min')) : null;
        $max = Fiyat::gorunur() ? self::sayi($request->query('fiyat_max')) : null;

        if ($min !== null) {
            $q->where('min_price', '>=', $min);
        }

        if ($max !== null) {
            $q->where('min_price', '<=', $max);
        }

        // Tükenenler her sıralamada sona
        $q->orderByRaw('CASE WHEN total_stock > 0 THEN 0 ELSE 1 END');

        return match (self::sira($request)) {
            'yeni' => $q->orderByDesc('created_at'),
            'fiyat-artan' => $q->orderBy('min_price'),
            'fiyat-azalan' => $q->orderByDesc('min_price'),
            'begenilen' => $q->orderByDesc('review_count')->orderByDesc('rating'),
            // Süzgeç + sıra: yalnız gerçekten indirimdekiler
            'indirim' => $q->whereHas('variants', fn ($v) => $v->where('is_active', true)
                ->whereNotNull('compare_at_price')
                ->whereColumn('compare_at_price', '>', 'price'))
                ->orderBy('position'),
            default => $q->orderByDesc('is_featured')->orderBy('position')->orderByDesc('created_at'),
        };
    }

    public static function sira(Request $request): string
    {
        $sira = (string) $request->query('sirala', 'onerilen');

        return array_key_exists($sira, self::siralar()) ? $sira : 'onerilen';
    }

    private static function sayi(mixed $deger): ?float
    {
        if (! is_scalar($deger) || trim((string) $deger) === '') {
            return null;
        }

        $d = str_replace(',', '.', trim((string) $deger));

        return is_numeric($d) && (float) $d >= 0 ? (float) $d : null;
    }
}
