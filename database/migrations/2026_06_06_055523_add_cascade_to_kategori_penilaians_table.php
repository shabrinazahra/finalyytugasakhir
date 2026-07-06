<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //mengubah relasi foreign key pada tabel kategori_penilaians agar menggunakan cascade on delete kriteria dihapus kategori penilaian dihapus
    public function up(): void
    {
        Schema::table('kategori_penilaians', function (Blueprint $table) {
            $table->dropForeign(['kriteria_id']);

            $table->foreign('kriteria_id')
                ->references('id')
                ->on('kriterias')
                ->onDelete('cascade');
        });
    }

    public function down(): void //berfungsi untuk mengembalikan foreign key seperti sebelum migrtaion dijalankan 
    {
        Schema::table('kategori_penilaians', function (Blueprint $table) {
            $table->dropForeign(['kriteria_id']); //menghapus foreign key yang menggunakan cascade

            $table->foreign('kriteria_id') //membuat kemabli foreign key tanpa atauran cascade  
                ->references('id')
                ->on('kriterias');
        });
    }
};
