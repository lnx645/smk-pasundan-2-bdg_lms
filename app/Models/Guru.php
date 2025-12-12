<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Guru extends Model
{
    /** @use HasFactory<\Database\Factories\GuruFactory> */
    use HasFactory;
    protected $primaryKey = 'nip';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'nip',
        'jenis_kelamin',
        'foto',
        'status',
        'user_id',
        'gelar_depan',
        'gelar_belakang',
    ];
    protected $appends = ['foto_url'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function getFotoUrlAttribute()
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return asset('storage/' . $this->foto);
        }
        // Return gambar default jika tidak ada foto
        // Pastikan Anda punya file ini di public/images/default-avatar.png
        return asset('images/default-avatar.png');
    }
    public function matpels()
    {
        // PENTING: Ganti 'nama_tabel_pivot' dengan nama tabel dari migrasi kamu
        // (biasanya nama tabelnya: 'guru_matpel', 'jadwal_pelajarans', atau 'pengajars')
        return $this->belongsToMany(
            Matpel::class,           // Model Tujuan
            'pengajarans',      // Nama Tabel Pivot (tabel di migrasi kamu)
            'guru_nip',              // Foreign Key di tabel pivot untuk model ini (Guru)
            'matpel_kode',           // Foreign Key di tabel pivot untuk model lawan (Matpel)
            'nip',                   // Primary Key lokal (Guru)
            'kode'                   // Primary Key lawan (Matpel)
        )->withPivot('kelas_id');    // Opsional: Jika ingin mengambil data kelas_id juga
    }
    public function pengajarans()
    {
        return $this->hasMany(Pengajaran::class, 'guru_nip', 'nip');
    }
}
