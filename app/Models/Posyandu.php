<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model //mempresentasikan tabel posyandu 
{
    protected $fillable = ['nama_posyandu', 'alamat'];

    public function users() //relasi ke user one to many
    {
        return $this->hasMany(User::class);
    }

    public function kader() //relasi ke user one to one dengan role kader
    {
        return $this->hasOne(User::class)->whereHas('roles', function ($query) {
            $query->where('name', 'kader');
        });
    }

    public function balitas() //relasi ke balita one to many
    {
        return $this->hasMany(Balita::class);
    }
}