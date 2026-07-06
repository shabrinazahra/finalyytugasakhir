<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriPenilaian extends Model //mempresentasikan tabel kategori penilaian
{
    use SoftDeletes; 
    protected $fillable = [
        'kriteria_id',
        'nama_kategori',
        'nilai'
    ];

    public function kriteria() //relasi ke kriteria many to one 
    {
        return $this->belongsTo(Kriteria::class);
    }
}