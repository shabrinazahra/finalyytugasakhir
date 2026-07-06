<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Perbandingan;

class PerhitunganAHPController extends Controller
{
    // =============================
    // HALAMAN INPUT
    // =============================
    // Menampilkan halaman yang berisi form input perbandingan antar kriteria.
    // Data kriteria diambil dari database lalu ditampilkan untuk dipilih dan dibandingkan.
    public function index()
    {
        $kriterias = Kriteria::orderBy('id')->get();
        return view('petugas.perhitunganAHP.index', compact('kriterias'));
    }

    // =============================
    // SIMPAN PERBANDINGAN
    // =============================
    // Menyimpan hasil penilaian perbandingan Jika pasangan kriteria sudah ada, data akan diupdate; jika belum, akan dibuat data baru.
    public function store(Request $request)
    {
        // Pastikan field perbandingan ada dan merupakan array sebelum diproses.
        if (!$request->has('perbandingan') || !is_array($request->perbandingan)) {
            return back()->with('error', 'Tidak ada data perbandingan yang dikirim.');
        }

        foreach ($request->perbandingan as $data) {
            // Lewati pasangan yang tidak memiliki nilai, misalnya field kosong.
            if (!isset($data['nilai']) || $data['nilai'] === '') {
                continue;
            }

            $nilai = $data['nilai'];

            // Jika input berupa pecahan seperti "1/3", ubah menjadi angka desimal.
            if (is_string($nilai) && strpos($nilai, '/') !== false) {
                $parts = explode('/', $nilai);
                if (isset($parts[0], $parts[1]) && $parts[1] != 0) {
                    $nilai = $parts[0] / $parts[1];
                }
            }

            // Simpan atau perbarui nilai perbandingan untuk pasangan kriteria tertentu.
            Perbandingan::updateOrCreate(
                [
                    'kriteria_1' => $data['k1'],
                    'kriteria_2' => $data['k2'],
                ],
                [
                    'nilai' => floatval($nilai)
                ]
            );
        }

        return back()->with('success', 'Nilai perbandingan berhasil disimpan');
    }

