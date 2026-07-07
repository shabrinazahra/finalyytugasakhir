<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill bobot_snapshot dan nilai_kategori_snapshot yang NULL pada tabel penilaians.
     * 
     * Penilaian lama yang disimpan sebelum fitur snapshot ada memiliki bobot_snapshot = NULL.
     * Ini menyebabkan MooraCalculationService fallback ke bobot live dari tabel kriterias,
     * sehingga skor historis berubah setiap kali bobot AHP diupdate.
     * 
     * Migration ini mengisi snapshot NULL dengan nilai bobot dan nilai_kategori terkini
     * agar data historis menjadi stabil dan tidak terpengaruh perubahan bobot di masa depan.
     */
    public function up(): void
    {
        // Backfill bobot_snapshot dari tabel kriterias
        DB::statement("
            UPDATE penilaians
            INNER JOIN kriterias ON kriterias.id = penilaians.kriteria_id
            SET penilaians.bobot_snapshot = kriterias.bobot
            WHERE penilaians.bobot_snapshot IS NULL
              AND kriterias.bobot IS NOT NULL
        ");

        // Backfill nilai_kategori_snapshot dari tabel kategori_penilaians
        DB::statement("
            UPDATE penilaians
            INNER JOIN kategori_penilaians ON kategori_penilaians.id = penilaians.kategori_penilaian_id
            SET penilaians.nilai_kategori_snapshot = kategori_penilaians.nilai
            WHERE penilaians.nilai_kategori_snapshot IS NULL
              AND kategori_penilaians.nilai IS NOT NULL
        ");
    }

    /**
     * Rollback: set snapshot kembali ke NULL untuk data yang di-backfill.
     * PERINGATAN: Tidak bisa membedakan data yang aslinya NULL vs yang di-backfill,
     * jadi rollback ini tidak disarankan di production.
     */
    public function down(): void {}
};
