<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balita extends Model //mempresentasikan tabel balita di database
{
    protected $fillable = [
        'nama',
        'nik',
        'jenis_kelamin',
        'tanggal_lahir',
        'nama_ortu',
        'posyandu_id',
    ];

    //Relasi ke Posyandu Many to One
    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }
}
