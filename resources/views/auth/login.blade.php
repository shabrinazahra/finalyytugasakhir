<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="{{ asset('storage/images/logo.png') }}" type="image/x-icon">
    <title>Login</title>
    @vite(['resources/css/app.css','resources/js/pages/login.js'])
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

            {{-- FORM --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- ERROR: EMAIL SALAH / TIDAK TERDAFTAR --}}
                @error('email')
                <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                    <x-lucide-circle-alert class="w-4 h-4 shrink-0" />
                    <span>{{ $message }}</span>
                </div>
                @enderror

                {{-- ERROR: PASSWORD SALAH --}}
                @error('password')
                <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                    <x-lucide-circle-alert class="w-4 h-4 shrink-0" />
                    <span>{{ $message }}</span>
                </div>
                @enderror

                {{-- INPUT EMAIL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Masukkan email"
                            required
                            class="w-full px-4 py-2.5 border rounded-xl outline-none transition
                                   focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53]
                                   {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <x-lucide-mail class="w-4 h-4" />
                        </div>
                    </div>
                </div>

                {{-- INPUT PASSWORD --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Masukkan password"
                            required
                            class="w-full px-4 py-2.5 border rounded-xl outline-none transition pr-10
                                   focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53]
                                   {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">

                        {{-- Toggle show/hide password --}}
                        <button type="button" id="togglePasswordBtn"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                            <span id="eyeClosed">
                                <x-lucide-eye-off class="w-4 h-4" />
                            </span>
                            <span id="eyeOpen" class="hidden">
                                <x-lucide-eye class="w-4 h-4" />
                            </span>
                        </button>
                    </div>
                </div>

                {{-- TOMBOL LOGIN --}}
                <button type="submit"
                    class="w-full bg-[#1B3C53] text-white py-2.5 rounded-xl font-semibold
                           hover:bg-[#234C6A] transition shadow-sm">
                    Login
                </button>

                {{-- KEMBALI KE HOME --}}
                <div class="text-center pt-2">
                    <a href="/"
                        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#1B3C53] transition">
                        Kembali ke Home
                    </a>
                </div>

            </form>

        </div>

    </div>

</body>

</html>