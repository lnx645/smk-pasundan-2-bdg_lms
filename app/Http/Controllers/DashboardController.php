<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Quiz; // Jangan lupa import Model Quiz
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $pending_tugas = [];
        $active_quizzes = []; // Variabel baru

        if ($user->role === 'siswa') {
            $kelasId = $user->siswa->kelas_id;

            // 1. Logika Tugas (Existing)
            $pending_tugas = Tugas::where(function ($q) use ($kelasId, $user) {
                $q->where('receiver_type', 'class_id')->whereJsonContains('receiver_type_id', $kelasId)
                    ->orWhere('receiver_type', 'siswa_id')->whereJsonContains('receiver_type_id', $user->id);
            })
                ->whereDoesntHave('jawaban', function ($q) use ($user) {
                    $q->where('answered_by_id', $user->id);
                })
                ->where('deadline', '>', now())
                ->with('matpel')
                ->orderBy('deadline', 'asc')
                ->limit(5)
                ->get();

            // 2. Logika Kuis Aktif (New)
            $active_quizzes = Quiz::where('is_published', true)
                // Filter Kuis yang sedang aktif (Start < Sekarang < End)
                ->where('start_date', '<=', now())
                ->where('end_date', '>', now())
                // Filter belum selesai dikerjakan
                ->whereDoesntHave('attempts', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->whereNotNull('finished_at'); // Asumsi finished_at terisi jika sudah selesai
                })
                ->with('matpel')
                ->orderBy('end_date', 'asc') // Urutkan yang mau habis duluan
                ->limit(3)
                ->get();
        }

        return inertia('home', [
            'pending_tugas' => $pending_tugas,
            'active_quizzes' => $active_quizzes, // Kirim ke Vue
        ]);
                ->with('matpel')
                ->orderBy('deadline', 'asc')
                ->limit(5)
                ->get();
        }

        return inertia('home', ['pending_tugas' => $pending_tugas]);
    }
}
