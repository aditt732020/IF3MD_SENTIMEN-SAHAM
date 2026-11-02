@extends('layouts.app')

@section('title', 'Log API & Cron Sync - StockSense AI')
@section('breadcrumb', 'Log_API_&_Cron_Sync')

@section('content')
@php $isAdmin = Auth::check() && Auth::user()->role === 'admin'; @endphp

<div class="page-header">
    <div>
        <div class="page-title-row">
            <h1 class="page-title">Log API & Cron Sync</h1>
        </div>
        <p class="page-desc">
            Pemantauan status penarikan berita RSS dan harga saham harian (Yahoo Finance),
            kesehatan microservice AI Python, serta penjadwalan cron otomatis.
        </p>
    </div>

    @if($isAdmin)
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <form action="{{ route('api.logs.trigger') }}" method="POST">
                @csrf
                <input type="hidden" name="target" value="news">
                <button type="submit" class="btn-outline">Tarik Berita RSS Sekarang</button>
            </form>
            <form action="{{ route('api.logs.trigger') }}" method="POST">
                @csrf
                <input type="hidden" name="target" value="prices">
                <button type="submit" class="btn-outline">Tarik Harga OHLC Sekarang</button>
            </form>
            <form action="{{ route('api.logs.trigger') }}" method="POST">
                @csrf
                <input type="hidden" name="target" value="all">
                <button type="submit" class="btn-accent">Sync Semua Sekarang</button>
            </form>
        </div>
    @endif
</div>

{{-- Status Kartu Layanan --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:0.85rem;">
    <div class="card-panel">
        <div style="font-size:0.65rem; color:var(--text-muted); text-transform:uppercase;">AI Microservice (Flask :5000)</div>
        @if($aiHealth)
            <div style="font-size:1.1rem; font-weight:800; color:var(--accent); margin:0.3rem 0;">ONLINE · {{ $aiHealth['model'] ?? 'Model Aktif' }}</div>
            <div style="font-size:0.7rem; color:var(--text-muted);">
                Akurasi Uji: <strong>{{ isset($aiHealth['test_accuracy']) ? round($aiHealth['test_accuracy'] * 100, 1) . '%' : '-' }}</strong>
                (Macro-F1: {{ $aiHealth['test_macro_f1'] ?? '-' }})
            </div>
        @else
            <div style="font-size:1.1rem; font-weight:800; color:var(--red); margin:0.3rem 0;">OFFLINE</div>
            <div style="font-size:0.7rem; color:var(--text-muted);">Jalankan: <code>python ai_service/app.py</code></div>
        @endif
    </div>

    <div class="card-panel">
        <div style="font-size:0.65rem; color:var(--text-muted); text-transform:uppercase;">Jadwal Cron Berita (FR-05)</div>
        <div style="font-size:1.1rem; font-weight:800; font-family:'JetBrains Mono',monospace; margin:0.3rem 0;">0 8,12,18 * * *</div>
        <div style="font-size:0.7rem; color:var(--text-muted);">3x sehari (08:00, 12:00, 18:00 WIB) · {{ count($feeds) }} Portal RSS</div>
    </div>

    <div class="card-panel">
        <div style="font-size:0.65rem; color:var(--text-muted); text-transform:uppercase;">Jadwal Cron Harga OHLC (FR-06)</div>
        <div style="font-size:1.1rem; font-weight:800; font-family:'JetBrains Mono',monospace; margin:0.3rem 0;">30 16 * * 1-5</div>
        <div style="font-size:0.7rem; color:var(--text-muted);">Senin–Jumat 16:30 WIB (setelah penutupan BEI)</div>
    </div>

    <div class="card-panel">
        <div style="font-size:0.65rem; color:var(--text-muted); text-transform:uppercase;">Ringkasan Eksekusi API</div>
        <div style="font-size:1.1rem; font-weight:800; margin:0.3rem 0;">
            <span style="color:var(--accent)">{{ $successLogs }} Sukses</span> /
            <span style="color:var(--red)">{{ $failedLogs }} Gagal</span>
        </div>
        <div style="font-size:0.7rem; color:var(--text-muted);">Dari total {{ $totalLogs }} riwayat penarikan</div>
    </div>
</div>

{{-- Tabel Riwayat Log Penarikan API (FR-04) --}}
<div class="card-panel">
    <div class="card-panel-title">
        <span>Riwayat Log Penarikan API ({{ $logs->count() }} Terakhir)</span>
        <form action="{{ route('api.logs') }}" method="GET" style="display:flex; gap:0.45rem;">
            <select name="type" onchange="this.form.submit()" style="background:var(--bg-input); border:1px solid var(--border); color:var(--text-white); border-radius:6px; padding:0.3rem 0.6rem; font-size:0.72rem;">
                <option value="">Semua Jenis</option>
                <option value="news" {{ $type === 'news' ? 'selected' : '' }}>Berita RSS</option>
                <option value="price" {{ $type === 'price' ? 'selected' : '' }}>Harga Saham (OHLC)</option>
            </select>
            <select name="status" onchange="this.form.submit()" style="background:var(--bg-input); border:1px solid var(--border); color:var(--text-white); border-radius:6px; padding:0.3rem 0.6rem; font-size:0.72rem;">
                <option value="">Semua Status</option>
                <option value="success" {{ $status === 'success' ? 'selected' : '' }}>Success</option>
                <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Waktu Mulai</th>
                <th>Durasi</th>
                <th>Jenis</th>
                <th>Sumber</th>
                <th>Status</th>
                <th>Data Tersimpan</th>
                <th>Pesan / Ringkasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                @php
                    $badgeClass = match($log->status) {
                        'success' => 'positive',
                        'partial' => 'neutral',
                        default   => 'negative',
                    };
                    $duration = ($log->started_at && $log->finished_at)
                        ? $log->started_at->diffInSeconds($log->finished_at) . ' dtk'
                        : '-';
                @endphp
                <tr>
                    <td style="font-family:'JetBrains Mono',monospace; color:var(--text-muted);">#{{ $log->id }}</td>
                    <td style="font-family:'JetBrains Mono',monospace; white-space:nowrap;">
                        {{ ($log->started_at ?? $log->created_at)->format('d M Y H:i:s') }}
                    </td>
                    <td style="font-family:'JetBrains Mono',monospace;">{{ $duration }}</td>
                    <td style="font-family:'JetBrains Mono',monospace; font-weight:700;">
                        {{ strtoupper($log->source_type) }}
                    </td>
                    <td>{{ $log->source_name }}</td>
                    <td><span class="sentiment-badge {{ $badgeClass }}">{{ strtoupper($log->status) }}</span></td>
                    <td style="font-family:'JetBrains Mono',monospace; font-weight:700;">+{{ $log->records_fetched }}</td>
                    <td style="color:var(--text-muted);">{{ $log->message ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:var(--text-dim); padding:1.5rem;">
                        Belum ada riwayat penarikan API.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

