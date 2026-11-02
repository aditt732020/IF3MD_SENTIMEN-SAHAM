@extends('layouts.app')

@section('title', 'Emiten & Berita Terkait - StockSense AI')
@section('breadcrumb', 'Emiten_&_Berita_Terkait')

@push('head')
<style>
    .ticker-pills { display: flex; flex-wrap: wrap; gap: 0.45rem; }
    .ticker-pill {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.4rem 0.8rem; border-radius: 8px;
        background: var(--bg-card); border: 1px solid var(--border);
        color: var(--text-muted); font-size: 0.74rem; font-weight: 600;
        text-decoration: none; font-family: 'JetBrains Mono', monospace;
        transition: all 0.15s;
    }
    .ticker-pill:hover { color: var(--text-white); border-color: rgba(52,211,153,0.4); }
    .ticker-pill.active {
        background: var(--accent-dim); color: var(--accent);
        border-color: rgba(52,211,153,0.45);
    }
    .ticker-pill .cnt {
        font-size: 0.6rem; padding: 0.08rem 0.35rem; border-radius: 4px;
        background: rgba(255,255,255,0.06); color: var(--text-muted);
    }
    .ohlc-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem;
    }
    .ohlc-box {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 10px; padding: 0.85rem 1rem;
    }
    .ohlc-label { font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 0.25rem; }
    .ohlc-val { font-size: 1.2rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: var(--text-white); }
    .two-col { display: grid; grid-template-columns: 1.35fr 1fr; gap: 1rem; }
    @media (max-width: 1100px) { .two-col { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title-row">
            <h1 class="page-title">Emiten & Berita Terkait</h1>
        </div>
        <p class="page-desc">
            Pilih kode emiten untuk melihat pergerakan harga harian (Open, High, Low, Close & Volume)
            serta daftar berita pasar dan hasil klasifikasi sentimen AI.
        </p>
    </div>

    <form action="{{ route('emiten.detail') }}" method="GET" style="display:flex; gap:0.5rem;">
        <input type="text" name="ticker" value="{{ $searchTicker }}" placeholder="Cari kode emiten (mis. BBCA)..."
               style="background:var(--bg-input); border:1px solid var(--border); border-radius:8px; color:var(--text-white); padding:0.45rem 0.8rem; font-size:0.75rem; outline:none;">
        <button type="submit" class="btn-accent">Cari Emiten</button>
    </form>
</div>

{{-- Daftar Pil Emiten --}}
<div class="ticker-pills">
    @foreach($stocks as $st)
        <a href="{{ route('emiten.detail', ['ticker' => $st->ticker_code]) }}"
           class="ticker-pill {{ $selectedStock && $selectedStock->id === $st->id ? 'active' : '' }}">
            <span>{{ $st->ticker_code }}</span>
            <span class="cnt">{{ $st->news_sentiments_count }} berita</span>
        </a>
    @endforeach
</div>

@if($selectedStock)
    @php
        $latestPrice = $prices->last();
        $prevPrice   = $prices->count() > 1 ? $prices->slice(-2, 1)->first() : $latestPrice;
        $diff        = ($latestPrice && $prevPrice) ? ($latestPrice->close_price - $prevPrice->close_price) : 0;
        $diffPct     = ($prevPrice && $prevPrice->close_price > 0) ? round(($diff / $prevPrice->close_price) * 100, 2) : 0;
    @endphp

    {{-- Ringkasan OHLC Terakhir (FR-06) --}}
    <div class="ohlc-grid">
        <div class="ohlc-box">
            <div class="ohlc-label">{{ $selectedStock->ticker_code }} · {{ $selectedStock->sector ?? 'IDX' }}</div>
            <div class="ohlc-val" style="font-size:0.95rem">{{ $selectedStock->company_name }}</div>
        </div>
        <div class="ohlc-box">
            <div class="ohlc-label">Open</div>
            <div class="ohlc-val">{{ $latestPrice ? number_format($latestPrice->open_price, 0, ',', '.') : '—' }}</div>
        </div>
        <div class="ohlc-box">
            <div class="ohlc-label">High</div>
            <div class="ohlc-val" style="color:var(--accent)">{{ $latestPrice ? number_format($latestPrice->high_price ?? $latestPrice->close_price, 0, ',', '.') : '—' }}</div>
        </div>
        <div class="ohlc-box">
            <div class="ohlc-label">Low</div>
            <div class="ohlc-val" style="color:var(--red)">{{ $latestPrice ? number_format($latestPrice->low_price ?? $latestPrice->open_price, 0, ',', '.') : '—' }}</div>
        </div>
        <div class="ohlc-box">
            <div class="ohlc-label">Close ({{ $latestPrice ? $latestPrice->price_date->format('d M Y') : '-' }})</div>
            <div class="ohlc-val">
                {{ $latestPrice ? number_format($latestPrice->close_price, 0, ',', '.') : '—' }}
                <span style="font-size:0.72rem; color:{{ $diff >= 0 ? 'var(--accent)' : 'var(--red)' }}">
                    ({{ $diff >= 0 ? '+' : '' }}{{ $diffPct }}%)
                </span>
            </div>
        </div>
        <div class="ohlc-box">
            <div class="ohlc-label">Volume</div>
            <div class="ohlc-val">{{ $latestPrice ? number_format($latestPrice->volume / 1000000, 1) . 'M' : '—' }}</div>
        </div>
    </div>

    <div class="two-col">
        {{-- Grafik Pergerakan Harga OHLC --}}
        <div class="card-panel">
            <div class="card-panel-title">
                <span>Grafik Pergerakan Harga Harian (OHLC) — {{ $selectedStock->ticker_code }}</span>
                <span style="font-size:0.68rem; color:var(--text-muted); font-family:'JetBrains Mono',monospace;">
                    {{ $prices->count() }} hari bursa terakhir
                </span>
            </div>
            <div style="height:260px; position:relative;">
                <canvas id="ohlcChart"></canvas>
            </div>
        </div>

        {{-- Tabel Riwayat OHLCV Harian --}}
        <div class="card-panel" style="max-height:330px; overflow-y:auto;">
            <div class="card-panel-title">
                <span>Tabel OHLC & Volume Harian</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Open</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Close</th>
                        <th>Volume</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prices->sortByDesc('price_date')->take(12) as $p)
                        <tr>
                            <td style="font-family:'JetBrains Mono',monospace;">{{ $p->price_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($p->open_price, 0, ',', '.') }}</td>
                            <td style="color:var(--accent)">{{ number_format($p->high_price ?? $p->close_price, 0, ',', '.') }}</td>
                            <td style="color:var(--red)">{{ number_format($p->low_price ?? $p->open_price, 0, ',', '.') }}</td>
                            <td style="font-weight:700">{{ number_format($p->close_price, 0, ',', '.') }}</td>
                            <td style="color:var(--text-muted)">{{ number_format($p->volume) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center; color:var(--text-dim);">Belum ada data harga</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FR-11: Tabel Detail Berita (Judul, Tanggal, Sumber, Label Sentimen) --}}
    <div class="card-panel">
        <div class="card-panel-title">
            <span>Tabel Detail Berita Terkait {{ $selectedStock->ticker_code }} ({{ $articles->count() }} Artikel)</span>
            <div style="display:flex; gap:0.4rem; font-size:0.68rem;">
                <span class="sentiment-badge positive">Positif: {{ $sentimentSummary['positive'] ?? 0 }}</span>
                <span class="sentiment-badge neutral">Netral: {{ $sentimentSummary['neutral'] ?? 0 }}</span>
                <span class="sentiment-badge negative">Negatif: {{ $sentimentSummary['negative'] ?? 0 }}</span>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:46%">Judul Berita</th>
                    <th>Tanggal Rilis</th>
                    <th>Sumber</th>
                    <th>Label Sentimen</th>
                    <th>Confidence</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $art)
                    @php
                        $sent = $art->sentiments->firstWhere('stock_id', $selectedStock->id) ?? $art->sentiments->first();
                        $lbl  = $sent ? $sent->sentiment_label : 'neutral';
                        $map  = ['positive' => 'Positif', 'negative' => 'Negatif', 'neutral' => 'Netral'];
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ $art->source_url }}" target="_blank" rel="noopener noreferrer"
                               style="color:var(--text-white); font-weight:600; text-decoration:none;">
                                {{ $art->title }}
                            </a>
                            <div style="font-size:0.68rem; color:var(--text-muted); margin-top:0.2rem;">
                                {{ Str::limit($art->content, 110) }}
                            </div>
                        </td>
                        <td style="font-family:'JetBrains Mono',monospace; font-size:0.7rem; color:var(--text-muted); white-space:nowrap;">
                            {{ $art->published_at ? $art->published_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td>{{ $art->source_name ?? parse_url($art->source_url, PHP_URL_HOST) }}</td>
                        <td>
                            <span class="sentiment-badge {{ $lbl }}">{{ $map[$lbl] ?? 'Netral' }}</span>
                        </td>
                        <td style="font-family:'JetBrains Mono',monospace;">
                            {{ $sent ? round($sent->confidence_score * 100, 1) . '%' : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color:var(--text-dim); padding:1.5rem;">
                            Belum ada berita yang menyebut emiten {{ $selectedStock->ticker_code }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
@endsection

@push('scripts')
@php
    $chartData = $prices->map(fn($p) => [
        'date'  => $p->price_date->format('d M'),
        'open'  => (float) $p->open_price,
        'high'  => (float) ($p->high_price ?? $p->close_price),
        'low'   => (float) ($p->low_price ?? $p->open_price),
        'close' => (float) $p->close_price,
    ])->values();
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const priceData = @json($chartData);

    const ctx = document.getElementById('ohlcChart');
    if (ctx && priceData.length) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: priceData.map(d => d.date),
                datasets: [
                    {
                        label: 'Close',
                        data: priceData.map(d => d.close),
                        borderColor: '#34d399',
                        backgroundColor: 'rgba(52,211,153,0.12)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25,
                    },
                    {
                        label: 'High',
                        data: priceData.map(d => d.high),
                        borderColor: '#60a5fa',
                        borderDash: [4, 4],
                        borderWidth: 1.3,
                        pointRadius: 0,
                        fill: false,
                    },
                    {
                        label: 'Low',
                        data: priceData.map(d => d.low),
                        borderColor: '#f87171',
                        borderDash: [4, 4],
                        borderWidth: 1.3,
                        pointRadius: 0,
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#94a3b8', font: { size: 11 } } } },
                scales: {
                    x: { ticks: { color: '#64748b', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' } },
                    y: { ticks: { color: '#64748b', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.05)' } }
                }
            }
        });
    }
</script>
@endpush

