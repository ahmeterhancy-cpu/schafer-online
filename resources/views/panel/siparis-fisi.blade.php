<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Sipariş fişi {{ $order->number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font: 13px/1.5 Arial, Helvetica, sans-serif; color: #1b1919; }
        .ust { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; border-bottom: 2px solid #1b1919; padding-bottom: 14px; }
        .ust img { width: 150px; }
        .ust h1 { margin: 0; font-size: 20px; }
        .kutular { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 18px 0; }
        .kutu { border: 1px solid #ccc; padding: 12px 14px; }
        .kutu h2 { margin: 0 0 6px; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 6px; border-bottom: 1px solid #ddd; text-align: left; vertical-align: top; }
        th { font-size: 11px; text-transform: uppercase; color: #555; }
        .sag { text-align: right; white-space: nowrap; }
        .orta { text-align: center; }
        .kutucuk { display: inline-block; width: 14px; height: 14px; border: 1.5px solid #1b1919; }
        .toplamlar { margin-left: auto; width: 300px; margin-top: 10px; }
        .toplamlar td { border: 0; padding: 3px 6px; }
        .genel td { font-size: 16px; font-weight: bold; border-top: 2px solid #1b1919; padding-top: 8px; }
        .odeme { margin-top: 16px; padding: 10px 14px; border: 2px solid #d21c22; font-weight: bold; }
        .imza { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 50px; }
        .imza div { border-top: 1px solid #1b1919; padding-top: 6px; color: #555; }
        .yazdir { position: fixed; right: 20px; bottom: 20px; padding: 10px 18px; background: #d21c22; color: #fff; border: 0; cursor: pointer; font-weight: bold; }
        @media print { .yazdir { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <button class="yazdir" onclick="window.print()">Yazdır</button>

    <div class="ust">
        <div>
            <img src="{{ asset('img/schafer/logo.png') }}" alt="{{ config('shop.ad') }}">
            <div>{{ config('shop.satici.adres') }}<br>{{ config('shop.satici.telefon') }}</div>
        </div>
        <div style="text-align:right">
            <h1>Sipariş {{ $order->number }}</h1>
            <div>{{ $order->created_at->format('d.m.Y H:i') }}</div>
            <div>{{ $order->status_label }} · {{ \App\Support\OdemeYontemleri::ad($order->payment_provider) }}</div>
        </div>
    </div>

    <div class="kutular">
        <div class="kutu">
            <h2>Teslimat</h2>
            {!! nl2br(e(\App\Models\Order::adresMetni($order->shipping_address))) !!}
        </div>
        <div class="kutu">
            <h2>Müşteri</h2>
            {{ $order->customer_name }}<br>
            {{ $order->customer_phone }}<br>
            {{ $order->customer_email }}
            @if ($order->customer_note)
                <br><br><strong>Not:</strong> {{ $order->customer_note }}
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px"></th>
                <th>Ürün</th>
                <th>SKU</th>
                <th class="orta">Adet</th>
                <th class="sag">Birim</th>
                <th class="sag">Tutar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $kalem)
                <tr>
                    <td><span class="kutucuk"></span></td>
                    <td>{{ $kalem->name }}@if ($kalem->variant_label && $kalem->variant_label !== $kalem->sku)<br><small>{{ $kalem->variant_label }}</small>@endif</td>
                    <td>{{ $kalem->sku }}</td>
                    <td class="orta"><strong>{{ $kalem->quantity }}</strong></td>
                    <td class="sag">{{ number_format((float) $kalem->unit_price, 2, ',', '.') }} TL</td>
                    <td class="sag">{{ number_format((float) $kalem->line_total, 2, ',', '.') }} TL</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="toplamlar">
        <tr><td>Ara toplam</td><td class="sag">{{ number_format((float) $order->subtotal, 2, ',', '.') }} TL</td></tr>
        @if ((float) $order->discount_total > 0)
            <tr><td>İndirim</td><td class="sag">-{{ number_format((float) $order->discount_total, 2, ',', '.') }} TL</td></tr>
        @endif
        <tr><td>Teslimat</td><td class="sag">{{ (float) $order->shipping_total > 0 ? number_format((float) $order->shipping_total, 2, ',', '.').' TL' : 'Ücretsiz' }}</td></tr>
        @if ((float) $order->payment_fee > 0)
            <tr><td>{{ \App\Support\OdemeYontemleri::ad($order->payment_provider) }} bedeli</td><td class="sag">{{ number_format((float) $order->payment_fee, 2, ',', '.') }} TL</td></tr>
        @endif
        <tr class="genel"><td>Toplam</td><td class="sag">{{ number_format((float) $order->grand_total, 2, ',', '.') }} TL</td></tr>
    </table>

    @if ($order->payment_provider === 'kapida' && $order->payment_status === 'pending')
        <div class="odeme">KAPIDA TAHSİL EDİLECEK: {{ number_format((float) $order->grand_total, 2, ',', '.') }} TL</div>
    @elseif ($order->payment_status === 'paid')
        <div class="odeme" style="border-color:#2f6f4a">ÖDEMESİ ALINDI — tahsilat yapılmayacak</div>
    @endif

    <div class="imza">
        <div>Hazırlayan</div>
        <div>Teslim alan (ad, imza, tarih)</div>
    </div>
</body>
</html>
