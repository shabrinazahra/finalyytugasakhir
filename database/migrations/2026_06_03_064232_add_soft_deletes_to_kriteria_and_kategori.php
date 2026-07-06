<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * menambahkan kolom deleted_at pada tabel kriteria dan kategori_penilaians di database
     */
    public function up(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('kategori_penilaians', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * berfungsi untuk menghapus kolom deleted_at pada tabel kriteria dan kategori_penilaians di database
     */
    public function down(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('kategori_penilaians', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
