<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat role default yang dipakai sistem untuk membedakan hak akses pengguna.
        $masterAdminRole = Role::firstOrCreate(['name' => 'master_admin']);
        $kaderRole = Role::firstOrCreate(['name' => 'kader']);
        $petugasRole = Role::firstOrCreate(['name' => 'petugas']);

        // Buat akun awal master admin jika belum ada.
        $masterAdmin = User::firstOrCreate(
            ['email' => 'masteradmin@gmail.com'],
            [
                'name' => 'Master Admin',
                'password' => 'master123',
            ]
        );

        // Berikan role master admin kepada akun yang baru dibuat.
        $masterAdmin->assignRole($masterAdminRole);

        // Buat akun awal kader jika belum ada.
        $kader = User::firstOrCreate(
            ['email' => 'kader@gmail.com'],
            [
                'name' => 'Kader',
                'password' => 'kader123',
            ]
        );
        // Berikan role kader kepada akun kader.
        $kader->assignRole($kaderRole);

        // Buat akun awal petugas jika belum ada.
        $petugas = User::firstOrCreate(
            ['email' => 'petugas@gmail.com'],
            [
                'name' => 'Petugas',
                'password' => 'petugas123',
            ]
        );

        // Berikan role petugas kepada akun petugas.
        $petugas->assignRole($petugasRole);
    }
}
