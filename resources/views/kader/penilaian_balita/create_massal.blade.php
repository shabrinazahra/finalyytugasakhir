<x-kader-layout>

    <div class="p-6">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Input Penilaian Massal</h1>
            <p class="text-sm text-gray-500 mt-1">Isi penilaian untuk semua balita sekaligus. Hanya balita yang belum
                dinilai bulan ini yang ditampilkan.</p>
        </div>

        @if ($balitas->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border p-8 text-center">
                <x-lucide-check-circle class="w-12 h-12 text-emerald-400 mx-auto mb-3" />
                <p class="text-gray-600 font-medium">Semua balita sudah dinilai bulan ini.</p>
                <p class="text-sm text-gray-400 mt-1">Tidak ada balita yang perlu dinilai untuk bulan
                    {{ $tanggal->translatedFormat('F Y') }}.</p>
                <a href="{{ route('penilaian_balita.index') }}"
                    class="inline-flex items-center gap-2 mt-4 px-5 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                    <x-lucide-arrow-left class="w-4 h-4" />
                    Kembali
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('penilaian_balita.store_massal') }}">
                @csrf

                {{-- TANGGAL PENILAIAN --}}
                <div class="mb-6 bg-white rounded-2xl shadow-sm border p-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Penilaian</label>
                            <input type="date" name="tanggal_penilaian" id="tanggal_penilaian_input"
                                value="{{ $tanggal->toDateString() }}"
                                class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition">
                        </div>
                        <div class="mt-5">
                            <p class="text-sm text-gray-500">
                                <span class="font-medium text-gray-700">{{ $balitas->count() }}</span> balita belum
                                dinilai bulan {{ $tanggal->translatedFormat('F Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- TABLE INPUT MASSAL --}}
                <div class="bg-white rounded-2xl shadow-sm border overflow-x-auto mb-6">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide border-b">
                            <tr>
                                <th class="px-4 py-3 text-left w-10">
                                    <input type="checkbox" id="checkAll"
                                        class="rounded border-gray-300 text-[#1B3C53] focus:ring-[#1B3C53]">
                                </th>
                                <th class="px-4 py-3 text-left w-8">No</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Nama Balita</th>
                                @foreach ($kriterias as $kriteria)
                                    <th class="px-4 py-3 text-center whitespace-nowrap"
                                        title="{{ $kriteria->nama_kriteria }}">
                                        {{ $kriteria->kode_kriteria }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($balitas as $index => $balita)
                                <tr class="hover:bg-gray-50 transition balita-row @if ($index > 0) opacity-40 @endif"
                                    data-balita-id="{{ $balita->id }}">
                                    {{-- Checkbox --}}
                                    <td class="px-4 py-3">
                                        <input type="checkbox"
                                            class="balita-check rounded border-gray-300 text-[#1B3C53] focus:ring-[#1B3C53]"
                                            data-balita-id="{{ $balita->id }}"
                                            @if ($index === 0) checked @endif>
                                    </td>

                                    {{-- No --}}
                                    <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>

                                    {{-- Nama Balita --}}
                                    <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                                        {{ $balita->nama }}
                                    </td>

                                    {{-- Kriteria Dropdowns --}}
                                    @foreach ($kriterias as $kriteria)
                                        <td class="px-3 py-2">
                                            <select data-name="penilaian[{{ $balita->id }}][{{ $kriteria->id }}]"
                                                @if ($index === 0) name="penilaian[{{ $balita->id }}][{{ $kriteria->id }}]" @else name="" disabled @endif
                                                class="kriteria-select w-full min-w-[140px] border border-gray-200 rounded-lg px-2 py-1.5 text-xs
                                                       focus:ring-2 focus:ring-[#1B3C53] focus:border-[#1B3C53] outline-none transition bg-white"
                                                data-balita-id="{{ $balita->id }}">
                                                <option value="">Pilih</option>
                                                @foreach ($kriteria->kategoriPenilaians as $kategori)
                                                    <option value="{{ $kategori->id }}">
                                                        {{ $kategori->nama_kategori }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('penilaian_balita.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm">
                        <x-lucide-arrow-left class="w-4 h-4" />
                        Kembali
                    </a>

                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 bg-[#1B3C53] text-white px-6 py-2.5 rounded-xl hover:bg-[#234C6A] transition shadow-sm text-sm font-medium">
                        <x-lucide-save class="w-4 h-4" />
                        Simpan Penilaian (<span id="selectedCount">1</span> balita)
                    </button>
                </div>
            </form>
        @endif

    </div>

    {{-- JavaScript untuk checkbox dan disable/enable --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAll');
            const checkboxes = document.querySelectorAll('.balita-check');
            const selectedCount = document.getElementById('selectedCount');
            const allSelects = document.querySelectorAll('.kriteria-select');

            function updateCount() {
                const checked = document.querySelectorAll('.balita-check:checked').length;
                if (selectedCount) selectedCount.textContent = checked;
            }

            function setRowChecked(row, checked) {
                const checkbox = row.querySelector('.balita-check');
                const selects = row.querySelectorAll('select');

                checkbox.checked = checked;

                if (checked) {
                    row.classList.remove('opacity-40');
                    selects.forEach(s => {
                        s.disabled = false;
                        s.name = s.dataset.name || s.name;
                    });
                } else {
                    row.classList.add('opacity-40');
                    selects.forEach(s => {
                        s.disabled = true;
                        s.name = '';
                    });
                }
                updateCount();
                updateCheckAll();
            }

            function updateCheckAll() {
                if (checkAll) {
                    const allChecked = document.querySelectorAll('.balita-check:checked').length === checkboxes
                        .length;
                    checkAll.checked = allChecked;
                }
            }

            // Ketika ada select kriteria yang diisi, otomatis centang checkbox baris tersebut
            allSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const checkbox = row.querySelector('.balita-check');

                    // Jika ada nilai yang dipilih dan checkbox belum centang, centang otomatis
                    if (this.value && !checkbox.checked) {
                        setRowChecked(row, true);
                    }

                    // Jika semua kriteria di baris ini terisi, otomatis centang baris berikutnya
                    const rowSelects = row.querySelectorAll('.kriteria-select');
                    let allFilled = true;
                    rowSelects.forEach(s => {
                        if (!s.value) {
                            allFilled = false;
                        }
                    });

                    if (allFilled) {
                        const nextRow = row.nextElementSibling;
                        if (nextRow && nextRow.classList.contains('balita-row')) {
                            const nextCheckbox = nextRow.querySelector('.balita-check');
                            if (!nextCheckbox.checked) {
                                setRowChecked(nextRow, true);
                            }
                        }
                    }
                });
            });

            // Check All
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        const row = cb.closest('tr');
                        setRowChecked(row, checkAll.checked);
                    });
                });
            }

            // Individual checkboxes
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const row = cb.closest('tr');
                    setRowChecked(row, cb.checked);
                });
            });

            // Ketika tanggal diubah, reload halaman dengan filter tanggal tersebut
            const tanggalInput = document.getElementById('tanggal_penilaian_input');
            if (tanggalInput) {
                tanggalInput.addEventListener('change', function() {
                    window.location.href =
                        `{{ route('penilaian_balita.create_massal') }}?tanggal=${this.value}`;
                });
            }

            updateCount();
            updateCheckAll();
        });
    </script>

</x-kader-layout>
