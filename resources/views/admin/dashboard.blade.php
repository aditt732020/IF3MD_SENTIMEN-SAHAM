<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StockSense AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 text-slate-800 flex h-screen overflow-hidden font-sans">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-950 text-white flex flex-col shrink-0">

        <div class="h-20 flex items-center px-6 border-b border-slate-800">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none"
                    stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 17l6-6 4 4 8-8"/>
                </svg>
            </div>

            <div>
                <h1 class="font-bold text-lg">StockSense AI</h1>
                <p class="text-xs text-slate-400">Admin Dashboard</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">

            <p class="px-4 mb-3 text-xs uppercase tracking-widest text-slate-500">
                Menu Utama
            </p>

            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold shadow-lg shadow-blue-950/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                AI Impact Radar
            </a>

            <a href="#database-emiten"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <ellipse cx="12" cy="5" rx="9" ry="3"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"/>
                </svg>
                Database Emiten
            </a>

            <a href="#log-sentimen"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                </svg>
                Log Sentimen
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800">
            <div class="rounded-xl bg-slate-900 border border-slate-800 p-4">
                <p class="text-sm font-semibold text-white">StockSense AI</p>
                <p class="text-xs text-slate-400 mt-1">
                    AI-Powered Market Analysis
                </p>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 min-w-0 flex flex-col h-full overflow-hidden">

        <!-- Header -->
        <header class="min-h-20 bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex flex-wrap items-center justify-between gap-4">

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                    Admin Workspace
                </p>
                <h2 class="text-xl font-bold text-slate-900 mt-1">
                    AI Impact Radar
                </h2>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-semibold text-slate-800">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>

                <div class="w-10 h-10 rounded-full bg-blue-100 border border-blue-200 flex items-center justify-center text-blue-700 font-bold">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none"
                            stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-6 lg:p-8">

            <!-- Hero Banner -->
            <section id="dashboard" class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 to-blue-900 text-white p-7 md:p-9 mb-8 shadow-lg">

                <div class="relative z-10">
                    <p class="text-blue-300 text-xs font-bold tracking-widest mb-3">
                        STOCK MARKET NEWS ANALYSIS
                    </p>

                    <h2 class="text-2xl md:text-3xl font-bold mb-3">
                        Selamat Datang di StockSense AI
                    </h2>

                    <p class="text-slate-300 text-sm md:text-base max-w-2xl leading-relaxed">
                        Pantau berita pasar, lihat data emiten, dan persiapkan
                        analisis sentimen saham melalui satu dashboard.
                    </p>

                    <a href="#log-sentimen"
                        class="inline-flex items-center gap-2 mt-5 bg-white text-slate-900 px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-50 transition">
                        Lihat Berita Terbaru
                        <span>&rarr;</span>
                    </a>
                </div>
            </section>

            <!-- Summary Cards -->
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">

                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-slate-500">Total Emiten</p>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"/>
                            </svg>
                        </div>
                    </div>

                    <p class="text-3xl font-bold text-slate-900">
                        {{ $daftar_saham->count() }}
                    </p>
                    <p class="text-sm text-slate-500 mt-2">
                        Data saham tersedia
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-slate-500">Total Berita</p>
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 8h10M7 12h10M7 16h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                            </svg>
                        </div>
                    </div>

                    <p class="text-3xl font-bold text-slate-900">
                        {{ $daftar_berita->count() }}
                    </p>
                    <p class="text-sm text-slate-500 mt-2">
                        Berita tersimpan di database
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-slate-500">Analisis AI</p>
                        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01"/>
                            </svg>
                        </div>
                    </div>

                    <p class="text-2xl font-bold text-amber-600">
                        Belum Aktif
                    </p>
                    <p class="text-sm text-slate-500 mt-2">
                        Menunggu integrasi layanan AI
                    </p>
                </div>
            </section>

            <!-- Main Panels -->
            <section class="grid grid-cols-1 xl:grid-cols-5 gap-6">

                <!-- News Stream -->
                <div id="log-sentimen" class="xl:col-span-2 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

                    <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900">Live News Stream</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Berita terbaru dari database
                            </p>
                        </div>

                        <span class="text-xs font-semibold bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full">
                            {{ $daftar_berita->count() }} Berita
                        </span>
                    </div>

                    <div class="p-4 space-y-4 max-h-[650px] overflow-y-auto">

                        @forelse ($daftar_berita as $berita)
                            <article class="border border-slate-200 rounded-xl p-4 hover:border-blue-300 hover:shadow-sm transition">

                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                    <span class="text-xs font-semibold bg-blue-50 text-blue-700 px-3 py-1 rounded-full">
                                        Berita Pasar
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        {{ $berita->published_at
                                            ? \Illuminate\Support\Carbon::parse($berita->published_at)->format('d M Y')
                                            : 'Tanggal belum tersedia' }}
                                    </span>
                                </div>

                                <h4 class="font-bold text-sm text-slate-900 leading-relaxed mb-2">
                                    {{ $berita->title }}
                                </h4>

                                <p class="text-xs text-slate-600 leading-relaxed">
                                    {{ \Illuminate\Support\Str::limit($berita->content ?? '', 120) }}
                                </p>

                                <div class="mt-3 pt-3 border-t border-slate-100">
                                    <span class="text-xs text-amber-700">
                                        Analisis: Belum tersedia
                                    </span>
                                </div>

                                @if ($berita->source_url)
                                    <a href="{{ $berita->source_url }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="inline-block mt-3 text-xs font-semibold text-blue-700 hover:text-blue-900 hover:underline">
                                        Baca berita &rarr;
                                    </a>
                                @endif
                            </article>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7 8h10M7 12h10M7 16h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                    </svg>
                                </div>
                                <p class="font-semibold text-slate-700">Belum ada berita</p>
                                <p class="text-sm text-slate-500 mt-1">
                                    Berita akan ditampilkan di sini.
                                </p>
                            </div>
                        @endforelse

                    </div>
                </div>

                <!-- Emiten and AI Analysis -->
                <div id="database-emiten" class="xl:col-span-3 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

                    <div class="p-5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-slate-900">AI Impact Analysis</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Data emiten dan kesiapan analisis
                            </p>
                        </div>

                        <span class="inline-flex items-center gap-2 text-xs font-semibold bg-amber-50 text-amber-700 px-3 py-1.5 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            AI Belum Aktif
                        </span>
                    </div>

                    <div class="p-5">

                        <!-- AI Information -->
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 mb-6">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 shrink-0 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4m0 4h.01"/>
                                    </svg>
                                </div>

                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">
                                        Ringkasan Analisis
                                    </h4>
                                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                        Layanan analisis sentimen AI belum aktif.
                                        Data berita dan emiten sudah dapat ditampilkan.
                                        Hasil sentimen dan dampak berita terhadap saham
                                        akan tersedia setelah integrasi layanan AI selesai.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Stock List -->
                        <div class="mb-4">
                            <h4 class="font-bold text-slate-900">Daftar Emiten Saham</h4>
                            <p class="text-xs text-slate-500 mt-1">
                                Informasi perusahaan yang tersimpan dalam sistem.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            @forelse ($daftar_saham as $saham)
                                <article class="border border-slate-200 rounded-xl p-5 hover:border-blue-300 hover:shadow-md transition">

                                    <div class="w-11 h-11 bg-blue-50 text-blue-700 rounded-lg flex items-center justify-center font-bold mb-4">
                                        {{ substr($saham->ticker_code, 0, 2) }}
                                    </div>

                                    <h5 class="text-lg font-bold text-slate-900">
                                        {{ $saham->company_name }}
                                    </h5>

                                    <p class="text-blue-700 font-semibold mt-1">
                                        {{ $saham->ticker_code }}
                                    </p>

                                    <div class="mt-4 pt-3 border-t border-slate-100">
                                        <p class="text-xs text-slate-500">Sektor</p>
                                        <p class="text-sm font-medium text-slate-700 mt-1">
                                            {{ $saham->sector ?? 'Belum tersedia' }}
                                        </p>
                                    </div>

                                    <div class="mt-3">
                                        <span class="inline-flex items-center gap-2 text-xs text-slate-500">
                                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                            Menunggu analisis AI
                                        </span>
                                    </div>
                                </article>
                            @empty
                                <div class="sm:col-span-2 border border-dashed border-slate-300 rounded-xl p-8 text-center">
                                    <p class="font-semibold text-slate-700">
                                        Belum ada data saham
                                    </p>
                                    <p class="text-sm text-slate-500 mt-1">
                                        Data emiten akan muncul setelah tersedia di database.
                                    </p>
                                </div>
                            @endforelse

                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="mt-8 py-5 border-t border-slate-200 flex flex-col sm:flex-row justify-between gap-2 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} StockSense AI.</p>
                <p>AI-Powered Market Analysis</p>
            </footer>

        </div>
    </main>
    
        </div>
    </main>

    <!-- Script untuk menu navbar aktif -->
    <script>
        const menuLinks = document.querySelectorAll('aside nav a');

        function setActiveMenu(activeLink) {
            menuLinks.forEach(link => {
                link.classList.remove(
                    'bg-blue-600',
                    'text-white',
                    'font-semibold',
                    'shadow-lg',
                    'shadow-blue-950/30'
                );

                link.classList.add(
                    'text-slate-300',
                    'hover:bg-slate-800',
                    'hover:text-white',
                    'transition'
                );
            });

            activeLink.classList.remove(
                'text-slate-300',
                'hover:bg-slate-800',
                'hover:text-white',
                'transition'
            );

            activeLink.classList.add(
                'bg-blue-600',
                'text-white',
                'font-semibold',
                'shadow-lg',
                'shadow-blue-950/30'
            );
        }

        menuLinks.forEach(link => {
            link.addEventListener('click', function () {
                setActiveMenu(this);
            });
        });
    </script>
</body>
</html>

</body>
</html>