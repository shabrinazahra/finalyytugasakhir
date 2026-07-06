<x-petugas-layout>

    <div class="p-6 max-w-5xl">

        {{-- HEADER --}}
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">
                Edit Kategori Penilaian
            </h1>
            <p class="text-sm text-gray-500">
                Perbarui kategori berdasarkan kriteria yang dipilih
            </p>
        </div>

        <form method="POST" action="{{ route('petugas.kategori_penilaian.update', $data->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Kriteria --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kriteria
                </label>

                <input type="text"
                    value="{{ $data->kriteria->kode_kriteria }} - {{ $data->kriteria->nama_kriteria }}"
                    readonly
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-100 text-gray-700">

                <input type="hidden"
                    name="kriteria_id"
                    value="{{ $data->kriteria_id }}">
            </div>

            {{-- Nama Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kategori
                </label>
                <input type="text" name="nama_kategori"
                    value="{{ old('nama_kategori', $data->nama_kategori) }}"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                               focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition"
                    placeholder="Masukkan nama kategori">
            </div>

            {{-- Nilai --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nilai
                </label>
                <select name="nilai"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                               focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition">

                    <option value="5" {{ $data->nilai == 5 ? 'selected' : '' }}>5 - Buruk</option>
                    <option value="3" {{ $data->nilai == 3 ? 'selected' : '' }}>3 - Cukup Buruk</option>
                    <option value="1" {{ $data->nilai == 1 ? 'selected' : '' }}>1 - Baik</option>

                </select>
            </div>

            {{-- BUTTON --}}
            <div class="flex justify-end gap-3 pt-4">

                <a href="{{ route('petugas.kategori_penilaian.index') }}"
                    class="px-4 py-2 rounded-xl border text-gray-600 hover:bg-gray-100 transition">
                    Kembali
                </a>

                <button
                    class="bg-[#1B3C53] text-white px-5 py-2 rounded-xl
                               hover:bg-[#234C6A] transition shadow-sm">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</x-petugas-layout>