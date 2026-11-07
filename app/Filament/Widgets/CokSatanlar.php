<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/** Son 30 günün en çok satanları (ödenmiş, iptal edilmemiş siparişlerden). */
class CokSatanlar extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 1];

    protected static ?string $heading = 'Çok satanlar (30 gün)';

    public function table(Table $table): Table
    {
        $donem = fn (Builder $o) => $o->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where('paid_at', '>=', now()->subDays(30));

        return $table
            ->query(Product::query()
                ->withSum(['orderItems as satilan' => fn ($q) => $q->whereHas('order', $donem)], 'quantity')
                ->whereHas('orderItems', fn ($q) => $q->whereHas('order', $donem))
                ->orderByDesc('satilan')
                ->limit(6))
            ->paginated(false)
            ->recordUrl(fn (Product $kayit) => ProductResource::getUrl('edit', ['record' => $kayit]))
            ->columns([
                ImageColumn::make('hero_image')->label('')->disk('public')->square()->imageSize(36),
                TextColumn::make('name')->label('Ürün')->lineClamp(2)->wrap(),
                TextColumn::make('satilan')->label('Adet')->alignEnd()->weight('bold'),
            ])
            ->emptyStateHeading('Son 30 günde satış yok');
    }
}
