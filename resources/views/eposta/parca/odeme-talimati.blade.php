{{-- Havale / kapıda ödeme talimatı — "Siparişiniz alındı" e-postasında --}}
@php($yontem = $order->payment_provider)
@if ($order->payment_status === 'pending' && \App\Support\OdemeYontemleri::cevrimdisi($yontem))
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0 0;">
        <tr>
            <td style="padding:16px 18px; background:#f4ede6; border-left:3px solid #d21c22; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:1.7; color:#3a3a3a;">
                @if ($yontem === 'havale')
                    <strong style="color:#1b1919;">Havale / EFT ile ödeme</strong><br>
                    Lütfen <strong>{{ number_format((float) $order->grand_total, 2, ',', '.') }} TL</strong> tutarı aşağıdaki hesaba gönderin,
                    açıklamaya sipariş numaranızı (<strong>{{ $order->number }}</strong>) yazın.<br><br>
                    @foreach (\App\Support\OdemeYontemleri::havaleHesaplari() as $h)
                        <strong>{{ $h['banka'] }}</strong>@if ($h['sahip']) — {{ $h['sahip'] }}@endif<br>
                        <span style="font-family:Consolas,monospace;">{{ $h['iban'] }}</span><br><br>
                    @endforeach
                    Ödeme {{ \App\Support\OdemeYontemleri::havaleSureGun() }} gün içinde yapılmazsa sipariş iptal edilir.
                @else
                    <strong style="color:#1b1919;">Kapıda ödeme</strong><br>
                    Siparişinizi teslim alırken <strong>{{ number_format((float) $order->grand_total, 2, ',', '.') }} TL</strong> ödeyeceksiniz.
                    {{ config('shop.odeme.kapida_aciklama') }}
                @endif
            </td>
        </tr>
    </table>
@endif
