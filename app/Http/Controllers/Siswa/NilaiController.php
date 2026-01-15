<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Nilai; // Pastikan model Nilai di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NilaiController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $siswaId = Auth::user()->id;
        $daftarNilai = Nilai::with(['tugas.mapel', 'tugas.guru'])
            ->where('siswa_id', $siswaId)
            ->latest()
            ->get();

        return Inertia::render('siswa/nilai/index', [
            'daftarNilai' => $daftarNilai
        ]);
    }
}
