<?php

namespace App\Support;

/** Metin yardımcıları (Türkçe büyük/küçük harf kurallarıyla). */
class Metin
{
    /** Bu kalıplar biçim değişmeden kalır (ölçü, ürün kodu kısaltmaları) */
    private const SABIT = ['XL', 'XXL', 'LED', 'USB', 'PRÇ.', 'PRÇ', 'CM', 'ML', 'LT', 'W'];

    /**
     * Düz metin ürün açıklamasını güvenli HTML'e çevirir:
     * boş satır = paragraf, "• " ile başlayan satırlar = liste,
     * ":" ile biten kısa satır ("Özellikler:") = ara başlık.
     * Girdi her zaman kaçırılır (e()); panelden HTML girilemez.
     */
    public static function aciklamaHtml(?string $metin): string
    {
        $bloklar = preg_split("/\n\s*\n/", trim(str_replace("\r\n", "\n", (string) $metin)));
        $html = '';

        foreach ($bloklar as $blok) {
            $satirlar = array_values(array_filter(array_map('trim', explode("\n", $blok)), fn ($s) => $s !== ''));
            $paragraf = [];
            $liste = [];

            $bosalt = function () use (&$paragraf, &$liste, &$html) {
                if ($paragraf) {
                    $html .= '<p>'.implode('<br>', array_map('e', $paragraf)).'</p>';
                    $paragraf = [];
                }
                if ($liste) {
                    $html .= '<ul>'.implode('', array_map(fn ($s) => '<li>'.e($s).'</li>', $liste)).'</ul>';
                    $liste = [];
                }
            };

            foreach ($satirlar as $s) {
                if (preg_match('/^[•\-\*]\s*(.+)$/u', $s, $m)) {
                    if ($paragraf) {
                        $html .= '<p>'.implode('<br>', array_map('e', $paragraf)).'</p>';
                        $paragraf = [];
                    }
                    $liste[] = $m[1];
                } elseif (mb_strlen($s) <= 40 && str_ends_with($s, ':')) {
                    $bosalt();
                    $html .= '<h3>'.e(rtrim($s, ':')).'</h3>';
                } else {
                    if ($liste) {
                        $html .= '<ul>'.implode('', array_map(fn ($x) => '<li>'.e($x).'</li>', $liste)).'</ul>';
                        $liste = [];
                    }
                    $paragraf[] = $s;
                }
            }
            $bosalt();
        }

        return $html;
    }

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
