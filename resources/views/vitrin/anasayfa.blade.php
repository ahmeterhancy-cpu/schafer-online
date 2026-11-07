@extends('layout.vitrin')

@section('baslik', config('shop.ad') . ' — Online Mağaza')
@section('aciklama', 'Schafer Kıbrıs online mağaza: yemek takımları, tencere ve tava setleri, Türk kahvesi makineleri, nevresim ve havlu. KKTC geneline teslimat.')

@push('yapisal_veri')
    @include('vitrin.parca.jsonld', ['tur' => 'magaza'])
@endpush

@php
    $esik = (float) config('shop.kargo.ucretsiz_esigi');

    /*
     * Slayt: panelden girilenler (Vitrin → Slayt ve Afişler). Yoksa markanın
     * kendi kahraman alanı çizilir (öne çıkan ürün görselleriyle).
     */
    $bannerBicim = fn ($b) => [
        'ust' => $b->ust_metin,
        'baslik' => $b->baslik,
        'alt' => $b->alt_metin,
        'dugme' => $b->dugme_metni ?: ($b->adres ? 'Alışverişe başla' : null),
        'adres' => $b->adres,
        'dis' => $b->dis_baglanti,
        'gorsel' => $b->gorsel ? asset('storage/' . $b->gorsel) : null,
        'konum' => $b->metin_konumu,
    ];

    $slaytListesi = $bannerSlayt->map($bannerBicim)->all();
    // Afişler yalnız panelden girildiyse çizilir (otomatik içerik yok)
    $afisListesi = $bannerAfis->map($bannerBicim)->all();
    $genisListesi = $bannerGenis->map($bannerBicim)->all();
@endphp

