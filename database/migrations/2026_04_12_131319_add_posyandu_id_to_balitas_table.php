<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * berfungsi untuk menambahkan kolom posyandu_id pada tabel balitas di database
     */
    public function up(): void
    {
        Schema::table('balitas', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balitas', function (Blueprint $table) {
            $table->foreignId('posyandu_id')
                ->constrained('posyandus')
                ->cascadeOnDelete();
        });
        
    }
};
