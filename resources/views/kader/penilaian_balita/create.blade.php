<x-kader-layout>

    <div class="p-6 max-w-5xl">

        {{-- HEADER --}}
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">Tambah Data Penilaian</h1>
            <p class="text-sm text-gray-500">Isi penilaian balita berdasarkan setiap kriteria</p>
        </div>

        <form method="POST" action="{{ route('penilaian_balita.store') }}" class="space-y-6">
            @csrf

            {{-- PILIH TANGGAL --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Penilaian
                </label>
                <input type="date" name="tanggal_penilaian" id="tanggal_penilaian"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                    value="{{ old('tanggal_penilaian') }}"
                    required>
            </div>

            {{-- PILIH BALITA --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Balita</label>
                <select name="balita_id" id="balita_select"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                           focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition bg-white">
                    <option value="">-- Pilih tanggal terlebih dahulu --</option>
                </select>
                <p id="info-balita" class="text-xs text-gray-400 mt-1"></p>
            </div>

            {{-- GRID KRITERIA --}}
            <div>
                <p class="text-sm font-medium text-gray-700 mb-3">Pilih Kategori dan Bobot</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($kriterias as $kriteria)
                    <div class="border border-gray-200 rounded-xl p-4 bg-white shadow-sm">

                        <p class="text-sm font-semibold text-[#1B3C53] mb-3">
                            {{ $kriteria->kode_kriteria }} - {{ $kriteria->nama_kriteria }}
                        </p>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Kategori</label>
                            <select name="penilaian[{{ $kriteria->id }}]"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition bg-white">
                                <option value="">Pilih Kategori</option>
                                @foreach($kriteria->kategoriPenilaians as $kategori)
                                <option value="{{ $kategori->id }}"
                                    {{ old("penilaian.{$kriteria->id}") == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }} (Nilai: {{ $kategori->nilai }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>

            {{-- BUTTONS --}}
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('penilaian_balita.index') }}"
                    class="px-5 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                    Kembali
                </a>
                <button type="submit"
                    class="bg-[#1B3C53] text-white px-6 py-2 rounded-xl hover:bg-[#234C6A] transition shadow-sm text-sm">
                    Simpan
                </button>
            </div>

        </form>

    </div>

    {{-- AJAX: load balita sesuai tanggal --}}
    <script>
        document.getElementById('tanggal_penilaian').addEventListener('change', function() {
            const tanggal = this.value;
            const select = document.getElementById('balita_select');
            const info = document.getElementById('info-balita');

            if (!tanggal) return;

            select.innerHTML = '<option value="">Memuat...</option>';
            info.textContent = '';

            fetch(`/kader/penilaian-balita/balita-tersedia?tanggal=${tanggal}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        select.innerHTML = '<option value="">Semua balita sudah dinilai bulan ini</option>';
                        info.textContent = 'Tidak ada balita yang tersedia untuk periode ini.';
                    } else {
                        select.innerHTML = '<option value="">Pilih Balita</option>';
                        data.forEach(balita => {
                            select.innerHTML += `<option value="${balita.id}">${balita.nama}</option>`;
                        });
                        info.textContent = `${data.length} balita belum dinilai pada bulan ini.`;
                    }
                })
                .catch(() => {
                    select.innerHTML = '<option value="">Gagal memuat data</option>';
                });
        });

        // Jika ada old value tanggal (saat validasi gagal), trigger otomatis
        const tanggalInput = document.getElementById('tanggal_penilaian');
        if (tanggalInput.value) {
            tanggalInput.dispatchEvent(new Event('change'));
        }
    </script>

</x-kader-layout>