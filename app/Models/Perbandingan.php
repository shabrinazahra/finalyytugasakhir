<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perbandingan extends Model //mempresentasikan tabel perbandingan di database
{
    protected $fillable = [
        'kriteria_1',
        'kriteria_2',
        'nilai'
    ];
}