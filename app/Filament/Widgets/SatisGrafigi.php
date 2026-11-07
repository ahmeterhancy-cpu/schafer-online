<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Support\Yetki;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * Ciro ve sipariş adedi — ödeme tarihine göre (ödenmemiş/iptal sayılmaz).
 * Ciro bilgisi yalnız yöneticiye.
 */
class SatisGrafigi extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Satışlar';

    protected ?string $maxHeight = '280px';

    public ?string $filter = '30';

    public static function canView(): bool
    {
        return Yetki::yonetici();
    }

    protected function getFilters(): ?array
    {
        return ['7' => 'Son 7 gün', '30' => 'Son 30 gün', '90' => 'Son 90 gün', '365' => 'Son 12 ay'];
    }

    public function getDescription(): ?string
    {
        [$ciro, $adet] = $this->toplamlar();

        return number_format($ciro, 2, ',', '.').' TL ciro · '.$adet.' sipariş · ortalama sepet '
            .($adet > 0 ? number_format($ciro / $adet, 2, ',', '.') : '0,00').' TL';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $gun = (int) ($this->filter ?: 30);
        $aylik = $gun > 90;
        $bas = $aylik ? now()->subMonths(11)->startOfMonth() : now()->subDays($gun - 1)->startOfDay();

        // SQLite ve MySQL'de çalışan gün/ay anahtarı
        $bicim = $aylik ? '%Y-%m' : '%Y-%m-%d';
        $ifade = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('{$bicim}', paid_at)"
            : "DATE_FORMAT(paid_at, '{$bicim}')";

        $satirlar = Order::query()
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where('paid_at', '>=', $bas)
            ->selectRaw("{$ifade} as anahtar, SUM(grand_total) as ciro, COUNT(*) as adet")
            ->groupBy('anahtar')
            ->get()
            ->keyBy('anahtar');

        $etiketler = [];
        $ciro = [];
        $adet = [];

        $donem = $aylik
            ? CarbonPeriod::create($bas, '1 month', now()->startOfMonth())
            : CarbonPeriod::create($bas, '1 day', now()->startOfDay());

        foreach ($donem as $t) {
            $k = $t->format($aylik ? 'Y-m' : 'Y-m-d');
            $etiketler[] = $aylik ? $t->translatedFormat('M Y') : $t->format('d.m');
            $ciro[] = round((float) ($satirlar[$k]->ciro ?? 0), 2);
            $adet[] = (int) ($satirlar[$k]->adet ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ciro (TL)',
                    'data' => $ciro,
                    'backgroundColor' => 'rgba(210, 28, 34, 0.75)',
                    'borderColor' => 'rgb(210, 28, 34)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Sipariş',
                    'data' => $adet,
                    'type' => 'line',
                    'borderColor' => 'rgb(169, 135, 106)',
                    'backgroundColor' => 'rgb(169, 135, 106)',
                    'tension' => 0,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $etiketler,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'position' => 'left'],
                'y1' => ['beginAtZero' => true, 'position' => 'right', 'grid' => ['drawOnChartArea' => false], 'ticks' => ['precision' => 0]],
            ],
            'plugins' => ['legend' => ['position' => 'bottom']],
        ];
    }

    /** @return array{0: float, 1: int} */
    private function toplamlar(): array
    {
        $gun = (int) ($this->filter ?: 30);
        $bas = $gun > 90 ? now()->subMonths(11)->startOfMonth() : now()->subDays($gun - 1)->startOfDay();

        $q = Order::query()->where('payment_status', 'paid')->where('status', '!=', 'cancelled')->where('paid_at', '>=', $bas);

        return [(float) (clone $q)->sum('grand_total'), (clone $q)->count()];
    }
}
