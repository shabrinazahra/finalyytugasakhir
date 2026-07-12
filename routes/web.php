<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BalitaController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\PosyanduController;
use App\Http\Controllers\KategoriPenilaianController;
use App\Http\Controllers\PenilaianBalitaController;
use App\Http\Controllers\PerhitunganAHPController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KaderPerhitunganController;
use App\Http\Controllers\TemplateExcelController;

// ==================
// MENAMPILKAN HALAMAN AWAL
// ==================
Route::get('/', function () {
    return view('landingpage');
});

// ==================
// DASHBOARD MASTER ADMIN
// ==================
Route::prefix('dashboard')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('posyandu', PosyanduController::class);
});

// ==================
// PROFILE
// ==================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ==================
// KADER
// ==================
Route::prefix('kader')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('kader.dashboard');

    Route::get('/balita/template', [TemplateExcelController::class, 'balitaTemplate'])->name('balita.template');
    Route::post('/balita/import', [BalitaController::class, 'import'])->name('balita.import');
    Route::get('/balita', [BalitaController::class, 'index'])->name('balita.index');
    Route::get('/balita/create', [BalitaController::class, 'create'])->name('balita.create');
    Route::post('/balita', [BalitaController::class, 'store'])->name('balita.store');
    Route::get('/balita/{id}/edit', [BalitaController::class, 'edit'])->name('balita.edit');
    Route::put('/balita/{id}', [BalitaController::class, 'update'])->name('balita.update');
    Route::delete('/balita/{id}', [BalitaController::class, 'destroy'])->name('balita.destroy');
    Route::get('/balita/{id}', [BalitaController::class, 'show'])->name('balita.show');

    // ======================
    // PENILAIAN BALITA
    // ======================
    Route::get('/penilaian-balita', [PenilaianBalitaController::class, 'index'])
        ->name('penilaian_balita.index');

    Route::get('/penilaian-balita/balita-tersedia', [PenilaianBalitaController::class, 'balitaTersedia'])
        ->name('penilaian_balita.balitaTersedia');

    Route::get('/penilaian-balita/create', [PenilaianBalitaController::class, 'create'])
        ->name('penilaian_balita.create');

    Route::post('/penilaian-balita', [PenilaianBalitaController::class, 'store'])
        ->name('penilaian_balita.store');

    Route::get('/penilaian-balita/input-massal', [PenilaianBalitaController::class, 'createMassal'])
        ->name('penilaian_balita.create_massal');

    Route::post('/penilaian-balita/input-massal', [PenilaianBalitaController::class, 'storeMassal'])
        ->name('penilaian_balita.store_massal');

    Route::get('/penilaian-balita/{id}/edit', [PenilaianBalitaController::class, 'edit'])
        ->name('penilaian_balita.edit');

    Route::put('/penilaian-balita/{id}', [PenilaianBalitaController::class, 'update'])
        ->name('penilaian_balita.update');

    Route::delete('/penilaian-balita/{balita_id}', [PenilaianBalitaController::class, 'destroy'])
        ->name('penilaian_balita.destroy');



    // ======================
    // LAPORAN KADER
    // ======================
    Route::get('/perhitungan', [KaderPerhitunganController::class, 'perhitungan'])
        ->name('kader.perhitungan.index');

    Route::get('/perangkingan', [KaderPerhitunganController::class, 'perangkingan'])
        ->name('kader.perangkingan.index');

    Route::get('/laporan', [KaderPerhitunganController::class, 'laporan'])
        ->name('kader.laporan.index');
});

// ==================
// PETUGAS 
// ==================
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('kriteria', KriteriaController::class);

        Route::resource('kategori_penilaian', KategoriPenilaianController::class);
        Route::get('/perhitungan-ahp', [PerhitunganAHPController::class, 'index'])
            ->name('perhitunganAHP.index');

        Route::post('/perhitungan-ahp/store', [PerhitunganAHPController::class, 'store'])
            ->name('perhitunganAHP.store');

        Route::get('/perhitungan-ahp/generate', [PerhitunganAHPController::class, 'generate'])
            ->name('perhitunganAHP.generate');

        Route::post('/perhitungan-ahp/save-weights', [PerhitunganAHPController::class, 'saveWeights'])
            ->name('perhitunganAHP.saveWeights');

        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');
    });

// ==================
require __DIR__ . '/auth.php';
