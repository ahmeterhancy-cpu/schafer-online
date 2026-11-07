<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Support\OdemeYontemleri;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Üyenin sipariş geçmişi — satıra tıklayınca sipariş detayı açılır. */
class SiparislerRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Siparişleri';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Order $kayit) => OrderResource::getUrl('view', ['record' => $kayit]))
            ->columns([
                TextColumn::make('number')->label('Sipariş')->weight('medium'),
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('grand_total')->label('Tutar')->money('TRY', locale: 'tr')->sortable(),
                TextColumn::make('payment_provider')->label('Yöntem')
                    ->formatStateUsing(fn (?string $state) => OdemeYontemleri::ad($state))->placeholder('Kart'),
                TextColumn::make('status_label')->label('Durum')->badge(),
            ])
            ->emptyStateHeading('Bu üyenin siparişi yok');
    }
}
