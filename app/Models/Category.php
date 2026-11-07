<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if (blank($category->slug)) {
                $category->slug = Str::slug($category->name) ?: 'kategori';
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('position');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function sizeChart(): BelongsTo
    {
        return $this->belongsTo(SizeChart::class);
    }

    /**
     * Kendisi + tüm alt kategorilerinin kimlikleri (en fazla 5 seviye).
     * Kategori sayfası ve ana sayfa bölümleri alt dallardaki ürünleri de gösterir.
     *
     * @return list<int>
     */
    public function altAgacIdleri(): array
    {
        $idler = [$this->id];
        $katman = [$this->id];

        for ($i = 0; $i < 5 && $katman; $i++) {
            $katman = static::whereIn('parent_id', $katman)->pluck('id')->all();
            $idler = array_merge($idler, $katman);
        }

        return $idler;
    }

    /**
     * Kendi ölçü tablosu yoksa üst kategoriye çıkar.
     * Döngüye karşı derinlik sınırı var.
     */
    public function resolvedSizeChart(int $depth = 0): ?SizeChart
    {
        if ($this->size_chart_id) {
            return $this->sizeChart;
        }

        if ($depth >= 5) {
            return null;
        }

        return $this->parent?->resolvedSizeChart($depth + 1);
    }
}
