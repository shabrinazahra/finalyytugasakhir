<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * menambahkan kolom bobot_snapshot dan nilai_kategori_snapshot pada tabel penilaians di database
     */
    public function up(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->double('bobot_snapshot')->nullable(); //menyimpan bobot snapshot dari penilaian
            $table->integer('nilai_kategori_snapshot')->nullable(); //menyimpan nilai kategori snapshot dari penilaian
        });
    }

    /**
     * menghapus kolom atau rollback
     */
    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->dropColumn(['bobot_snapshot', 'nilai_kategori_snapshot']); //menghapus kolom bobot_snapshot dan nilai_kategori_snapshot dari tabel penilaians
        });
    }
};
