<?php

namespace App\Service\Contract;

use App\Models\User;

interface DiscusionServiceInterface
{
    public function loadDiscusion($kelas_id, $matpel_kode);
    public function isCanAccessesdDiscusion(User $user, $kelas_id): bool;
}
