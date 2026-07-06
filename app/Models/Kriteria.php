<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kriteria extends Model //mempresentasikan tabel kriterias
{
    use SoftDeletes;
    
    protected $table = 'kriterias'; //menentukan nama tabel  kriteria 

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'atribut',
        'bobot'
    ];


    public function kategoriPenilaians() //relasi  one to many  dengan kategori penilaian 
    {
        return $this->hasMany(KategoriPenilaian::class);
    }
}