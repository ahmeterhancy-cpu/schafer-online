<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Panelde seçili ürünlere toplu işlemler: fiyat değişimi, kampanya indirimi,
 * stok ayarlama.
 *
 * Varyantlar MODEL üzerinden güncellenir (sorgu ile değil): gözlemci ürünün
 * fiyat/stok önbelleğini tazeler ve "stoğa girdi" e-postalarını tetikler.
 */
class TopluUrunIslemleri
{
    /** Yuvarlama seçenekleri — panelde gösterilir. */
    public const YUVARLAMA = [
        'yok' => 'Yuvarlama yok (kuruşlu)',
        'tam' => 'Tam liraya (4.563,40 → 4.563)',
        'on' => '10 liraya (4.563 → 4.560)',
        'elli' => '50 liraya (4.563 → 4.550)',
    ];

    /**
     * Fiyatı yüzde ya da tutar olarak değiştirir.
     *
     * @param  Collection<int, Product>  $urunler
     * @param  'yuzde'|'tutar'  $tur
     * @return int güncellenen varyant sayısı
     */
    public function fiyatDegistir(Collection $urunler, string $tur, float $deger, string $yuvarlama = 'yok'): int
    {
        if (! in_array($tur, ['yuzde', 'tutar'], true)) {
            throw new InvalidArgumentException('Geçersiz değişim türü.');
        }

        if ($tur === 'yuzde' && $deger <= -100) {
            throw new InvalidArgumentException('Fiyat %100 ya da daha fazla düşürülemez.');
        }

        return $this->varyantlaraUygula($urunler, function (ProductVariant $v) use ($tur, $deger, $yuvarlama) {
            $eski = (float) $v->price;
            $yeni = $tur === 'yuzde' ? $eski * (1 + $deger / 100) : $eski + $deger;
            $yeni = max(0.0, $this->yuvarla($yeni, $yuvarlama));

            $alanlar = ['price' => $yeni];

            // Eski (üstü çizili) fiyat yeni fiyatın altına düştüyse indirim anlamsızlaşır
            if ($v->compare_at_price !== null && (float) $v->compare_at_price <= $yeni) {
                $alanlar['compare_at_price'] = null;
            }

            return $alanlar;
        });
    }

    /**
     * Kampanya: yüzde indirim. Var olan fiyat "eski fiyat" olur.
     * Zaten indirimdeki üründe indirim ESKİ fiyattan yeniden hesaplanır
     * (üst üste indirim birikmez).
     */
    public function indirimUygula(Collection $urunler, float $yuzde, string $yuvarlama = 'yok'): int
    {
        if ($yuzde <= 0 || $yuzde >= 100) {
            throw new InvalidArgumentException('İndirim oranı 0 ile 100 arasında olmalı.');
        }

        return $this->varyantlaraUygula($urunler, function (ProductVariant $v) use ($yuzde, $yuvarlama) {
            $liste = (float) ($v->compare_at_price ?: $v->price);
            $yeni = $this->yuvarla($liste * (1 - $yuzde / 100), $yuvarlama);

            return $yeni < $liste
                ? ['price' => $yeni, 'compare_at_price' => $liste]
                : null;
        });
    }

    /** İndirimi kaldırır: fiyat eski fiyata döner. */
    public function indirimKaldir(Collection $urunler): int
    {
        return $this->varyantlaraUygula($urunler, fn (ProductVariant $v) => $v->compare_at_price !== null
            ? ['price' => (float) $v->compare_at_price, 'compare_at_price' => null]
            : null);
    }

    /**
     * Stoğu sabit bir adede ayarlar (tedarikçi listesinden gelen ürünler).
     * Rezerve edilmiş adedin altına inilmez — bekleyen siparişler bozulmasın.
     */
    public function stokAyarla(Collection $urunler, int $adet): int
    {
        $adet = max(0, $adet);

        return $this->varyantlaraUygula($urunler, fn (ProductVariant $v) => ['stock' => max($adet, (int) $v->reserved)]);
    }

    public function yuvarla(float $tutar, string $yuvarlama): float
    {
        return match ($yuvarlama) {
            'tam' => round($tutar),
            'on' => round($tutar / 10) * 10,
            'elli' => round($tutar / 50) * 50,
            default => round($tutar, 2),
        };
    }

    /**
     * @param  callable(ProductVariant): (array<string, mixed>|null)  $hesapla  null = dokunma
     */
    private function varyantlaraUygula(Collection $urunler, callable $hesapla): int
    {
        $sayi = 0;

        DB::transaction(function () use ($urunler, $hesapla, &$sayi) {
            $idler = $urunler->pluck('id');

            ProductVariant::whereIn('product_id', $idler)->where('is_active', true)
                ->orderBy('id')
                ->each(function (ProductVariant $v) use ($hesapla, &$sayi) {
                    $alanlar = $hesapla($v);

                    if ($alanlar === null) {
                        return;
                    }

                    $v->fill($alanlar);

                    if ($v->isDirty()) {
                        $v->save();
                        $sayi++;
                    }
                });
        });

        return $sayi;
    }
}
