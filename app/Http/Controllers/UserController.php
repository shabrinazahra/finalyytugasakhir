<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Menentukan middleware yang wajib dipenuhi sebelum mengakses controller ini.
     * Hanya user yang sudah login dan memiliki role master_admin yang boleh masuk.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:master_admin')
        ];
    }

    public function index() //menampilkan daftar pengguna yang dapat dikelola
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'master_admin');
        })->with('roles:id,name', 'posyandu')->get();

        return view('master_admin.users.index', compact('users'));
    }

    public function create() //menampilkan form untuk menambah pengguna baru kader dan petugas 
    {
        $roles = Role::whereIn('name', ['kader', 'petugas'])->get();
        $posyandus = Posyandu::withCount(['users as kader_count' => function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'kader');
            });
        }])->get();

        return view('master_admin.users.create', compact('roles', 'posyandus'));
    }

    public function store(Request $request) //menyimpan data pengguna baru 
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'password'    => 'required|string|min:8',
            'role'        => 'required|exists:roles,name',
            'posyandu_id' => 'nullable|exists:posyandus,id'
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'role.required'     => 'Jabatan wajib dipilih.',
        ]);

        // Jika pengguna memiliki role kader, maka wajib memilih posyandu.
        if ($request->role === 'kader' && !$request->posyandu_id) {
            return back()->withErrors(['posyandu_id' => 'Kader wajib memilih posyandu'])->withInput();
        }

        // Membatasi agar satu posyandu hanya boleh memiliki satu kader.
        if ($request->role === 'kader' && $request->posyandu_id) {
            $isAssigned = User::role('kader')->where('posyandu_id', $request->posyandu_id)->exists();
            if ($isAssigned) {
                return back()->withErrors(['posyandu_id' => 'Posyandu ini sudah memiliki Kader. Satu posyandu hanya boleh memiliki satu kader.'])->withInput();
            }
        }

        // Membuat akun baru dengan password yang di-hash.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'posyandu_id' => $request->posyandu_id
        ]);

        // Menetapkan role yang dipilih ke pengguna baru.
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil ditambahkan.');
    }

    public function edit(string $id) //menampilkan form edit untuk pengguna yang dipilih 
    {
        $user = User::findOrFail($id);

        // Mencegah master_admin diedit lewat form ini.
        if ($user->hasRole('master_admin')) {
            abort(403, 'Aksi tidak diperbolehkan untuk akun Master Admin.');
        }

        $roles = Role::whereIn('name', ['kader', 'petugas'])->get();
        $posyandus = Posyandu::withCount(['users as kader_count' => function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'kader');
            });
        }])->get();

        return view('master_admin.users.edit', compact('user', 'roles', 'posyandus'));
    }

    public function update(Request $request, string $id) //memperbarui data pengguna yang dipilih
    {
        $user = User::findOrFail($id);

        // Mencegah master_admin diupdate lewat form ini.
        if ($user->hasRole('master_admin')) {
            abort(403, 'Aksi tidak diperbolehkan untuk akun Master Admin.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'    => 'nullable|string|min:8',
            'role'        => 'required|exists:roles,name',
            'posyandu_id' => 'nullable|exists:posyandus,id'
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan.',
            'password.min'   => 'Password minimal 8 karakter.',
            'role.required'  => 'Jabatan wajib dipilih.',
        ]);

        // Jika role yang dipilih adalah kader, wajib ada posyandu.
        if ($request->role === 'kader' && !$request->posyandu_id) {
            return back()->withErrors(['posyandu_id' => 'Kader wajib memilih posyandu'])->withInput();
        }

        // Memastikan satu posyandu hanya memiliki satu kader, kecuali untuk kader yang sedang diedit.
        if ($request->role === 'kader' && $request->posyandu_id) {
            $isAssigned = User::role('kader')
                ->where('posyandu_id', $request->posyandu_id)
                ->where('id', '!=', $user->id)
                ->exists();
            if ($isAssigned) {
                return back()->withErrors(['posyandu_id' => 'Posyandu ini sudah memiliki Kader. Satu posyandu hanya boleh memiliki satu kader.'])->withInput();
            }
        }

        // Memperbarui data pengguna, termasuk password jika diisi.
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
                ? Hash::make($request->password)
                : $user->password,
            'posyandu_id' => $request->posyandu_id
        ]);

        // Sinkronisasi role pengguna dengan role yang baru dipilih.
        $user->syncRoles($request->role);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(string $id) //menghapus data pengguna yang dipilih 
    {
        $user = User::findOrFail($id);

        // Mencegah master_admin dihapus.
        if ($user->hasRole('master_admin')) {
            abort(403, 'Aksi tidak diperbolehkan untuk akun Master Admin.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil dihapus.');
    }
}
