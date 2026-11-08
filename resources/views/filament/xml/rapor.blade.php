@php
    $r = $onizleme ?? $son;
    $etiketler = ['toplam' => 'XML satırı', 'eslesen' => 'Eşleşen', 'guncellenen' => 'Değişen', 'degismeyen' => 'Aynı kalan', 'bulunamayan' => 'Sitede bulunamayan', 'yeni' => 'Yeni (taslak)', 'atlanan' => 'Kodsuz, atlanan'];
    $tl = fn ($v) => $v === null ? '—' : number_format((float) $v, 2, ',', '.');
    $hucre = 'padding:6px 8px;border-bottom:1px solid rgba(128,128,128,.2);text-align:left;font-size:13px';
@endphp

@if (! $r)
    <p style="font-size:13px;opacity:.7">Henüz aktarım yapılmadı. Adresi girip kaydedin, sonra üstteki "Önizle" düğmesine basın.</p>
@else
    <div style="display:grid;gap:14px">
        <p style="font-size:13px;margin:0">
            <strong>{{ $onizleme ? 'ÖNİZLEME (hiçbir şey değişmedi)' : 'Son aktarım' }}</strong>
            · {{ \Illuminate\Support\Carbon::parse($r['tarih'])->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
        </p>

        @if (isset($r['hata']))
            <p style="color:#dc2626;font-size:13px;margin:0">Hata: {{ $r['hata'] }}</p>
        @else
            <div style="display:flex;flex-wrap:wrap;gap:8px">
                @foreach ($r['sayac'] as $k => $v)
                    <div style="border:1px solid rgba(128,128,128,.3);border-radius:8px;padding:8px 12px;min-width:110px">
                        <div style="font-size:18px;font-weight:700">{{ $v }}</div>
                        <div style="font-size:12px;opacity:.75">{{ $etiketler[$k] ?? $k }}</div>
                    </div>
                @endforeach
            </div>

            <p style="font-size:12px;opacity:.75;margin:0">
                Ürün öğesi: <code>{{ $r['urun_etiketi'] }}</code> ·
                @foreach ($r['alanlar'] as $alan => $etiket)
                    {{ $alan }} → <code>{{ $etiket ?? 'bulunamadı' }}</code>@if (! $loop->last), @endif
                @endforeach
            </p>

            @if ($r['ornekler'])
                <div style="overflow-x:auto">
                    <table style="width:100%;border-collapse:collapse">
                        <thead><tr>
                            <th style="{{ $hucre }}">Kod</th><th style="{{ $hucre }}">Ürün</th>
                            <th style="{{ $hucre }}">Fiyat</th><th style="{{ $hucre }}">Stok</th>
                        </tr></thead>
                        <tbody>
                            @foreach (array_slice($r['ornekler'], 0, 100) as $o)
                                <tr>
                                    <td style="{{ $hucre }}">{{ $o['kod'] }}@if ($o['tur'] === 'yeni') <strong>(yeni)</strong>@endif</td>
                                    <td style="{{ $hucre }}">{{ \Illuminate\Support\Str::limit($o['ad'] ?? '', 60) }}</td>
                                    <td style="{{ $hucre }}">@if ($o['fiyat']) {{ $tl($o['fiyat'][0]) }} → <strong>{{ $tl($o['fiyat'][1]) }}</strong> @else — @endif</td>
                                    <td style="{{ $hucre }}">@if ($o['stok']) {{ $o['stok'][0] ?? '—' }} → <strong>{{ $o['stok'][1] ?? '—' }}</strong> @else — @endif</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($r['bulunamayanlar'])
                <details>
                    <summary style="font-size:13px;cursor:pointer">Sitede bulunamayanlar ({{ $r['sayac']['bulunamayan'] }})</summary>
                    <ul style="font-size:12px;margin:8px 0 0 18px">
                        @foreach (array_slice($r['bulunamayanlar'], 0, 200) as $b)
                            <li>{{ $b['kod'] }} — {{ $b['ad'] }}</li>
                        @endforeach
                    </ul>
                </details>
            @endif
        @endif
    </div>
@endif