    // =============================
    // GENERATE AHP
    // =============================
    // Menghasilkan bobot kriteria menggunakan metode AHP berdasarkan perbandingan pasangan.
    // Prosesnya meliputi pembentukan matriks perbandingan, normalisasi, perhitungan bobot,
    // lalu evaluasi konsistensi dengan rasio konsistensi (CR).
    public function generate()
    {
        // Ambil semua kriteria yang digunakan sebagai dasar perhitungan AHP.
        $kriterias = Kriteria::orderBy('id')->get();
        $n = $kriterias->count();

        // Pastikan minimal terdapat dua kriteria sebelum menghitung.
        if ($n < 2) {
            return back()->with('error', 'Minimal harus ada 2 kriteria untuk melakukan perhitungan AHP.');
        }

        $perbandingans = Perbandingan::all();

        // Rumus jumlah pasangan perbandingan untuk n kriteria adalah n(n-1)/2.
        // Contoh: jika ada 4 kriteria, maka dibutuhkan 6 pasangan perbandingan.
        $expectedCount = ($n * ($n - 1)) / 2;

        // Cek apakah semua perbandingan antar kriteria sudah lengkap.
        if ($perbandingans->count() < $expectedCount) {
            return back()->with(
                'error',
                'Perbandingan belum lengkap! Dibutuhkan ' . $expectedCount .
                    ' pasangan, baru ada ' . $perbandingans->count() . ' pasangan. ' .
                    'Silakan lengkapi perbandingan yang masih kurang.'
            );
        }

        // 1. MEMBANGUN MATRIKS PERBANDINGAN BERPASANGAN
        // Nilai diagonal utama selalu 1 karena kriteria dibandingkan dengan dirinya sendiri.
        // Jika nilai untuk pasangan (i,j) tidak ada, gunakan nilai kebalikan dari (j,i).
        $matrix = [];
        foreach ($kriterias as $i) {
            foreach ($kriterias as $j) {

                if ($i->id == $j->id) {
                    $matrix[$i->id][$j->id] = 1;
                } else {

                    // Cari nilai perbandingan yang tersimpan untuk pasangan (i, j).
                    $data = $perbandingans
                        ->where('kriteria_1', $i->id)
                        ->where('kriteria_2', $j->id)
                        ->first();

                    if ($data) {
                        // Jika ada data langsung, gunakan nilai tersebut.
                        $matrix[$i->id][$j->id] = round($data->nilai, 4);
                    } else {
                        // Jika tidak ada data untuk (i, j), cari pasangan kebalikannya (j, i).
                        // Nilai pada (i, j) diasumsikan sebagai kebalikan dari (j, i).
                        $reverse = $perbandingans
                            ->where('kriteria_1', $j->id)
                            ->where('kriteria_2', $i->id)
                            ->first();

                        $matrix[$i->id][$j->id] = $reverse ? round(1 / $reverse->nilai, 4) : 1;
                    }
                }
            }
        }

        // 2. MENGHITUNG JUMLAH SETIAP KOLOM MATRIKS
        $jumlahKolom = [];
        foreach ($kriterias as $j) {
            $total = 0;
            foreach ($kriterias as $i) {
                $total += $matrix[$i->id][$j->id];
            }
            $jumlahKolom[$j->id] = $total;
        }

        // 3. NORMALISASI MATRIKS
        // Setiap elemen dibagi dengan jumlah kolomnya agar total kolom menjadi 1.
        $normalisasi = [];
        foreach ($kriterias as $i) {
            foreach ($kriterias as $j) {
                $normalisasi[$i->id][$j->id] =
                    $matrix[$i->id][$j->id] / $jumlahKolom[$j->id];
            }
        }

        // 4. MENGHITUNG JUMLAH BARIS
        // Jumlah baris ini nantinya dipakai untuk menghitung bobot prioritas.
        $jumlahBaris = [];
        foreach ($kriterias as $i) {
            $total = 0;
            foreach ($kriterias as $j) {
                $total += $normalisasi[$i->id][$j->id];
            }
            $jumlahBaris[$i->id] = $total;
        }

        // 5. MENGHITUNG BOBOT KRITERIA
        // Bobot = rata-rata nilai normalisasi pada setiap baris.
        $bobot = [];
        foreach ($jumlahBaris as $id => $total) {
            $bobot[$id] = $total / $n;
        }

        // 6. MENGHITUNG VEKTOR KONSISTENSI
        // Menentukan hasil perkalian matriks awal dengan bobot untuk mengecek konsistensi.
        $konsistensi = [];
        foreach ($kriterias as $i) {
            $total = 0;
            foreach ($kriterias as $j) {
                $total += $matrix[$i->id][$j->id] * $bobot[$j->id];
            }
            $konsistensi[$i->id] = $total;
        }

        // 7. MENGHITUNG LAMBDA MAX
        // Lambda max adalah rata-rata rasio konsistensi setiap baris.
        $lambdaMax = 0;
        foreach ($kriterias as $i) {
            $lambdaMax += $konsistensi[$i->id] / $bobot[$i->id];
        }
        $lambdaMax /= $n;

        // 8. MENGHITUNG INDEKS KONSISTENSI (CI)
        $CI = ($lambdaMax - $n) / ($n - 1);

        // 9. MENGHITUNG RASIO KONSISTENSI (CR)
        // RI (Random Index) dipilih sesuai jumlah kriteria.
        $RI = [
            1 => 0,
            2 => 0,
            3 => 0.58,
            4 => 0.90,
            5 => 1.12,
            6 => 1.24,
            7 => 1.32,
            8 => 1.41,
            9 => 1.45,
            10 => 1.49
        ];

        // Jika RI 0, maka CR dianggap 0 untuk menghindari pembagian dengan nol.
        $CR = $RI[$n] != 0 ? $CI / $RI[$n] : 0;

        return view('petugas.perhitunganAHP.hasilAHP', compact(
            'kriterias',
            'matrix',
            'jumlahKolom',
            'normalisasi',
            'jumlahBaris',
            'bobot',
            'konsistensi',
            'lambdaMax',
            'CI',
            'CR'
        ));
    }

    // =============================
    // SIMPAN BOBOT HASIL AHP
    // =============================
    // Menyimpan bobot hasil perhitungan AHP ke tabel kriteria.
    // Bobot ini nantinya dipakai pada proses keputusan atau perhitungan metode lain.
    public function saveWeights(Request $request)
    {
        // Validasi input agar semua bobot berupa array angka numerik.
        $request->validate([
            'bobot' => 'required|array',
            'bobot.*' => 'required|numeric'
        ]);

        foreach ($request->bobot as $kriteriaId => $nilaiBobot) {
            $kriteria = Kriteria::find($kriteriaId);
            if ($kriteria) {
                // Simpan bobot ke database dengan format float.
                $kriteria->update(['bobot' => floatval($nilaiBobot)]);
            }
        }

        return redirect()->route('petugas.perhitunganAHP.index')
            ->with('success', 'Bobot kriteria hasil perhitungan AHP berhasil disimpan ke database!');
    }
}
