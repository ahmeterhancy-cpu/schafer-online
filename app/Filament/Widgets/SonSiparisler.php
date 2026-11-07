<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Support\OdemeYontemleri;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class SonSiparisler extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 2];

    protected static ?string $heading = 'Son siparişler';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(8))
            ->paginated(false)
            ->recordUrl(fn (Order $kayit) => OrderResource::getUrl('view', ['record' => $kayit]))
            ->columns([
                TextColumn::make('number')->label('Sipariş')->weight('medium')
                    ->description(fn (Order $kayit) => $kayit->created_at->diffForHumans()),
                TextColumn::make('customer_name')->label('Müşteri'),
                TextColumn::make('grand_total')->label('Tutar')->money('TRY', locale: 'tr'),
                TextColumn::make('payment_provider')->label('Yöntem')
                    ->formatStateUsing(fn (?string $state) => OdemeYontemleri::ad($state))->placeholder('Kart'),
                TextColumn::make('status_label')->label('Durum')->badge()
                    ->color(fn (Order $kayit) => match ($kayit->status) {
                        'delivered' => 'success',
                        'shipped' => 'info',
                        'cancelled', 'refunded' => 'danger',
                        'paid', 'preparing' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Action::make('tumu')->label('Tümü')->link()->url(OrderResource::getUrl('index')),
            ])
            ->emptyStateHeading('Henüz sipariş yok');
    }
}
