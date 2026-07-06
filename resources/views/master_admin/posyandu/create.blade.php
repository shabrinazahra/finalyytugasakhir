<x-master-admin-layout>

    <div class="p-6 max-w-5xl">

        <div class="mb-4">
            <h1 class="text-xl font-semibold text-gray-800">
                Tambah Posyandu
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Masukkan informasi posyandu secara lengkap
            </p>
        </div>

        <form method="POST" action="{{ route('posyandu.store') }}" class="space-y-4">
            @csrf

            <input type="text" name="nama_posyandu" required
                class="w-full border rounded-lg px-3 py-2
                     focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition"
                placeholder="Masukkan nama posyandu">

            <textarea name="alamat" required
                class="w-full border rounded-lg px-3 py-2
                    focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition"
                placeholder="Alamat"></textarea>

            <div class="flex justify-end gap-2">
                <a href="{{ route('posyandu.index') }}" class="px-4 py-2 border rounded">
                    Kembali
                </a>

                <button class="bg-[#1B3C53] text-white px-4 py-2 rounded">
                    Simpan
                </button>
            </div>

        </form>

    </div>

</x-master-admin-layout>
