<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/** Satışta olup stoğu eşiğe inen ya da biten ürünler — önce tükenenler. */
class KritikStok extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Kritik stok';

    public function table(Table $table): Table
    {
        $esik = max(1, (int) config('shop.dusuk_stok_esigi'));

        return $table
            ->query(Product::query()
                ->where('is_active', true)
                ->where('total_stock', '<=', $esik)
                ->orderBy('total_stock')
                ->orderBy('name'))
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25])
            ->recordUrl(fn (Product $kayit) => ProductResource::getUrl('edit', ['record' => $kayit]))
            ->columns([
                ImageColumn::make('hero_image')->label('')->disk('public')->square()->imageSize(36),
                TextColumn::make('name')->label('Ürün')->searchable()->wrap(),
                TextColumn::make('base_sku')->label('SKU')->placeholder('—'),
                TextColumn::make('total_stock')->label('Stok')->badge()->alignCenter()
                    ->color(fn ($state) => (int) $state < 1 ? 'danger' : 'warning')
                    ->formatStateUsing(fn ($state) => (int) $state < 1 ? 'Tükendi' : $state.' adet'),
            ])
            ->headerActions([
                Action::make('hepsi')->label('Ürün listesinde aç')->link()
                    ->url(ProductResource::getUrl('index', ['tableFilters' => ['stok' => ['value' => 'az']]])),
            ])
            ->emptyStateHeading('Stok sorunu yok')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
