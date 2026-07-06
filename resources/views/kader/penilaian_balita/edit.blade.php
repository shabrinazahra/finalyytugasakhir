<x-kader-layout>

    <div class="p-6 max-w-5xl">

        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">Edit Penilaian</h1>
            <p class="text-sm text-gray-500">Perbarui penilaian balita</p>
        </div>

        <form method="POST" action="{{ route('penilaian_balita.update', $penilaian->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- TANGGAL PENILAIAN --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Penilaian
                </label>
                <input type="date" name="tanggal_penilaian"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed"
                    value="{{ old('tanggal_penilaian', $penilaian->tanggal_penilaian) }}"
                    readonly>
            </div>

            {{-- PILIH BALITA --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Balita</label>
                <input type="text"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-100 cursor-not-allowed"
                    value="{{ $penilaian->balita->nama }}"
                    readonly>
                {{-- Hidden input agar balita_id tetap terkirim ke controller --}}
                <input type="hidden" name="balita_id" value="{{ $penilaian->balita_id }}">
                <input type="hidden" name="tanggal_penilaian" value="{{ $penilaian->tanggal_penilaian }}">
            </div>

            {{-- KRITERIA --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($kriterias as $kriteria)
                <div class="border rounded-xl p-4">

                    <p class="font-semibold mb-2">
                        {{ $kriteria->nama_kriteria }}
                    </p>

                    <select name="penilaian[{{ $kriteria->id }}]"
                        class="w-full border rounded-lg px-3 py-2">
                        <option value="">Pilih Kategori</option>

                        @foreach($kriteria->kategoriPenilaians as $kategori)
                        <option value="{{ $kategori->id }}"
                            {{ (isset($selected[$kriteria->id]) && $selected[$kriteria->id] == $kategori->id) ? 'selected' : '' }}>

                            {{ $kategori->nama_kategori }} ({{ $kategori->nilai }})
                        </option>
                        @endforeach

                    </select>

                </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('penilaian_balita.index') }}"
                    class="px-4 py-2 border rounded-xl">Kembali</a>

                <button type="submit"
                    class="bg-[#1B3C53] text-white px-6 py-2 rounded-xl">
                    Update
                </button>
            </div>

        </form>

    </div>

</x-kader-layout>