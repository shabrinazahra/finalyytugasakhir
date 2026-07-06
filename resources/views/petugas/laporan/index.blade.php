<x-petugas-layout>

    <div class="p-6 print-container">

        {{-- HEADER --}}
        <div class="flex justify-between items-start mb-6 no-print">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Laporan Prioritas Penanganan Balita </h1>
                <p class="text-sm text-gray-500">
                    Laporan Prioritas Penanganan Balita Berisiko Stunting
                </p>
            </div>

            @if (!empty($results))
                <button onclick="window.print()"
                    class="bg-[#1B3C53] text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#244E6B] transition shadow-sm flex items-center gap-2">
                    <x-lucide-printer class="w-4 h-4" />
                    Cetak Laporan
                </button>
            @endif
        </div>

        {{-- KOP SURAT (HANYA MUNCUL SAAT DI-PRINT) --}}
        <div class="hidden print:flex flex-col items-center text-center border-b-2 border-gray-800 pb-4 mb-6">
            <h1 class="text-2xl font-black uppercase tracking-widest text-indigo-900 mt-1">Laporan Prioritas Prioritas
                Penanganan Balita Berisiko Stunting</h1>
            <p class="text-xs text-gray-500 mt-2">
                Dicetak pada: {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y H:i') }}
            </p>
        </div>

        {{-- WARNING UNTUK BOBOT AHP YANG BELUM DIHITUNG --}}
        @if ($hasAnyNullBobot)
            <div
                class="mb-6 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm no-print">
                <x-lucide-alert-triangle class="w-5 h-5 mt-0.5 shrink-0 text-amber-500" />
                <div>
                    <p class="font-semibold text-amber-800">Perhatian: Perhitungan AHP Belum Disimpan</p>
                    <p class="text-xs text-amber-600 mt-1 leading-relaxed">
                        Sebagian kriteria belum memiliki nilai bobot hasil perhitungan AHP. Nilai akhir SPK saat ini
                        menggunakan bobot default (0). Harap masuk ke menu <a
                            href="{{ route('petugas.perhitunganAHP.index') }}"
                            class="underline font-semibold hover:text-amber-800">Perhitungan AHP</a> untuk melakukan dan
                        menyimpan bobot perbandingan kriteria terlebih dahulu.
                    </p>
                </div>
            </div>
        @endif

        {{-- FILTER CARD (NO-PRINT) --}}
        <div class="bg-white border rounded-2xl p-4 shadow-sm mb-6 no-print">
            <form action="{{ route('petugas.laporan.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

                {{-- POSYANDU --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pilih Posyandu</label>
                    <select name="posyandu_id"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none bg-gray-50 transition">
                        <option value="">-- Semua Posyandu --</option>
                        @foreach ($posyandus as $pos)
                            <option value="{{ $pos->id }}"
                                {{ request('posyandu_id') == $pos->id ? 'selected' : '' }}>
                                {{ $pos->nama_posyandu }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PERIODE --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pilih Periode
                        Penilaian</label>
                    <select name="periode"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none bg-gray-50 transition">
                        <option value="">-- Semua Periode --</option>
                        @foreach ($periodes as $key => $label)
                            <option value="{{ $key }}" {{ request('periode') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TOMBOL FILTER --}}
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-[#1B3C53] text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#244E6B] transition flex items-center justify-center gap-2">
                        <x-lucide-filter class="w-4 h-4" />
                        Terapkan Filter
                    </button>
                    @if (request()->filled('posyandu_id') || request()->filled('periode'))
                        <a href="{{ route('petugas.laporan.index') }}"
                            class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center justify-center"
                            title="Reset Filter">
                            <x-lucide-rotate-ccw class="w-4 h-4" />
                        </a>
                    @endif
                </div>

            </form>
        </div>

        @if (empty($results))
            <div
                class="bg-white rounded-2xl border shadow-sm p-12 text-center text-gray-400 font-medium animate-fade-in">
                <div class="flex flex-col items-center justify-center gap-2">
                    <x-lucide-file-text class="w-12 h-12 text-gray-300" />
                    <span class="font-bold text-lg text-gray-500">Data tidak ada</span>
                    <span class="text-xs text-gray-400">Belum ada hasil penilaian balita pada periode ini. Pastikan data
                        penilaian kriteria balita diinputkan secara lengkap terlebih dahulu.</span>
                </div>
            </div>
        @else
            {{-- RINGKASAN STATUS FILTER (UNTUK PRINT) --}}
            <div class="hidden print:block mb-4 text-sm text-gray-700">
                <p><strong>Posyandu:</strong>
                    {{ request('posyandu_id') ? $posyandus->firstWhere('id', request('posyandu_id'))->nama_posyandu ?? 'Semua' : 'Semua Posyandu' }}
                </p>
                <p><strong>Periode Penilaian:</strong>
                    {{ request('periode') ? $periodes[request('periode')] ?? 'Semua' : 'Semua Periode' }}</p>
            </div>

            {{-- REPORT TABLE --}}
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        {{-- HEADER --}}
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide border-b">
                            <tr>
                                <th class="px-4 py-3.5 text-center w-12">Rank</th>
                                <th class="px-4 py-3.5 text-left">Nama Balita</th>
                                <th class="px-4 py-3.5 text-left">Posyandu</th>
                                <th class="px-4 py-3.5 text-center">Tanggal Penilaian</th>
                                <th class="px-4 py-3.5 text-center">Skor</th>
                                <th class="px-4 py-3.5 text-center">Status Prioritas</th>
                            </tr>
                        </thead>

                        {{-- BODY --}}
                        <tbody class="divide-y text-gray-700">
                            @forelse ($results as $index => $row)
                                <tr class="hover:bg-gray-50/50 transition">

                                    {{-- RANK --}}
                                    <td class="px-4 py-3 text-center font-bold text-gray-900">
                                        {{ $index + 1 }}
                                    </td>

                                    {{-- NAMA BALITA --}}
                                    <td class="px-4 py-3 font-semibold text-gray-800">
                                        {{ $row['balita']->nama }}
                                    </td>

                                    {{-- POSYANDU --}}
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $row['balita']->posyandu->nama_posyandu ?? '-' }}
                                    </td>

                                    {{-- TANGGAL PENILAIAN --}}
                                    <td class="px-4 py-3 text-center text-gray-500">
                                        {{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d M Y') }}
                                    </td>

                                    {{-- SKOR SPK --}}
                                    <td class="px-4 py-3 text-center font-bold text-[#1B3C53]">
                                        {{ number_format($row['nilai_akhir'], 3) }}
                                    </td>

                                    {{-- STATUS PRIORITAS --}}
                                    <td class="px-4 py-3 text-center">
                                        @if ($row['color'] === 'red')
                                            <span
                                                class="px-3 py-1 text-xs rounded-full font-bold bg-red-50 text-red-600 border border-red-200">
                                                {{ $row['status'] }}
                                            </span>
                                        @elseif ($row['color'] === 'yellow')
                                            <span
                                                class="px-3 py-1 text-xs rounded-full font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                                {{ $row['status'] }}
                                            </span>
                                        @else
                                            <span
                                                class="px-3 py-1 text-xs rounded-full font-bold bg-green-50 text-green-600 border border-green-200">
                                                {{ $row['status'] }}
                                            </span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-gray-400 font-medium">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <x-lucide-file-text class="w-8 h-8 text-gray-300" />
                                            <span>Tidak ditemukan data penilaian balita yang cocok.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        @endif

    </div>

    {{-- PRINT STYLES --}}
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            aside,
            nav,
            button,
            form,
            .no-print {
                display: none !important;
            }

            .ml-64,
            .p-6 {
                margin: 0 !important;
                padding: 0 !important;
            }

            .bg-white,
            .border,
            .rounded-2xl {
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
            }

            table {
                border-collapse: collapse !important;
                width: 100% !important;
                margin-top: 15px !important;
            }

            th,
            td {
                border: 1px solid #d1d5db !important;
                padding: 8px !important;
            }

            th {
                background-color: #f3f4f6 !important;
                color: black !important;
            }
        }
    </style>

</x-petugas-layout>
