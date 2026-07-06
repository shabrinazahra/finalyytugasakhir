<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void //memanggil seeder untuk mengisi data awal pada tabel-tabel di database
    {
        $this->call([
            UserSeeder::class, //memanggil seeder untuk tabel users
            KriteriaSeeder::class, //memanggil seeder untuk tabel kriterias
            KategoriPenilaianSeeder::class, //memanggil seeder untuk tabel kategori_penilaians
            PosyanduSeeder::class, //memanggil seeder untuk tabel posyandus
        ]);
    }
}
