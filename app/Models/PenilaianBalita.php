<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianBalita extends Model //mempresentasikan tabel penilaian balita di database 
{
    protected $table = 'penilaians'; //menentukan nama tabel penilaian 

    protected $fillable = [
        'balita_id',
        'kriteria_id',
        'kategori_penilaian_id',
        'tanggal_penilaian',
        'bobot_snapshot',
        'nilai_kategori_snapshot'
    ];

    // RELASI
    public function balita() //relasi ke balita many to one
    {
        return $this->belongsTo(Balita::class);
    }

    public function kriteria() //relasi ke kriteria many to one
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function kategori() //relasi ke kategori penilaian many to one

    {
        return $this->belongsTo(KategoriPenilaian::class, 'kategori_penilaian_id');
    }
}
