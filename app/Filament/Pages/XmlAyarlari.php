<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\YalnizYonetici;
use App\Http\Controllers\XmlBeslemeController;
use App\Models\Setting;
use App\Services\Xml\XmlIceAktarim;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;

/**
 * XML: dışa ürün beslemesi + tedarikçi XML'inden fiyat/stok güncelleme.
 */
class XmlAyarlari extends Page
{
    use YalnizYonetici;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracketSquare;

    protected static ?string $navigationLabel = 'XML';

    protected static ?string $title = 'XML Beslemesi ve Aktarımı';

    protected static string|\UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'xml';

    public ?array $data = [];

    /** Son "Önizle" sonucu — kaydedilmez, yalnız ekranda gösterilir. */
    public ?array $onizleme = null;

    public function mount(): void
    {
        $this->form->fill(collect(Setting::etkinDegerler())
            ->filter(fn ($v, $k) => str_starts_with($k, 'xml_'))
            ->all());
    }

    public function form(Schema $schema): Schema
    {
        $alan = fn (string $ad, string $etiket, string $ornek) => TextInput::make('xml_ice_alan_'.$ad)
            ->label($etiket)
            ->placeholder('otomatik — örn. '.$ornek);

        return $schema
            ->statePath('data')
            ->components([
                Section::make('Dışa XML (ürün beslemesi)')
                    ->description('Google Merchant (Google Alışveriş), Meta/Instagram katalog, Akakçe/Cimri ve bayiler bu adresi okuyabilir. Yayındaki ürünler, fiyat, indirim, stok ve görsellerle birlikte; 30 dakikada bir tazelenir.')
                    ->schema([
                        Toggle::make('xml_disa_acik')->label('Ürün XML\'i yayında'),
                        View::make('filament.xml.besleme-adresi')
                            ->viewData(['adres' => route('xml.urunler')]),
                    ]),

                Section::make('İçeri XML (tedarikçiden fiyat ve stok)')
                    ->description('Tedarikçinin XML adresinden ürünler okunur, stok kodu ya da barkodla eşleşen ürünlerin fiyatı ve stoğu güncellenir. Kaydettikten sonra önce "Önizle" ile ne değişeceğini görün.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('xml_ice_url')
                            ->label('Tedarikçi XML adresi')
                            ->url()
                            ->placeholder('https://tedarikci.com/xml/urunler.xml')
                            ->columnSpanFull(),
                        Radio::make('xml_ice_eslesme')
                            ->label('Ürünler neyle eşleşsin?')
                            ->options(['sku' => 'Stok kodu (SKU)', 'barkod' => 'Barkod'])
                            ->default('sku')
                            ->required()
                            ->helperText('Birincisiyle bulunamazsa diğeri de denenir.'),
                        Toggle::make('xml_ice_otomatik')
                            ->label('Saatte bir otomatik çalıştır')
                            ->helperText('Sunucuda zamanlanmış görev (cron) kurulu olmalı.'),
                        Toggle::make('xml_ice_fiyat')
                            ->label('Fiyatları güncelle')
                            ->helperText('XML\'de eski fiyat yoksa mevcut indirim oranı korunur.'),
                        Toggle::make('xml_ice_stok')->label('Stokları güncelle'),
                        Toggle::make('xml_ice_yeni_urun')
                            ->label('Sitede olmayan ürünleri taslak olarak ekle')
                            ->helperText('Yayında olmadan eklenir; kategori ve açıklamayı panelden tamamlayıp yayına alırsınız.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Alan eşleştirmesi (isteğe bağlı)')
                    ->description('Boş bırakılan alanlar yaygın etiket adlarından tahmin edilir. Önizlemede yanlış alan seçildiyse buraya XML\'deki etiket adını yazın.')
                    ->collapsed()
                    ->columns(3)
                    ->schema([
                        $alan('urun', 'Ürün öğesi', 'item, product, urun'),
                        $alan('kod', 'Stok kodu', 'stok_kodu, sku'),
                        $alan('barkod', 'Barkod', 'barkod, gtin'),
                        $alan('ad', 'Ürün adı', 'urun_adi, title'),
                        $alan('fiyat', 'Satış fiyatı', 'satis_fiyati, price'),
                        $alan('eski_fiyat', 'Eski / liste fiyatı', 'liste_fiyati'),
                        $alan('stok', 'Stok adedi', 'stok, quantity'),
                        $alan('gorsel', 'Görsel adresi', 'resim, image'),
                    ]),

                Section::make('Sonuç')
                    ->schema([
                        View::make('filament.xml.rapor')
                            ->viewData(fn () => ['onizleme' => $this->onizleme, 'son' => XmlIceAktarim::sonRapor()]),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([$this->formIcerigi()]);
    }

    private function formIcerigi(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')->label('Kaydet')->submit('save'),
                ]),
            ]);
    }

    public function save(): void
    {
        Setting::kaydet($this->form->getState());
        Cache::forget(XmlBeslemeController::ONBELLEK);

        Notification::make()->title('XML ayarları kaydedildi')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('onizle')
                ->label('Önizle')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->action(function () {
                    $this->save();
                    $this->onizleme = app(XmlIceAktarim::class)->calistir(null, kuru: true);
                    $this->bildir($this->onizleme, 'Önizleme hazır — hiçbir şey değişmedi');
                }),

            Action::make('aktar')
                ->label('Şimdi aktar')
                ->icon('heroicon-o-arrow-down-tray')
                ->requiresConfirmation()
                ->modalDescription('XML\'deki fiyat ve stoklar eşleşen ürünlere yazılacak. Önce "Önizle" ile kontrol etmeniz önerilir.')
                ->action(function () {
                    $this->save();
                    $rapor = app(XmlIceAktarim::class)->calistir();
                    Cache::forget(XmlBeslemeController::ONBELLEK);
                    $this->onizleme = null;
                    $this->bildir($rapor, 'Aktarım tamamlandı');
                }),
        ];
    }

    private function bildir(array $rapor, string $baslik): void
    {
        if (isset($rapor['hata'])) {
            Notification::make()->title('XML okunamadı')->body($rapor['hata'])->danger()->persistent()->send();

            return;
        }

        $s = $rapor['sayac'];
        Notification::make()
            ->title($baslik)
            ->body("{$s['toplam']} satır · {$s['eslesen']} eşleşti · {$s['guncellenen']} güncellenecek/güncellendi · {$s['bulunamayan']} bulunamadı · {$s['yeni']} yeni")
            ->success()
            ->send();
    }
}
