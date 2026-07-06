<x-master-admin-layout>

    <div class="p-6">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Data Posyandu</h1>
            <p class="text-sm text-gray-500">
                Kelola data posyandu yang terdaftar
            </p>
        </div>

        {{-- TOMBOL TAMBAH (KIRI) --}}
        <div class="mb-4">
            <a href="{{ route('posyandu.create') }}"
                class="inline-flex items-center gap-2 bg-[#1B3C53] text-white px-4 py-2 rounded-lg text-sm font-medium
                       hover:bg-[#234C6A] transition shadow-sm">

                <x-lucide-plus class="w-4 h-4" />
                Tambah Posyandu
            </a>
        </div>

        {{-- SEARCH (KANAN) --}}
        <div class="flex justify-end mb-3">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Search:</label>
                <input type="text" id="searchPosyandu"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B3C53]"
                    placeholder="Cari Posyandu">
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

            <table class="w-full text-sm">

                {{-- HEADER --}}
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide border-b">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">#</th>
                        <th class="px-4 py-3 text-left">Nama Posyandu</th>
                        <th class="px-4 py-3 text-left">Alamat</th>
                        <th class="px-4 py-3 text-left">Kader Terpilih</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y" id="tablePosyandu">

                    @forelse($posyandus as $index => $p)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- NO --}}
                        <td class="px-4 py-3 text-gray-500">
                            {{ $index + 1 }}
                        </td>

                        {{-- NAMA --}}
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $p->nama_posyandu }}
                        </td>

                        {{-- ALAMAT --}}
                        <td class="px-4 py-3 text-gray-600">
                            {{ $p->alamat }}
                        </td>

                        {{-- KADER --}}
                        <td class="px-4 py-3">
                            @if($p->kader)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <x-lucide-user class="w-3.5 h-3.5" />
                                {{ $p->kader->name }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                <x-lucide-user-x class="w-3.5 h-3.5" />
                                Belum ada kader
                            </span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">

                                {{-- Edit --}}
                                <a href="{{ route('posyandu.edit', $p->id) }}"
                                    class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                    title="Edit">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </a>

                                {{-- DELETE --}}
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
                                                    Hapus Posyandu?
                                                </h2>

                                                <p class="text-gray-500 mt-2">
                                                    Apakah Anda yakin ingin menghapus data posyandu
                                                    <span class="font-medium text-gray-800">
                                                        {{ $p->nama_posyandu }}
                                                    </span>?
                                                </p>
                                            </div>

                                            {{-- BUTTON --}}
                                            <div class="flex gap-3 mt-6">

                                                {{-- CANCEL --}}
                                                <button
                                                    @click="close()"
                                                    class="flex-1 py-3 rounded-xl border border-[#1B3C53]/20 text-[#1B3C53] hover:bg-[#1B3C53]/5 transition">

                                                    Batal
                                                </button>

                                                {{-- DELETE --}}
                                                <form action="{{ route('posyandu.destroy', $p->id) }}"
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
                        <td colspan="5" class="text-center py-10 text-gray-400">
                            Belum ada data posyandu
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-master-admin-layout>