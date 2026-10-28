<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - StockSense AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center p-5">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14
                        bg-blue-100 text-blue-700 rounded-xl text-2xl font-bold mb-4">
                S
            </div>

            <h1 class="text-2xl font-bold text-slate-900">
                StockSense AI
            </h1>

            <p class="text-slate-500 mt-2">
                Login untuk mengakses dashboard admin
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700
                        rounded-lg p-3 mb-5 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="email" class="block text-sm font-semibold mb-2">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Masukkan email admin"
                    required
                    autofocus
                    class="w-full border border-slate-300 rounded-lg px-4 py-3
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold mb-2">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                    class="w-full border border-slate-300 rounded-lg px-4 py-3
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white
                       font-semibold py-3 rounded-lg transition"
            >
                Login Admin
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="{{ route('public.dashboard') }}"
               class="text-sm text-blue-600 hover:underline">
                Kembali ke halaman publik
            </a>
        </div>

    </div>

</body>
</html>
