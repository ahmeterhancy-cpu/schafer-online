<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\SiparisIslemleri;
use App\Models\Order;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return 'Sipariş '.$this->getRecord()->number;
    }

    protected function getHeaderActions(): array
    {
        // İşlem sonrası sayfadaki kayıt tazelensin (durum rozetleri)
        $tazele = fn ($action) => $action->after(fn (Order $record) => $record->refresh());

        return [
            ...array_map($tazele, SiparisIslemleri::birincil()),
            EditAction::make()->label('Not / takip no')->icon('heroicon-o-pencil-square')->color('gray'),
            ActionGroup::make(array_map($tazele, SiparisIslemleri::ikincil()))
                ->label('Diğer')
                ->button()
                ->color('gray'),
        ];
    }
}
