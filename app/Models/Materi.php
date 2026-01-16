<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    /** @use HasFactory<\Database\Factories\MateriFactory> */
    use HasUuids, HasFactory;
    protected $table = 'materials';

    protected $fillable = [
        'title',
        'created_by_user_id',
        'status',
        'publish_date',
        'kategori_materi',
        'description',
        'file_materi',
        'nomor_materi',
        'youtube_id',
        'kelas_ids',
        'matpel_kode',
    ];
    public function casts()
    {
        return [
            'kelas_ids' => "json",
            'file_materi' => "json",
            'created_at' => "datetime:d-M-Y h:i"
        ];
    }
    public function matpel()
    {
        return $this->belongsTo(Matpel::class);
    }
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'pengajarans', 'kelas_id');
    }
}
