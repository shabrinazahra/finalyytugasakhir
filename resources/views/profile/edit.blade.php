@php
$role = Auth::user()->roles->first()->name ?? 'guest';
@endphp

@if($role === 'kader')

<x-kader-layout>
    @include('profile.content')
</x-kader-layout>

@elseif($role === 'petugas')

<x-petugas-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Profile
        </h2>
    </x-slot>

    @include('profile.content')
</x-petugas-layout>

@else

<x-master-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Profile
        </h2>
    </x-slot>

    @include('profile.content')
</x-master-admin-layout>

@endif