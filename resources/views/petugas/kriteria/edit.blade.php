<x-petugas-layout>

    <div class="p-6 max-w-5xl">

        {{-- Title --}}
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-800">
                Edit Kriteria
            </h1>
            <p class="text-sm text-gray-500">
                Perbarui data kriteria penilaian
            </p>
        </div>

        <form method="POST" action="{{ route('petugas.kriteria.update', $kriteria->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Kode --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Kriteria
                </label>
                <input type="text"
                    value="{{ $kriteria->kode_kriteria }}"
                    readonly
                    class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed">
            </div>

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kriteria
                </label>
                <input type="text" name="nama_kriteria"
                    value="{{ $kriteria->nama_kriteria }}"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#1B3C53] focus:outline-none"
                    placeholder="Masukkan nama kriteria">
            </div>

            {{-- Atribut --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Atribut
                </label>
                <select name="atribut"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#1B3C53] focus:outline-none">

                    <option value="benefit"
                        {{ $kriteria->atribut == 'benefit' ? 'selected' : '' }}>
                        Benefit
                    </option>

                    <option value="cost"
                        {{ $kriteria->atribut == 'cost' ? 'selected' : '' }}>
                        Cost
                    </option>

                </select>
            </div>

            {{-- Button --}}
            <div class="flex justify-end gap-3 pt-4">

                {{-- Batal --}}
                <a href="{{ route('petugas.kriteria.index') }}"
                    class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">
                    Kembali
                </a>

                {{-- Update --}}
                <button
                    class="bg-[#1B3C53] text-white px-5 py-2 rounded-lg
                           hover:bg-[#234C6A] transition shadow-sm">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</x-petugas-layout>