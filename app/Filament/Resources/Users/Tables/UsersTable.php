<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->withSum(['orders as harcama' => fn ($o) => $o->where('payment_status', 'paid')->where('status', '!=', 'cancelled')], 'grand_total')
                ->withMax('orders as son_siparis', 'created_at'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => User::ROLLER[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'admin' => 'primary',
                        'staff' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('orders_count')
                    ->label('Sipariş')
                    ->counts('orders')
                    ->sortable()
                    ->alignCenter(),

                // Ödenmiş ve iptal edilmemiş siparişlerin toplamı
                TextColumn::make('harcama')
                    ->label('Toplam harcama')
                    ->state(fn (User $kayit) => $kayit->harcama)
                    ->formatStateUsing(fn ($state) => (float) $state > 0 ? number_format((float) $state, 2, ',', '.').' TL' : '—')
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('harcama', $direction))
                    ->alignEnd(),

                TextColumn::make('son_siparis')
                    ->label('Son sipariş')
                    ->state(fn (User $kayit) => $kayit->son_siparis)
                    ->dateTime('d.m.Y')
                    ->placeholder('—')
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('son_siparis', $direction)),

                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rol')
                    ->options(User::ROLLER),

                \Filament\Tables\Filters\Filter::make('alisveris_yapan')
                    ->label('Sipariş vermiş olanlar')
                    ->query(fn ($query) => $query->has('orders')),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
                DeleteAction::make()
                    ->label('Sil')
                    ->visible(fn (User $kayit) => UserResource::silinebilirMi($kayit))
                    ->modalDescription('Kullanıcının siparişleri silinmez; misafir sipariş olarak kalır.'),
            ])
            ->emptyStateHeading('Kullanıcı yok');
    }
}
