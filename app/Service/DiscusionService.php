<?php

namespace App\Service;

use App\Models\Discusion;
use App\Models\User;
use App\Service\Contract\DiscusionServiceInterface;
use App\Service\Contract\KelasServiceInterface;

class DiscusionService implements DiscusionServiceInterface
{
    public KelasServiceInterface $kelasService;
    public function __construct(KelasServiceInterface $kelasService)
    {
        $this->kelasService = $kelasService;
    }
    //load diskusi
    public function loadDiscusion($kelas_id, $matpel_kode)
    {
        return Discusion::with(['user', 'matpel', 'comments', 'linkedObject'])
            ->where('kelas_id', $kelas_id)
            ->where('matpel_kode', $matpel_kode)
            ->latest()
            ->get()->map(function ($item) {
                $item->created_at_human = $item->created_at->diffForHumans();
                return $item;
            });
    }
    /**
     * untuk mengakses diskusi
     */
    public function isCanAccessesdDiscusion(User $user, $kelas_id): bool
    {
        $user->with(['siswa.kelas', 'guru']);
        if ($user->role == 'siswa') {
            if ($user->siswa->kelas->id == $kelas_id) {
                return true;
            }
        } else if ($user->role == 'guru') {
            $kelas = $this->kelasService->getKelasByGuru($user->guru->nip);
            $isAuthorized = $kelas->contains('id_kelas', $kelas_id);
            if ($isAuthorized) {
                return true;
            }
        }
        return false;
    }
}
