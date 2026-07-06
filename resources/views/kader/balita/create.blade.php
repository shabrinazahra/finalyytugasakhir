<x-kader-layout>

    <div class="p-6 max-w-5xl">

        {{-- Title --}}
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-800">
                Tambah Balita
            </h1>
            <p class="text-sm text-gray-500">
                Masukkan data balita secara lengkap
            </p>
        </div>

        <form method="POST" action="{{ route('balita.store') }}" class="space-y-5">
            @csrf

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Balita
                </label>
                <input type="text" name="nama"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#1B3C53] focus:outline-none"
                    placeholder="Masukkan nama">
            </div>

            {{-- NIK --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    NIK
                </label>
                <input type="text" name="nik"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#1B3C53] focus:outline-none"
                    placeholder="Masukkan NIK">
            </div>

            {{-- Jenis Kelamin --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Jenis Kelamin
                </label>
                <select name="jenis_kelamin"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#1B3C53] focus:outline-none">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">LAKI-LAKI</option>
                    <option value="Perempuan">PEREMPUAN</option>
                </select>
            </div>

            {{-- Tanggal Lahir --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Lahir
                </label>
                <input type="date" name="tanggal_lahir"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#1B3C53] focus:outline-none">
            </div>

            {{-- Nama Orang Tua --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Orang Tua
                </label>
                <input type="text" name="nama_ortu"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#1B3C53] focus:outline-none"
                    placeholder="Masukkan nama orang tua">
            </div>

            {{-- Button --}}
            <div class="flex justify-end gap-3 pt-4">

                {{-- Batal --}}
                <a href="{{ route('balita.index') }}"
                    class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">
                    Kembali
                </a>

                {{-- Simpan --}}
                <button
                    class="bg-[#1B3C53] text-white px-5 py-2 rounded-lg
                           hover:bg-[#234C6A] transition shadow-sm">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</x-kader-layout>