@section('icerik')

    @if (session('bilgi'))
        <div class="kap" style="margin-top:20px"><p class="uyari">{{ session('bilgi') }}</p></div>
    @endif

    {{-- 1. Slayt (panelden) ya da marka kahraman alanı --}}
    @if ($slaytListesi !== [])
        <section class="slayt" aria-label="Öne çıkanlar" data-slayt>
            <div class="slayt-ray">
                @foreach ($slaytListesi as $slayt)
                    <div @class(['slayt-oge', 'slayt-marka' => ! $slayt['gorsel']]) id="slayt-{{ $loop->iteration }}"
                         @if ($slayt['gorsel']) style="background-image: url('{{ $slayt['gorsel'] }}')" @endif>
                        <div @class(['kap', 'slayt-ic', 'slayt-ic-sol' => $slayt['konum'] === 'sol'])>
                            <div class="slayt-metin">
                                @if ($slayt['ust'])
                                    <p class="slayt-vurgu">{{ $slayt['ust'] }}</p>
                                @endif
                                @if ($loop->first)
                                    <h1 class="slayt-baslik">{{ $slayt['baslik'] }}</h1>
                                @else
                                    <h2 class="slayt-baslik">{{ $slayt['baslik'] }}</h2>
                                @endif
                                @if ($slayt['alt'])
                                    <p class="slayt-alt">{{ $slayt['alt'] }}</p>
                                @endif
                                @if ($slayt['adres'])
                                    <div class="slayt-dugmeler">
                                        <a class="dugme" href="{{ $slayt['adres'] }}"
                                           @if ($slayt['dis']) rel="noopener" target="_blank" @endif>{{ $slayt['dugme'] }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (count($slaytListesi) > 1)
                <div class="slayt-noktalar">
                    @foreach ($slaytListesi as $slayt)
                        <a href="#slayt-{{ $loop->iteration }}" aria-label="{{ $loop->iteration }}. slayt"
                           @class(['aktif' => $loop->first])></a>
                    @endforeach
                </div>
            @endif
        </section>
    @else
        <section class="kahraman">
            <div class="kap kahraman-ic">
                <div class="kahraman-metin">
                    <p class="etiket-hashtag">#SCHAFER #KIBRIS</p>
                    <h1>Mutfağınız ve sofranız için Schafer kalitesi</h1>
                    <p>
                        Yemek takımlarından döküm tencerelere, Türk kahvesi makinelerinden nevresim takımlarına
                        Schafer ürünleri Kıbrıs'ta.
                        @if ($esik > 0)
                            {{ number_format($esik, 0, ',', '.') }} TL üzeri siparişlerde teslimat ücretsiz.
                        @endif
                    </p>
                    <div class="kahraman-dugmeler">
                        <a class="dugme" href="{{ url('/koleksiyonlar') }}">Alışverişe başla</a>
                        <a class="dugme dugme-cizgi" href="{{ url('/koleksiyonlar?sirala=indirim') }}">Kampanyalı ürünler</a>
                    </div>
                </div>
                @if ($vitrinUrunleri->isNotEmpty())
                    <div class="kahraman-gorseller">
                        @foreach ($vitrinUrunleri as $u)
                            <a href="{{ url('/urun/' . $u->slug) }}" class="kahraman-gorsel kahraman-gorsel-{{ $loop->iteration }}">
                                <img src="{{ asset('storage/' . $u->hero_image) }}" alt="{{ $u->name }}"
                                     @if (! $loop->first) loading="lazy" @endif>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- 2. Trend kategoriler --}}
    @if ($trend->isNotEmpty())
        <section class="kap bolum">
            <header class="bolum-basi-orta">
                <p class="etiket-hashtag">#SCHAFER #KEŞFET</p>
                <h2>Trend Kategoriler</h2>
            </header>
            <div class="trend-izgara">
                @foreach ($trend as $kat)
                    <a class="trend-kart" href="{{ route('catalog.category', $kat->slug) }}">
                        <span class="trend-gorsel"><img src="{{ asset('storage/' . $kat->image) }}" alt="" loading="lazy"></span>
                        <span class="trend-ad">{{ $kat->name }}</span>
                        <span class="trend-sayi">{{ $kat->products_count }} ürün</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Panelden girilen afişler (Vitrin → Slayt ve Afişler → Afiş) --}}
    @if ($afisListesi !== [])
        <section class="kap bolum">
            <div class="afisler afisler-{{ min(3, count($afisListesi)) }}">
                @foreach ($afisListesi as $afis)
                    @include('vitrin.parca.afis', ['afis' => $afis, 'sira' => $loop->iteration])
                @endforeach
            </div>
        </section>
    @endif

    {{-- 3. Kategori bölümleri — panelde "Ana sayfada göster" işaretliler --}}
    @foreach ($bolumler as $bolum)
        <section class="kap bolum">
            <header class="bolum-basi-orta">
                <p class="etiket-hashtag">#SCHAFER #KEŞFET</p>
                <h2>{{ $bolum['kategori']->name }}</h2>
            </header>
            <div class="urun-izgara">
                @foreach ($bolum['urunler'] as $urun)
                    @include('vitrin.parca.urun-kart', ['urun' => $urun])
                @endforeach
            </div>
            <p class="bolum-alt">
                <a class="dugme dugme-cizgi" href="{{ route('catalog.category', $bolum['kategori']->slug) }}">Tüm ürünleri incele</a>
            </p>
        </section>
    @endforeach

    {{-- Panelden girilen geniş afişler --}}
    @if ($genisListesi !== [])
        <section class="kap bolum">
            <div class="afisler afisler-{{ min(2, count($genisListesi)) }}">
                @foreach ($genisListesi as $afis)
                    @include('vitrin.parca.afis', ['afis' => $afis, 'sira' => $loop->iteration, 'genis' => true])
                @endforeach
            </div>
        </section>
    @endif

    {{-- 4. Kampanyadakiler --}}
    @if ($kampanyalar->isNotEmpty())
        <section class="kap bolum">
            <header class="bolum-basi-orta">
                <p class="etiket-hashtag">#SCHAFER #KAMPANYA</p>
                <h2>Kampanyalı Ürünler</h2>
            </header>
            <div class="urun-izgara">
                @foreach ($kampanyalar as $urun)
                    @include('vitrin.parca.urun-kart', ['urun' => $urun])
                @endforeach
            </div>
            <p class="bolum-alt">
                <a class="dugme dugme-cizgi" href="{{ url('/koleksiyonlar?sirala=indirim') }}">Tüm kampanyalar</a>
            </p>
        </section>
    @endif

@endsection
