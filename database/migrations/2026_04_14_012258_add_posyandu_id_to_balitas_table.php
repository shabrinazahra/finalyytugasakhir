<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * berfungsi menambahkan kolom posyandu_id pada tabel balitas di database
     */
    public function up(): void
    {
        Schema::table('balitas', function (Blueprint $table) {
            $table->foreignId('posyandu_id')
                ->nullable()
                ->constrained('posyandus')
                ->onDelete('cascade');
        });
    }

    /**
     * menghapus kolom posyandu_id pada tabel balitas di database 
     */
    public function down(): void
    {
        Schema::table('balitas', function (Blueprint $table) {
            $table->dropForeign(['posyandu_id']); 
            $table->dropColumn('posyandu_id'); 
        });
    }
};
