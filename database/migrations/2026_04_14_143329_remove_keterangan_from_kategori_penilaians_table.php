<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void //menghapus kolom keterangan pada tabel kategori_penilaians di database
    {
        Schema::table('kategori_penilaians', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }

    public function down(): void //mengembalikan kolom keterangan pada tabel kategori_penilaians di database
    {
        Schema::table('kategori_penilaians', function (Blueprint $table) {
            $table->string('keterangan')->nullable();
        });
    }
};