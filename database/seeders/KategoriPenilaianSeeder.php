<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kriteria;
use App\Models\KategoriPenilaian;

class KategoriPenilaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definisikan kategori penilaian untuk setiap kriteria.
        // Setiap kategori memiliki nama dan nilai skor yang akan dipakai saat penilaian.
        $categories = [
            'K1' => [
                ['nama_kategori' => 'Sangat Kurang (<-3 SD)', 'nilai' => 5],
                ['nama_kategori' => 'Kurang (<-2 SD sampai -3 SD)', 'nilai' => 3],
                ['nama_kategori' => 'Normal  (- 2 SD sampai + 2 SD)', 'nilai' => 1],
            ],
            'K2' => [
                ['nama_kategori' => 'Berisiko (≤-1)', 'nilai' => 5],
                ['nama_kategori' => 'Normal (>-1)', 'nilai' => 1],
            ],
            'K3' => [
                ['nama_kategori' => 'Sering (Lebih dari 3 kali dalam sebulan )', 'nilai' => 5],
                ['nama_kategori' => 'Jarang (kurang dari 1 - 3 kali dalam sebulan )', 'nilai' => 3],
                ['nama_kategori' => 'Tidak pernah dalam sebulan', 'nilai' => 1],
            ],
            'K4' => [
                ['nama_kategori' => 'BBLR < 2500g', 'nilai' => 5],
                ['nama_kategori' => 'Normal > sama dengan 2500g', 'nilai' => 1],
            ],
            'K5' => [
                ['nama_kategori' => 'KEK (Lila < 23,5cm)', 'nilai' => 5],
                ['nama_kategori' => 'Normal (Lila lebih sama dengan dari 23,5cm)', 'nilai' => 1],
            ],
            'K6' => [
                ['nama_kategori' => 'Tidak Asi', 'nilai' => 5],
                ['nama_kategori' => 'Asi tidak eksklusif', 'nilai' => 3],
                ['nama_kategori' => 'Asi eksklusif', 'nilai' => 1],
            ],
            'K7' => [
                ['nama_kategori' => 'Belum imunisasi sesuai usia', 'nilai' => 5],
                ['nama_kategori' => 'Tidak lengkap sesuai usia', 'nilai' => 3],
                ['nama_kategori' => 'Lengkap sesuai usia', 'nilai' => 1],
            ],
        ];

        // Iterasi setiap kriteria dan sisipkan kategori penilaiannya.
        foreach ($categories as $kode => $items) {
            // Cari kriteria berdasarkan kode agar kategori masuk ke kriteria yang tepat.
            $kriteria = Kriteria::where('kode_kriteria', $kode)->first();
            if ($kriteria) {
                // Hapus kategori lama untuk kriteria ini agar tidak duplikat jika seeder dijalankan berulang kali.
                KategoriPenilaian::where('kriteria_id', $kriteria->id)->forceDelete();

                // Buat kategori penilaian baru untuk kriteria tersebut.
                foreach ($items as $item) {
                    KategoriPenilaian::create([
                        'kriteria_id'   => $kriteria->id,
                        'nama_kategori' => $item['nama_kategori'],
                        'nilai'         => $item['nilai'],
                    ]);
                }
            }
        }
    }
}
