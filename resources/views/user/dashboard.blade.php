<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockSense AI - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800">

    <nav class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">StockSense AI</h1>

        <a href="{{ route('login') }}" class="bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700">
            Login Admin
        </a>
    </nav>

    <main class="max-w-6xl mx-auto p-6">

        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-2">
                Analisis Sentimen Pasar Saham
            </h2>

            <p class="text-gray-600">
                Pantau berita pasar dan hasil analisis sentimen menggunakan AI.
            </p>
        </section>

        <!-- Data Saham -->
        <section class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Data Saham</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse ($daftar_saham as $saham)
                    <div class="bg-white p-5 rounded-xl shadow-sm">
                        <h4 class="font-bold">
                            {{ $saham->company_name }}
                        </h4>

                        <p class="text-sm text-gray-500 mt-2">
                            {{ $saham->ticker_code }}
                        </p>

                        <p class="text-sm text-gray-600 mt-1">
                            {{ $saham->sector ?? 'Sektor belum tersedia' }}
                        </p>
                    </div>
                @empty
                    <p>Belum ada data saham.</p>
                @endforelse
            </div>
        </section>

        <!-- Berita dan Sentimen AI -->
        <section class="mb-8">
            <h3 class="text-xl font-semibold mb-4">
                Berita dan Sentimen AI
            </h3>

            <div class="space-y-4">
                @forelse ($daftar_berita as $berita)
                    <article class="bg-white p-5 rounded-xl shadow-sm">
                        <h4 class="font-bold text-lg">
                            {{ $berita->title }}
                        </h4>

                        <p class="text-gray-600 text-sm mt-2">
                            {{ \Illuminate\Support\Str::limit($berita->content, 180) }}
                        </p>

                        <p class="mt-3 text-sm">
                            Sentimen:
                            <span class="font-semibold">
                                {{ $berita->label_ai ?? 'Belum dianalisis' }}
                            </span>
                        </p>
                    </article>
                @empty
                    <p>Belum ada berita yang tersedia.</p>
                @endforelse
            </div>
        </section>

    </main>

    <footer class="text-center text-sm text-gray-500 py-6">
        &copy; {{ date('Y') }} StockSense AI
    </footer>

</body>

</html>