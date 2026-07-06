<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('storage/images/logo.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kader - {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-gradient-to-b from-[#1B3C53] to-[#142F40] text-white fixed top-0 left-0 h-screen flex flex-col shadow-xl">

            {{-- Logo --}}
            <div class="p-6 flex flex-col items-center border-b border-white/10">
                <img src="{{ asset('storage/images/logo.png') }}" class="h-14 mb-2">
            </div>

            {{-- Menu --}}
            <nav class="flex-1 p-4 space-y-2 text-sm">

                <a href="{{ route('kader.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                {{ request()->routeIs('kader.dashboard') ? 'bg-white/10 font-semibold' : 'hover:bg-white/10' }}">
                    <x-lucide-layout-dashboard class="w-5 h-5" />
                    Dashboard
                </a>

                <a href="{{ route('balita.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                {{ request()->routeIs('balita.*') ? 'bg-white/10 font-semibold' : 'hover:bg-white/10' }}">
                    <x-lucide-baby class="w-5 h-5" />
                    Data Balita
                </a>

                <a href="{{ route('penilaian_balita.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                {{ request()->routeIs('penilaian_balita.*') ? 'bg-white/10 font-semibold' : 'hover:bg-white/10' }}">
                    <x-lucide-star class="w-5 h-5" />
                    Penilaian Balita
                </a>

                <div class="pt-3 border-t border-white/10"></div>

                <a href="{{ route('kader.perhitungan.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                    {{ request()->routeIs('kader.perhitungan.*') ? 'bg-white/10 font-semibold' : 'hover:bg-white/10' }}">
                    <x-lucide-calculator class="w-5 h-5" />
                     Kalkulasi Penilaian Balita  
                </a>

                <a href="{{ route('kader.perangkingan.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                    {{ request()->routeIs('kader.perangkingan.*') ? 'bg-white/10 font-semibold' : 'hover:bg-white/10' }}">
                    <x-lucide-chart-no-axes-column-decreasing class="w-5 h-5" />
                    Perangkingan
                </a>

                <a href="{{ route('kader.laporan.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                    {{ request()->routeIs('kader.laporan.*') ? 'bg-white/10 font-semibold' : 'hover:bg-white/10' }}">
                    <x-lucide-file-text class="w-5 h-5" />
                    Laporan
                </a>

            </nav>

            {{-- Footer --}}
            <div class="p-4 text-xs text-white/50 text-center border-t border-white/10">
                © {{ date('Y') }} SPK Balita
            </div>

        </aside>

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col ml-64">

            {{-- TOPBAR --}}
            <nav class="fixed top-0 left-64 right-0 z-40 bg-white px-6 py-4 flex items-center justify-between border-b shadow-sm">

                {{-- POSYANDU --}}
                <div class="text-sm text-gray-500 uppercase tracking-wide">
                    <span class="text-gray-800 font-semibold">
                        {{ Auth::user()->posyandu->nama_posyandu ?? '-' }}
                    </span>
                </div>

                {{-- USER --}}
                <div class="relative">
                    <button id="dropdownBtn"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-100 transition">

                        <div class="w-9 h-9 rounded-full bg-[#1B3C53] flex items-center justify-center">
                            <x-lucide-user class="w-5 h-5 text-white" />
                        </div>

                        <div class="text-left leading-tight">
                            <p class="text-sm font-medium text-gray-700">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Kader Posyandu
                            </p>
                        </div>

                        <x-lucide-chevron-down class="w-4 h-4 text-gray-500" />
                    </button>

                    {{-- Dropdown --}}
                    <div id="dropdownMenu"
                        class="hidden absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border overflow-hidden z-50">

                        {{-- profil --}}
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            <x-lucide-user class="w-4 h-4" />
                            Profil Saya
                        </a>

                        <div class="border-t border-gray-100"></div>

                        {{-- logout --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">

                                <x-lucide-log-out class="w-4 h-4" />
                                Logout
                            </button>
                        </form>

                    </div>
                </div>

            </nav>

            {{-- CONTENT --}}
            <main class="p-6 pt-24 bg-gray-100 flex-1">

                {{-- notifikasi --}}
                @if (session('success'))
                    <div
                        class="mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                        <x-lucide-check-circle class="w-5 h-5" />
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                        <x-lucide-alert-circle class="w-5 h-5" />
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm">
                        <x-lucide-alert-circle class="w-5 h-5 mt-0.5 shrink-0" />
                        <div>
                            <p class="text-sm font-medium mb-1">Terdapat kesalahan:</p>
                            <ul class="text-sm list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- CARD --}}
                <div class="bg-white rounded-2xl shadow-sm border p-6 hover:shadow-md transition">
                    {{ $slot }}
                </div>

            </main>

        </div>

    </div>

</body>

</html>