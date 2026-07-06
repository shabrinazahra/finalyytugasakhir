<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //berfungsi membuat tabel penilaian di database
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();

            // RELASI
            $table->foreignId('balita_id') //menghubungkan dengan tabel balita
                  ->constrained('balitas')
                  ->cascadeOnDelete();

            $table->foreignId('kriteria_id')//menghubungkan dengan tabel kriteria
                  ->constrained('kriterias')
                  ->cascadeOnDelete();

            $table->foreignId('kategori_penilaian_id')//menghubungkan dengan tabel kategori penilaian
                  ->constrained('kategori_penilaians')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};