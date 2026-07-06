<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kriteria;
use App\Models\KategoriPenilaian;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definisikan daftar kriteria awal yang digunakan dalam sistem penilaian balita.
        // Setiap kriteria memiliki kode, nama, atribut, bobot AHP, dan kategori penilaiannya.
        $kriterias = [
            [
                'kode_kriteria' => 'K1',
                'nama_kriteria' => 'Berat badan',
                'atribut' => 'benefit',
                'bobot' => 0.320,
                'kategori' => [
                    ['nama_kategori' => 'Berat badan sangat kurang', 'nilai' => 5],
                    ['nama_kategori' => 'Berat badan kurang', 'nilai' => 3],
                    ['nama_kategori' => 'Berat badan Normal', 'nilai' => 1],
                ]
            ],
            [
                'kode_kriteria' => 'K2',
                'nama_kriteria' => 'Tinggi badan',
                'atribut' => 'benefit',
                'bobot' => 0.228,
                'kategori' => [
                    ['nama_kategori' => 'Tinggi Badan Dibawah standar usia/Melandai', 'nilai' => 5],
                    ['nama_kategori' => 'Normal Sesuai/diatas standar usia', 'nilai' => 1],
                ]
            ],
            [
                'kode_kriteria' => 'K3',
                'nama_kriteria' => 'Sakit Infeksi',
                'atribut' => 'benefit',
                'bobot' => 0.163,
                'kategori' => [
                    ['nama_kategori' => 'Sering (Lebih dari 3 kali dalam sebulan )', 'nilai' => 5],
                    ['nama_kategori' => 'Jarang (kurang dari 1 - 3 kali dalam sebulan )', 'nilai' => 3],
                    ['nama_kategori' => 'Tidak pernah dalam sebulan', 'nilai' => 1],
                ]
            ],
            [
                'kode_kriteria' => 'K4',
                'nama_kriteria' => 'Berat Badan Lahir',
                'atribut' => 'benefit',
                'bobot' => 0.108,
                'kategori' => [
                    ['nama_kategori' => 'BBLR < 2500g', 'nilai' => 5],
                    ['nama_kategori' => 'Normal > sama dengan 2500g', 'nilai' => 1],
                ]
            ],
            [
                'kode_kriteria' => 'K5',
                'nama_kriteria' => 'Riwayat Ibu KEK',
                'atribut' => 'benefit',
                'bobot' => 0.077,
                'kategori' => [
                    ['nama_kategori' => 'KEK (Lila < 23,5cm)', 'nilai' => 5],
                    ['nama_kategori' => 'Normal (Lila lebih sama dengan dari 23,5cm)', 'nilai' => 1],
                ]
            ],
            [
                'kode_kriteria' => 'K6',
                'nama_kriteria' => 'Pemberian Asi',
                'atribut' => 'benefit',
                'bobot' => 0.061,
                'kategori' => [
                    ['nama_kategori' => 'Tidak Asi', 'nilai' => 5],
                    ['nama_kategori' => 'Asi tidak eksklusif', 'nilai' => 3],
                    ['nama_kategori' => 'Asi eksklusif', 'nilai' => 1],
                ]
            ],
            [
                'kode_kriteria' => 'K7',
                'nama_kriteria' => 'Imunisasi',
                'atribut' => 'benefit',
                'bobot' => 0.043,
                'kategori' => [
                    ['nama_kategori' => 'Belum imunisasi', 'nilai' => 5],
                    ['nama_kategori' => 'Tidak lengkap sesuai usia', 'nilai' => 3],
                    ['nama_kategori' => 'Lengkap sesuai usia', 'nilai' => 1],
                ]
            ],
        ];

        // Proses setiap kriteria dan buat data kategori penilaiannya.
        foreach ($kriterias as $kriteriaData) {
            // Pisahkan kategori dari data utama kriteria agar bisa dibuat setelah kriteria tersimpan.
            $kategoriPenilaians = $kriteriaData['kategori'];
            unset($kriteriaData['kategori']);

            // Hapus data kriteria lama dengan kode yang sama, termasuk yang di-soft-delete, agar tidak duplikat.
            $existing = Kriteria::withTrashed()->where('kode_kriteria', $kriteriaData['kode_kriteria'])->first();
            if ($existing) {
                $existing->forceDelete();
            }

            // Simpan kriteria baru ke database.
            $kriteria = Kriteria::create($kriteriaData);

            // Buat kategori penilaian yang terkait dengan kriteria tersebut.
            foreach ($kategoriPenilaians as $kategoriData) {
                $kategoriData['kriteria_id'] = $kriteria->id;
                KategoriPenilaian::create($kategoriData);
            }
        }
    }
}
