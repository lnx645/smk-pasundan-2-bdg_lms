<?php

namespace App\Service\Contract;


interface KelasServiceInterface
{
    public function getActiveClass(string $kelas_kode);
    public function getKelasByGuru(string $nip);
    public function handleFirstMatpel();
    public function get_matpels(string|null $kelas = null): \Illuminate\Support\Collection;
    public function getMatpelForUser($targetKelasId, $role, $user_id);
}
