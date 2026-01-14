<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruQuizQuestionController extends Controller
{
    public function index(Quiz $quiz)
    {
        $questions = $quiz->questions()
            ->with('options')
            ->orderBy('id', 'desc') // Supaya soal yang baru diinput muncul di atas
            ->get();
        return inertia('guru/quiz/questions/index', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }
    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'points' => 'required|numeric|min:1',
            'options' => 'required|array|min:2', // Minimal 2 pilihan jawaban
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        // Gunakan Transaksi DB agar Soal & Opsi tersimpan bersamaan
        DB::transaction(function () use ($quiz, $validated) {
            $question = $quiz->questions()->create([
                'question_text' => $validated['question_text'],
                'points' => $validated['points'],
                'sequence' => $quiz->questions()->count() + 1, // Urutan otomatis
            ]);

            // 2. Simpan Opsi Jawaban
            // Kita createMany secara otomatis dari array options
            $question->options()->createMany($validated['options']);
        });

        return back()->with('success', 'Soal berhasil ditambahkan!');
    }
    public function destroy(QuizQuestion $question)
    {
        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus.');
    }
}
