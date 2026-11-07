<?php

namespace App\Filament\Resources\Orders;

use App\Models\Order;
use App\Services\CevrimdisiOdeme;
use App\Services\Faturalar;
use App\Services\OrderShipping;
use App\Services\OrderStock;
use App\Services\PaymentRefunds;
use App\Support\OdemeYontemleri;
use App\Support\Yetki;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Sipariş işlemleri — listede (satır) ve sipariş detay sayfasında (üst
 * düğmeler) AYNI tanım kullanılır; kural iki yerde ayrı ayrı bozulmasın.
 */
class SiparisIslemleri
{
    /** Havale ödemesi hesaba geçti. */
    public static function havaleOnay(): Action
    {
        return Action::make('havaleOnay')
            ->label('Ödeme alındı')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->authorize(fn () => Yetki::yonetici())
            ->visible(fn (Order $kayit) => $kayit->payment_provider === 'havale'
                && $kayit->payment_status === 'pending')
            ->requiresConfirmation()
            ->modalHeading(fn (Order $kayit) => 'Havale onayı — '.$kayit->number)
            ->modalDescription(fn (Order $kayit) => number_format((float) $kayit->grand_total, 2, ',', '.')
                .' TL hesaba geçti mi? Onaylayınca stok düşer ve sipariş hazırlanmaya alınır.'
                .($kayit->status === 'cancelled' ? ' Bu sipariş süresi dolduğu için iptal edilmişti; stok yeniden ayrılmaya çalışılacak.' : ''))
            ->action(function (Order $record) {
                app(CevrimdisiOdeme::class)->havaleOnayla($record, auth()->user()?->email);

                $record->refresh();

                Notification::make()
                    ->title($record->status === 'cancelled'
                        ? 'Ödeme kaydedildi ama stok yetmedi — sipariş notuna bakın'
                        : 'Ödeme onaylandı, sipariş hazırlanıyor')
                    ->color($record->status === 'cancelled' ? 'warning' : 'success')
                    ->send();
            });
    }

    /** Kargoya / kuryeye ver. */
    public static function kargola(): Action
    {
        return Action::make('kargola')
            ->label('Kargoya ver')
            ->icon('heroicon-o-truck')
            ->color('info')
            // Kapıda ödeme ödenmeden kargolanır (ödeme teslimatta alınır)
            ->visible(fn (Order $kayit) => ($kayit->payment_status === 'paid'
                    || ($kayit->payment_provider === 'kapida' && $kayit->payment_status === 'pending'))
                && in_array($kayit->status, ['paid', 'preparing'], true))
            ->schema([
                TextInput::make('kargo_firmasi')
                    ->label('Kargo firması')
                    ->default(fn () => config('shop.kargo.firma'))
                    ->required(),
                TextInput::make('takip_no')
                    ->label('Takip numarası')
                    ->required(),
            ])
            ->action(function (Order $record, array $data) {
                try {
                    app(OrderShipping::class)->markShipped(
                        $record,
                        $data['kargo_firmasi'],
                        $data['takip_no'],
                    );
                } catch (RuntimeException $e) {
                    Notification::make()->title($e->getMessage())->danger()->send();

                    return;
                }

                Notification::make()->title('Sipariş kargoya verildi')->success()->send();
            });
    }

    /** Teslim edildi (kapıda ödemede tahsilat da). */
    public static function teslimEdildi(): Action
    {
        return Action::make('teslimEdildi')
            ->label('Teslim edildi')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->requiresConfirmation()
            ->modalDescription(fn (Order $kayit) => 'Cayma hakkı süresi BU tarihten başlar ('
                .config('shop.cayma_hakki_gun').' gün), '
                .'bu yüzden gerçekten teslim edildiğinde işaretleyin.'
                .($kayit->payment_provider === 'kapida' && $kayit->payment_status === 'pending'
                    ? ' KAPIDA ÖDEME: '.number_format((float) $kayit->grand_total, 2, ',', '.').' TL tahsil edilmiş sayılacak.'
                    : ''))
            ->visible(fn (Order $kayit) => $kayit->status === 'shipped')
            ->action(function (Order $record) {
                try {
                    app(OrderShipping::class)->markDelivered($record);
                } catch (RuntimeException $e) {
                    Notification::make()->title($e->getMessage())->danger()->send();

                    return;
                }

                Notification::make()->title('Teslim edildi olarak işaretlendi')->success()->send();
            });
    }

