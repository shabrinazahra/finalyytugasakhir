<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * berfungsi untuk memperbarui bobot kriteria dan snapshot bobot penilaian sesuai hasil AHP
     */
    public function up(): void
    {
        $weights = [
            'K1' => 0.320,
            'K2' => 0.228,
            'K3' => 0.163,
            'K4' => 0.108,
            'K5' => 0.077,
            'K6' => 0.061,
            'K7' => 0.043,
        ];

        foreach ($weights as $kode => $weight) {
            // Perbarui bobot pada tabel kriteria berdasarkan kode kriteria.
            DB::table('kriterias')
                ->where('kode_kriteria', $kode)
                ->update(['bobot' => $weight]);

            // Ambil data kriteria yang baru saja diperbarui untuk mengetahui id-nya.
            $kriteria = DB::table('kriterias')
                ->where('kode_kriteria', $kode)
                ->first();

            // Jika kriteria ditemukan, update juga snapshot bobot pada tabel penilaians.
            // Tujuannya agar penilaian lama tetap memakai bobot yang sama saat diproses.
            if ($kriteria) {
                DB::table('penilaians')
                    ->where('kriteria_id', $kriteria->id)
                    ->update(['bobot_snapshot' => $weight]);
            }
        }
    }

    /**
     * Mengembalikan nilai bobot ke default jika migrasi ini di rollback.
     */
    public function down(): void
    {
        // Kembalikan bobot ke nilai default 1.0 saat migrasi dibatalkan.
        DB::table('kriterias')->update(['bobot' => 1.0]);
        DB::table('penilaians')->update(['bobot_snapshot' => 1.0]);
    }
};
