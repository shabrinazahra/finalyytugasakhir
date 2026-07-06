<x-petugas-layout>

    <div class="p-6 max-w-5xl mx-auto">

        <h2 class="text-lg font-semibold mb-2">
            Bobot Kriteria
        </h2>
        <p class="text-sm text-gray-500 mb-6">
            Bandingkan kriteria untuk menentukan prioritas kepentingan antar kriteria dan menghasilkan bobot kriteria
        </p>

        {{-- INFO BOX --}}
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-2xl p-4 flex gap-3 text-sm text-blue-800">
            <x-lucide-info class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
            <div>
                <p class="font-semibold mb-1">Panduan Pengisian Perbandingan Kriteria (AHP):</p>
                <p class="mb-2 text-blue-700 leading-relaxed">
                    Metode AHP menggunakan perbandingan berpasangan antar kriteria untuk mencari bobot kepentingan
                    masing-masing kriteria. Silakan tentukan slider untuk setiap pasangan kriteria:
                </p>
                <ul class="list-disc list-inside space-y-1 text-blue-700 mb-2">
                    <li>Pilih angka di sebelah <strong class="text-blue-900">Kiri</strong> jika kriteria kiri lebih
                        penting daripada kriteria kanan.</li>
                    <li>Pilih angka di sebelah <strong class="text-blue-900">Kanan</strong> jika kriteria kanan lebih
                        penting daripada kriteria kiri.</li>
                    <li>Pilih angka <strong class="text-blue-900">1 (Tengah)</strong> jika kedua kriteria sama penting.
                    </li>
                </ul>
                <p class="text-xs text-blue-600 font-semibold">
                    * Catatan: Semua perbandingan kriteria wajib diisi (tidak boleh ada yang terlewat) dan wajib disimpan.
                </p>
            </div>
        </div>

        <form action="{{ route('petugas.perhitunganAHP.store') }}" method="POST">
            @csrf

            <div class="space-y-4">

                @foreach ($kriterias as $i => $k1)
                    @foreach ($kriterias as $j => $k2)
                        @if ($i < $j)
                            @php
                                $dbData = \App\Models\Perbandingan::where('kriteria_1', $k1->id)
                                    ->where('kriteria_2', $k2->id)
                                    ->first();
                                $dbValue = $dbData ? $dbData->nilai : null;
                                if ($dbValue !== null && $dbValue < 1 && $dbValue > 0) {
                                    $dbValue = '1/' . round(1 / $dbValue);
                                }

                                $oldValue = old("perbandingan.{$i}{$j}.nilai", $dbValue);
                                $scale_labels = [9, 8, 7, 6, 5, 4, 3, 2, 1, 2, 3, 4, 5, 6, 7, 8, 9];
                                $scale_values = [
                                    9,
                                    8,
                                    7,
                                    6,
                                    5,
                                    4,
                                    3,
                                    2,
                                    1,
                                    '1/2',
                                    '1/3',
                                    '1/4',
                                    '1/5',
                                    '1/6',
                                    '1/7',
                                    '1/8',
                                    '1/9',
                                ];

                                $selectedIndex = array_search($oldValue, $scale_values);
                                if ($selectedIndex === false) {
                                    $selectedIndex = null;
                                }
                            @endphp

                            <div class="bg-white border rounded-xl p-4 shadow-sm">

                                {{-- HEADER --}}
                                <div
                                    class="flex justify-between text-xs font-semibold text-gray-700 mb-3 border-b pb-2">
                                    <span>({{ $k1->kode_kriteria }}) {{ $k1->nama_kriteria }}</span>
                                    <span>({{ $k2->kode_kriteria }}) {{ $k2->nama_kriteria }}</span>
                                </div>

                                {{-- SLIDER WRAPPER FOR MOBILE RESPONSIVENESS --}}
                                <div class="overflow-x-auto select-none py-1 -mx-3 px-3">
                                    <div
                                        class="bg-[#1B3C53] rounded-lg p-2 relative overflow-hidden min-w-[500px] h-10">

                                        {{-- INDICATOR TRANSPARAN --}}
                                        <div id="indicator-{{ $i . $j }}"
                                            class="absolute top-1 bottom-1 w-[24px] bg-white/25 border border-white/60 rounded-md transition-all duration-300 {{ $selectedIndex === null ? 'hidden' : '' }}"
                                            data-index="{{ $selectedIndex }}">
                                        </div>

                                        {{-- ANGKA --}}
                                        <div
                                            class="flex justify-between text-white text-xs font-semibold relative z-10">

                                            @foreach ($scale_labels as $index => $label)
                                                <label class="cursor-pointer w-6 text-center leading-6"
                                                    data-id="{{ $i . $j }}" data-index="{{ $index }}"
                                                    onclick="handleClick(this)">

                                                    <input type="radio"
                                                        name="perbandingan[{{ $i . $j }}][nilai]"
                                                        value="{{ $scale_values[$index] }}" class="hidden"
                                                        {{ $oldValue !== null && $oldValue == $scale_values[$index] ? 'checked' : '' }}>

                                                    {{ $label }}
                                                </label>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>

                                {{-- KETERANGAN --}}
                                <div class="flex justify-between text-[10px] text-gray-400 mt-2 font-medium">
                                    <span>← {{ $k1->nama_kriteria }} lebih penting</span>
                                    <span>{{ $k2->nama_kriteria }} lebih penting →</span>
                                </div>

                                {{-- HIDDEN --}}
                                <input type="hidden" name="perbandingan[{{ $i . $j }}][k1]"
                                    value="{{ $k1->id }}">
                                <input type="hidden" name="perbandingan[{{ $i . $j }}][k2]"
                                    value="{{ $k2->id }}">

                            </div>
                        @endif
                    @endforeach
                @endforeach

            </div>

            {{-- BUTTON --}}
            <div class="mt-8 flex justify-center gap-4">

                <button type="submit"
                    class="bg-[#1B3C53] text-white px-6 py-2.5 text-sm font-semibold rounded-xl shadow-sm hover:bg-[#244E6B] transition flex items-center gap-2">
                    <x-lucide-save class="w-4 h-4" />
                    Simpan Perbandingan
                </button>

        </form>

        <form action="{{ route('petugas.perhitunganAHP.generate') }}" method="GET">
            <button type="submit"
                class="bg-emerald-600 text-white px-6 py-2.5 text-sm font-semibold rounded-xl shadow-sm hover:bg-emerald-700 transition flex items-center gap-2">
                <x-lucide-play class="w-4 h-4" />
                Hitung Bobot Kriteria
            </button>
        </form>

    </div>
    </div>

    {{-- SCRIPT --}}
    @push('scripts')
        @vite('resources/js/pages/perhitungan-ahp.js')
    @endpush

</x-petugas-layout>
