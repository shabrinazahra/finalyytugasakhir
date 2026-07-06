<div class="py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- PROFILE --}}
        <div class="bg-white p-6 rounded-xl shadow">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- PASSWORD --}}
        <div class="bg-white p-6 rounded-xl shadow">
            @include('profile.partials.update-password-form')
        </div>

    </div>
</div>