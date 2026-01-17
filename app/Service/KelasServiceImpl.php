<?php

namespace App\Service;

use App\Models\Kelas;
use App\Models\Pengajaran;
use App\Models\User;
use App\Service\Contract\KelasServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KelasServiceImpl implements KelasServiceInterface
{
    public function getMatpelForUser($targetKelasId, $role, $user_id)
    {
        $matpels = $this->get_matpels($targetKelasId);
        if ($role == 'guru') {
            $matpels =  $matpels->filter(function ($kelas) use ($user_id) {
                return $kelas->guru_nip === $user_id;
            });
        }
        return $matpels;
    }
    public function getActiveClass(string $kelas_kode)
    {
        $kelasActive = collect([]);
        $kelasActive->push(Kelas::select([
            'id as id_kelas',
            'nama as nama_kelas'
        ])->where('id', $kelas_kode)->first());
        return $kelasActive;
    }
    public function getKelasByGuru(string $nip)
    {
        return  Kelas::withCount('siswa')
            ->join('pengajarans', 'pengajarans.kelas_id', '=', 'kelas.id')
            ->join('gurus', 'gurus.nip', '=', 'pengajarans.guru_nip')
            ->where('pengajarans.guru_nip', '=', $nip)
            ->select([
                'kelas.nama as nama_kelas',
                'kelas.id as id_kelas',
                DB::raw("(SELECT COUNT(*) FROM siswas WHERE siswas.kelas_id = pengajarans.kelas_id AND siswas.status='aktif') as jumlah_siswa")
            ])->groupBy('kelas.id', 'kelas.nama')
            ->get();
    }
    /**
     * Buat relasi mengunakan query builder manual
     * agar lebih cepet dan
     */
    public function get_matpels(string|null $kelas_id = null): Collection
    {
        $matpel = Pengajaran::join('kelas', 'pengajarans.kelas_id', '=', 'kelas.id')
            ->join('matpels', 'pengajarans.matpel_kode', '=', 'matpels.kode')
            ->join('gurus', 'pengajarans.guru_nip', '=', 'gurus.nip')
            ->join('users', 'gurus.user_id', '=', 'users.id')
            ->select(
                'pengajarans.kelas_id',
                'pengajarans.matpel_kode',
                'pengajarans.guru_nip',
                'kelas.nama as nama_kelas',
                'matpels.nama as nama_matpel',
                'matpels.kategori',
                'matpels.created_at',
                'matpels.kode as kode_matpel',
            )->selectRaw("CONCAT_WS(' ',gurus.gelar_depan,users.name,',',gurus.gelar_belakang) AS nama_guru");

        if ($kelas_id) {
            $matpel->where('kelas.id', $kelas_id);
            return $matpel->get();
        }
        return $matpel->get();
    }

    public function handleFirstMatpel()
    {
        $request = request();
        $kelasService = app(KelasServiceInterface::class);
        $matpel = $kelasService
            ->get_matpels($request->kelas['id'])
            ->select('matpel_kode', 'nama_matpel');
        if ($data = $matpel->first()) {
            return to_route('siswa.materi', [
                'matpel_id' => $data['matpel_kode'],
            ]);
        }
    }
}
