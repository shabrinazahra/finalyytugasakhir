<x-kader-layout>

    <div class="p-6">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Data Balita</h1>
        </div>

        {{-- ACTIONS --}}
        <div class="mb-4 flex flex-wrap gap-2">
            <a href="{{ route('balita.create') }}"
                class="inline-flex items-center gap-2 bg-[#1B3C53] text-white px-4 py-2 rounded-lg text-sm font-medium
               hover:bg-[#234C6A] transition shadow-sm">
                <x-lucide-plus class="w-4 h-4" />
                Tambah Balita
            </a>

            <a href="{{ route('balita.template') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">
                <x-lucide-download class="w-4 h-4" />
                Template Excel
            </a>

            <form action="{{ route('balita.import') }}" method="POST" enctype="multipart/form-data" class="inline-flex items-center gap-2">
                @csrf
                <label class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm cursor-pointer">
                    <x-lucide-file-up class="w-4 h-4" />
                    Import Excel
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                </label>
                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                    Unggah
                </button>
            </form>
        </div>

        {{-- SEARCH --}}
        <form method="GET" class="flex justify-end mb-4">
            <div class="relative w-64">

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

        @if ($balitas->isEmpty())
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
            Belum ada data balita yang diupload. Silakan tambahkan data secara manual atau impor melalui Excel.
        </div>
        @endif

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <table class="w-full text-sm">

                {{-- HEADER --}}
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide border-b">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">#</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">NIK</th>
                        <th class="px-4 py-3 text-left">Jenis Kelamin</th>
                        <th class="px-4 py-3 text-left">Tanggal Lahir</th>
                        <th class="px-4 py-3 text-left">Orang Tua</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y" id="tableBalita">

                    @forelse ($balitas as $index => $b)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- NO --}}
                        <td class="px-4 py-3 text-gray-500">
                            @if(method_exists($balitas, 'firstItem'))
                            {{ $balitas->firstItem() + $index }}
                            @else
                            {{ $index + 1 }}
                            @endif
                        </td>

                        {{-- NAMA --}}
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $b->nama }}
                        </td>

                        {{-- NIK --}}
                        <td class="px-4 py-3 text-gray-600">
                            {{ $b->nik }}
                        </td>

                        {{-- JENIS KELAMIN --}}
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                {{ strtoupper($b->jenis_kelamin) }}
                            </span>
                        </td>

                        {{-- TANGGAL LAHIR --}}
                        <td class="px-4 py-3 text-gray-600">
                            {{ \Carbon\Carbon::parse($b->tanggal_lahir)->format('d M Y') }}
                        </td>

                        {{-- ORANG TUA --}}
                        <td class="px-4 py-3 text-gray-600">
                            {{ $b->nama_ortu }}
                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">

                                {{-- Edit --}}
                                <a href="{{ route('balita.edit', $b->id) }}"
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
                                                    Apakah Anda yakin ingin menghapus data balita
                                                    <span class="font-medium text-gray-800">{{ $b->nama }}</span>?
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
                                                <form action="{{ route('balita.destroy', $b->id) }}"
                                                    method="POST"
                                                    class="flex-1">

                                                    @csrf
                                                    @method('DELETE')

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
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            Belum ada data balita
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

            @if(method_exists($balitas, 'links'))
            <div class="mt-4">
                {{ $balitas->links() }}
            </div>
            @endif
        </div>

    </div>

</x-kader-layout>