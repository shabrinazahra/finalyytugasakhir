<x-petugas-layout>

    <div class="p-6 max-w-5xl">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                Tambah Kategori Penilaian
            </h1>
            <p class="text-sm text-gray-500">
                Tambahkan beberapa kategori sekaligus berdasarkan kriteria yang dipilih
            </p>
        </div>

        <form method="POST" action="{{ route('petugas.kategori_penilaian.store') }}" class="space-y-6">
            @csrf

            {{-- Kriteria --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kriteria
                </label>
                <select name="kriteria_id" required
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5
           focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition">

                    <option value="" selected disabled>
                        -- Pilih Kriteria --
                    </option>

                    @foreach($kriterias as $k)
                    <option value="{{ $k->id }}">
                        {{ $k->kode_kriteria }} - {{ $k->nama_kriteria }}
                    </option>
                    @endforeach

                </select>
            </div>

            {{-- Tabel Kategori (Multi-row) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Daftar Kategori
                </label>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium w-8">#</th>
                                <th class="text-left px-4 py-3 font-medium">Nama Kategori</th>
                                <th class="text-left px-4 py-3 font-medium w-48">Nilai</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody id="kategori-body">
                            <tr class="border-t border-gray-100 kategori-row">
                                <td class="px-4 py-3 text-gray-400 row-number">1</td>
                                <td class="px-4 py-3">
                                    <input type="text" name="kategoris[0][nama_kategori]"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2
                                               focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition"
                                        placeholder="Nama kategori" required>
                                </td>
                                <td class="px-4 py-3">
                                    <select name="kategoris[0][nilai]"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2
                                               focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition">
                                        <option value="5">5 - Buruk</option>
                                        <option value="3">3 - Cukup Buruk</option>
                                        <option value="1">1 - Baik</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{-- Baris pertama tidak bisa dihapus --}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Tombol Tambah Baris --}}
                <button type="button" id="btn-tambah-baris"
                    class="mt-3 flex items-center gap-2 text-sm text-[#1B3C53] hover:text-[#234C6A] font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Baris Kategori
                </button>
            </div>

            {{-- BUTTON --}}
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('petugas.kategori_penilaian.index') }}"
                    class="px-4 py-2 rounded-xl border text-gray-600 hover:bg-gray-100 transition">
                    Kembali
                </a>
                <button type="submit"
                    class="bg-[#1B3C53] text-white px-5 py-2 rounded-xl hover:bg-[#234C6A] transition shadow-sm">
                    Simpan Semua
                </button>
            </div>

        </form>
    </div>

    {{-- Template baris baru (hidden) --}}
    <template id="row-template">
        <tr class="border-t border-gray-100 kategori-row">
            <td class="px-4 py-3 text-gray-400 row-number"></td>
            <td class="px-4 py-3">
                <input type="text" name=""
                    class="w-full border border-gray-200 rounded-lg px-3 py-2
                           focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition"
                    placeholder="Nama kategori" required>
            </td>
            <td class="px-4 py-3">
                <select name=""
                    class="w-full border border-gray-200 rounded-lg px-3 py-2
                           focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition">
                    <option value="5">5 - Buruk</option>
                    <option value="3">3 - Cukup Buruk</option>
                    <option value="1">1 - Baik</option>
                </select>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" class="btn-hapus-baris text-gray-400 hover:text-red-500 transition" title="Hapus baris">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    @push('scripts')
    @vite('resources/js/pages/kategori-penilaian-create.js')
    @endpush

</x-petugas-layout>