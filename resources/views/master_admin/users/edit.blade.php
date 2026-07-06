<x-master-admin-layout>

    <div class="p-6 max-w-5xl">

        {{-- Title --}}
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-800">
                Edit Akun
            </h1>
            <p class="text-sm text-gray-500">
                Perbarui data akun kader atau petugas kesehatan
            </p>
        </div>
        
        <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-[#1B3C53] focus:outline-none">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-[#1B3C53] focus:outline-none">
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password Baru
                </label>

                <div class="relative">
                    <input type="password"
                        name="password"
                        id="password"
                        autocomplete="new-password"
                        class="w-full border rounded-lg px-3 py-2 pr-10 bg-white
                   focus:ring-2 focus:ring-[#1B3C53] focus:outline-none"
                        placeholder="Kosongkan jika tidak diubah">

                    <button type="button" id="togglePasswordBtn"
                        class="absolute right-3 top-1/2 -translate-y-1/2 
                   text-gray-400 hover:text-gray-600 z-10 cursor-pointer">

                        <span id="eyeClosed">
                            <x-lucide-eye-off class="w-4 h-4" />
                        </span>

                        <span id="eyeOpen" class="hidden">
                            <x-lucide-eye class="w-4 h-4" />
                        </span>

                    </button>
                </div>
            </div>

            {{-- Jabatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                <select name="role" id="roleSelect"
                    class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-[#1B3C53] focus:outline-none">

                    @foreach($roles as $role)
                    <option value="{{ $role->name }}"
                        {{ $user->roles->contains('name', $role->name) ? 'selected' : '' }}>
                        {{ $role->name == 'kader' ? 'Kader Posyandu' : 'Petugas Kesehatan' }}
                    </option>
                    @endforeach

                </select>
            </div>

            {{-- Posyandu --}}
            <div id="posyanduField"
                class="{{ $user->roles->first()->name == 'kader' ? '' : 'hidden' }}">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Posyandu
                </label>

                <select name="posyandu_id"
                    class="w-full border rounded-lg px-3 py-2">

                    <option value="">-- Pilih Posyandu --</option>

                    @foreach($posyandus as $p)
                    @php
                        $isOccupied = $p->kader_count > 0 && $p->id !== $user->posyandu_id;
                    @endphp
                    <option value="{{ $p->id }}"
                        {{ old('posyandu_id', $user->posyandu_id ?? '') == $p->id ? 'selected' : '' }}
                        {{ $isOccupied ? 'disabled' : '' }}>
                        {{ $p->nama_posyandu }} {{ $isOccupied ? '(Sudah memiliki kader)' : '' }}
                    </option>
                    @endforeach

                </select>
            </div>

            {{-- Button --}}
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('users.index') }}"
                    class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">
                    Kembali
                </a>

                <button
                    class="bg-[#1B3C53] text-white px-5 py-2 rounded-lg hover:bg-[#234C6A] transition shadow-sm">
                    Simpan
                </button>
            </div>

        </form>

    </div>
    @push('script')
         @vite('resources/js/pages/users/edit.js')
    @endpush


</x-master-admin-layout>