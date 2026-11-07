{{--
    Havale / kapıda ödeme talimatı — sipariş sayfasında, ödeme beklenirken.
    $order zorunlu.
--}}
@php($yontem = $order->payment_provider)
@if ($order->payment_status === 'pending' && $order->status !== 'cancelled' && \App\Support\OdemeYontemleri::cevrimdisi($yontem))
    <section class="odeme-talimat" aria-label="Ödeme bilgisi">
        @if ($yontem === 'havale')
            @php($hesaplar = \App\Support\OdemeYontemleri::havaleHesaplari())
            <h3>Havale / EFT ile ödeme</h3>
            <p>
                Aşağıdaki hesaba <strong>{{ number_format((float) $order->grand_total, 2, ',', '.') }} TL</strong>
                gönderin ve açıklamaya sipariş numaranızı
                (<strong>{{ $order->number }}</strong>) yazın. Ödemeniz onaylandığında siparişiniz hazırlanır.
            </p>
            <div class="havale-hesaplar">
                @foreach ($hesaplar as $h)
                    <div class="havale-hesap">
                        <strong>{{ $h['banka'] }}</strong>
                        @if ($h['sahip'])
                            <span>Alıcı: {{ $h['sahip'] }}</span><br>
                        @endif
                        @if ($h['iban'])
                            <span class="havale-iban">{{ $h['iban'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
            <p>
                Ödeme {{ $order->created_at->copy()->addDays(\App\Support\OdemeYontemleri::havaleSureGun())->format('d.m.Y') }}
                tarihine kadar yapılmazsa sipariş iptal edilir.
            </p>
        @else
            <h3>Kapıda ödeme</h3>
            <p>
                Siparişinizi teslim alırken <strong>{{ number_format((float) $order->grand_total, 2, ',', '.') }} TL</strong> ödeyeceksiniz.
                {{ config('shop.odeme.kapida_aciklama') }}
            </p>
        @endif
    </section>
@endif
