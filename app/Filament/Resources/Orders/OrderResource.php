<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Schemas\OrderInfolist;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Siparişler';

    protected static ?string $modelLabel = 'sipariş';

    protected static ?string $pluralModelLabel = 'siparişler';

    protected static ?int $navigationSort = 1;

    protected static string|\UnitEnum|null $navigationGroup = 'Satış';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    public static function canCreate(): bool
    {
        return false; // siparişler yalnız vitrinden gelir
    }

    /** Üst çubuktaki genel arama: sipariş no, müşteri adı, telefon, e-posta */
    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'customer_name', 'customer_phone', 'customer_email'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->number.' — '.$record->customer_name;
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Tutar' => number_format((float) $record->grand_total, 2, ',', '.').' TL',
            'Durum' => $record->status_label,
        ];
    }

    /** Menü rozeti: işlem bekleyen siparişler (kargolanacak + havale bekleyen) */
    public static function getNavigationBadge(): ?string
    {
        $n = static::getModel()::query()
            ->where(fn ($q) => $q
                ->where(fn ($k) => $k->whereIn('status', ['paid', 'preparing'])
                    ->where(fn ($o) => $o->where('payment_status', 'paid')
                        ->orWhere(fn ($kp) => $kp->where('payment_provider', 'kapida')->where('payment_status', 'pending'))))
                ->orWhere(fn ($h) => $h->where('payment_provider', 'havale')->where('payment_status', 'pending')->where('status', 'pending')))
            ->count();

        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Kargolanacak ya da havale ödemesi bekleyen siparişler';
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
