<x-master-admin-layout>
    <div class="p-6">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Data Akun</h1>
            <p class="text-sm text-gray-500">
                Kelola akun kader dan petugas
            </p>
        </div>

        {{-- TOMBOL TAMBAH (KIRI) --}}
        <div class="mb-4">
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center gap-2 bg-[#1B3C53] text-white px-4 py-2 rounded-lg text-sm font-medium
                       hover:bg-[#234C6A] transition shadow-sm">
                <x-lucide-plus class="w-4 h-4" />
                Tambah Akun
            </a>
        </div>

        {{-- SEARCH (KANAN) --}}
        <div class="flex justify-end mb-3">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Search:</label>
                <input type="text" id="searchUser"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B3C53]"
                    placeholder="Cari nama ">
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

            <table class="w-full text-sm text-left">

                {{-- HEADER --}}
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide border-b">
                    <tr>
                        <th class="px-4 py-3 w-10">#</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3">Posyandu</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y" id="tableUser">

                    @forelse ($users as $index => $user)

                    @php
                    $role = $user->roles->first();
                    $roleName = $role->name ?? 'unknown';

                    $roleColor = match($roleName) {
                    'master_admin' => 'bg-gray-100 text-gray-600',
                    'kader' => 'bg-green-100 text-green-600',
                    'petugas' => 'bg-blue-100 text-blue-600',
                    default => 'bg-gray-100 text-gray-600'
                    };
                    @endphp

                    <tr class="hover:bg-gray-50 transition">

                        {{-- NO --}}
                        <td class="px-4 py-3 text-gray-500">
                            {{ $index + 1 }}
                        </td>

                        {{-- Nama --}}
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $user->name }}
                        </td>

                        {{-- Email --}}
                        <td class="px-4 py-3 text-gray-600">
                            {{ $user->email }}
                        </td>

                        {{-- Role --}}
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $roleColor }}">
                                {{ ucfirst(str_replace('_', ' ', $roleName)) }}
                            </span>
                        </td>

                        {{-- Posyandu --}}
                        <td class="px-4 py-3 text-gray-600">
                            {{ $user->posyandu->nama_posyandu ?? '-' }}
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">

                                <a href="{{ route('users.edit', $user->id) }}"
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
                                                    Hapus Akun?
                                                </h2>

                                                <p class="text-gray-500 mt-2">
                                                    Apakah Anda yakin ingin menghapus akun
                                                    <span class="font-medium text-gray-800">
                                                        {{ $user->name }}
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
                                                <form action="{{ route('users.destroy', $user->id) }}"
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
                        <td colspan="6" class="text-center py-10 text-gray-400">
                            Belum ada data akun
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-master-admin-layout>