    /** Fatura numarası + PDF. */
    public static function fatura(): Action
    {
        return Action::make('fatura')
            ->label(fn (Order $kayit) => $kayit->invoice_number ? 'Fatura' : 'Fatura gir')
            ->icon('heroicon-o-document-text')
            ->color(fn (Order $kayit) => $kayit->invoice_number ? 'gray' : 'warning')
            ->visible(fn (Order $kayit) => $kayit->payment_status === 'paid')
            ->modalHeading(fn (Order $kayit) => 'Fatura — '.$kayit->number)
            ->fillForm(fn (Order $kayit) => [
                'numara' => $kayit->invoice_number,
                'tarih' => $kayit->invoice_date?->toDateString() ?? now()->toDateString(),
                'gonder' => ! $kayit->invoice_sent_at,
            ])
            ->schema(fn (Order $kayit) => [
                TextInput::make('numara')
                    ->label('Fatura numarası')
                    ->required()
                    ->maxLength(40)
                    ->unique('orders', 'invoice_number', ignorable: $kayit),
                DatePicker::make('tarih')
                    ->label('Fatura tarihi')
                    ->required()
                    ->native(false)
                    ->displayFormat('d.m.Y'),
                FileUpload::make('pdf')
                    ->label($kayit->invoice_pdf ? 'Yeni PDF (mevcut dosyanın yerine geçer)' : 'Fatura PDF')
                    ->disk('local')
                    ->directory('faturalar')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(5120)
                    ->required(fn () => ! $kayit->invoice_pdf),
                Toggle::make('gonder')
                    ->label('Müşteriye e-postayla gönder (PDF ekli)')
                    ->helperText($kayit->invoice_sent_at
                        ? 'Daha önce '.$kayit->invoice_sent_at->format('d.m.Y H:i').' tarihinde gönderildi.'
                        : null),
            ])
            ->action(function (Order $record, array $data) {
                $gitti = app(Faturalar::class)->kaydet($record, $data['numara'], $data['tarih'], $data['pdf'] ?? null, (bool) $data['gonder']);

                Notification::make()
                    ->title('Fatura kaydedildi')
                    ->body($data['gonder'] ? ($gitti ? 'Müşteriye e-postayla gönderildi.' : 'E-posta gönderilemedi — günlüğe bakın.') : null)
                    ->color($data['gonder'] && ! $gitti ? 'warning' : 'success')
                    ->send();
            });
    }

    /** Fatura PDF indir. */
    public static function faturaIndir(): Action
    {
        return Action::make('faturaIndir')
            ->label('Fatura PDF')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->visible(fn (Order $kayit) => (bool) $kayit->invoice_pdf)
            ->action(fn (Order $record) => Storage::disk('local')->download(
                $record->invoice_pdf,
                'Fatura-'.$record->invoice_number.'.pdf',
            ));
    }

    /** İptal (+ kartla ödendiyse PayTR iadesi). */
    public static function iptal(): Action
    {
        return Action::make('iptal')->authorize(fn () => Yetki::yonetici())
            ->label('İptal et')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription(fn (Order $kayit) => $kayit->payment_status === 'paid'
                ? 'Rezerve ya da düşülmüş stok geri verilir. ÖDEME ALINMIŞ: parayı iade etmeyi unutmayın.'
                : 'Rezerve ya da düşülmüş stok varsa geri verilir.')
            /*
             * Ödenmiş siparişi iptal etmek önceden parayı geri VERMİYORDU;
             * stok dönüyor, para mağazada kalıyordu. Artık iptal ekranında
             * PayTR iadesi seçilebiliyor (varsayılan açık).
             */
            // Karta iade yalnız kartla ödenmiş siparişte; havale/kapıda iadesi elden yapılır
            ->schema(fn (Order $kayit) => $kayit->payment_status === 'paid'
                && in_array($kayit->payment_provider, [null, 'paytr'], true)
                && app(PaymentRefunds::class)->kullanilabilir()
                ? [
                    Toggle::make('para_iade')
                        ->label(number_format((float) $kayit->grand_total, 2, ',', '.').' TL PayTR ile karta iade edilsin')
                        ->default(true),
                ]
                : [])
            ->visible(fn (Order $kayit) => ! in_array($kayit->status, ['cancelled', 'delivered'], true))
            ->action(function (Order $record, array $data) {
                /*
                 * Önce para: iade başarısız olursa iptal DE yapılmaz —
                 * "iptal edildi ama para mağazada" yarım durumu olmasın.
                 */
                if (! empty($data['para_iade'])) {
                    try {
                        app(PaymentRefunds::class)->refundOrder($record);
                    } catch (RuntimeException $e) {
                        Notification::make()
                            ->title('İptal yapılmadı')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }
                }

                $stock = app(OrderStock::class);

                /*
                 * Sipariş hangi aşamadaysa o geçiş çalışır; ötekiler
                 * durum uyuşmadığı için kendiliğinden atlanır.
                 */
                $stock->restore($record->fresh('items'));
                $stock->release($record->fresh('items'));

                $record->update(['status' => 'cancelled']);

                Notification::make()
                    ->title('Sipariş iptal edildi')
                    ->body(! empty($data['para_iade']) ? 'Tutar PayTR ile iade edildi.' : null)
                    ->success()
                    ->send();
            });
    }

    /** Yazdırılabilir sipariş fişi (paketleme / teslimat). */
    public static function yazdir(): Action
    {
        return Action::make('yazdir')
            ->label('Fiş yazdır')
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->url(fn (Order $kayit) => route('admin.siparis.fis', $kayit), shouldOpenInNewTab: true);
    }

    /** Satırda doğrudan görünenler: sıradaki adım. */
    public static function birincil(): array
    {
        return [static::havaleOnay(), static::kargola(), static::teslimEdildi()];
    }

    /** "Diğer" menüsündekiler. */
    public static function ikincil(): array
    {
        return [static::fatura(), static::faturaIndir(), static::yazdir(), static::iptal()];
    }
}
