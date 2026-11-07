<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Category;
use App\Models\Product;
use App\Services\TopluUrunIslemleri;
use App\Support\Yetki;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

/**
 * Ürün listesi — 1.500'e yakın ürünlük katalog için hızlı yönetim:
 * kategori ağacıyla süzme, SKU araması, tek tip üründe satır içi fiyat/stok,
 * toplu fiyat/indirim/stok/kategori işlemleri.
 */
class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['variants', 'options', 'categories:id,name']))
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Ürün adı ya da SKU')
            ->striped()
            ->paginationPageOptions([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->columns([
                ImageColumn::make('hero_image')
                    ->disk('public')
                    ->label('')
                    ->square()
                    ->imageSize(52)
                    ->defaultImageUrl(asset('img/schafer/favicon.png')),

                TextColumn::make('name')
                    ->label('Ürün')
                    ->searchable(query: fn (Builder $query, string $search) => $query->where(fn ($w) => $w
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('base_sku', 'like', "%{$search}%")
                        ->orWhereHas('variants', fn ($v) => $v->where('sku', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%"))))
                    ->sortable()
                    ->wrap()
                    ->lineClamp(2)
                    ->extraAttributes(['style' => 'min-width: 280px'])
                    ->description(fn (Product $kayit) => $kayit->tek_varyant?->sku ?? $kayit->base_sku),

                TextColumn::make('categories.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->toggleable(),

                /*
                 * Tek tip üründe fiyat ve stok doğrudan satırda düzenlenir
                 * (varyant gözlemcisi önbelleği ve "stoğa girdi" postasını
                 * halleder). Seçenekli üründe kombinasyon tablosundan.
                 */
                TextInputColumn::make('satir_fiyat')
                    ->label('Fiyat (TL)')
                    ->type('number')
                    ->step('0.01')
                    ->width('110px')
                    ->getStateUsing(fn (Product $kayit) => $kayit->tek_varyant ? (float) $kayit->tek_varyant->price : null)
                    ->placeholder(fn (Product $kayit) => $kayit->tek_varyant ? null : 'Kombinasyonlar')
                    ->disabled(fn (Product $kayit) => ! Yetki::yonetici() || ! $kayit->tek_varyant)
                    ->rules(['required', 'numeric', 'min:0'])
                    ->updateStateUsing(function (Product $record, $state) {
                        $v = $record->tek_varyant;
                        if (! $v) {
                            return null;
                        }
                        $fiyat = round((float) $state, 2);
                        $v->update([
                            'price' => $fiyat,
                            // Eski fiyat yeni fiyatın altına düştüyse indirim kalkar
                            'compare_at_price' => $v->compare_at_price !== null && (float) $v->compare_at_price > $fiyat ? $v->compare_at_price : null,
                        ]);

                        return $fiyat;
                    })
                    ->sortable(['min_price']),

                TextColumn::make('kart_eski')
                    ->label('Eski fiyat')
                    ->getStateUsing(fn (Product $kayit) => $kayit->variants->where('is_active', true)->max('compare_at_price'))
                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 2, ',', '.').' TL' : null)
                    ->color('danger')
                    ->placeholder('—')
                    ->toggleable(),

                TextInputColumn::make('satir_stok')
                    ->label('Stok')
                    ->type('number')
                    ->width('80px')
                    ->getStateUsing(fn (Product $kayit) => $kayit->tek_varyant ? (int) $kayit->tek_varyant->stock : (int) $kayit->total_stock)
                    ->disabled(fn (Product $kayit) => ! $kayit->tek_varyant)
                    ->rules(fn (Product $record) => ['required', 'integer', 'min:'.(int) ($record->tek_varyant?->reserved ?? 0)])
                    ->updateStateUsing(function (Product $record, $state) {
                        $record->tek_varyant?->update(['stock' => max(0, (int) $state)]);

                        return (int) $state;
                    })
                    ->sortable(['total_stock']),

                TextColumn::make('total_stock')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match (true) {
                        (int) $state < 1 => 'Tükendi',
                        (int) $state <= (int) config('shop.dusuk_stok_esigi') => 'Az',
                        default => 'Var',
                    })
                    ->color(fn ($state) => match (true) {
                        (int) $state < 1 => 'danger',
                        (int) $state <= (int) config('shop.dusuk_stok_esigi') => 'warning',
                        default => 'success',
                    })
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Satışta')
                    ->disabled(fn () => ! Yetki::yonetici()),

                ToggleColumn::make('is_featured')
                    ->label('Öne çıkan')
                    ->toggleable(),

                TextColumn::make('collection.name')
                    ->label('Koleksiyon')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Eklendi')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->label('Kategori (alt kategorileriyle)')
                    ->options(fn () => static::kategoriSecenekleri())
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['value'] ?? null) || ! ($kat = Category::find($data['value']))) {
                            return $query;
                        }

                        return $query->whereHas('categories', fn ($c) => $c->whereIn('categories.id', $kat->altAgacIdleri()));
                    }),

                TernaryFilter::make('is_active')
                    ->label('Satış durumu')
                    ->placeholder('Hepsi')
                    ->trueLabel('Satışta')
                    ->falseLabel('Satışta değil (taslak)'),

                SelectFilter::make('stok')
                    ->label('Stok')
                    ->options(['bitti' => 'Tükendi', 'az' => 'Az kaldı', 'var' => 'Stokta'])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'bitti' => $query->where('total_stock', '<', 1),
                        'az' => $query->whereBetween('total_stock', [1, max(1, (int) config('shop.dusuk_stok_esigi'))]),
                        'var' => $query->where('total_stock', '>', 0),
                        default => $query,
                    }),

                Filter::make('indirimli')
                    ->label('İndirimdekiler')
                    ->query(fn (Builder $query) => $query->whereHas('variants', fn ($v) => $v->where('is_active', true)
                        ->whereNotNull('compare_at_price')->whereColumn('compare_at_price', '>', 'price'))),

                TernaryFilter::make('is_featured')
                    ->label('Öne çıkan')
                    ->placeholder('Hepsi'),

                Filter::make('gorselsiz')
                    ->label('Görseli olmayanlar')
                    ->query(fn (Builder $query) => $query->whereNull('hero_image')),

                Filter::make('kategorisiz')
                    ->label('Kategorisi olmayanlar')
                    ->query(fn (Builder $query) => $query->doesntHave('categories')),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                Action::make('vitrin')
                    ->label('')
                    ->tooltip('Vitrinde gör')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Product $kayit) => url('/urun/'.$kayit->slug), shouldOpenInNewTab: true),
                EditAction::make()->label('Düzenle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('satisaAl')
                        ->label('Satışa al')
                        ->icon('heroicon-o-eye')
                        ->authorize(fn () => Yetki::yonetici())
                        ->action(fn (Collection $records) => static::bildir(
                            Product::whereKey($records->modelKeys())->update(['is_active' => true]).' ürün satışa alındı'
                        ))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('satistanKaldir')
                        ->label('Satıştan kaldır (taslak)')
                        ->icon('heroicon-o-eye-slash')
                        ->authorize(fn () => Yetki::yonetici())
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => static::bildir(
                            Product::whereKey($records->modelKeys())->update(['is_active' => false]).' ürün satıştan kaldırıldı'
                        ))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('oneCikar')
                        ->label('Öne çıkar / çıkarma')
                        ->icon('heroicon-o-star')
                        ->schema([
                            Radio::make('deger')->label('')->options(['1' => 'Öne çıkar', '0' => 'Öne çıkanlardan kaldır'])->default('1')->required(),
                        ])
                        ->action(fn (Collection $records, array $data) => static::bildir(
                            Product::whereKey($records->modelKeys())->update(['is_featured' => $data['deger'] === '1']).' ürün güncellendi'
                        ))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('kategoriEkle')
                        ->label('Kategoriye ekle / çıkar')
                        ->icon('heroicon-o-tag')
                        ->schema([
                            Radio::make('islem')->label('İşlem')->options([
                                'ekle' => 'Seçili kategorilere EKLE (mevcutlar kalır)',
                                'cikar' => 'Seçili kategorilerden ÇIKAR',
                                'degistir' => 'Kategorileri bunlarla DEĞİŞTİR',
                            ])->default('ekle')->required(),
                            Select::make('kategoriler')->label('Kategoriler')
                                ->options(fn () => static::kategoriSecenekleri())
                                ->multiple()->searchable()->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $urun) {
                                match ($data['islem']) {
                                    'cikar' => $urun->categories()->detach($data['kategoriler']),
                                    'degistir' => $urun->categories()->sync($data['kategoriler']),
                                    default => $urun->categories()->syncWithoutDetaching($data['kategoriler']),
                                };
                            }
                            static::bildir($records->count().' ürünün kategorileri güncellendi');
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('fiyatDegistir')
                        ->label('Fiyatı değiştir (% / TL)')
                        ->icon('heroicon-o-currency-dollar')
                        ->authorize(fn () => Yetki::yonetici())
                        ->modalDescription('Seçili ürünlerin satıştaki tüm varyantlarına uygulanır. Zam için pozitif, indirim için negatif yazın.')
                        ->schema([
                            Radio::make('tur')->label('Değişim')->options(['yuzde' => 'Yüzde (%)', 'tutar' => 'Tutar (TL)'])->default('yuzde')->inline()->required(),
                            TextInput::make('deger')->label('Değer')->numeric()->required()->placeholder('ör. 10 ya da -5'),
                            Select::make('yuvarlama')->label('Yuvarlama')->options(TopluUrunIslemleri::YUVARLAMA)->default('tam')->required(),
                        ])
                        ->action(fn (Collection $records, array $data) => static::calistir(
                            fn () => app(TopluUrunIslemleri::class)->fiyatDegistir($records, $data['tur'], (float) $data['deger'], $data['yuvarlama']),
                            ':n varyantın fiyatı güncellendi'
                        ))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('indirimUygula')
                        ->label('Kampanya indirimi uygula')
                        ->icon('heroicon-o-receipt-percent')
                        ->authorize(fn () => Yetki::yonetici())
                        ->modalDescription('Mevcut fiyat üstü çizili "eski fiyat" olur; vitrinde indirim rozeti çıkar. Zaten indirimdeki üründe oran eski fiyattan hesaplanır.')
                        ->schema([
                            TextInput::make('yuzde')->label('İndirim (%)')->numeric()->minValue(1)->maxValue(90)->required(),
                            Select::make('yuvarlama')->label('Yuvarlama')->options(TopluUrunIslemleri::YUVARLAMA)->default('tam')->required(),
                        ])
                        ->action(fn (Collection $records, array $data) => static::calistir(
                            fn () => app(TopluUrunIslemleri::class)->indirimUygula($records, (float) $data['yuzde'], $data['yuvarlama']),
                            ':n varyanta indirim uygulandı'
                        ))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('indirimKaldir')
                        ->label('İndirimi kaldır')
                        ->icon('heroicon-o-x-mark')
                        ->authorize(fn () => Yetki::yonetici())
                        ->requiresConfirmation()
                        ->modalDescription('Fiyat, üstü çizili eski fiyata geri döner.')
                        ->action(fn (Collection $records) => static::calistir(
                            fn () => app(TopluUrunIslemleri::class)->indirimKaldir($records),
                            ':n varyantın indirimi kaldırıldı'
                        ))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('stokAyarla')
                        ->label('Stoğu ayarla')
                        ->icon('heroicon-o-archive-box')
                        ->modalDescription('Seçili ürünlerin stoğu bu adede ayarlanır (üstüne eklenmez). Bekleyen siparişlerin ayırdığı adedin altına inilmez.')
                        ->schema([
                            TextInput::make('adet')->label('Stok adedi')->numeric()->integer()->minValue(0)->required()->default(10),
                        ])
                        ->action(fn (Collection $records, array $data) => static::calistir(
                            fn () => app(TopluUrunIslemleri::class)->stokAyarla($records, (int) $data['adet']),
                            ':n varyantın stoğu ayarlandı'
                        ))
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()->authorize(fn () => Yetki::yonetici())->label('Sil'),
                ])->label('Toplu işlemler'),
            ])
            ->emptyStateHeading('Ürün bulunamadı')
            ->emptyStateDescription('Süzgeçleri temizleyin ya da yeni ürün ekleyin.');
    }

    /** Girintili kategori listesi: "MUTFAK › Pişirme › Çelik Tencere". */
    public static function kategoriSecenekleri(): array
    {
        $hepsi = Category::orderBy('position')->orderBy('name')->get(['id', 'name', 'parent_id']);
        $cocuklar = $hepsi->groupBy(fn ($k) => $k->parent_id ?? 0);
        $sonuc = [];

        $gez = function (int $ust, string $onek, int $derinlik) use (&$gez, $cocuklar, &$sonuc) {
            if ($derinlik > 5) {
                return;
            }
            foreach ($cocuklar->get($ust, []) as $k) {
                $sonuc[$k->id] = $onek.$k->name;
                $gez($k->id, $onek.$k->name.' › ', $derinlik + 1);
            }
        };
        $gez(0, '', 0);

        return $sonuc;
    }

    private static function bildir(string $mesaj): void
    {
        Notification::make()->title($mesaj)->success()->send();
    }

    private static function calistir(callable $is, string $mesaj): void
    {
        try {
            $n = $is();
        } catch (InvalidArgumentException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();

            return;
        }

        static::bildir(str_replace(':n', (string) $n, $mesaj));
    }
}
