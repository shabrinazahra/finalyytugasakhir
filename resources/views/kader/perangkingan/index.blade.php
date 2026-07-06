<x-kader-layout>

    <div class="p-6">

        {{-- HEADER --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Hasil Perangkingan Balita</h1>
                <p class="text-sm text-gray-500">
                    Prioritas urutan penanganan balita berisiko stunting berdasarkan skor optimasi MOORA
                </p>
            </div>

            {{-- FILTER PERIODE --}}
            <form action="{{ route('kader.perangkingan.index') }}" method="GET" class="flex items-center gap-2">
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

        {{-- RANKING TABLE --}}
        @if (empty($results))
            <div class="bg-white rounded-2xl border shadow-sm p-12 text-center text-gray-400 animate-fade-in">
                <div class="flex flex-col items-center justify-center gap-2">
                    <x-lucide-award class="w-12 h-12 text-gray-300" />
                    <span class="font-bold text-lg text-gray-500">Data tidak ada</span>
                    <span class="text-xs text-gray-400">Belum ada hasil perangkingan pada periode ini. Pastikan data
                        penilaian kriteria balita diinputkan secara
                        lengkap terlebih dahulu.</span>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">

                <div
                    class="bg-white border rounded-2xl p-4 flex gap-3 text-xs text-amber-800 border-amber-100 shadow-sm leading-relaxed">
                    <x-lucide-info class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                    <div>
                        <span class="font-bold">Informasi Prioritas Penanganan:</span>
                        <ul class="list-disc list-inside mt-1 space-y-0.5 text-amber-700">
                            <li><strong class="text-amber-900">Prioritas Tinggi (Sangat Berisiko):</strong> Balita yang
                                memiliki indikasi stunting paling berat/kritis dan memerlukan penanganan segera dari
                                posyandu/puskesmas.</li>
                            <li><strong class="text-amber-900">Prioritas Sedang (Berisiko):</strong> Balita dengan
                                risiko stunting moderat yang memerlukan pemantauan intensif.</li>
                            <li><strong class="text-amber-900">Prioritas Rendah (Normal):</strong> Balita dalam kondisi
                                tumbuh kembang normal atau berisiko sangat rendah.</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                                <tr>
                                    <th class="px-6 py-4 text-center w-16">Rank</th>
                                    <th class="px-6 py-4 text-left">Nama Balita</th>
                                    <th class="px-6 py-4 text-center">Jenis Kelamin</th>
                                    <th class="px-6 py-4 text-left">Orang Tua</th>
                                    <th class="px-6 py-4 text-center">Skor</th>
                                    <th class="px-6 py-4 text-center">Status Prioritas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-gray-700">
                                @foreach ($results as $index => $row)
                                    <tr class="hover:bg-gray-50/50 transition">

                                        {{-- RANK --}}
                                        <td class="px-6 py-3.5 text-center font-black text-gray-900">
                                            {{ $index + 1 }}
                                        </td>

                                        {{-- NAMA --}}
                                        <td class="px-6 py-3.5 font-bold text-gray-800">
                                            {{ $row['balita']->nama }}
                                        </td>

                                        {{-- GENDER --}}
                                        <td class="px-6 py-3.5 text-center">
                                            <span
                                                class="px-3 py-1 text-xs rounded-full font-medium
                                                {{ $row['balita']->jenis_kelamin == 'Laki-laki' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-500' }}">
                                                {{ $row['balita']->jenis_kelamin }}
                                            </span>
                                        </td>

                                        {{-- ORTU --}}
                                        <td class="px-6 py-3.5 text-gray-500">
                                            {{ $row['balita']->nama_ortu }}
                                        </td>

                                        {{-- SKOR MOORA YI --}}
                                        <td class="px-6 py-3.5 text-center font-bold font-mono text-[#1B3C53]">
                                            {{ number_format($row['nilai_akhir'], 3) }}
                                        </td>

                                        {{-- STATUS --}}
                                        <td class="px-6 py-3.5 text-center">
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        @endif

    </div>

</x-kader-layout>
