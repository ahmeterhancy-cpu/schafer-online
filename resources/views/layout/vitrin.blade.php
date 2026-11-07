<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('baslik', config('shop.ad'))</title>
    <meta name="description" content="@yield('aciklama', 'Schafer Kıbrıs — mutfak, sofra, ev tekstili ve küçük ev aletleri. KKTC online mağaza.')">
    <link rel="icon" href="{{ asset('img/schafer/favicon.png') }}" type="image/png">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Acik Grafik: baglanti paylasildiginda kart gorunumu --}}
    <meta property="og:site_name" content="{{ config('shop.ad') }}">
    <meta property="og:type" content="@yield('og_tur', 'website')">
    <meta property="og:title" content="@yield('baslik', config('shop.ad'))">
    <meta property="og:description" content="@yield('aciklama', 'Schafer Kıbrıs — mutfak, sofra, ev tekstili ve küçük ev aletleri. KKTC online mağaza.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_gorsel', asset('img/schafer/logo.png'))">
    <meta property="og:locale" content="tr_TR">
    <meta name="twitter:card" content="summary_large_image">

    @stack('yapisal_veri')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="ust">
    <a href="#icerik" class="gorunmez">İçeriğe atla</a>

    {{-- Perde açıkken siteyi gören tek kişi yönetici: unutulmasın diye uyarı --}}
    @if (config('shop.bakim_modu') && auth()->user()?->panelde())
        <div class="bakim-seridi">
            Bakım perdesi AÇIK — ziyaretçiler "Çok yakında" sayfasını görüyor.
            <a href="{{ url('/admin/site-ayarlari') }}">Ayarlardan kapat</a>
        </div>
    @endif

    {{-- Üst şerit (siyah) — referanstaki "DESTEK HATTI" satırı --}}
    <div class="ust-serit">
        <div class="kap ust-serit-ic">
            <ul class="ust-serit-sol">
                @if (config('shop.satici.telefon'))
                    <li>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', config('shop.satici.telefon')) }}">
                            @include('vitrin.parca.ikon', ['ad' => 'telefon'])
                            <strong>DESTEK HATTI:</strong> {{ config('shop.satici.telefon') }}
                        </a>
                    </li>
                @endif
                @if ((float) config('shop.kargo.ucretsiz_esigi') > 0)
                    <li class="ust-serit-duyuru">
                        {{ number_format((float) config('shop.kargo.ucretsiz_esigi'), 0, ',', '.') }} TL üzeri ücretsiz teslimat
                    </li>
                @endif
            </ul>

            <ul class="ust-serit-sag">
                @foreach ($menuler['ust-serit'] as $bag)
                    <li><a href="{{ $bag['adres'] }}" @if ($bag['yeni_sekme']) target="_blank" rel="noopener" @endif>{{ $bag['etiket'] }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>

    <header class="baslik">
        <div class="kap baslik-ic">
            {{-- Mobil menü — <details>: JS olmadan da açılır --}}
            <details class="mobil-menu">
                <summary aria-label="Menüyü aç">
                    @include('vitrin.parca.ikon', ['ad' => 'menu'])
                </summary>
                <div class="mobil-menu-panel">
                    <div class="mobil-menu-ust"><span>Menü</span></div>
                    <form class="mobil-menu-arama" method="GET" action="{{ route('catalog.search') }}" role="search">
                        <label class="gorunmez" for="mobil-q">Ürün ara</label>
                        <input type="search" id="mobil-q" name="q" placeholder="Ürün ara…">
                        <button type="submit" aria-label="Ara">@include('vitrin.parca.ikon', ['ad' => 'arama'])</button>
                    </form>
                    <nav aria-label="Mobil menü">
                        <a href="{{ url('/') }}">Ana Sayfa</a>
                        @foreach ($menuKategorileri as $kat)
                            <a href="{{ route('catalog.category', $kat->slug) }}">{{ $kat->name }}</a>
                            @foreach ($kat->children as $alt)
                                <a class="mobil-menu-alt" href="{{ route('catalog.category', $alt->slug) }}">{{ $alt->name }}</a>
                            @endforeach
                        @endforeach
                        @foreach ($menuler['ana'] as $bag)
                            <a href="{{ $bag['adres'] }}" @if ($bag['yeni_sekme']) target="_blank" rel="noopener" @endif>{{ $bag['etiket'] }}</a>
                        @endforeach
                        @auth
                            <a href="{{ route('account.index') }}">Hesabım</a>
                        @else
                            <a href="{{ route('login') }}">Giriş / Üye Ol</a>
                        @endauth
                    </nav>
                </div>
            </details>

            <form class="baslik-arama" method="GET" action="{{ route('catalog.search') }}" role="search">
                <label class="gorunmez" for="baslik-q">Ürün ara</label>
                <input type="search" id="baslik-q" name="q" value="{{ request('q') }}"
                       placeholder="Ürün ya da ürün kodu ara…" autocomplete="off">
                <button type="submit" aria-label="Ara">
                    @include('vitrin.parca.ikon', ['ad' => 'arama'])
                </button>
            </form>

            <a href="{{ url('/') }}" class="baslik-logo" aria-label="{{ config('shop.ad') }} ana sayfa">
                <img src="{{ asset('img/schafer/logo.png') }}" alt="{{ config('shop.ad') }}" width="300" height="94">
            </a>

            <div class="baslik-ikonlar">
                <a href="{{ route('catalog.search') }}" class="baslik-ikon baslik-ikon-arama" aria-label="Ara">
                    @include('vitrin.parca.ikon', ['ad' => 'arama'])
                </a>

                @auth
                    <a href="{{ route('account.index') }}" class="baslik-ikon baslik-hesap">
                        @include('vitrin.parca.ikon', ['ad' => 'kullanici'])
                        <span class="baslik-ikon-metin">Hesabım</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="baslik-ikon baslik-hesap">
                        @include('vitrin.parca.ikon', ['ad' => 'kullanici'])
                        <span class="baslik-ikon-metin">Giriş / Kayıt</span>
                    </a>
                @endauth

                <a href="{{ url('/sepet') }}" class="baslik-ikon sepet-bag" aria-label="Sepet, {{ $sepetAdedi }} ürün">
                    <span class="baslik-ikon-kutu">
                        @include('vitrin.parca.ikon', ['ad' => 'sepet'])
                        <span class="sepet-sayi">{{ $sepetAdedi }}</span>
                    </span>
                    <span class="baslik-ikon-metin sepet-tutar">{{ number_format($sepetTutari, 2, ',', '.') }} ₺</span>
                </a>
            </div>
        </div>

        {{-- Menü: ana kategoriler büyük harfle, üzerine gelince açılan geniş menü --}}
        <nav class="menu-bant" aria-label="Ana menü">
            <ul class="kap ana-menu">
                @foreach ($menuKategorileri as $kat)
                    <li @class(['alti-var' => $kat->children->isNotEmpty()])>
                        <a href="{{ route('catalog.category', $kat->slug) }}">
                            {{ $kat->name }}
                            @if ($kat->children->isNotEmpty())
                                @include('vitrin.parca.ikon', ['ad' => 'ok-asagi'])
                            @endif
                        </a>
                        @if ($kat->children->isNotEmpty())
                            <div class="mega">
                                <div class="mega-ic">
                                    @foreach ($kat->children as $alt)
                                        <div class="mega-sutun">
                                            <a class="mega-baslik" href="{{ route('catalog.category', $alt->slug) }}">{{ $alt->name }}</a>
                                            @if ($alt->children->isNotEmpty())
                                                <ul>
                                                    @foreach ($alt->children as $torun)
                                                        <li><a href="{{ route('catalog.category', $torun->slug) }}">{{ $torun->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
                @foreach ($menuler['ana'] as $bag)
                    <li><a href="{{ $bag['adres'] }}" @if ($bag['yeni_sekme']) target="_blank" rel="noopener" @endif>{{ $bag['etiket'] }}</a></li>
                @endforeach
            </ul>
        </nav>
    </header>

    <main id="icerik">
        @yield('icerik')
    </main>

    {{-- Güven şeridi — üç özellik --}}
    <section class="guven-serit" aria-label="Alışveriş güvenceleri">
        <div class="kap guven-serit-ic">
            <div>
                @include('vitrin.parca.ikon', ['ad' => 'kamyon'])
                <span><strong>Hızlı Teslimat</strong>{{ (float) config('shop.kargo.ucretsiz_esigi') > 0 ? number_format((float) config('shop.kargo.ucretsiz_esigi'), 0, ',', '.') . ' TL üzeri ücretsiz' : 'KKTC geneline' }}</span>
            </div>
            <div>
                @include('vitrin.parca.ikon', ['ad' => 'kalkan'])
                <span><strong>Güvenli Alışveriş</strong>256 bit SSL ile korunan ödeme</span>
            </div>
            <div>
                @include('vitrin.parca.ikon', ['ad' => 'kart'])
                <span><strong>Taksitli Alışveriş</strong>Kredi kartına taksit imkânı</span>
            </div>
        </div>
    </section>

    <footer class="alt">
        <div class="alt-ana">
            <div class="kap alt-izgara">
                <div class="alt-marka">
                    <img src="{{ asset('img/schafer/logo-beyaz.png') }}" alt="{{ config('shop.ad') }}" width="200" height="63" loading="lazy">
                    <p>Schafer'in mutfak, sofra, ev tekstili ve küçük ev aletleri ürünleri Kıbrıs'ta, güvenle kapınızda.</p>
                </div>

                <div>
                    <h3>Hızlı Linkler</h3>
                    <ul>
                        <li><a href="{{ url('/') }}">Ana Sayfa</a></li>
                        <li><a href="{{ url('/sayfa/hakkimizda') }}">Hakkımızda</a></li>
                        <li><a href="{{ url('/iletisim') }}">İletişim</a></li>
                        @guest
                            <li><a href="{{ route('register') }}">Üye Ol</a></li>
                            <li><a href="{{ route('login') }}">Giriş Yap</a></li>
                        @else
                            <li><a href="{{ route('account.index') }}">Hesabım</a></li>
                        @endguest
                        <li><a href="{{ url('/siparis-sorgula') }}">Sipariş Takibi</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Kurallar</h3>
                    <ul>
                        @foreach ($menuler['alt-yardim'] as $bag)
                            <li><a href="{{ $bag['adres'] }}" @if ($bag['yeni_sekme']) target="_blank" rel="noopener" @endif>{{ $bag['etiket'] }}</a></li>
                        @endforeach
                        <li><a href="{{ url('/sayfa/mesafeli-satis') }}">Mesafeli Satış Sözleşmesi</a></li>
                        <li><a href="{{ url('/sayfa/kvkk') }}">Gizlilik ve Kişisel Veriler</a></li>
                        <li><a href="{{ url('/sayfa/cerez') }}">Çerez Politikası</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Kategoriler</h3>
                    <ul>
                        @foreach ($menuKategorileri as $kat)
                            <li><a href="{{ route('catalog.category', $kat->slug) }}">{{ $kat->name }}</a></li>
                        @endforeach
                        <li><a href="{{ url('/ara?q=çeyiz') }}">Evlilik Paketi</a></li>
                    </ul>
                </div>

                <div>
                    <h3>İletişim</h3>
                    <ul class="alt-iletisim">
                        <li>@include('vitrin.parca.ikon', ['ad' => 'konum']) <address>{{ config('shop.satici.adres') }}</address></li>
                        @foreach (array_filter([config('shop.satici.telefon'), config('shop.satici.telefon2')]) as $tel)
                            <li>@include('vitrin.parca.ikon', ['ad' => 'telefon']) <a href="tel:{{ preg_replace('/[^0-9+]/', '', $tel) }}">{{ $tel }}</a></li>
                        @endforeach
                        @if (config('shop.satici.eposta'))
                            <li>@include('vitrin.parca.ikon', ['ad' => 'eposta']) <a href="mailto:{{ config('shop.satici.eposta') }}">{{ config('shop.satici.eposta') }}</a></li>
                        @endif
                        @if (config('shop.sosyal.instagram'))
                            <li>@include('vitrin.parca.ikon', ['ad' => 'instagram']) <a href="https://instagram.com/{{ config('shop.sosyal.instagram') }}" rel="noopener noreferrer" target="_blank">&#64;{{ config('shop.sosyal.instagram') }}</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="alt-kunye">
            <div class="kap alt-kunye-ic">
                <span>&copy; {{ now()->year }} {{ config('shop.alan_adi') ?: config('shop.ad') }} · Tüm hakları saklıdır.</span>
                <span class="alt-imza">
                    Web Site by <a href="https://www.amesis.com.tr" rel="noopener" target="_blank">Amesis Dijital Ajans</a>
                </span>
                <span class="alt-odeme">
                    @include('vitrin.parca.ikon', ['ad' => 'kalkan'])
                    Kredi ve banka kartıyla güvenli ödeme
                </span>
            </div>
        </div>
    </footer>

    {{-- Mobil alt menü — sabit alt çubuk --}}
    <nav class="mobil-alt" aria-label="Hızlı erişim">
        <a href="{{ url('/koleksiyonlar') }}">@include('vitrin.parca.ikon', ['ad' => 'magaza'])<span>Mağaza</span></a>
        <a href="{{ route('catalog.search') }}">@include('vitrin.parca.ikon', ['ad' => 'arama'])<span>Ara</span></a>
        <a href="{{ url('/sepet') }}">
            <span class="baslik-ikon-kutu">
                @include('vitrin.parca.ikon', ['ad' => 'sepet'])
                <span class="sepet-sayi">{{ $sepetAdedi }}</span>
            </span>
            <span>Sepet</span>
        </a>
        <a href="{{ route('account.index') }}">@include('vitrin.parca.ikon', ['ad' => 'kullanici'])<span>Hesap</span></a>
    </nav>

    {{--
        Sabit sag alt kose: WhatsApp + yukari cik. Ikisi ayri ayri
        position:fixed olsaydi ust uste binerdi; tek kapta dizilirler.
    --}}
    @php($waBaglanti = \App\Support\Iletisim::whatsappBaglanti())
    <div class="sabit-kose">
        @if ($waBaglanti)
            <a class="wa-dugme" href="{{ $waBaglanti }}" target="_blank" rel="noopener noreferrer"
               aria-label="WhatsApp ile yazın">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path fill="currentColor" d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.450 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.910-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm0 18.13h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.220 8.22 0 0 1-1.26-4.36c0-4.54 3.7-8.23 8.25-8.23 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 0 1 2.41 5.82c0 4.54-3.7 8.21-8.24 8.21Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.15.16-.29.18-.53.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.13-.15.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.47c-.16 0-.43.06-.65.31-.22.25-.85.84-.85 2.04s.87 2.37.99 2.53c.12.16 1.71 2.61 4.15 3.66.58.25 1.03.4 1.39.51.58.19 1.11.16 1.53.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.11-.22-.17-.47-.29Z"/>
                </svg>
            </a>
        @endif

        <a href="#ust" class="yukari" aria-label="Sayfa başına dön">@include('vitrin.parca.ikon', ['ad' => 'ok-yukari'])</a>
    </div>

    @stack('betik')
</body>
</html>
