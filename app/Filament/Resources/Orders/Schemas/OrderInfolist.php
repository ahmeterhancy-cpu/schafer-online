<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use App\Support\OdemeYontemleri;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

/**
 * Sipariş detay sayfası — salt okunur özet. Durum geçişleri üstteki
 * düğmelerle (SiparisIslemleri) yapılır; onlar stok ve ödemeyi de yürütür.
 */
class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $tl = fn ($state) => number_format((float) $state, 2, ',', '.').' TL';

        return $schema->columns(3)->components([

            // --- Özet şeridi ---
            Section::make()
                ->columnSpanFull()
                ->columns(['default' => 2, 'md' => 5])
                ->schema([
                    TextEntry::make('status_label')->label('Sipariş durumu')->badge()
                        ->color(fn (Order $record) => match ($record->status) {
                            'delivered' => 'success',
                            'shipped' => 'info',
                            'cancelled', 'refunded' => 'danger',
                            'paid', 'preparing' => 'warning',
                            default => 'gray',
                        }),
                    TextEntry::make('payment_status_label')->label('Ödeme')->badge()
                        ->color(fn (Order $record) => match ($record->payment_status) {
                            'paid' => 'success',
                            'failed' => 'danger',
                            'refunded', 'partially_refunded' => 'warning',
                            default => 'gray',
                        }),
                    TextEntry::make('payment_provider')->label('Ödeme yöntemi')
                        ->formatStateUsing(fn (?string $state) => OdemeYontemleri::ad($state))
                        ->placeholder('Kart'),
                    TextEntry::make('grand_total')->label('Toplam')->formatStateUsing($tl)
                        ->weight(FontWeight::Bold)->size(TextSize::Large),
                    TextEntry::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i'),
                ]),

            // --- Sol: kalemler + tutarlar ---
            Grid::make(1)->columnSpan(['default' => 3, 'lg' => 2])->schema([
                Section::make('Ürünler')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make(''),
                                TableColumn::make('Ürün'),
                                TableColumn::make('Adet')->alignment('center'),
                                TableColumn::make('Birim')->alignment('end'),
                                TableColumn::make('Tutar')->alignment('end'),
                            ])
                            ->schema([
                                ImageEntry::make('image')->disk('public')->imageSize(44)->square()
                                    ->defaultImageUrl(asset('img/schafer/favicon.png')),
                                TextEntry::make('name')
                                    ->formatStateUsing(fn ($state, $record) => e($state).'<br><span style="font-size:12px;opacity:.65">'
                                        .e(trim(($record->variant_label && $record->variant_label !== $record->sku ? $record->variant_label.' · ' : '').$record->sku, ' ·'))
                                        .'</span>')
                                    ->html()
                                    ->url(fn ($record) => $record->product_id
                                        ? route('filament.admin.resources.products.edit', $record->product_id)
                                        : null),
                                TextEntry::make('quantity')->alignment('center'),
                                TextEntry::make('unit_price')->formatStateUsing($tl)->alignment('end'),
                                TextEntry::make('line_total')->formatStateUsing($tl)->alignment('end')->weight(FontWeight::SemiBold),
                            ]),
                    ]),

                Section::make('Tutarlar')
                    ->columns(['default' => 2, 'md' => 3])
                    ->schema([
                        TextEntry::make('subtotal')->label('Ara toplam')->formatStateUsing($tl),
                        TextEntry::make('discount_total')->label('İndirim')
                            ->formatStateUsing(fn ($state, Order $record) => (float) $state > 0 ? '-'.$tl($state).($record->coupon_code ? ' ('.$record->coupon_code.')' : '') : '—'),
                        TextEntry::make('shipping_total')->label('Teslimat')
                            ->formatStateUsing(fn ($state) => (float) $state > 0 ? $tl($state) : 'Ücretsiz'),
                        TextEntry::make('payment_fee')->label('Ödeme yöntemi bedeli')
                            ->formatStateUsing(fn ($state) => (float) $state > 0 ? $tl($state) : '—'),
                        TextEntry::make('grand_total')->label('Toplam')->formatStateUsing($tl)->weight(FontWeight::Bold),
                        TextEntry::make('refunded_total')->label('İade edilen')
                            ->formatStateUsing(fn ($state) => (float) $state > 0 ? $tl($state) : '—')
                            ->color(fn ($state) => (float) $state > 0 ? 'danger' : null),
                    ]),

                Section::make('Notlar')
                    ->schema([
                        TextEntry::make('customer_note')->label('Müşteri notu')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('admin_note')->label('Mağaza notu (yalnız panelde)')->placeholder('—')
                            ->formatStateUsing(fn (?string $state) => nl2br(e((string) $state)))->html(),
                    ]),
            ]),

            // --- Sağ: müşteri, ödeme, kargo, fatura ---
            Grid::make(1)->columnSpan(['default' => 3, 'lg' => 1])->schema([
                Section::make('Müşteri')
                    ->schema([
                        TextEntry::make('customer_name')->label('Ad soyad')->weight(FontWeight::SemiBold),
                        TextEntry::make('customer_phone')->label('Telefon')
                            ->url(fn (Order $record) => 'tel:'.preg_replace('/[^0-9+]/', '', $record->customer_phone))
                            ->copyable(),
                        TextEntry::make('customer_email')->label('E-posta')
                            ->url(fn (Order $record) => 'mailto:'.$record->customer_email)
                            ->copyable(),
                        TextEntry::make('user.name')->label('Üyelik')->placeholder('Misafir alışverişi'),
                        TextEntry::make('teslimat')->label('Teslimat adresi')
                            ->state(fn (Order $record) => Order::adresMetni($record->shipping_address))
                            ->formatStateUsing(fn ($state) => nl2br(e($state)))->html(),
                    ]),

                Section::make('Ödeme')
                    ->schema([
                        TextEntry::make('paid_at')->label('Ödeme zamanı')->dateTime('d.m.Y H:i')->placeholder('—'),
                        TextEntry::make('payment_ref')->label('Ödeme referansı')->placeholder('—')->copyable(),
                        TextEntry::make('stock_state')->label('Stok')
                            ->formatStateUsing(fn (string $state) => ['none' => 'Ayrılmadı', 'reserved' => 'Ayrıldı (rezerve)', 'committed' => 'Düşüldü'][$state] ?? $state),
                    ]),

                Section::make('Teslimat')
                    ->schema([
                        TextEntry::make('shipping_carrier')->label('Kargo / kurye')->placeholder('—'),
                        TextEntry::make('tracking_number')->label('Takip no')->placeholder('—')->copyable(),
                        TextEntry::make('shipped_at')->label('Gönderildi')->dateTime('d.m.Y H:i')->placeholder('—'),
                        TextEntry::make('delivered_at')->label('Teslim')->dateTime('d.m.Y H:i')->placeholder('—'),
                    ]),

                Section::make('Fatura')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('invoice_number')->label('Fatura no')->placeholder('Kesilmedi'),
                        TextEntry::make('fatura_tipi')->label('Tür')
                            ->state(fn (Order $record) => ($record->billing_address['invoice_type'] ?? 'individual') === 'corporate' ? 'Kurumsal' : 'Bireysel'),
                        TextEntry::make('fatura_kimlik')->label('Kimlik / vergi no')
                            ->state(fn (Order $record) => trim(($record->billing_address['tax_office'] ?? '').' '.($record->billing_address['tax_number'] ?? '')))
                            ->placeholder('—'),
                        TextEntry::make('fatura_adres')->label('Fatura adresi')
                            ->state(fn (Order $record) => Order::adresMetni($record->invoice_address))
                            ->formatStateUsing(fn ($state) => nl2br(e($state)))->html(),
                        TextEntry::make('contract_version')->label('Onaylanan sözleşme')
                            ->formatStateUsing(fn ($state, Order $record) => $state.($record->contract_accepted_at ? ' · '.$record->contract_accepted_at->format('d.m.Y H:i') : ''))
                            ->placeholder('—'),
                    ]),
            ]),
        ]);
    }
}
