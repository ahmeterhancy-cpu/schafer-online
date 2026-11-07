<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use App\Services\CevrimdisiOdeme;
use App\Services\Faturalar;
use App\Services\OrderShipping;
use App\Services\OrderStock;
use App\Services\PaymentRefunds;
use App\Support\OdemeYontemleri;
use App\Support\Yetki;
use Filament\Actions\Action;
use App\Filament\Resources\Orders\SiparisIslemleri;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')
                    ->label('Sipariş')
                    ->searchable()
                    ->copyable()
                    ->weight('medium'),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Müşteri')
                    ->searchable()
                    ->description(fn (Order $kayit) => $kayit->customer_phone),

                TextColumn::make('grand_total')
                    ->label('Tutar')
                    ->money('TRY', locale: 'tr')
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Ödeme')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'paid' => 'Ödendi',
                        'failed' => 'Başarısız',
                        'refunded' => 'İade edildi',
                        'partially_refunded' => 'Kısmi iade',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded', 'partially_refunded' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('payment_provider')
                    ->label('Yöntem')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => OdemeYontemleri::ad($state))
                    ->color(fn (?string $state) => match ($state) {
                        'havale' => 'warning',
                        'kapida' => 'info',
                        default => 'gray',
                    })
                    ->placeholder('Kart'),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'paid' => 'Ödendi',
                        'preparing' => 'Hazırlanıyor',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim edildi',
                        'cancelled' => 'İptal',
                        'refunded' => 'İade',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'shipped' => 'info',
                        'delivered' => 'success',
                        'cancelled', 'refunded' => 'danger',
                        default => 'gray',
                    }),

                /*
                 * Stok durumu listede görünür: "Rezerve"de takılı kalmış eski
                 * bir sipariş hayalet stok tutuyor demektir. Bu sütun olmasa
                 * o durum ancak müşteri "stokta yok" deyince fark edilirdi.
                 */
                TextColumn::make('stock_state')
                    ->label('Stok')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'none' => 'Yok',
                        'reserved' => 'Rezerve',
                        'committed' => 'Düşüldü',
                        default => $state,
                    })
                    ->color(fn (string $state) => $state === 'reserved' ? 'warning' : 'gray')
                    ->toggleable(),

                TextColumn::make('tracking_number')
                    ->label('Takip no')
                    ->placeholder('—')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('invoice_number')
                    ->label('Fatura')
                    ->getStateUsing(fn (Order $kayit) => $kayit->invoice_number
                        ?: ($kayit->payment_status === 'paid' ? 'Kesilmedi' : null))
                    ->badge()
                    ->color(fn (Order $kayit) => $kayit->invoice_number ? 'success' : 'warning')
                    ->description(fn (Order $kayit) => $kayit->invoice_sent_at ? 'Gönderildi' : null)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Ödeme durumu')
                    ->options([
                        'pending' => 'Bekliyor',
                        'paid' => 'Ödendi',
                        'failed' => 'Başarısız',
                        'refunded' => 'İade edildi',
                        'partially_refunded' => 'Kısmi iade',
                    ]),

                SelectFilter::make('payment_provider')
                    ->label('Ödeme yöntemi')
                    ->options(OdemeYontemleri::ADLAR),

                Filter::make('havale_bekleyen')
                    ->label('Havale ödemesi bekleyenler')
                    ->query(fn ($query) => $query->where('payment_provider', 'havale')
                        ->where('payment_status', 'pending')
                        ->where('status', 'pending')),

                SelectFilter::make('status')
                    ->label('Sipariş durumu')
                    ->options([
                        'pending' => 'Bekliyor',
                        'paid' => 'Ödendi',
                        'shipped' => 'Kargoda',
                        'delivered' => 'Teslim edildi',
                        'cancelled' => 'İptal',
                    ]),

                Filter::make('faturasiz')
                    ->label('Faturası kesilmemiş ödenmiş siparişler')
                    ->query(fn ($query) => $query->where('payment_status', 'paid')
                        ->where('status', '!=', 'cancelled')
                        ->whereNull('invoice_number')),

                Filter::make('takili_rezerv')
                    ->label('Takılı rezervler (2 saatten eski)')
                    ->query(fn ($query) => $query->where('stock_state', 'reserved')
                        ->where('created_at', '<', now()->subHours(2))
                        ->where(fn ($q) => $q->whereNull('payment_provider')->orWhere('payment_provider', '!=', 'havale'))),
            ])
            ->recordActions([
                ...SiparisIslemleri::birincil(),
                ActionGroup::make(SiparisIslemleri::ikincil())->tooltip('Diğer işlemler'),
                ViewAction::make()->label('Detay'),
            ])
            ->emptyStateHeading('Henüz sipariş yok');
    }
}
