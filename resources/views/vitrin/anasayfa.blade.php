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
    @elseif ($vitrinSlaytlari->isNotEmpty())
        {{--
            Ürün slaytları. Görseller beyaz zeminli ürün fotoğrafları:
            mix-blend-mode: multiply ile beyaz zemin, slaytın renkli zeminine
            karışır — ürün sahnede "yüzer". JS yoksa ilk slayt görünür kalır.
        --}}
        <section class="hero" aria-roledescription="carousel" aria-label="Öne çıkan ürünler" data-hero>
            <div class="hero-sahne">
                @foreach ($vitrinSlaytlari as $s)
                    @php($u = $s['urun'])
                    <article @class(['hero-slayt', 'hero-ton-' . (($loop->index % 4) + 1), 'aktif' => $loop->first])
                             aria-roledescription="slide" aria-label="{{ $loop->iteration }} / {{ $loop->count }}"
                             @unless ($loop->first) aria-hidden="true" @endunless>
                        <div class="kap hero-ic">
                            <div class="hero-metin">
                                <p class="hero-ust">
                                    <span>#SCHAFER</span> {{ $s['kategori']->name }}
                                </p>
                                @if ($loop->first)
                                    <h1 class="hero-baslik">{{ \App\Support\Metin::baslik($u->name) }}</h1>
                                @else
                                    <h2 class="hero-baslik">{{ \App\Support\Metin::baslik($u->name) }}</h2>
                                @endif
                                @fiyatgorunur
                                <p class="hero-fiyat">
                                    @if ($u->kart_eski_fiyat)
                                        <del>{{ number_format($u->kart_eski_fiyat, 2, ',', '.') }} ₺</del>
                                    @endif
                                    <strong>{{ number_format((float) $u->min_price, 2, ',', '.') }} ₺</strong>
                                    @if ($u->indirim_orani)
                                        <span class="hero-rozet">-%{{ $u->indirim_orani }}</span>
                                    @endif
                                </p>
                                @endfiyatgorunur
                                <div class="hero-dugmeler">
                                    <a class="dugme" href="{{ url('/urun/' . $u->slug) }}" @unless ($loop->first) tabindex="-1" @endunless>Ürünü incele</a>
                                    <a class="hero-bag" href="{{ route('catalog.category', $s['kategori']->slug) }}" @unless ($loop->first) tabindex="-1" @endunless>
                                        Tüm {{ $s['kategori']->name }} @include('vitrin.parca.ikon', ['ad' => 'ok-sag'])
                                    </a>
                                </div>
                                @if ($esik > 0 && $loop->first && \App\Support\Fiyat::gorunur())
                                    <p class="hero-not">{{ number_format($esik, 0, ',', '.') }} TL üzeri siparişlerde teslimat ücretsiz</p>
                                @endif
                            </div>
                            <a @class(['hero-gorsel', 'hero-gorsel-sahne' => ! \App\Support\Gorsel::beyazZeminMi($u->hero_image)])
                               href="{{ url('/urun/' . $u->slug) }}" tabindex="-1" aria-hidden="true">
                                <span class="hero-halka"></span>
                                <img src="{{ asset('storage/' . $u->hero_image) }}" alt=""
                                     @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($vitrinSlaytlari->count() > 1)
                <div class="kap hero-gezinti">
                    <div class="hero-kucukler" role="tablist" aria-label="Slayt seç">
                        @foreach ($vitrinSlaytlari as $s)
                            <button type="button" role="tab" @class(['hero-kucuk', 'aktif' => $loop->first])
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-label="{{ $s['kategori']->name }}" data-hero-git="{{ $loop->index }}">
                                <img src="{{ asset('storage/' . $s['urun']->hero_image) }}" alt="" loading="lazy">
                                <span class="hero-kucuk-ad">{{ $s['kategori']->name }}</span>
                                <span class="hero-ilerleme"><i></i></span>
                            </button>
                        @endforeach
                    </div>
                    <div class="hero-oklar">
                        <button type="button" class="hero-ok" data-hero-onceki aria-label="Önceki slayt">@include('vitrin.parca.ikon', ['ad' => 'ok-sol'])</button>
                        <button type="button" class="hero-ok" data-hero-sonraki aria-label="Sonraki slayt">@include('vitrin.parca.ikon', ['ad' => 'ok-sag'])</button>
                    </div>
                </div>
            @endif
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
