<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    /**
     * Kolom yang boleh diisi secara massal (mass assignment).
     * Berdasarkan penggunaan di QuizSiswaController.
     */
    protected $fillable = [
        'quiz_id',      // Diisi saat start()
        'user_id',      // Diisi saat start()
        'started_at',   // Diisi saat start()
        'finished_at',  // Diisi saat submit()
        'score',        // Diisi saat submit()
    ];

    /**
     * Casting tipe data otomatis.
     * Penting agar started_at & finished_at langsung jadi object Carbon (Date).
     */
    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'score' => 'integer', // Ganti 'float' jika nilai bisa desimal
    ];

    /* -------------------------------------------------------------------------- */
    /* RELASI (Berdasarkan kode Controller)                                       */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke Quiz (Kuis apa yang dikerjakan)
     * Controller: $attempt->quiz
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relasi ke User (Siapa yang mengerjakan)
     * Controller: $attempt->user_id
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Jawaban Detail
     * Controller: QuizAttemptAnswer::where('quiz_attempt_id', ...)
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }
}
