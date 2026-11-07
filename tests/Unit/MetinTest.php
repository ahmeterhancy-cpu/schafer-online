<?php

namespace Tests\Unit;

use App\Support\Metin;
use PHPUnit\Framework\TestCase;

class MetinTest extends TestCase
{
    public function test_aciklama_paragraf_baslik_ve_listeye_donusur(): void
    {
        $html = Metin::aciklamaHtml("Giriş cümlesi.\n\nÖzellikler:\n• 24 cm\n• Granit kaplama");

        $this->assertSame('<p>Giriş cümlesi.</p><h3>Özellikler</h3><ul><li>24 cm</li><li>Granit kaplama</li></ul>', $html);
    }

    public function test_aciklamadaki_html_kacirilir(): void
    {
        $html = Metin::aciklamaHtml("<script>alert(1)</script>\n• <b>x</b>");

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringContainsString('<li>&lt;b&gt;x&lt;/b&gt;</li>', $html);
    }

    public function test_tamamen_buyuk_harfli_ad_turkce_kurala_gore_duzelir(): void
    {
        $this->assertSame('Schafer Barista Kahve Makinesi-2 Prç.- Siyah', Metin::baslik('SCHAFER BARİSTA KAHVE MAKİNESİ-2 PRÇ.- SİYAH'));
        // "I" küçülünce "ı" olur, kelime başında yine "I"
        $this->assertSame('Schafer Iron Man Termos', Metin::baslik('SCHAFER IRON MAN TERMOS'));
        $this->assertSame('Schafer Adel Navy Blue', Metin::baslik('Schafer Adel Navy Blue'), 'Karışık yazıma dokunulmaz');
    }
}
