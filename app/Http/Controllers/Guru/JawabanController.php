<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JawabanTugas;
use Illuminate\Http\Request;

class JawabanController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id, string $jawaban_id)
    {
        $data = JawabanTugas::select(
            'jawaban_tugas.jawabanID',
            'jawaban_tugas.jawaban',
            'jawaban_tugas.file_url',
            'jawaban_tugas.created_at',
            'matpels.nama as nama_matpel',
            'users.id as user_id',
            'users.name as user_name',

            'siswas.nis',
            'kelas.nama as kelas_nama',
        )
            ->join('users', 'users.id', '=', 'jawaban_tugas.answered_by_id')
            ->leftJoin('siswas', 'siswas.user_id', '=', 'users.id')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswas.kelas_id')
            ->leftJoin('tugas', 'tugas.tugasID', '=', 'jawaban_tugas.tugas_id')
            ->leftJoin("matpels", 'matpels.kode', '=', 'tugas.matpel_kode')
            ->where('jawaban_tugas.jawabanID', $jawaban_id)
            ->firstOrFail();
        return inertia('guru/tugas/lihat-jawaban', [
            'jawaban' => $data,
        ]);
    }
}
