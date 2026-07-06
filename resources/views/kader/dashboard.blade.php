<x-kader-layout>

    <div class="p-6">

        {{-- HEADER --}}
        <div class="mb-8">
            <p class="text-xs font-semibold text-gray-500 tracking-wide uppercase">
                Dashboard
            </p>

            <h1 class="text-2xl font-bold text-gray-800 mt-1 leading-snug">
                Sistem Pendukung Keputusan Prioritas Penanganan Balita Berisiko Stunting
            </h1>
            <p class="text-sm text-gray-400 mt-1">
                Selamat datang! Silakan kelola data balita dan lakukan penilaian berkala.
            </p>
        </div>

        {{-- CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- TOTAL BALITA --}}
            <div class="bg-white rounded-2xl border shadow-sm p-6 min-h-[150px]
                flex items-center justify-between
                hover:shadow-md transition duration-300">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                        Total Balita
                    </p>

                    <h2 class="text-4xl font-extrabold text-gray-800">
                        {{ $totalBalita }}
                    </h2>
                </div>

                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl
                    flex items-center justify-center shadow-sm shrink-0">
                    <x-lucide-baby class="w-8 h-8" />
                </div>
            </div>

            {{-- LAKI-LAKI --}}
            <div class="bg-white rounded-2xl border shadow-sm p-6 min-h-[150px]
                flex items-center justify-between
                hover:shadow-md transition duration-300">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                        Laki-laki
                    </p>

                    <h2 class="text-4xl font-extrabold text-blue-600">
                        {{ $jumlahLakiLaki }}
                    </h2>
                </div>

                <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl
                    flex items-center justify-center shadow-sm shrink-0">
                    <x-lucide-user class="w-8 h-8" />
                </div>
            </div>

            {{-- PEREMPUAN --}}
            <div class="bg-white rounded-2xl border shadow-sm p-6 min-h-[150px]
                flex items-center justify-between
                hover:shadow-md transition duration-300">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                        Perempuan
                    </p>

                    <h2 class="text-4xl font-extrabold text-pink-500">
                        {{ $jumlahPerempuan }}
                    </h2>
                </div>

                <div class="w-16 h-16 bg-pink-50 text-pink-500 rounded-2xl
                    flex items-center justify-center shadow-sm shrink-0">
                    <x-lucide-user class="w-8 h-8" />
                </div>
            </div>

        </div>

    </div>

</x-kader-layout>