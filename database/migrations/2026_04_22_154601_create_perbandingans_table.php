<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void //digunakan untuk membuat tabel perbandingans di database
    {
        Schema::create('perbandingans', function (Blueprint $table) {
            $table->id(); //primary key perbandingans

            $table->foreignId('kriteria_1') //menghubungkan dengan tabel kriteria
                  ->constrained('kriterias')
                  ->cascadeOnDelete();

            $table->foreignId('kriteria_2') //menghubungkan dengan tabel kriteria
                  ->constrained('kriterias')
                  ->cascadeOnDelete();

            $table->double('nilai'); //menyimpan nilai perbandingan antara kriteria

            $table->timestamps();

            $table->unique(['kriteria_1', 'kriteria_2']); //memastikan kombinasi kriteria_1 dan kriteria_2 tidak duplikat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perbandingans');
    }
};