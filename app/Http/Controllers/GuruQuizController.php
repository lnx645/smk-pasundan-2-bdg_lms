<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengajaran;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GuruQuizController extends Controller
{
    public function create()
    {
        $nip = Auth::user()->guru->nip;

        // Ambil data pengajaran untuk dropdown
        // Kita perlu list Kelas dan Matpel yang unik untuk guru ini
        $pengajarans = Pengajaran::with(['kelas', 'matpel'])
            ->where('guru_nip', $nip)
            ->get();
        return inertia('guru/quiz/create', [
            'kelas_list' => $pengajarans->pluck('kelas')->unique('id')->values(),
            'matpel_list' => $pengajarans->pluck('matpel')->unique('kode')->values(),
        ]);
    }
    public function index(Request $request)
    {

        $user = Auth::user();
        $guruNip = $user->guru->nip;
        $pengajaran = Pengajaran::where('guru_nip', $guruNip)->get();
        $matpelKodeList = $pengajaran->pluck('matpel_kode')->unique();
        $kelasIdList = $pengajaran->pluck('kelas_id')->unique();
        $quizzes = Quiz::query()
            ->with(['kelas', 'matpel'])
            ->whereIn('matpel_kode', $matpelKodeList)
            ->whereIn('kelas_id', $kelasIdList)
            ->latest()
            ->paginate(10);
        return inertia('guru/quiz/index', [
            'quizzes' => $quizzes
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'matpel_kode' => 'required|exists:matpels,kode',
            'duration' => 'required|integer|min:1',
            'passing_grade' => 'required|numeric|min:0|max:100',
            'max_attempts' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_randomized' => 'boolean',
            'is_published' => 'boolean',
        ]);
        // Konversi tanggal ke Timestamp (Integer) sesuai struktur DB Anda
        $validated['start_date'] = $request->start_date;
        $validated['end_date'] = $request->end_date;
        $validated['guru_nip'] = $request->role_id;


        // Generate Token Akses Random (Opsional)
        $validated['access_code'] = strtoupper(substr(md5(time()), 0, 6));
        $quiz = Quiz::create($validated);

        return to_route('guru.quiz.index', $quiz->id)
            ->with('success', 'Kuis berhasil dibuat. Silakan tambah soal!');
    }
    public function result(Request $request, Quiz $quiz)
    {
        $search = $request->input('search');
        $kelasId = $request->input('kelas'); 

        // 2. Query Attempts (Percobaan Siswa)
        $attempts = $quiz->attempts()
            ->with(['user', 'user.siswa.kelas'])
            // Filter Search Nama
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                });
            })
            // Filter Kelas (LOGIKA BARU)
            ->when($kelasId, function ($q) use ($kelasId) {
                $q->whereHas('user.siswa', function ($s) use ($kelasId) {
                    $s->where('kelas_id', $kelasId);
                });
            })
            ->latest('finished_at')
            ->get();

        $stats = [
            'average' => $attempts->avg('score'),
            'max' => $attempts->max('score'),
            'min' => $attempts->min('score'),
            'total_students' => $attempts->unique('user_id')->count(),
            'passed' => $attempts->where('score', '>=', $quiz->passing_grade)->count(),
        ];

        $allKelas = Kelas::orderBy('nama', 'asc')->get(); // Sesuaikan nama kolom di DB (misal: nama_kelas)

        return Inertia::render('guru/quiz/result', [
            'quiz' => $quiz->load(['matpel', 'kelas']),
            'attempts' => $attempts,
            'stats' => $stats,
            'allKelas' => $allKelas, // Kirim data kelas ke frontend
            'filters' => [
                'search' => $search,
                'kelas' => $kelasId // Kirim state filter saat ini agar dropdown tidak reset
            ]
        ]);
    }
}
