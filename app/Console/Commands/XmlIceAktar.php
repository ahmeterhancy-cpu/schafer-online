<?php

namespace App\Console\Commands;

use App\Services\Xml\XmlIceAktarim;
use Illuminate\Console\Command;

/**
 * Tedarikçi XML'inden fiyat/stok güncelleme. Adres verilmezse panelde
 * (Ayarlar → XML) kayıtlı adres kullanılır.
 *
 *   php artisan schafer:xml-ice-aktar --kuru     # yalnız önizleme
 *   php artisan schafer:xml-ice-aktar --kaynak=https://.../urunler.xml
 */
class XmlIceAktar extends Command
{
    protected $signature = 'schafer:xml-ice-aktar {--kaynak= : XML adresi ya da dosya yolu} {--kuru : Hiçbir şeyi değiştirmeden raporla}';

    protected $description = 'Tedarikçi XML\'inden ürün fiyat ve stoklarını günceller';

    public function handle(XmlIceAktarim $aktarim): int
    {
        $rapor = $aktarim->calistir($this->option('kaynak') ?: null, (bool) $this->option('kuru'));

        if (isset($rapor['hata'])) {
            $this->error($rapor['hata']);

            return self::FAILURE;
        }

        $this->info(($rapor['kuru'] ? 'ÖNİZLEME — hiçbir şey değişmedi. ' : '').'Ürün etiketi: '.$rapor['urun_etiketi']);
        $this->table(['Alan', 'XML etiketi'], collect($rapor['alanlar'])->map(fn ($v, $k) => [$k, $v ?? '—'])->values()->all());
        $this->table(['Sayaç', 'Adet'], collect($rapor['sayac'])->map(fn ($v, $k) => [$k, $v])->values()->all());

        return self::SUCCESS;
    }
}
