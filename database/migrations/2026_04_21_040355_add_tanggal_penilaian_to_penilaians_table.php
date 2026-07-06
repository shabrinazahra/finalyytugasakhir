<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void //menambahkan kolom tanggal_penilaian pada tabel penilaians di database
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->date('tanggal_penilaian')->nullable()->after('balita_id');
        });
    }

    public function down(): void //menghapus kolom tanggal_penilaian pada tabel penilaians di database
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->dropColumn('tanggal_penilaian');
        });
    }
};
