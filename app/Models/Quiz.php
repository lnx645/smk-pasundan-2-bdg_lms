<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration',
        'passing_grade',
        'max_attempts',
        'is_randomized',
        'access_code',
        'is_published',
        'start_date',
        'end_date',
        'matpel_kode',
        'kelas_id',
        'guru_nip'
    ];

    protected $casts = [
        'is_randomized' => 'boolean',
        'is_published' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'passing_grade' => 'double',
    ];

    /**
     * Relasi ke Matpel (Mata Pelajaran)
     */
    public function matpel(): BelongsTo
    {
        // Parameter: (Model, foreign_key, owner_key)
        return $this->belongsTo(Matpel::class, 'matpel_kode', 'kode');
    }

    /**
     * Relasi ke Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi ke Guru (Pembuat Kuis)
     * Ditambahkan karena ada 'guru_nip' di fillable
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_nip', 'nip');
    }

    /**
     * Relasi ke Soal-soal (Questions)
     * Satu Quiz punya banyak Questions
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }
    /**
     * Relasi ke Percobaan Pengerjaan (Attempts)
     * Digunakan untuk mengecek apakah siswa sudah mengerjakan kuis ini.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
