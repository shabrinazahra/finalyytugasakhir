<x-master-admin-layout>

    <div class="p-6">

        {{-- Title --}}
        <div class="mb-6">
            <p class="text-sm  text-gray-800 uppercase tracking-wider">
                Dashboard
            </p>

            <h1 class="text-2xl font-semibold text-gray-800 mt-1">
                Sistem Pendukung Keputusan Prioritas Penanganan Balita Berisiko Stunting
            </h1>
        </div>

        {{-- Card --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Kader --}}
            <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center justify-between">

                <div>
                    <p class="text-sm text-gray-500 mb-1">
                        Jumlah Kader Posyandu
                    </p>
                    <h2 class="text-3xl font-bold text-gray-800">
                        {{ $jumlahKader }}
                    </h2>
                </div>

                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                    <x-lucide-users class="w-6 h-6" />
                </div>

            </div>

            {{-- Petugas --}}
            <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center justify-between">

                <div>
                    <p class="text-sm text-gray-500 mb-1">
                        Jumlah Petugas Kesehatan
                    </p>
                    <h2 class="text-3xl font-bold text-gray-800">
                        {{ $jumlahPetugas }}
                    </h2>
                </div>

                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <x-lucide-user class="w-6 h-6" />
                </div>

            </div>

            {{-- Posyandu --}}
            <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center justify-between">

                <div>
                    <p class="text-sm text-gray-500 mb-1">
                        Jumlah Posyandu
                    </p>
                    <h2 class="text-3xl font-bold text-gray-800">
                        {{ $jumlahPosyandu }}
                    </h2>
                </div>

                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                    <x-lucide-building-2 class="w-6 h-6" />
                </div>

            </div>

        </div>

    </div>

</x-master-admin-layout>