<x-petugas-layout>

    <div class="p-6">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Kategori Penilaian Kriteria</h1>
            <p class="text-sm text-gray-500">
                Data kategori penilaian berdasarkan kriteria
            </p>
        </div>

        {{-- INFO BOX --}}
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-2xl p-4 flex gap-3 text-sm text-blue-800">
            <x-lucide-info class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
            <div>
                <p class="font-semibold mb-1">Informasi Penilaian Kategori Kriteria:</p>
                <p class="mb-2 text-blue-700 leading-relaxed">
                    Setiap kriteria memiliki beberapa tingkatan kategori kondisi balita yang dikonversi ke dalam nilai
                    bobot standar (1, 3, 5):
                </p>
                <ul class="list-disc list-inside space-y-1 text-blue-700">
                    <li><strong class="text-blue-900">Nilai 5 (Buruk):</strong> Menunjukkan kondisi balita pada kriteria
                        tersebut berada dalam status <span class="font-bold text-red-500">kurang baik / berisiko /
                            butuh penanganan
                            segera</span>.</li>
                    <li><strong class="text-blue-900">Nilai 3 (Cukup Buruk):</strong> Menunjukkan kondisi balita berada
                        dalam status <span class="font-bold text-yellow-500">sedang / kurang optimal / perlu
                            dipantau</span>.</li>
                    <li><strong class="text-blue-900">Nilai 1 (Baik):</strong> Menunjukkan kondisi balita berada dalam
                        status <span class="font-bold text-green-500">sehat / normal / optimal / sesuai standar</span>.
                    </li>
                </ul>
            </div>
        </div>

        {{-- TAMBAH --}}
        <div class="mb-4">
            <a href="{{ route('petugas.kategori_penilaian.create') }}"
                class="inline-flex items-center gap-2 bg-[#1B3C53] text-white px-4 py-2 rounded-lg text-sm font-medium
                       hover:bg-[#234C6A] transition shadow-sm">
                <x-lucide-plus class="w-4 h-4" />
                Tambah Kategori
            </a>
        </div>

        {{-- SEARCH --}}
        <div class="flex justify-end mb-3">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Search:</label>
                <input type="text" id="searchKategori"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B3C53]"
                    placeholder="Cari...">
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <table class="w-full text-sm">

                {{-- HEADER --}}
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide border-b">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">#</th>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Kriteria</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Nilai</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y" id="tableKategori">

                    @forelse($data as $index => $item)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- NO --}}
                            <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>

                            {{-- KODE KRITERIA --}}
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $item->kriteria->kode_kriteria ?? '-' }}
                            </td>

                            {{-- KRITERIA --}}
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $item->kriteria->nama_kriteria ?? '-' }}
                            </td>

                            {{-- KATEGORI --}}
                            <td class="px-4 py-3 text-gray-600">
                                {{ $item->nama_kategori }}
                            </td>

                            {{-- NILAI --}}
                            <td class="px-4 py-3">
                                @php
                                    $keterangan = match ($item->nilai) {
                                        5 => 'Buruk',
                                        3 => 'Cukup Buruk',
                                        1 => 'Baik',
                                        default => '-',
                                    };
                                @endphp

                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $item->nilai == 1
                                    ? 'bg-green-50 text-green-600'
                                    : ($item->nilai == 3
                                        ? 'bg-yellow-50 text-yellow-600'
                                        : 'bg-red-50 text-red-600') }}">
                                    {{ $item->nilai }} - {{ $keterangan }}
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('petugas.kategori_penilaian.edit', $item->id) }}"
                                        class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                        title="Edit">
                                        <x-lucide-pencil class="w-4 h-4" />
                                    </a>

                                    {{-- DELETE --}}
                                    <div x-data="deleteModal" class="inline-block">

                                        {{-- BUTTON --}}
                                        <button @click="show()" type="button"
                                            class="p-2 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"
                                            title="Hapus">

                                            <x-lucide-trash class="w-4 h-4" />
                                        </button>

                                        {{-- MODAL --}}
                                        <div x-show="open" x-cloak style="display: none;"
                                            class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm">

                                            {{-- BOX --}}
                                            <div @click.away="close()"
                                                class="relative bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl">

                                                {{-- CLOSE --}}
                                                <button @click="close()"
                                                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">

                                                    <x-lucide-x class="w-5 h-5" />
                                                </button>

                                                {{-- ICON --}}
                                                <div class="flex justify-center">
                                                    <div
                                                        class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                                                        <x-lucide-triangle-alert class="w-8 h-8 text-red-500" />
                                                    </div>
                                                </div>

                                                {{-- TEXT --}}
                                                <div class="text-center mt-4">
                                                    <h2 class="text-xl font-bold text-gray-800">
                                                        Hapus Kategori?
                                                    </h2>

                                                    <p class="text-gray-500 mt-2">
                                                        Apakah Anda yakin ingin menghapus kategori
                                                        <span class="font-medium text-gray-800">
                                                            {{ $item->nama_kategori }}
                                                        </span>?
                                                    </p>
                                                </div>

                                                {{-- BUTTON --}}
                                                <div class="flex gap-3 mt-6">

                                                    {{-- CANCEL --}}
                                                    <button @click="close()"
                                                        class="flex-1 py-3 rounded-xl border border-[#1B3C53]/20 text-[#1B3C53] hover:bg-[#1B3C53]/5 transition">

                                                        Batal
                                                    </button>

                                                    {{-- DELETE --}}
                                                    <form
                                                        action="{{ route('petugas.kategori_penilaian.destroy', $item->id) }}"
                                                        method="POST" class="flex-1">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
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
                            <td colspan="6" class="text-center py-10 text-gray-400">
                                Belum ada data kategori penilaian
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>

</x-petugas-layout>
