<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Perbarui nama dan email akun kamu.
        </p>
    </header>

    @if (session('status') === 'profile-updated')
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-init="setTimeout(() => show = false, 3000)"
        class="mt-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
        <x-lucide-check-circle class="w-5 h-5" />
        <span class="text-sm font-medium">Profil berhasil diperbarui.</span>
    </div>
    @endif

    {{-- FORM KIRIM VERIFIKASI --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- FORM UPDATE PROFILE --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- NAMA --}}
        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                placeholder="Masukkan nama"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- EMAIL --}}
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                placeholder="Masukkan email"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            {{-- STATUS VERIFIKASI --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    Email kamu belum terverifikasi.

                    <button form="send-verification"
                        class="underline text-sm text-gray-600 hover:text-gray-900">
                        Kirim ulang verifikasi
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600">
                    Link verifikasi berhasil dikirim ulang.
                </p>
                @endif
            </div>
            @endif
        </div>

        {{-- BUTTON --}}
        <div class="flex items-center gap-4">
            <button type="submit"
                class="bg-[#1B3C53] text-white px-4 py-2 rounded-lg text-sm font-medium
                       hover:bg-[#234C6A] transition shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</section>