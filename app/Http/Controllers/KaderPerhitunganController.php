<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Balita;
use App\Models\Kriteria;
use App\Models\PenilaianBalita;
use App\Services\MooraCalculationService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KaderPerhitunganController extends Controller
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

    // ==========================================
    // dropdown periode daftar penilaian balita 
    // ==========================================
    private function getPeriodes($posyanduId) //mengambil periode daftar penilaian balita sesuai posyandu 
    {
        $balitaIds = Balita::where('posyandu_id', $posyanduId)->pluck('id'); //ambil id balita sesuai posyandu 
        
        $minDate = PenilaianBalita::whereIn('balita_id', $balitaIds) //mengambil tanggal penilaian balita terawal 
            ->whereNotNull('tanggal_penilaian')
            ->min('tanggal_penilaian');
        $maxDate = PenilaianBalita::whereIn('balita_id', $balitaIds) //mengambil tanggal penilaian balita terakhir
            ->whereNotNull('tanggal_penilaian')
            ->max('tanggal_penilaian');

        $minYear = $minDate ? Carbon::parse($minDate)->year : now()->year;
        $maxYear = $maxDate ? Carbon::parse($maxDate)->year : now()->year;

        $minYear = min($minYear, now()->year);
        $maxYear = max($maxYear, now()->year);

        $periodes = [];
        for ($year = $maxYear; $year >= $minYear; $year--) {
            for ($month = 12; $month >= 1; $month--) {
                $carbon = Carbon::createFromDate($year, $month, 1);
                $key = $carbon->format('Y-m');
                $label = $this->getIndonesianMonth($month);
                $periodes[$key] = $label;
            }
        }

        return $periodes;
    }

    private function getSelectedPeriode(Request $request, $posyanduId) //mengambil periode yang dipilih user 
    {
        if ($request->filled('periode')) {
            return $request->periode;
        }

        $balitaIds = Balita::where('posyandu_id', $posyanduId)->pluck('id');
        $latestDate = PenilaianBalita::whereIn('balita_id', $balitaIds)
            ->whereNotNull('tanggal_penilaian')
            ->max('tanggal_penilaian');

        if ($latestDate) {
            return Carbon::parse($latestDate)->format('Y-m');
        }

        return now()->format('Y-m');
    }

    // ==========================================
    // Halaman Perhitungan MOORA
    // ==========================================
    public function perhitungan(Request $request) //menampilkan halaman perhitungan moora 
    {
        $user = Auth::user();
        $periodes = $this->getPeriodes($user->posyandu_id);
        $selectedPeriode = $this->getSelectedPeriode($request, $user->posyandu_id);

        $service = new MooraCalculationService();
        $mooraData = $service->calculateMoora($selectedPeriode, $user->posyandu_id);

        return view('kader.perhitungan.index', array_merge($mooraData, [
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode
        ]));
    }

    // ==========================================
    // Halaman Perangkingan MOORA
    // ==========================================
    public function perangkingan(Request $request)// menampilan halaman perangkingan 
    {
        $user = Auth::user();
        $periodes = $this->getPeriodes($user->posyandu_id);
        $selectedPeriode = $this->getSelectedPeriode($request, $user->posyandu_id);

        $service = new MooraCalculationService();
        $mooraData = $service->calculateMoora($selectedPeriode, $user->posyandu_id);

        return view('kader.perangkingan.index', [
            'results' => $mooraData['results'],
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
            'isComplete' => $mooraData['isComplete'],
            'incompleteBalitas' => $mooraData['incompleteBalitas']
        ]);
    }

    // ==========================================
    // Halaman Laporan MOORA
    // ==========================================
    public function laporan(Request $request)//menampilkan laporan lengkap 
    {
        $user = Auth::user();
        $periodes = $this->getPeriodes($user->posyandu_id); 
        $selectedPeriode = $this->getSelectedPeriode($request, $user->posyandu_id); 

        $service = new MooraCalculationService(); //menghitung moora sesuai periode  dan posyandu user 
        $mooraData = $service->calculateMoora($selectedPeriode, $user->posyandu_id);

        return view('kader.laporan.index', [
            'results' => $mooraData['results'],
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
            'kriterias' => $mooraData['kriterias'],
            'isComplete' => $mooraData['isComplete'],
            'incompleteBalitas' => $mooraData['incompleteBalitas']
        ]);
    }
}
