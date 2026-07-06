<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Posyandu;

class PosyanduSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar posyandu awal yang akan dimasukkan ke database sebagai data master.
        $data = [
            ['nama_posyandu' => 'ANYELIR 1', 'alamat' => 'NDASEM'],
            ['nama_posyandu' => 'ANYELIR 2', 'alamat' => 'DURENAN'],
            ['nama_posyandu' => 'ANYELIR 3', 'alamat' => 'WINONG'],
            ['nama_posyandu' => 'ANYELIR 4', 'alamat' => 'KEDUNGGALAR'],
            ['nama_posyandu' => 'ANYELIR 5', 'alamat' => 'KALIWO 2 TIMUR'],
            ['nama_posyandu' => 'ANYELIR 6', 'alamat' => 'KALIWO 2 BARAT'],
            ['nama_posyandu' => 'ANYELIR 7', 'alamat' => 'URUNG-URUNG'],
            ['nama_posyandu' => 'ANYELIR 8', 'alamat' => 'PLOSOREJO'],
            ['nama_posyandu' => 'ANYELIR 9', 'alamat' => 'PULOREJO'],
        ];

        // Simpan setiap posyandu ke tabel posyandus.
        foreach ($data as $item) {
            Posyandu::create($item);
        }
    }
}
