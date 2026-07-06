<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Balita;
use App\Models\Kriteria;
use App\Models\Posyandu;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); //ambil data user yang login 

        // jika master admin
        if ($user->hasRole('master_admin')) {

            // hitung jumlah kader
            $jumlahKader = User::role('kader')->count();

            // hitung jumlah petugas
            $jumlahPetugas = User::role('petugas')->count();

            // hitung jumlah posyandu
            $jumlahPosyandu = Posyandu::count();

            return view('master_admin.dashboard', compact(
                'jumlahPosyandu',
                'jumlahKader',
                'jumlahPetugas'
            ));
        }

        // jika kader
        if ($user->hasRole('kader')) {
            $totalBalita = Balita::where('posyandu_id', $user->posyandu_id)->count();
            $jumlahLakiLaki = Balita::where('posyandu_id', $user->posyandu_id)->where('jenis_kelamin', 'Laki-laki')->count();
            $jumlahPerempuan = Balita::where('posyandu_id', $user->posyandu_id)->where('jenis_kelamin', 'Perempuan')->count();

            // Ambil bulan & tahun berjalan, misal: '2026-05'
            $currentMonth = now()->format('Y-m');
            $totalKriteria = Kriteria::count();

            // Ambil ID semua balita di posyandu kader ini
            $balitaIds = Balita::where('posyandu_id', $user->posyandu_id)->pluck('id');


            return view('kader.dashboard', compact(
                'totalBalita',
                'jumlahLakiLaki',
                'jumlahPerempuan',
            ));
        }

        // jika petugas
        if ($user->hasRole('petugas')) {
            $totalBalita     = Balita::count();
            $jumlahLakiLaki  = Balita::where('jenis_kelamin', 'Laki-laki')->count();
            $jumlahPerempuan = Balita::where('jenis_kelamin', 'Perempuan')->count();

            return view('petugas.dashboard', compact(
                'totalBalita',
                'jumlahLakiLaki',
                'jumlahPerempuan'
            ));
        }

        // paksa logout jika tidak memiliki role
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
