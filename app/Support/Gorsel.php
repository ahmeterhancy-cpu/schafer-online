<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Throwable;

/** Ürün görseli yardımcıları. */
class Gorsel
{
    /**
     * Görsel beyaz zeminli bir ürün çekimi mi (köşeler ve kenar ortaları
     * neredeyse beyaz)? Değilse "sahne" (yaşam alanı) fotoğrafıdır.
     * Sonuç dosya değişene kadar önbellekte tutulur.
     */
    public static function beyazZeminMi(?string $yol): bool
    {
        if (! $yol) {
            return true;
        }

        $tam = Storage::disk('public')->path($yol);
        if (! is_file($tam)) {
            return true;
        }

        return Cache::rememberForever('gorsel-zemin:'.md5($yol.filemtime($tam)), function () use ($tam) {
            try {
                $im = @imagecreatefromstring((string) file_get_contents($tam));
                if (! $im) {
                    return true;
                }
                $w = imagesx($im);
                $h = imagesy($im);
                $noktalar = [[2, 2], [$w - 3, 2], [2, $h - 3], [$w - 3, $h - 3], [(int) ($w / 2), 2], [(int) ($w / 2), $h - 3], [2, (int) ($h / 2)], [$w - 3, (int) ($h / 2)]];
                $beyaz = 0;
                foreach ($noktalar as [$x, $y]) {
                    $c = imagecolorat($im, $x, $y);
                    $r = ($c >> 16) & 0xFF;
                    $g = ($c >> 8) & 0xFF;
                    $b = $c & 0xFF;
                    if (min($r, $g, $b) >= 232) {
                        $beyaz++;
                    }
                }

                return $beyaz >= 7;
            } catch (Throwable) {
                return true;
            }
        });
    }
}
