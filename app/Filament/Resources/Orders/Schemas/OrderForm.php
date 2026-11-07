<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Sipariş düzenleme — yalnız elle değişebilen alanlar.
 *
 * Tutarlar, kalemler ve ödeme bilgisi muhasebe ve yasal kayıttır; detay
 * sayfasında (OrderInfolist) salt okunur gösterilir. Durum geçişleri
 * SiparisIslemleri düğmeleriyle yapılır, çünkü onlar stok hareketini de
 * yürütüyor.
 */
class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Teslimat bilgisi')
                ->description('Kargoya verirken girilen bilgiler; yanlış girildiyse buradan düzeltin.')
                ->columns(2)
                ->schema([
                    TextInput::make('shipping_carrier')->label('Kargo firması / kurye')->maxLength(120),
                    TextInput::make('tracking_number')->label('Takip numarası')->maxLength(120),
                ]),

            Section::make('Mağaza notu')
                ->schema([
                    Textarea::make('admin_note')
                        ->label('Not')
                        ->rows(5)
                        ->helperText('Yalnızca panelde görünür; müşteri görmez.'),
                ]),
        ]);
    }
}
