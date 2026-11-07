<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Kategoriler ağaç sırasıyla: "MUTFAK › Pişirme › Çelik Tencere".
 * Vitrinde gösterme ve ANA SAYFA BÖLÜMÜ olma satırdan açılıp kapanır.
 */
class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        // Ağaç yolu → sıralama anahtarı ve görünen ad
        $yollar = ProductsTable::kategoriSecenekleri();
        $sira = array_flip(array_keys($yollar));

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('parent')
                ->withCount(['products as aktif_urun' => fn ($p) => $p->where('is_active', true)]))
            ->defaultSort(fn (Builder $query) => $query->orderByRaw(
                // Ağaç sırası: PHP'de hesaplanan yol sırası CASE ile uygulanır
                $sira === [] ? 'id' : 'CASE id '.collect($sira)->map(fn ($s, $id) => 'WHEN '.(int) $id.' THEN '.(int) $s)->implode(' ').' ELSE 99999 END'
            ))
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50)
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->label('')
                    ->square()
                    ->imageSize(40)
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('Kategori')
                    ->searchable()
                    ->formatStateUsing(function (string $state, Category $record) use ($yollar) {
                        $parcalar = explode(' › ', $yollar[$record->id] ?? $state);
                        $son = array_pop($parcalar);
                        $derinlik = count($parcalar);

                        return str_repeat('<span style="opacity:.35">— </span>', $derinlik)
                            .'<strong>'.e($son).'</strong>'
                            .($derinlik ? '<br><span style="font-size:12px;opacity:.6">'.e(implode(' › ', $parcalar)).'</span>' : '');
                    })
                    ->html(),

                TextColumn::make('aktif_urun')
                    ->label('Satıştaki ürün')
                    ->alignCenter()
                    ->sortable()
                    ->url(fn (Category $record) => ProductResource::getUrl('index', [
                        'tableFilters' => ['kategori' => ['value' => $record->id]],
                    ])),

                ToggleColumn::make('is_active')
                    ->label('Vitrinde'),

                ToggleColumn::make('is_featured')
                    ->label('Ana sayfada bölüm')
                    ->tooltip('Açıksa ana sayfada bu kategorinin ürünleri ayrı bir bölüm olarak çıkar'),

                TextInputColumn::make('position')
                    ->label('Sıra')
                    ->type('number')
                    ->width('80px')
                    ->rules(['required', 'integer', 'min:0']),
            ])
            ->filters([
                SelectFilter::make('ust')
                    ->label('Ana kategori')
                    ->options(fn () => Category::whereNull('parent_id')->orderBy('position')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['value'] ?? null) || ! ($kat = Category::find($data['value']))) {
                            return $query;
                        }

                        return $query->whereIn('id', $kat->altAgacIdleri());
                    }),

                TernaryFilter::make('is_active')
                    ->label('Vitrin durumu')
                    ->placeholder('Hepsi')
                    ->trueLabel('Gösteriliyor')
                    ->falseLabel('Gizli'),

                TernaryFilter::make('is_featured')
                    ->label('Ana sayfa bölümü')
                    ->placeholder('Hepsi'),
            ])
            ->recordActions([
                Action::make('vitrin')
                    ->label('')
                    ->tooltip('Vitrinde gör')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Category $record) => route('catalog.category', $record->slug), shouldOpenInNewTab: true),
                EditAction::make()->label('Düzenle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Sil'),
                ]),
            ])
            ->emptyStateHeading('Henüz kategori yok');
    }
}
