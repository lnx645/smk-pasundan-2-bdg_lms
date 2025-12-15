<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    /** @use HasFactory<\Database\Factories\KelasFactory> */
    use HasFactory;
    protected $fillable = [
        'id',
        'nama',
        'tingkat',
    ];
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
    public function matpel()
    {
        return $this->belongsToMany(Matpel::class, 'pengajarans', 'kelas_id', 'matpel_kode');
    }
    public function pengajarans()
    {
        return $this->hasMany(Pengajaran::class, 'kelas_id', 'id');
    }
}
