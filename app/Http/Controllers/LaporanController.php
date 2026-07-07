<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenilaianBalita;
use App\Models\Posyandu;
use App\Models\Kriteria;
use Carbon\Carbon;

class LaporanController extends Controller
{
    private function getIndonesianMonth($monthNum) // mengubah angka menjadi nama bulan 
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[intval($monthNum)] ?? '';
    }

    public function index(Request $request) // menampilkan halaman laporan sesuai filter 
    {
        // Ambil data kriteria dan posyandu untuk ditampilkan di form filter
        $kriterias = Kriteria::orderBy('id')->get();
        $posyandus = Posyandu::orderBy('nama_posyandu')->get();

        // Ambil tanggal penilaian terawal dan terakhir untuk menentukan rentang periode
        $minDate = PenilaianBalita::whereNotNull('tanggal_penilaian')->min('tanggal_penilaian');
        $maxDate = PenilaianBalita::whereNotNull('tanggal_penilaian')->max('tanggal_penilaian');

        // Tentukan tahun minimum dan maksimum yang akan ditampilkan di dropdown periode
        $minYear = $minDate ? Carbon::parse($minDate)->year : now()->year;
        $maxYear = $maxDate ? Carbon::parse($maxDate)->year : now()->year;

        // Pastikan rentang periode tidak melewati tahun saat ini
        $minYear = min($minYear, now()->year);
        $maxYear = max($maxYear, now()->year);

        // Bangun daftar periode dari tahun terbaru ke terlama, dari bulan 12 ke 1
        $periodes = [];
        for ($year = $maxYear; $year >= $minYear; $year--) {
            for ($month = 12; $month >= 1; $month--) {
                $carbon = Carbon::createFromDate($year, $month, 1);
                $key = $carbon->format('Y-m');
                $label = $this->getIndonesianMonth($month);
                $periodes[$key] = $label;
            }
        }

        $service = new \App\Services\MooraCalculationService();

        // Tentukan daftar posyandu yang akan dihitung
        $posyanduIds = $request->filled('posyandu_id')
            ? [$request->posyandu_id]
            : $posyandus->pluck('id')->toArray();

        // Tentukan daftar periode yang akan dihitung
        $periodeKeys = $request->filled('periode')
            ? [$request->periode]
            : array_keys($periodes);

        $results = [];
        $isComplete = true;
        $incompleteBalitas = [];

        // Hitung MOORA untuk setiap kombinasi (posyandu, periode)
        foreach ($posyanduIds as $posId) {
            foreach ($periodeKeys as $periodeKey) {
                $mooraData = $service->calculateMoora($periodeKey, $posId);

                $results = array_merge($results, $mooraData['results']);

                if (!$mooraData['isComplete']) {
                    $isComplete = false;
                }
                $incompleteBalitas = array_merge($incompleteBalitas, $mooraData['incompleteBalitas']);
            }
        }

        // Urutkan ulang hasil gabungan berdasarkan skor tertinggi
        usort($results, function ($a, $b) {
            if (abs($b['nilai_akhir'] - $a['nilai_akhir']) > 0.000001) {
                return $b['nilai_akhir'] <=> $a['nilai_akhir'];
            }
            return strcmp($a['balita']->nama, $b['balita']->nama);
        });

        // Cek apakah ada kriteria yang belum memiliki bobot
        $hasAnyNullBobot = $kriterias->whereNull('bobot')->isNotEmpty();

        return view('petugas.laporan.index', compact(
            'kriterias',
            'posyandus',
            'periodes',
            'results',
            'hasAnyNullBobot',
            'isComplete',
            'incompleteBalitas'
        ));
    }
}
