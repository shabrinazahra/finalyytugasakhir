<x-kader-layout>

    <div class="p-6">

        {{-- HEADER --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Perhitungan Penilaian Balita</h1>
                <p class="text-sm text-gray-500">
                    Transparansi kalkulasi berdasarkan perhitungan AHP dan MOORA untuk menentukan penanganan prioritas
                    balita berisiko stunting.
                </p>
            </div>

            {{-- FILTER PERIODE --}}
            <form action="{{ route('kader.perhitungan.index') }}" method="GET" class="flex items-center gap-2">
                <select name="periode" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B3C53] outline-none bg-white font-medium transition shadow-sm">
                    @forelse ($periodes as $key => $label)
                        <option value="{{ $key }}" {{ $selectedPeriode == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @empty
                        <option value="">Tidak ada data penilaian</option>
                    @endforelse
                </select>
            </form>
        </div>

        @if (empty($decisionMatrix))
            <div class="bg-white rounded-2xl border shadow-sm p-12 text-center text-gray-400">
                <div class="flex flex-col items-center justify-center gap-2">
                    <x-lucide-calculator class="w-12 h-12 text-gray-300" />
                    <span class="font-bold text-lg text-gray-500">Data tidak ada</span>
                    <span class="text-xs text-gray-400">Belum ada penilaian balita yang lengkap pada periode
                        ini. Pastikan kader telah menginputkan penilaian seluruh kriteria
                        balita.</span>
                </div>
            </div>
        @else
            {{-- LANGKAH 1: Matriks Keputusan --}}
            <div class="mb-8">
                <div class="mb-3 flex items-center gap-2">
                    <span
                        class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">1</span>
                    <h2 class="text-lg font-bold text-gray-800">Matriks Keputusan (X)</h2>
                </div>
                <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                    Matriks keputusan berisi skor kategori penilaian (5, 3, atau 1) yang diinputkan untuk masing-masing
                    kriteria.
                </p>
                <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">Nama Balita</th>
                                    @foreach ($kriterias as $k)
                                        <th class="px-4 py-3 text-center" title="{{ $k->nama_kriteria }}">
                                            {{ $k->kode_kriteria }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y text-gray-700">
                                @foreach ($decisionMatrix as $balitaId => $scores)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-800">
                                            {{ $alternatives[$balitaId]->nama }}
                                        </td>
                                        @foreach ($kriterias as $k)
                                            <td class="px-4 py-3 text-center font-medium">{{ $scores[$k->id] }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- LANGKAH 2: Matriks Ternormalisasi --}}
            <div class="mb-8">
                <div class="mb-3 flex items-center gap-2">
                    <span
                        class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">2</span>
                    <h2 class="text-lg font-bold text-gray-800">Matriks Keputusan Ternormalisasi (X*)</h2>
                </div>
                <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                    Setiap sel dibagi dengan akar dari jumlah kuadrat seluruh nilai alternatif pada kriteria tersebut
                    untuk mereduksi satuan dimensi.
                </p>
                <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">Nama Balita</th>
                                    @foreach ($kriterias as $k)
                                        <th class="px-4 py-3 text-center" title="{{ $k->nama_kriteria }}">
                                            {{ $k->kode_kriteria }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y text-gray-700">
                                @foreach ($normalizedMatrix as $balitaId => $scores)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-800">
                                            {{ $alternatives[$balitaId]->nama }}
                                        </td>
                                        @foreach ($kriterias as $k)
                                            <td class="px-4 py-3 text-center font-mono text-xs text-gray-600">
                                                {{ number_format($scores[$k->id], 3) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- LANGKAH 3: Matriks Ternormalisasi Terbobot --}}
            <div class="mb-8">
                <div class="mb-3 flex items-center gap-2">
                    <span
                        class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">3</span>
                    <h2 class="text-lg font-bold text-gray-800">Matriks Ternormalisasi Terbobot (Weighted X*)</h2>
                </div>
                <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                    Setiap nilai ternormalisasi dikalikan dengan bobot kriteria AHP hasil perbandingan petugas.
                    <strong>Weighted X* = X* × Bobot AHP</strong>
                </p>
                <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            {{-- Hitung firstBalitaId SEKALI di luar loop kolom --}}
                            @php $firstBalitaId = array_key_first($bobotMatrix ?? []); @endphp
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">Nama Balita</th>
                                    @foreach ($kriterias as $k)
                                        {{-- Bobot dari snapshot baris pertama, fallback ke live $k->bobot --}}
                                        @php
                                            $displayBobot =
                                                $firstBalitaId !== null
                                                    ? $bobotMatrix[$firstBalitaId][$k->id] ?? ($k->bobot ?? 0)
                                                    : $k->bobot ?? 0;
                                        @endphp
                                        <th class="px-4 py-3 text-center"
                                            title="{{ $k->nama_kriteria }} | Bobot: {{ number_format($displayBobot, 3) }}">
                                            {{ $k->kode_kriteria }}
                                            <span class="block text-[10px] font-normal text-gray-400 font-mono">
                                                w: {{ number_format($displayBobot, 3) }}
                                            </span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y text-gray-700">
                                @foreach ($weightedMatrix as $balitaId => $scores)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-800">
                                            {{ $alternatives[$balitaId]->nama }}
                                        </td>
                                        @foreach ($kriterias as $k)
                                            <td class="px-4 py-3 text-center font-mono text-xs text-gray-600">
                                                {{ number_format($scores[$k->id], 3) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabel verifikasi: Normalisasi × Bobot (untuk transparansi) --}}
                <div class="mt-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-xs text-blue-700">
                    <strong>Catatan:</strong> Nilai Weighted = Nilai Ternormalisasi (X*) × Bobot AHP.
                    Contoh baris pertama:
                    @php
                        $firstId = array_key_first($normalizedMatrix ?? []);
                        $examples = [];
                        if ($firstId !== null) {
                            foreach ($kriterias->take(3) as $k) {
                                $norm = $normalizedMatrix[$firstId][$k->id] ?? 0;
                                $bobot = $bobotMatrix[$firstId][$k->id] ?? ($k->bobot ?? 0);
                                $wv = $weightedMatrix[$firstId][$k->id] ?? 0;
                                $examples[] =
                                    $k->kode_kriteria .
                                    ': ' .
                                    number_format($norm, 3) .
                                    ' × ' .
                                    number_format($bobot, 3) .
                                    ' = ' .
                                    number_format($wv, 3);
                            }
                        }
                    @endphp
                    {{ implode(' | ', $examples) }}
                </div>
            </div>

            {{-- LANGKAH 4: Nilai Optimasi Yi --}}
            <div class="mb-8 mt-8">
                <div class="mb-3 flex items-center gap-2">
                    <span
                        class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">4</span>
                    <h2 class="text-lg font-bold text-gray-800">Nilai Optimasi MOORA (Yi)</h2>
                </div>
                <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                    Yi dihitung dari penjumlahan nilai weighted untuk kriteria bertipe <strong>Benefit</strong>
                    dikurangi penjumlahan nilai weighted untuk kriteria bertipe <strong>Cost</strong>.
                    Semakin tinggi Yi, semakin tinggi prioritas penanganan balita.
                </p>

                <div
                    class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 mb-4 text-xs text-indigo-800 font-mono">
                    Yi = Σ (Weighted Benefit) − Σ (Weighted Cost)
                </div>

                <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">Nama Balita</th>
                                    <th class="px-4 py-3 text-center">Σ Benefit</th>
                                    <th class="px-4 py-3 text-center">Σ Cost</th>
                                    <th class="px-4 py-3 text-center">Yi = Benefit − Cost</th>
                                    <th class="px-4 py-3 text-center">Rank</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-gray-700">
                                @foreach ($results as $index => $row)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $row['balita']->nama }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-mono text-xs text-green-600">
                                            {{ number_format($row['sum_benefit'], 3) }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-mono text-xs text-red-500">
                                            {{ number_format($row['sum_cost'], 3) }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-[#1B3C53] font-mono">
                                            {{ number_format($row['nilai_akhir'], 3) }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700">
                                            {{ $index + 1 }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @endif

    </div>

</x-kader-layout>
