<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="{{ asset('storage/images/logo.png') }}" type="image/x-icon">
    <title>Lupa Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200">

    <div class="min-h-screen flex items-center justify-center">

        <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md">

            {{-- LOGO --}}
            <div class="text-center mb-8">
                <img src="{{ asset('storage/images/logo.png') }}" class="mx-auto w-16 mb-3">
                <h2 class="text-xl font-bold text-[#1B3C53]">
                    Sistem Pendukung Keputusan
                </h2>
                <p class="text-sm text-gray-500">
                    Prioritas Penanganan Balita Berisiko Stunting
                </p>
            </div>

            {{-- SESSION STATUS --}}
            @if (session('status'))
            <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-center">
                {{ session('status') }}
            </div>
            @endif

            {{-- INFO --}}
            <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">
                Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
            </p>

            {{-- FORM --}}
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>

                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl
                                   focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53]
                                   outline-none transition"
                            placeholder="Masukkan email" required autofocus>

                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <x-lucide-mail class="w-4 h-4" />
                        </div>
                    </div>

                    @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- BUTTON --}}
                <button type="submit"
                    class="w-full bg-[#1B3C53] text-white py-2.5 rounded-xl font-semibold
                           hover:bg-[#234C6A] transition shadow-sm">
                    Kirim Tautan Reset Password
                </button>

                {{-- LINK --}}
                <div class="text-center">
                    <a href="{{ route('login') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 hover:underline transition">
                        Kembali ke Login
                    </a>
                </div>

            </form>

        </div>

    </div>

</body>

</html>