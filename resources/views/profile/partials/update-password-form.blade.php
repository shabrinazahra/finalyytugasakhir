<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Ubah Password
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Gunakan password yang kuat dan tidak mudah ditebak untuk menjaga keamanan akun.
        </p>
    </header>

    @if (session('status') === 'password-updated')
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-init="setTimeout(() => show = false, 3000)"
        class="mt-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
        <x-lucide-check-circle class="w-5 h-5" />
        <span class="text-sm font-medium">Password berhasil diperbarui.</span>
    </div>
    @endif

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        {{-- PASSWORD LAMA --}}
        <div>
            <x-input-label for="update_password_current_password" value="Password Saat Ini" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        {{-- PASSWORD BARU --}}
        <div>
            <x-input-label for="update_password_password" value="Password Baru" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        {{-- KONFIRMASI PASSWORD --}}
        <div>
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- BUTTON --}}
        <div class="flex items-center gap-4">
            <button type="submit"
                class="bg-[#1B3C53] text-white px-4 py-2 rounded-lg text-sm font-medium
                       hover:bg-[#234C6A] transition shadow-sm">
                Simpan Password
            </button>
        </div>
    </form>
</section>