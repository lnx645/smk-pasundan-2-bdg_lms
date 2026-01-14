<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizOption extends Model
{
    /** @use HasFactory<\Database\Factories\QuizOptionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quiz_question_id', // Nama kolom otomatis dari foreignIdFor(QuizQuestion::class)
        'option_text',
        'is_correct',
        'sequence',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_correct' => 'boolean', // Penting untuk logika frontend
        'sequence' => 'integer',
    ];

    /**
     * Relasi ke Soal (Parent)
     */
    public function question(): BelongsTo
    {
        // Parameter ke-2 opsional jika mengikuti konvensi Laravel (quiz_question_id)
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}
