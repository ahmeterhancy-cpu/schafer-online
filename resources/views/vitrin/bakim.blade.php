@php
    $baslik = trim((string) config('shop.yapim.baslik')) ?: 'Sitemiz yapım aşamasında';
    $metin = trim((string) config('shop.yapim.metin'))
        ?: 'Yeni online mağazamızı hazırlıyoruz, çok yakında yayındayız. Bu sürede mağazamızda ve telefonla hizmet vermeye devam ediyoruz.';
    $telefon = config('shop.satici.telefon');
    $whatsapp = \App\Support\Iletisim::whatsappBaglanti();
    $instagram = config('shop.sosyal.instagram');
    $pinVar = (string) config('shop.yapim.pin') !== '';
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('shop.ad') }} — {{ $baslik }}</title>
    <link rel="icon" href="{{ asset('img/schafer/favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
</head>
<body>
    {{-- Yapım aşaması perdesi: menü, sepet ya da oturuma bağlı hiçbir şey yok --}}
    <main class="bakim">
        <img src="{{ asset('img/schafer/logo.png') }}" alt="{{ config('shop.ad') }}" width="300" height="221">

        @if (trim((string) config('shop.yapim.baslik')) !== '')
            <p class="bakim-etiket">Yapım aşamasında</p>
        @endif
        <h1>{{ $baslik }}</h1>
        <p>{{ $metin }}</p>

        @if ($telefon || $whatsapp)
            <div class="bakim-iletisim">
                @if ($whatsapp)
                    <a class="dugme" href="{{ $whatsapp }}" rel="noopener noreferrer" target="_blank">WhatsApp'tan yazın</a>
                @endif
                @if ($telefon)
                    <a class="dugme dugme-cizgi" href="tel:{{ preg_replace('/[^0-9+]/', '', $telefon) }}">{{ $telefon }}</a>
                @endif
            </div>
        @endif

        @if (config('shop.satici.adres'))
            <address>{{ config('shop.satici.adres') }}</address>
        @endif

        @if ($instagram)
            <p>
                <a href="https://instagram.com/{{ $instagram }}" rel="noopener noreferrer" target="_blank">&#64;{{ $instagram }}</a>
            </p>
        @endif

        @if ($pinVar)
            <details class="bakim-pin" @if (session('pin_hata')) open @endif>
                <summary>Önizleme kodu</summary>
                <form method="POST" action="{{ route('yapim.pin') }}">
                    @csrf
                    <input type="hidden" name="to" value="{{ $hedef ?? '/' }}">
                    <label class="gorunmez" for="pin">PIN kodu</label>
                    <input type="password" id="pin" name="pin" inputmode="numeric" autocomplete="off"
                           placeholder="PIN" class="metin-girdi" required maxlength="20">
                    <button type="submit" class="dugme">Siteyi gör</button>
                </form>
                @if (session('pin_hata'))
                    <p class="bakim-pin-hata" role="alert">{{ session('pin_hata') }}</p>
                @endif
            </details>
        @endif
    </main>
</body>
</html>
