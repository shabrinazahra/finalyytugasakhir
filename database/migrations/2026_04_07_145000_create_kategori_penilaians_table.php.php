<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    //berfungsi membuat tabel kategori penilaian di database
    public function up(): void
    {
        Schema::create('kategori_penilaians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kriteria_id') //menghubungkan dengan tabel kriteria
                  ->constrained('kriterias')
                  ->cascadeOnDelete(); //jika kriteria dihapus, kategori penilaian terkait juga akan dihapus

            $table->string('nama_kategori');
            $table->string('keterangan');
            $table->integer('nilai');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_penilaians');
    }
};