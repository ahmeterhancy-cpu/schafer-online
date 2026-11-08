<?php

namespace App\Services\Xml;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use XMLWriter;

/**
 * Dışa ürün XML'i — Google Merchant RSS 2.0 biçimi.
 *
 * Aynı dosyayı Meta (Facebook/Instagram) katalog, Akakçe/Cimri gibi fiyat
 * karşılaştırma siteleri ve bayiler de okuyabiliyor; ayrı biçim yazmaya
 * gerek yok.
 *
 * Kurallar:
 *  - Yalnız yayındaki ve fiyatı olan ürünler girer. Stoğu bitenler
 *    "out_of_stock" olarak kalır (Google, ürünü listeden düşürmek yerine
 *    "stokta yok" gösterir; stok gelince yeniden onay beklenmez).
 *  - Her varyant ayrı <item>; tek varyantlı üründe kimlik = SKU.
 *  - İndirimdeyse g:price eski (normal) fiyat, g:sale_price satış fiyatı.
 */
class UrunBeslemesi
{
    private const PARA = 'TRY';

    /** @var array<int, Category> */
    private array $kategoriler = [];

    public function uret(): string
    {
        $this->kategoriler = Category::all()->keyBy('id')->all();

        $x = new XMLWriter;
        $x->openMemory();
        $x->setIndent(true);
        $x->startDocument('1.0', 'UTF-8');
        $x->startElement('rss');
        $x->writeAttribute('version', '2.0');
        $x->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $x->startElement('channel');
        $x->writeElement('title', (string) config('shop.ad', config('app.name')));
        $x->writeElement('link', url('/'));
        $x->writeElement('description', 'Ürün kataloğu');

        Product::query()
            ->where('is_active', true)
            ->with(['variants' => fn ($q) => $q->where('is_active', true)->orderBy('position'), 'variants.optionValues.option', 'media', 'categories'])
            ->orderBy('id')
            ->chunkById(200, function ($urunler) use ($x) {
                foreach ($urunler as $urun) {
                    $tekil = $urun->variants->count() === 1;
                    foreach ($urun->variants as $varyant) {
                        if ((float) $varyant->price <= 0) {
                            continue;
                        }
                        $this->oge($x, $urun, $varyant, $tekil);
                    }
                }
            });

        $x->endElement(); // channel
        $x->endElement(); // rss
        $x->endDocument();

        return $x->outputMemory();
    }

    private function oge(XMLWriter $x, Product $urun, ProductVariant $v, bool $tekil): void
    {
        $fiyat = (float) $v->price;
        $eski = (float) $v->compare_at_price;
        $etiket = $tekil ? '' : $v->label;
        $baslik = $etiket === '' ? $urun->name : $urun->name.' - '.$etiket;
        $aciklama = $this->duzMetin($urun->description ?: $urun->short_description ?: $urun->name);

        $x->startElement('item');
        $x->writeElement('g:id', $v->sku ?: 'V'.$v->id);
        if (! $tekil) {
            $x->writeElement('g:item_group_id', $urun->base_sku ?: 'P'.$urun->id);
        }
        $x->writeElement('g:title', Str::limit($baslik, 150, ''));
        $x->writeElement('g:description', Str::limit($aciklama, 5000, ''));
        $x->writeElement('g:link', route('products.show', $urun->slug));

        $gorseller = $urun->media->sortBy('position')->pluck('path')->prepend($v->image ?: $urun->hero_image)
            ->filter()->unique()->values();
        if ($gorseller->isNotEmpty()) {
            $x->writeElement('g:image_link', asset('storage/'.$gorseller->first()));
            foreach ($gorseller->slice(1, 10) as $yol) {
                $x->writeElement('g:additional_image_link', asset('storage/'.$yol));
            }
        }

        $x->writeElement('g:availability', $v->is_orderable ? 'in_stock' : 'out_of_stock');
        if ($eski > $fiyat) {
            $x->writeElement('g:price', $this->para($eski));
            $x->writeElement('g:sale_price', $this->para($fiyat));
        } else {
            $x->writeElement('g:price', $this->para($fiyat));
        }
        $x->writeElement('g:condition', 'new');
        $x->writeElement('g:brand', 'Schafer');

        $barkod = preg_replace('/\D/', '', (string) $v->barcode);
        if (in_array(strlen($barkod), [8, 12, 13, 14], true)) {
            $x->writeElement('g:gtin', $barkod);
        } else {
            $x->writeElement('g:identifier_exists', 'no');
        }
        if ($v->sku) {
            $x->writeElement('g:mpn', $v->sku);
        }

        $tur = $this->kategoriYolu($urun);
        if ($tur !== '') {
            $x->writeElement('g:product_type', $tur);
        }
        $x->writeElement('g:quantity', (string) max(0, $v->available_stock));
        $x->endElement();
    }

    private function para(float $tutar): string
    {
        return number_format($tutar, 2, '.', '').' '.self::PARA;
    }

    /** En derin kategorinin yolu: "Mutfak > Tencere". */
    private function kategoriYolu(Product $urun): string
    {
        $enIyi = [];
        foreach ($urun->categories as $kat) {
            $yol = [];
            $k = $this->kategoriler[$kat->id] ?? null;
            $koruma = 0;
            while ($k && $koruma++ < 10) {
                array_unshift($yol, $k->name);
                $k = $k->parent_id ? ($this->kategoriler[$k->parent_id] ?? null) : null;
            }
            if (count($yol) > count($enIyi)) {
                $enIyi = $yol;
            }
        }

        return implode(' > ', $enIyi);
    }

    private function duzMetin(string $html): string
    {
        $metin = html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</li>'], ' ', $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $metin));
    }
}
