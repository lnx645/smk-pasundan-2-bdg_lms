<?php

namespace App\Http\Controllers;

use App\Service\Contract\KelasServiceInterface;
use Illuminate\Http\Request;
use App\Service\Contract\MateriServiceInterface;

class MateriController extends Controller
{
    public function showMateri(
        Request $request,
        KelasServiceInterface $kelasService,
        MateriServiceInterface $materiService
    ) {
        //current matpel
        $current_matpel = $request->query('matpel_id');
        //kelas
        $kelas_id = $request->kelas['id'];
        //get kelas by service
        $matpel = $kelasService
            ->get_matpels($kelas_id)
            ->select([
                'matpel_kode',
                'nama_matpel',
            ]);
        //create first matpel id
        if (empty($current_matpel)) {
            return $kelasService->handleFirstMatpel();
        }
        $materi = $materiService
            ->getMateri($kelas_id, $current_matpel)
            ->select([
                'materi_id',
                'nomor_materi',
                'title',
                'nama_kelas',
                'nama_matpel',
                'nama_guru',
            ])->values();
        //response data
        $dataResponse = [
            'materials' => $materi,
            'matpels' => $matpel,
            'current_matpel' => $current_matpel,
        ];

        return inertia('siswa/materi', $dataResponse);
    }
    public function view(string $id_materi, MateriServiceInterface $materiService, Request $request)
    {
        $materi  = $materiService->getDetailMateri($id_materi);
        $isVisiting = true;
        
        return inertia('siswa/view-materi', [
            'materi' => $materi,
            'visiting' => $isVisiting,
        ]);
    }
}
