<x-petugas-layout>

    <div class="p-6">

        {{-- Title --}}
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">Dashboard Petugas</h1>
            <p class="text-sm text-gray-500">Ringkasan data dan aktivitas verifikasi balita</p>
        </div>

        {{-- Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Total Balita --}}
            <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Balita</p>
                    <h2 class="text-3xl font-bold text-gray-800">{{ $totalBalita}}</h2>
                    <p class="text-xs text-gray-400 mt-1">dari seluruh posyandu</p>
                </div>
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                    <x-lucide-baby class="w-6 h-6" />
                </div>
            </div>

            {{-- Balita Laki-laki --}}
            <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Balita Laki-laki</p>
                    <h2 class="text-3xl font-bold text-gray-800">{{ $jumlahLakiLaki}}</h2>
                    <p class="text-xs text-gray-400 mt-1">dari seluruh posyandu</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <x-lucide-mars class="w-6 h-6" />
                </div>
            </div>

            {{-- Balita Perempuan --}}
            <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Balita Perempuan</p>
                    <h2 class="text-3xl font-bold text-gray-800">{{ $jumlahPerempuan}}</h2>
                    <p class="text-xs text-gray-400 mt-1">dari seluruh posyandu</p>
                </div>
                <div class="w-12 h-12 bg-pink-100 text-pink-500 rounded-lg flex items-center justify-center">
                    <x-lucide-venus class="w-6 h-6" />
                </div>
            </div>

        </div>

    </div>

</x-petugas-layout>