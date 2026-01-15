<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Tugas extends Model
{
    protected $primaryKey = 'tugasID';
    protected $fillable = [
        'matpel_kode',
        'title',
        'receiver_type',
        'receiver_type_id',
        'content',
        'created_by_user_id',
        'mode_pengumpulan',
        'deadline',
        'publish_date',
    ];
    /** @use HasFactory<\Database\Factories\TugasFactory> */
    use HasFactory, HasUuids;
    public function matpel()
    {
        return $this->belongsTo(Matpel::class);
    }
    public function casts()
    {
        return [
            'deadline' => "datetime",
            'receiver_type_id' => "array"
        ];
    }
    protected function deadline(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)
                ->timezone('Asia/Jakarta')
                ->format('d-M-Y h:i:s A'),
        );
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
    public function jawaban()
    {
        return $this->hasMany(JawabanTugas::class, 'tugas_id');
    }
    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'tugas_id', 'tugasID');
    }
    public function mapel()
    {
        return $this->belongsTo(Matpel::class, 'matpel_kode');
    }
    public function guru()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
