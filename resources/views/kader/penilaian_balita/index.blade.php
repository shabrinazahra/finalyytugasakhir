<x-kader-layout>

    <div class="p-6">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Penilaian Balita</h1>
        </div>

        {{-- TAMBAH --}}
        <div class="mb-4">
            <a href="{{ route('penilaian_balita.create') }}"
                class="inline-flex items-center gap-2 bg-[#1B3C53] text-white px-4 py-2 rounded-lg text-sm font-medium
                       hover:bg-[#234C6A] transition shadow-sm">
                <x-lucide-plus class="w-4 h-4" />
                Tambah Penilaian
            </a>
        </div>

        {{-- FILTER PERIODE + SEARCH (satu form) --}}
        <form method="GET" class="flex items-center gap-3 mb-4">

            {{-- Pilih Bulan --}}
            <select name="bulan"
                onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B3C53]">
                @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
                @endforeach
            </select>

            {{-- Pilih Tahun --}}
            <select name="tahun"
                onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B3C53]">
                @foreach($tahunList as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>

            {{-- Search --}}
            <div class="relative ml-auto w-64">
                <input type="text" name="search"
                    value="{{ request('search') }}"
                    onkeyup="this.form.submit()"
                    placeholder="Cari Nama Balita..."
                    class="w-full pr-10 pl-4 py-2 text-sm border border-gray-300 rounded-xl
                           focus:outline-none focus:ring-2 focus:ring-[#1B3C53] shadow-sm">
                <button type="submit"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#1B3C53]">
                    <x-lucide-search class="w-4 h-4" />
                </button>
            </div>

        </form>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border overflow-x-auto">
            <table class="w-full text-sm">

                {{-- HEADER --}}
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide border-b">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">No</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Tanggal Penilaian</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        @foreach($kriterias as $kriteria)
                        <th class="px-4 py-3 text-center">{{ $kriteria->kode_kriteria }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y">
                    @forelse($penilaians as $balita_id => $items)
                    @php
                    $balita = $items->first()->balita;
                    $nilaiMap = $items->keyBy('kriteria_id');
                    $firstId = $items->first()->id;
                    $realBalitaId = $items->first()->balita_id;
                    $tanggalPenilaian = $items->first()->tanggal_penilaian
                    ?? $items->first()->created_at;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-4 py-3 text-gray-700">{{ $loop->iteration }}</td>

                        {{-- Tanggal Penilaian --}}
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            {{ $tanggalPenilaian
                                ? \Carbon\Carbon::parse($tanggalPenilaian)->translatedFormat('d F Y')
                                : '-' }}
                        </td>

                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $balita->nama ?? '-' }}
                        </td>

                        {{-- Nilai per kriteria (dinamis) --}}
                        @foreach($kriterias as $kriteria)
                        @php $p = $nilaiMap->get($kriteria->id); @endphp
                        <td class="px-4 py-3 text-center text-gray-700">
                            {{ $p ? number_format($p->kategori->nilai ?? 0, 0) : '-' }}
                        </td>
                        @endforeach

                        {{-- Aksi --}}
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">

                                {{-- Edit --}}
                                <a href="{{ route('penilaian_balita.edit', $firstId) }}"
                                    class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                    title="Edit">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </a>

                                {{-- Hapus --}}
                                <div x-data="deleteModal" class="inline-block">

                                    {{-- BUTTON --}}
                                    <button
                                        @click="show()"
                                        type="button"
                                        class="p-2 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"
                                        title="Hapus">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>

                                    {{-- MODAL --}}
                                    <div
                                        x-show="open"
                                        x-cloak
                                        style="display: none;"
                                        class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm">

                                        {{-- BOX --}}
                                        <div
                                            @click.away="close()"
                                            class="relative bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl">

                                            {{-- CLOSE --}}
                                            <button
                                                @click="close()"
                                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                                                <x-lucide-x class="w-5 h-5" />
                                            </button>

                                            {{-- ICON --}}
                                            <div class="flex justify-center">
                                                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                                                    <x-lucide-triangle-alert class="w-8 h-8 text-red-500" />
                                                </div>
                                            </div>

                                            {{-- TEXT --}}
                                            <div class="text-center mt-4">
                                                <h2 class="text-xl font-bold text-gray-800">
                                                    Hapus Data?
                                                </h2>
                                                <p class="text-gray-500 mt-2">
                                                    Apakah Anda yakin ingin menghapus penilaian balita
                                                    <span class="font-medium text-gray-800">{{ $balita->nama ?? '-' }}</span>?
                                                </p>
                                            </div>

                                            {{-- BUTTON --}}
                                            <div class="flex gap-3 mt-6">

                                                {{-- CANCEL --}}
                                                <button
                                                    @click="close()"
                                                    class="flex-1 py-3 rounded-xl border text-gray-600 hover:bg-gray-100 transition">
                                                    Batal
                                                </button>

                                                {{-- DELETE --}}
                                                <form action="{{ route('penilaian_balita.destroy', $realBalitaId) }}"
                                                    method="POST"
                                                    class="flex-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                                    <button
                                                        type="submit"
                                                        class="w-full py-3 rounded-xl bg-[#1B3C53] text-white hover:bg-[#234C6A] transition">
                                                        Ya, Hapus
                                                    </button>
                                                </form>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $kriterias->count() + 4 }}" class="text-center py-10 text-gray-400">
                            Belum ada data penilaian
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>

            {{-- INFO + PAGINATION --}}
            <div class="px-4 py-3 flex items-center justify-between border-t text-sm text-gray-500">
                <span>
                    Showing {{ $penilaians->firstItem() }} to {{ $penilaians->lastItem() }}
                    of {{ $penilaians->total() }} entries
                </span>
                {{ $penilaians->links() }}
            </div>
        </div>

    </div>

</x-kader-layout>