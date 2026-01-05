<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuizSiswaController extends Controller
{
    /**
     * 1. Halaman Daftar Kuis (Dashboard Siswa)
     */
    public function index()
    {

        $user = Auth::user();

        $quizzes = Quiz::with(['matpel', 'guru.user'])
            // ->where('kelas_id', $kelasId) // Uncomment jika sudah ada filter kelas
            ->where('is_published', true)
            ->withCount('questions')
            ->latest()
            ->get()
            ->map(function ($quiz) use ($user) {
                // Cek apakah siswa sudah pernah mengerjakan kuis ini?
                $lastAttempt = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->latest()
                    ->first();

                $quiz->last_attempt = $lastAttempt;
                return $quiz;
            });

        return Inertia::render('siswa/quiz/index', [
            'quizzes' => $quizzes
        ]);
    }

    /**
     * 2. Halaman Persiapan (Lobby)
     */
    public function show(Quiz $quiz)
    {
        return Inertia::render('siswa/quiz/show', [
            'quiz' => $quiz->load(['matpel', 'kelas', 'guru'])
                ->loadCount('questions'),
        ]);
    }

    /**
     * 3. Mulai Mengerjakan (Start Logic)
     */
    public function start(Quiz $quiz)
    {
        // Cek Max Attempts (Opsional)
        /*
        $count = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', Auth::id())->count();
        if ($count >= $quiz->max_attempts) {
            return back()->with('error', 'Kesempatan habis.');
        }
        */

        // Buat session attempt baru
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::id(),
            'started_at' => now(),
        ]);

        return redirect()->route('siswa.quiz.work', $attempt->id);
    }

    /**
     * 4. Halaman Pengerjaan (Exam Interface)
     */
    public function work(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) abort(403);

        // Jika sudah selesai, lempar ke halaman result
        if ($attempt->finished_at) {
            return redirect()->route('siswa.quiz.result', $attempt->id);
        }

        $quiz = $attempt->quiz;

        $questions = $quiz->questions()
            ->with(['options' => function ($query) {
                $query->select('id', 'quiz_question_id', 'option_text'); // Hide is_correct
            }])
            ->when($quiz->is_randomized, function ($q) {
                $q->inRandomOrder();
            })
            ->get()
            ->map(function ($q) {
                $q->options = $q->options->shuffle();
                return $q;
            });

        return Inertia::render('siswa/quiz/work', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'questions' => $questions,
        ]);
    }

    /**
     * 5. Submit Jawaban (Save Logic)
     */
    public function submit(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->finished_at) return redirect()->route('siswa.quiz.result', $attempt->id);

        $answers = $request->input('answers') ?? [];

        DB::transaction(function () use ($attempt, $answers) {
            $totalScore = 0;

            foreach ($answers as $questionId => $optionId) {
                $question = \App\Models\QuizQuestion::find($questionId);
                $selectedOption = \App\Models\QuizOption::find($optionId);

                // Validasi agar tidak error jika optionId null (tidak dijawab)
                $isCorrect = $selectedOption && $selectedOption->is_correct;
                $score = $isCorrect ? $question->points : 0;

                QuizAttemptAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'quiz_question_id' => $questionId,
                    'quiz_option_id' => $optionId,
                ]);

                $totalScore += $score;
            }

            $attempt->update([
                'finished_at' => now(),
                'score' => $totalScore,
            ]);
        });

        return redirect()->route('siswa.quiz.result', $attempt->id);
    }

    /**
     * 6. Halaman Hasil (Result Score)
     */
    public function result(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) abort(403);

        $quiz = $attempt->quiz->load('matpel');

        // Hitung statistik sederhana
        $totalQuestions = $quiz->questions()->count();
        $correctAnswers = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)
            ->whereHas('option', function ($q) {
                $q->where('is_correct', true);
            })->count();

        // Tentukan lulus/gagal
        $isPassed = $attempt->score >= $quiz->passing_grade;

        return Inertia::render('siswa/quiz/result', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'stats' => [
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'is_passed' => $isPassed,
            ]
        ]);
    }
}
