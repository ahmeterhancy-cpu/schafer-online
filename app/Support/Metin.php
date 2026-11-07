<?php

namespace App\Support;

/** Metin yardımcıları (Türkçe büyük/küçük harf kurallarıyla). */
class Metin
{
    /** Bu kalıplar biçim değişmeden kalır (ölçü, ürün kodu kısaltmaları) */
    private const SABIT = ['XL', 'XXL', 'LED', 'USB', 'PRÇ.', 'PRÇ', 'CM', 'ML', 'LT', 'W'];

    /**
     * Tamamen büyük harfle girilmiş adı "Baş Harfleri Büyük" yapar:
     * "SCHAFER BARİSTA KAHVE MAKİNESİ-2 PRÇ." → "Schafer Barista Kahve Makinesi-2 Prç."
     * Zaten karışık yazılmış adlara dokunmaz (markanın kendi yazımı korunur).
     */
    public static function baslik(string $metin): string
    {
        $harfler = preg_replace('/[^\p{L}]/u', '', $metin);
        if ($harfler === '' || mb_strtoupper($harfler, 'UTF-8') !== $harfler) {
            return $metin;
        }

        $kucuk = mb_strtolower(strtr($metin, ['I' => 'ı', 'İ' => 'i']), 'UTF-8');

        return preg_replace_callback('/(^|[\s\-\/(])(\p{L})/u', function ($m) {
            $harf = $m[2] === 'i' ? 'İ' : ($m[2] === 'ı' ? 'I' : mb_strtoupper($m[2], 'UTF-8'));

            return $m[1].$harf;
        }, $kucuk);
    }
}
