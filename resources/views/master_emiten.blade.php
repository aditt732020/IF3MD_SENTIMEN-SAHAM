@extends('layouts.app')

@section('title', 'Master Data Emiten - StockSense AI')
@section('breadcrumb', 'Master_Data_Emiten')

@push('head')
<style>
    .modal-backdrop {
        display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7);
        z-index: 300; align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-backdrop.show { display: flex; }
    .modal-box {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 12px; width: 100%; max-width: 480px; padding: 1.25rem;
    }
</style>
@endpush

@section('content')
@php $isAdmin = Auth::check() && Auth::user()->role === 'admin'; @endphp

<div class="page-header">
    <div>
        <div class="page-title-row">
            <h1 class="page-title">Master Data Emiten</h1>
        </div>
    </div>
    @if(!$isAdmin)
        <a href="{{ route('login') }}" class="btn-accent">Login sebagai Admin untuk Kelola Data</a>
    @endif
</div>

@if($isAdmin)
    {{-- Form Tambah Emiten Baru (FR-03) --}}
    <div class="card-panel">
        <div class="card-panel-title">
            <span>Tambah Emiten Baru</span>
        </div>
        <form action="{{ route('master.emiten.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label>Kode Emiten (4 Huruf IDX)</label>
                    <input type="text" name="ticker_code" maxlength="4" placeholder="Contoh: BREN" required style="text-transform:uppercase;">
                </div>
                <div class="field">
                    <label>Nama Perusahaan</label>
                    <input type="text" name="company_name" placeholder="PT Barito Renewables Energy Tbk" required>
                </div>
                <div class="field">
                    <label>Sektor</label>
                    <input type="text" name="sector" placeholder="Energy / Finance / Technology">
                </div>
                <div class="field">
                    <label>Keywords Berita (pisahkan koma)</label>
                    <input type="text" name="keywords" placeholder="Barito Renewables, BREN">
                </div>
            </div>
            <div style="margin-top:0.85rem; display:flex; justify-content:flex-end;">
                <button type="submit" class="btn-accent">+ Simpan & Tarik Harga Awal</button>
            </div>
        </form>
    </div>
@else
    <div class="card-panel" style="border-color:rgba(251,191,36,0.3); background:rgba(251,191,36,0.06);">
        <div style="font-size:0.78rem; color:var(--yellow);">
            <strong>Mode Visitor (Read-Only):</strong> Anda hanya dapat melihat daftar emiten.
            Untuk menambah, mengubah, atau menghapus emiten, silakan <a href="{{ route('login') }}" style="color:var(--accent); font-weight:700;">login sebagai Administrator</a>.
        </div>
    </div>
@endif

{{-- Tabel Master Emiten --}}
<div class="card-panel">
    <div class="card-panel-title">
        <span>Daftar Emiten Dipantau ({{ $stocks->count() }} Emiten)</span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Perusahaan</th>
                <th>Sektor</th>
                <th>Keywords Pencocokan Berita</th>
                <th>Harga Terakhir</th>
                <th>Data OHLC</th>
                <th>Berita</th>
                @if($isAdmin)
                    <th style="text-align:right;">Aksi (CRUD)</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
                @php
                    $lastPrice = $stock->stockPrices->first();
                    $stockPayload = $stock->only(['id', 'ticker_code', 'company_name', 'sector', 'keywords']);
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('emiten.detail', ['ticker' => $stock->ticker_code]) }}"
                           style="font-family:'JetBrains Mono',monospace; font-weight:800; color:var(--accent); text-decoration:none;">
                            {{ $stock->ticker_code }}
                        </a>
                    </td>
                    <td style="font-weight:600;">{{ $stock->company_name }}</td>
                    <td style="color:var(--text-muted);">{{ $stock->sector ?? '-' }}</td>
                    <td style="font-size:0.7rem; color:var(--text-muted);">{{ $stock->keywords ?: $stock->ticker_code }}</td>
                    <td style="font-family:'JetBrains Mono',monospace;">
                        {{ $lastPrice ? 'Rp ' . number_format($lastPrice->close_price, 0, ',', '.') : '—' }}
                    </td>
                    <td style="font-family:'JetBrains Mono',monospace;">{{ $stock->stock_prices_count }} hari</td>
                    <td style="font-family:'JetBrains Mono',monospace;">{{ $stock->news_sentiments_count }}</td>
                    @if($isAdmin)
                        <td style="text-align:right; white-space:nowrap;">
                            <button type="button" class="btn-outline"
                                    onclick='openEditModal(@json($stockPayload))'>
                                Edit
                            </button>
                            <form action="{{ route('master.emiten.destroy', $stock) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Hapus emiten {{ $stock->ticker_code }} beserta riwayatnya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Hapus</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($isAdmin)
    {{-- Modal Edit Emiten --}}
    <div class="modal-backdrop" id="editModal">
        <div class="modal-box">
            <div class="card-panel-title">
                <span>Edit Emiten</span>
                <button type="button" class="btn-outline" onclick="closeEditModal()">✕</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                    <div class="field">
                        <label>Kode Emiten</label>
                        <input type="text" name="ticker_code" id="editTicker" maxlength="4" required style="text-transform:uppercase;">
                    </div>
                    <div class="field">
                        <label>Nama Perusahaan</label>
                        <input type="text" name="company_name" id="editName" required>
                    </div>
                    <div class="field">
                        <label>Sektor</label>
                        <input type="text" name="sector" id="editSector">
                    </div>
                    <div class="field">
                        <label>Keywords Berita (pisahkan koma)</label>
                        <input type="text" name="keywords" id="editKeywords">
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:0.5rem;">
                        <button type="button" class="btn-outline" onclick="closeEditModal()">Batal</button>
                        <button type="submit" class="btn-accent">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    function openEditModal(stock) {
        document.getElementById('editForm').action = `/dashboard/master-emiten/${stock.id}`;
        document.getElementById('editTicker').value   = stock.ticker_code || '';
        document.getElementById('editName').value     = stock.company_name || '';
        document.getElementById('editSector').value   = stock.sector || '';
        document.getElementById('editKeywords').value = stock.keywords || '';
        document.getElementById('editModal').classList.add('show');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show');
    }
</script>
@endpush

