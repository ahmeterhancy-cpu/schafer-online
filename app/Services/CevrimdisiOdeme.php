<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Havale/EFT ve kapıda ödeme siparişlerinin yaşam döngüsü.
 *
 * HAVALE  pending (stok REZERVE) → panelde "Ödeme alındı" → paid (stok düşer)
 *         → süre dolarsa schafer:rezerv-temizle iptal eder, rezerv bırakılır.
 * KAPIDA  sipariş anında preparing (stok DÜŞER, ödeme pending) → kargola →
 *         teslim edildi → ödeme paid (teslimatta tahsil edildi).
 *
 * İki yolda da "siparişiniz alındı" e-postası SİPARİŞ ANINDA gider (kartta
 * ödeme onayında gidiyor); talimat (IBAN / kapıda bilgisi) e-postada yer alır.
 * payment_meta.onay_epostasi işareti markPaid'in ikinci kez göndermesini önler.
 */
class CevrimdisiOdeme
{
    public function __construct(
        private readonly OrderStock $stock,
        private readonly Notifier $notifier,
        private readonly OrderPayments $payments,
    ) {}

    public function baslat(Order $order): void
    {
        if ($order->payment_provider === 'kapida') {
            // Kapıda ödemede sipariş kesinleşmiştir: rezerv gerçek düşüme döner
            $this->stock->commit($order->fresh('items'));
            $order->forceFill(['status' => 'preparing'])->save();
        }

        $order->forceFill([
            'payment_meta' => array_merge($order->payment_meta ?? [], [
                'onay_epostasi' => now()->toIso8601String(),
            ]),
        ])->save();

        $this->notifier->orderPlaced($order->fresh('items'));
        $this->notifier->contractDocuments($order->fresh('items'));
    }

    /** Havale ödemesi panelden onaylandı. */
    public function havaleOnayla(Order $order, ?string $onaylayan = null): void
    {
        if ($order->payment_provider !== 'havale') {
            throw new RuntimeException('Bu sipariş havale siparişi değil.');
        }

        if ($order->status === 'cancelled') {
            /*
             * Süresi dolup iptal edilmiş siparişe para geldiyse markPaid stoğu
             * yeniden ayırmayı dener (stock_state none); yetmezse siparişi
             * elle incelemeye işaretler. Yine de iptal durumundan çıkarıyoruz.
             */
            $order->forceFill(['status' => 'pending'])->save();
        }

        $this->payments->markPaid($order->fresh(), [
            'yontem' => 'havale',
            'onaylayan' => $onaylayan,
            'onay_zamani' => now()->toIso8601String(),
        ]);
    }

    /** Kapıda ödeme teslimatta tahsil edildi. */
    public function kapidaTahsilEdildi(Order $order): void
    {
        if ($order->payment_provider !== 'kapida' || $order->payment_status === 'paid') {
            return;
        }

        $this->payments->markPaid($order->fresh(), [
            'yontem' => 'kapida',
            'tahsil_zamani' => now()->toIso8601String(),
        ]);
    }

    /**
     * Süresi dolan havale siparişlerini iptal eder.
     *
     * @return int iptal edilen sayısı
     */
    public function suresiDolanlariIptalEt(int $gun): int
    {
        $siparisler = Order::query()
            ->where('payment_provider', 'havale')
            ->where('payment_status', 'pending')
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subDays($gun))
            ->with('items')
            ->get();

        foreach ($siparisler as $order) {
            $this->stock->release($order);

            $order->forceFill([
                'status' => 'cancelled',
                'admin_note' => trim(($order->admin_note ?? '')
                    ."\nHavale {$gun} gün içinde onaylanmadı; sipariş otomatik iptal edildi, stok serbest bırakıldı."),
            ])->save();

            Log::info('Havale süresi doldu, sipariş iptal', ['siparis' => $order->number]);
        }

        return $siparisler->count();
    }
}
