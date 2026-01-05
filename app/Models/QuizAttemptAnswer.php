<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttemptAnswer extends Model
{
    /**
     * Kolom yang bisa diisi (Mass Assignment).
     * Digunakan saat user submit jawaban di controller.
     */
    protected $fillable = [
        'quiz_attempt_id',   // ID sesi ujian
        'quiz_question_id',  // ID soal
        'quiz_option_id',    // ID jawaban yang dipilih user
    ];

    /* -------------------------------------------------------------------------- */
    /* RELASI                                                                     */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke QuizAttempt (Sesi pengerjaan induknya)
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    /**
     * Relasi ke Soal (Pertanyaan)
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    /**
     * Relasi ke Jawaban yang dipilih.
     * * PENTING: Nama fungsi ini 'option' (bukan quizOption),
     * karena di controller Anda memanggilnya dengan:
     * ->whereHas('option', ...)
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(QuizOption::class, 'quiz_option_id');
    }
}
