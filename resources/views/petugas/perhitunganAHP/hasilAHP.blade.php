<x-petugas-layout>

    <div class="p-6 max-w-6xl mx-auto space-y-6">

        <h2 class="text-xl font-bold text-gray-800">
            Hasil Perhitungan AHP
        </h2>

        {{-- 1. MATRIKS PERBANDINGAN --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">
                    1. Matriks Perbandingan & Jumlah Kolom
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-center">
                    <thead class="bg-[#1B3C53] text-white">
                        <tr>
                            <th class="p-3">Kriteria</th>
                            @foreach ($kriterias as $k)
                                <th class="p-3">{{ $k->kode_kriteria }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach ($kriterias as $i)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 font-semibold bg-gray-50">
                                    {{ $i->kode_kriteria }}
                                </td>

                                @foreach ($kriterias as $j)
                                    <td class="p-3">
                                        {{ number_format($matrix[$i->id][$j->id], 3) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        {{-- JUMLAH KOLOM --}}
                        <tr class="bg-blue-50 font-bold">
                            <td class="p-3">Jumlah</td>
                            @foreach ($kriterias as $k)
                                <td class="p-3 text-blue-700">
                                    {{ number_format($jumlahKolom[$k->id], 3) }}
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 2. NORMALISASI --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">
                    2. Normalisasi
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-center">
                    <thead class="bg-[#1B3C53] text-white">
                        <tr>
                            <th class="p-3">Kriteria</th>
                            @foreach ($kriterias as $k)
                                <th class="p-3">{{ $k->kode_kriteria }}</th>
                            @endforeach
                            <th class="p-3">Jumlah</th>
                            <th class="p-3">Bobot</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach ($kriterias as $i)
                            <tr class="hover:bg-gray-50">

                                <td class="p-3 font-semibold bg-gray-50">
                                    {{ $i->kode_kriteria }}
                                </td>

                                @foreach ($kriterias as $j)
                                    <td class="p-3">
                                        {{ number_format($normalisasi[$i->id][$j->id], 3) }}
                                    </td>
                                @endforeach

                                <td class="p-3 font-semibold text-blue-600">
                                    {{ number_format($jumlahBaris[$i->id], 3) }}
                                </td>

                                <td class="p-3 font-bold text-green-600">
                                    {{ number_format($bobot[$i->id], 3) }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 3. KONSISTENSI --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">
                    3. Uji Konsistensi (Jumlah Per Baris)
                </h3>
            </div>

            <div class="p-4 space-y-2">
                @foreach ($kriterias as $k)
                    <div class="flex justify-between bg-gray-50 p-3 rounded-lg border">
                        <span>{{ $k->nama_kriteria }}</span>
                        <span class="font-semibold text-indigo-600">
                            {{ number_format($konsistensi[$k->id], 3) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 4. LAMBDA MAX --}}
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">
                4. Nilai Eigen Maksimum (λ max)
            </h3>

            <p class="text-lg font-bold text-purple-600">
                {{ number_format($lambdaMax, 3) }}
            </p>
        </div>

        {{-- 5. CI --}}
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">
                5. Consistency Index (CI)
            </h3>

            <p class="text-lg font-bold text-orange-600">
                {{ number_format($CI, 3) }}
            </p>
        </div>

        {{-- 6. CR --}}
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">
                6. Consistency Ratio (CR)
            </h3>

            <p class="text-lg font-bold 
            {{ $CR <= 0.1 ? 'text-green-600' : 'text-red-600' }}">

                {{ number_format($CR, 3) }}

                <span class="text-sm ml-2">
                    ({{ $CR <= 0.1 ? 'Konsisten (Sah)' : 'Tidak Konsisten (Admin Harus Mengisi Ulang)' }})
                </span>
            </p>
        </div>

        {{-- BUTTON --}}
        <div class="flex justify-center gap-4 mt-6">

            <a href="{{ route('petugas.perhitunganAHP.index') }}"
                class="bg-gray-500 text-white px-5 py-2.5 rounded-xl hover:bg-gray-600 transition flex items-center gap-2">
                ← Kembali
            </a>

            @if ($CR <= 0.1)
                <form action="{{ route('petugas.perhitunganAHP.saveWeights') }}" method="POST">
                    @csrf
                    @foreach($bobot as $id => $val)
                        <input type="hidden" name="bobot[{{ $id }}]" value="{{ $val }}">
                    @endforeach
                    <button type="submit" class="bg-[#1B3C53] text-white px-5 py-2.5 rounded-xl hover:bg-[#244E6B] transition flex items-center gap-2">
                        <x-lucide-save class="w-4 h-4" />
                        Simpan Hasil
                    </button>
                </form>
            @else
                <button disabled class="bg-gray-300 text-gray-500 px-5 py-2.5 rounded-xl cursor-not-allowed flex items-center gap-2"
                    title="Nilai rasio konsistensi di atas 10%, harap isi ulang nilai perbandingan.">
                    <x-lucide-ban class="w-4 h-4" />
                    Simpan Hasil (Tidak Sah)
                </button>
            @endif

        </div>

    </div>

</x-petugas-layout>
