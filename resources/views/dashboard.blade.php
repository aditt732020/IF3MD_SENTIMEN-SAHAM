<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StockSense AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden font-sans">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
        <div class="h-16 flex items-center px-6 border-b border-gray-200">
            <div class="w-8 h-8 bg-[#c2e0b4] rounded-full flex items-center justify-center mr-3 border border-gray-800">
                <svg class="w-4 h-4 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8"></path></svg>
            </div>
            <span class="font-bold text-lg text-gray-900">StockSense AI</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="#" class="block px-4 py-2 bg-[#e8f5e3] text-green-800 rounded-lg font-medium border border-green-200">AI Impact Radar</a>
            <a href="#" class="block px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-lg">Database Emiten</a>
            <a href="#" class="block px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-lg">Log Sentimen</a>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
            <div class="relative w-96">
                <input type="text" placeholder="Cari emiten atau kata kunci berita..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-bold">A</div>
            </div>
        </header>

        <!-- Dashboard Layout -->
        <div class="flex-1 p-6 flex gap-6 overflow-hidden">
            
            <!-- Kolom Kiri: News Stream -->
            <div class="w-4/12 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col h-full overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                    <h2 class="font-bold text-gray-800">Live News Stream</h2>
                    <p class="text-xs text-gray-500">Menunggu antrean analisis AI</p>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    
                    <!-- Looping Data Berita dari Database -->
                    @foreach($daftar_berita as $berita)
                    <div class="p-4 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-gray-50 cursor-pointer transition">
                        <div class="flex justify-between items-start mb-2">
                            
                            <!-- Label Warna Dinamis Hasil Analisis AI -->
                            @if($berita->label_ai == 'Positif')
                                <span class="text-[10px] font-bold px-2 py-1 bg-green-200 text-green-800 rounded uppercase">Positif</span>
                            @elseif($berita->label_ai == 'Negatif')
                                <span class="text-[10px] font-bold px-2 py-1 bg-red-200 text-red-800 rounded uppercase">Negatif</span>
                            @else
                                <span class="text-[10px] font-bold px-2 py-1 bg-gray-200 text-gray-800 rounded uppercase">{{ $berita->label_ai }}</span>
                            @endif
                            
                            <span class="text-xs text-gray-500">{{ $berita->created_at ? $berita->created_at->diffForHumans() : 'Baru saja' }}</span>
                        </div>
                        
                        <h3 class="font-semibold text-sm text-gray-900 mb-1">{{ $berita->judul }}</h3>
                        <p class="text-xs text-gray-600">{{ \Illuminate\Support\Str::limit($berita->isi_berita, 80) }}</p>
                    </div>
                    @endforeach

                </div>
            </div>
            
            <!-- Kolom Kanan: AI Impact Analysis -->
            <div class="w-8/12 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col h-full overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
                    <h2 class="font-bold text-gray-800">AI Impact Analysis</h2>
                    <span class="text-xs text-gray-500 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span> AI Engine Active
                    </span>
                </div>
                <div class="p-8 flex-1 overflow-y-auto">
                    
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 leading-tight">Kredit Konsumsi Bank Besar Tembus Rekor di Kuartal III</h3>
                        <p class="text-sm text-gray-500 mt-2">Sumber: Bisnis.com • Diindeks: Hari ini, 09:45 WIB</p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 mb-8">
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Ringkasan AI</h4>
                        <p class="text-sm text-gray-700">Artikel ini membahas peningkatan signifikan pada penyaluran kredit konsumsi oleh bank-bank berkapitalisasi besar. Hal ini mengindikasikan daya beli masyarakat yang menguat dan potensi kenaikan margin bunga bersih (NIM) bagi sektor perbankan.</p>
                    </div>

                    <h4 class="text-sm font-bold text-gray-800 mb-4">Emiten Terdampak</h4>
                    
                    <!-- Kartu Emiten -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-5 rounded-xl border border-gray-200 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-2 h-full bg-green-500"></div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-2xl font-bold text-gray-900">BBCA</span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full">POSITIF (92%)</span>
                            </div>
                            <p class="text-xs text-gray-600">Potensi peningkatan pendapatan dari segmen kredit KPR dan kendaraan bermotor sejalan dengan tren makro.</p>
                        </div>

                        <div class="p-5 rounded-xl border border-gray-200 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-2 h-full bg-green-500"></div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-2xl font-bold text-gray-900">BMRI</span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full">POSITIF (85%)</span>
                            </div>
                            <p class="text-xs text-gray-600">Eksposur kuat pada kredit konsolidasi ritel memberikan katalis positif pada laporan keuangan kuartal berikutnya.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>
</body>
</html>