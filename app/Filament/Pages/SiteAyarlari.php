<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\YalnizYonetici;
use App\Models\Setting;
use App\Services\LegalPlaceholders;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Mağaza ayarları.
 *
 * Önceden kargo ücreti, ücretsiz kargo eşiği ve satıcı bilgileri yalnızca
 * .env'deydi; mağaza bir kampanya için eşiği düşürmek istese sunucuya
 * dosya düzenlemeye girmek zorundaydı.
 *
 * Buradaki değerler .env'yi EZER (bkz. App\Models\Setting). Boş
 * bırakılan alan .env değerine döner.
 */
class SiteAyarlari extends Page
{
    use YalnizYonetici;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Site Ayarları';

    protected static ?string $title = 'Site Ayarları';

    protected static string|\UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'site-ayarlari';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::etkinDegerler());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Yapım aşaması')
                    ->description('Açıkken ziyaretçiler sitenin yerine "yapım aşamasında" sayfasını görür. Siz yönetici olarak siteyi normal görmeye devam edersiniz. Ödeme bildirimleri etkilenmez.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('bakim_modu')
                            ->label('"Yapım aşamasında" sayfasını göster')
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('yapim_pin')
                            ->label('Önizleme PIN kodu')
                            ->password()
                            ->revealable()
                            ->regex('/^\d{4,12}$/')
                            ->validationMessages(['regex' => '4–12 haneli, yalnız rakam olmalı.'])
                            ->helperText('İsteğe bağlı. Girerseniz sayfada "Önizleme kodu" çıkar; bu PIN\'i giren cihaz siteyi normal görür (müşteriye, ekibe göstermek için). Değiştirince eski izinler düşer.'),
                        TextInput::make('yapim_baslik')
                            ->label('Başlık')
                            ->placeholder('Sitemiz yapım aşamasında')
                            ->maxLength(120),
                        Textarea::make('yapim_metin')
                            ->label('Metin')
                            ->rows(2)
                            ->maxLength(400)
                            ->placeholder('Yeni online mağazamızı hazırlıyoruz, çok yakında yayındayız…')
                            ->helperText('Boş bırakılırsa varsayılan metin gösterilir. Telefon, WhatsApp, adres ve Instagram aşağıdaki satıcı bilgilerinden otomatik eklenir.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Fiyatları gizle')
                    ->description('Açıkken vitrinde fiyat, indirim ve sepet görünmez; ürünlerde "Fiyat sor" (WhatsApp) düğmesi çıkar. Sepet ve ödeme kapanır, ürün XML beslemesi durur. Siz yönetici olarak fiyatları görmeye devam edersiniz.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('fiyat_gizle')
                            ->label('Fiyatları gizle')
                            ->live()
                            ->columnSpanFull(),
                        Toggle::make('fiyat_uyelere_goster')
                            ->label('Üye girişi yapanlara göster')
                            ->helperText('Açıksa giriş yapmış müşteriler fiyatları görür ve alışveriş yapabilir.'),
                        TextInput::make('fiyat_metin')
                            ->label('Fiyat yerine yazılacak metin')
                            ->placeholder('Fiyat bilgisi için bize ulaşın')
                            ->maxLength(80),
                    ]),

                Section::make('Kargo')
                    ->description('Sepette ve kasada anında geçerli olur. Verilmiş siparişlerin kargo bedeli değişmez.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('kargo_ucret')
                            ->label('Kargo ücreti (TL)')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('kargo_ucretsiz_esigi')
                            ->label('Ücretsiz kargo eşiği (TL)')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText('İndirim SONRASI tutara bakılır.'),

                        TextInput::make('kargo_firma')
                            ->label('Varsayılan kargo firması')
                            ->placeholder('Yurtiçi Kargo'),
                    ]),

                Section::make('Ödeme yöntemleri')
                    ->description('Kasada sunulan yöntemler. Kapatılan yöntem yeni siparişlerde görünmez; verilmiş siparişler etkilenmez.')
                    ->columns(3)
                    ->schema([
                        Toggle::make('odeme_kart')
                            ->label('Kredi / banka kartı (PayTR)')
                            ->helperText('PayTR anahtarları .env dosyasında tanımlı olmalı.'),
                        Toggle::make('odeme_havale')
                            ->label('Havale / EFT')
                            ->helperText('Aşağıya en az bir hesap yazılmadıkça kasada görünmez.'),
                        Toggle::make('odeme_kapida')
                            ->label('Kapıda ödeme'),

                        Textarea::make('havale_hesaplari')
                            ->label('Havale hesapları')
                            ->rows(3)
                            ->placeholder('Banka adı | Hesap sahibi | TR00 0000 0000 0000 0000 0000 00')
                            ->helperText('Her satıra bir hesap: Banka | Hesap sahibi | IBAN. Sipariş sayfasında ve e-postada gösterilir.')
                            ->columnSpan(2),
                        TextInput::make('havale_sure_gun')
                            ->label('Havale ödeme süresi (gün)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(14)
                            ->required()
                            ->helperText('Bu sürede ödemesi onaylanmayan havale siparişi iptal edilir, ayrılan stok serbest kalır.'),

                        TextInput::make('kapida_ek_ucret')
                            ->label('Kapıda ödeme bedeli (TL)')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText('0 ise ek ücret alınmaz.'),
                        TextInput::make('kapida_aciklama')
                            ->label('Kapıda ödeme açıklaması')
                            ->maxLength(200)
                            ->columnSpan(2),
                    ]),

                Section::make('Bildirim ve stok')
                    ->columns(2)
                    ->schema([
                        TextInput::make('siparis_bildirim_epostasi')
                            ->label('Yeni sipariş bildirimi')
                            ->email()
                            ->helperText('Her ödenen siparişin kopyası ve düşük stok uyarıları bu adrese gider. Boşsa gönderilmez.'),

                        TextInput::make('dusuk_stok_esigi')
                            ->label('Düşük stok eşiği')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText('Panelde bu adet ve altı sarı gösterilir. Raftaki stok bu eşiğe inince ya da bitince soldaki adrese uyarı gider.'),
                    ]),

                Section::make('Satıcı bilgileri')
                    ->description('Yasal metinlerde, faturada ve e-postalarda görünür. Tüketici mevzuatı gereği eksiksiz olmalı.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('satici_unvan')->label('Ticari unvan')->columnSpanFull(),
                        TextInput::make('satici_adres')->label('Adres')->columnSpanFull(),
                        TextInput::make('satici_telefon')->label('Telefon')->tel(),
                        TextInput::make('satici_eposta')->label('E-posta')->email(),
                        TextInput::make('satici_mersis')->label('Ticaret sicil no'),
                        TextInput::make('satici_vergi_dairesi')->label('Vergi dairesi'),
                        TextInput::make('satici_vergi_no')->label('Vergi no'),
                        TextInput::make('instagram')->label('Instagram kullanıcı adı')->prefix('@'),
                        TextInput::make('facebook')->label('Facebook sayfası')->prefix('facebook.com/')->placeholder('schaferKibris'),
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
                    Action::make('save')
                        ->label('Kaydet')
                        ->submit('save'),
                ]),
            ]);
    }

    public function save(): void
    {
        Setting::kaydet($this->form->getState());

        Notification::make()->title('Ayarlar kaydedildi')->success()->send();

        $bekleyen = app(LegalPlaceholders::class)->bekleyenSayisi();

        if ($bekleyen > 0) {
            Notification::make()
                ->title($bekleyen.' yasal metinde hâlâ "[GİRİLMEDİ]" yer tutucusu var')
                ->body('Üstteki "Yasal metinleri doldur" düğmesiyle yeni sürüm yayımlayabilirsiniz.')
                ->warning()
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('yasalDoldur')
                ->label('Yasal metinleri doldur')
                ->icon('heroicon-o-document-check')
                ->color('gray')
                ->visible(fn () => app(LegalPlaceholders::class)->bekleyenSayisi() > 0)
                ->requiresConfirmation()
                ->modalHeading('Yasal metinlerdeki yer tutucuları doldur')
                ->modalDescription(
                    'Yürürlükteki metinlerde "[... GİRİLMEDİ]" yazan yerler kayıtlı satıcı '
                    .'bilgileriyle doldurulur ve her metin için YENİ SÜRÜM yayımlanır. '
                    .'Metnin geri kalanına dokunulmaz; eski sürümler arşivde kalır.'
                )
                ->action(function () {
                    $sonuc = app(LegalPlaceholders::class)->doldur();

                    $bildirim = Notification::make()
                        ->title($sonuc['yayimlanan'].' metnin yeni sürümü yayımlandı');

                    if ($sonuc['eksik'] !== []) {
                        $bildirim->body('Hâlâ boş olanlar: '.implode(', ', $sonuc['eksik']))->warning();
                    } else {
                        $bildirim->success();
                    }

                    $bildirim->send();
                }),
        ];
    }
}
