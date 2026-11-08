{{--
    Ürün kartı — schaferkibris.com kartı: kare görsel (üzerine gelince ikinci
    görsel), ortalı ad + fiyat, altta "Sepete Ekle".
    Sorgu Product::kartIcin() kapsamıyla gelmeli (bkz. model).
    Seçeneksiz ürün kartta doğrudan sepete girer; seçenekli ürün sayfaya gider.
--}}
@php
    $urunAdresi = url('/urun/' . $urun->slug);
    $ikinci = $urun->ikinci_gorsel;
    $tek = $urun->tek_varyant;
@endphp
<article class="urun-kart">
    <div class="urun-gorsel {{ $urun->hero_image ? '' : 'urun-gorsel-yok' }}">
        <a href="{{ $urunAdresi }}" class="urun-gorsel-bag" tabindex="-1" aria-hidden="true">
            @if ($urun->hero_image)
                <img src="{{ asset('storage/' . $urun->hero_image) }}" alt="" loading="lazy" class="urun-gorsel-on">
                @if ($ikinci)
                    <img src="{{ asset('storage/' . $ikinci) }}" alt="" loading="lazy" class="urun-gorsel-arka">
                @endif
            @else
                <span class="urun-harf">S</span>
            @endif
        </a>

        <div class="urun-rozetler">
            @if ($urun->indirim_orani && \App\Support\Fiyat::gorunur())
                <span class="rozet rozet-indirim">-%{{ $urun->indirim_orani }}</span>
            @endif
            @if ($urun->badge)
                <span class="rozet rozet-yeni urun-rozet">{{ $urun->badge }}</span>
            @endif
            @if ($urun->total_stock < 1)
                <span class="rozet rozet-tukendi">Tükendi</span>
            @endif
        </div>
    </div>

    <div class="urun-bilgi">
        <h3 class="urun-ad"><a href="{{ $urunAdresi }}">{{ $urun->name }}</a></h3>

        @if ($urun->review_count > 0)
            <p class="urun-puan" aria-label="{{ number_format((float) $urun->rating, 1, ',', '') }} / 5, {{ $urun->review_count }} değerlendirme">
                <span class="yildiz" aria-hidden="true">{{ str_repeat('★', (int) round((float) $urun->rating)) }}<span class="yildiz-bos">{{ str_repeat('★', 5 - (int) round((float) $urun->rating)) }}</span></span>
                <span aria-hidden="true">({{ $urun->review_count }})</span>
            </p>
        @endif

        @fiyatgorunur
        <p class="urun-fiyat">
            @if ($urun->kart_eski_fiyat)
                <del>{{ number_format($urun->kart_eski_fiyat, 2, ',', '.') }} ₺</del>
            @endif
            <strong>
                @if ($urun->has_price_range)
                    {{ number_format((float) $urun->min_price, 2, ',', '.') }} – {{ number_format((float) $urun->max_price, 2, ',', '.') }} ₺
                @else
                    {{ number_format((float) $urun->min_price, 2, ',', '.') }} ₺
                @endif
            </strong>
        </p>
        @else
        <p class="urun-fiyat urun-fiyat-gizli">{{ \App\Support\Fiyat::metin() }}</p>
        @endfiyatgorunur

        @if ($urun->renkler->isNotEmpty())
            <div class="urun-renkler" aria-label="Renkler: {{ $urun->renkler->pluck('value')->implode(', ') }}">
                @foreach ($urun->renkler as $renk)
                    <span class="urun-renk" style="background: {{ $renk->color_hex ?: 'transparent' }}"
                          title="{{ $renk->value }}"></span>
                @endforeach
            </div>
        @endif

        @if (\App\Support\Fiyat::gizli())
            <a href="{{ \App\Support\Fiyat::sorBaglantisi($urun->name, $urunAdresi) }}" class="urun-sepet" target="_blank" rel="noopener"
               aria-label="{{ $urun->name }} — fiyat sor">Fiyat sor</a>
        @elseif ($urun->total_stock < 1)
            <a href="{{ $urunAdresi }}" class="urun-sepet" aria-label="{{ $urun->name }} — stoğa girince haber ver">Gelince haber ver</a>
        @elseif ($tek)
            <form method="POST" action="{{ route('cart.add') }}" class="urun-sepet-form">
                @csrf
                <input type="hidden" name="variant_id" value="{{ $tek->id }}">
                <button type="submit" class="urun-sepet" aria-label="{{ $urun->name }} — sepete ekle">
                    @include('vitrin.parca.ikon', ['ad' => 'sepet']) Sepete Ekle
                </button>
            </form>
        @else
            <a href="{{ $urunAdresi }}" class="urun-sepet" aria-label="{{ $urun->name }} — seçenekleri gör">Seçenekleri gör</a>
        @endif
    </div>
</article>
