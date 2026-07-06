<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller //mengelola halaman profil pengguna 
{
    
    public function edit(Request $request): View //menampilkan halaman edit profile
    {
        // Ambil data pengguna yang sedang login dari session/request.
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse //memperbarui informasi profil pengguna 
    {
        // Isi data user dengan input yang sudah valid.
        $request->user()->fill($request->validated());

        // Jika email pengguna berubah, reset status verifikasi email.
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Simpan perubahan ke database.
        $request->user()->save();

        // Kembalikan ke halaman edit profil dengan pesan status.
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    
}
