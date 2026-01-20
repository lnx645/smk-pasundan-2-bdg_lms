<?php

namespace App\Service;

use App\Facades\Youtube;
use App\Models\Discusion;
use App\Models\Materi;
use App\Models\Matpel;
use App\Models\Pengajaran;
use App\Models\Siswa;
use App\Notifications\FcmNotification;
use App\Notifications\NewMateriNotification;
use App\Service\Contract\MateriServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class MateriService implements MateriServiceInterface
{
    public function getMateri(string $kelas_id, string $matpel_id): Collection
    {
        $materials = Pengajaran::where('materials.matpel_kode', '=', $matpel_id)
            ->join('gurus', 'gurus.nip', '=', 'pengajarans.guru_nip')
            ->join('kelas', 'kelas.id', '=', 'pengajarans.kelas_id')
            ->where('pengajarans.kelas_id', '=', $kelas_id)
            ->join('matpels', 'matpels.kode', '=', 'pengajarans.matpel_kode')
            ->join('users', 'users.id', '=', 'gurus.user_id')
            ->join('materials', 'materials.matpel_kode', 'pengajarans.matpel_kode')
            ->select(['pengajarans.*', 'materials.publish_date', 'materials.file_materi', 'materials.nomor_materi', 'kelas.nama  as nama_kelas', 'materials.id as materi_id', 'gurus.gelar_depan', 'gurus.gelar_belakang', 'materials.title', 'materials.kelas_ids', 'matpels.nama as nama_matpel', 'users.name as nama_guru'])
            ->orderBy('materials.nomor_materi', 'desc')
            ->get();

        $data = $materials->filter(function ($materi) use ($kelas_id) {
            $kelas_ids = is_string($materi->kelas_ids)
                ? json_decode($materi->kelas_ids, true)
                : $materi->kelas_ids;

            return collect($kelas_ids)
                ->map(function ($k) {
                    if (is_array($k)) {
                        return $k['id_kelas'];
                    }
                    return trim(strtolower($k));
                })
                ->contains(trim(strtolower($kelas_id)));
        })->filter(function ($item) {
            return Carbon::parse($item->publish_date)->lessThanOrEqualTo(Carbon::now());
        });
        return $data;
    }
    public function getDetailMateri(string $id)
    {
        $materi  = Materi::join('matpels', 'matpels.kode', '=', 'materials.matpel_kode')
            ->join('pengajarans', 'pengajarans.matpel_kode', '=', 'materials.matpel_kode')
            ->join('gurus', 'gurus.nip', '=', 'pengajarans.guru_nip')
            ->join('users', 'users.id', '=', 'gurus.user_id')
            ->where('materials.id', $id)
            ->select([
                'users.name as nama_guru',
                'gurus.user_id',
                'pengajarans.guru_nip',
                'pengajarans.kelas_id',
                'pengajarans.matpel_kode',
                'matpels.nama as nama_matpel',
                'materials.title',
                'materials.nomor_materi as nomor_materi',
                'materials.created_at',
                'materials.description',
                'materials.youtube_id',
                'materials.file_materi'
            ])
            ->first();
        return $materi;
    }
    public function getMateriByGuruDanKelas(string $matpel_kode, string $guru_id, string $kelas_kode)
    {
        return Materi::where('materials.matpel_kode', $matpel_kode)
            ->where('materials.created_by_user_id', '=', $guru_id)
            ->get()
            ->filter(function ($mater) use ($kelas_kode) {
                $kelas = is_string($mater->kelas_ids) ? json_decode($mater->kelas_ids, true) : $mater->kelas_ids;
                return collect($kelas)->contains($kelas_kode);
            })
            ->map(function ($value) {
                $fileMateri = is_string($value->file_materi) ? json_decode($value->file_materi, true) : $value->file_materi;
                return [
                    ...$value->toArray(),
                    'jumlahFileMateri' => count($fileMateri)
                ];
            })->values();
    }
    public function simpanMateri(array $data, string $kelas_kode, string $guru_id)
    {
        try {
            $kelass = $data['kelas_ids'] ?? [];
            $matpel = $data['matpel']['kode_matpel'];
            $nomorMateriTerakhir = 1 + (int)Materi::where('matpel_kode', $matpel)->max('nomor_materi');
            $save = Materi::create([
                'title' => $data['title'],
                'created_by_user_id' => $guru_id,
                'status' => "publish",
                'publish_date' => SupportCarbon::parse($data['publish_date']) ?? now(),
                'description' => $data['description'],
                'file_materi' =>   $data['file_materi'],
                'youtube_id' => Youtube::parseVideoID($data['youtube_id']),
                'kelas_ids' => $kelass,
                'matpel_kode' => $matpel,
                'nomor_materi' => $nomorMateriTerakhir
            ]);
            if ($save) {
                $matpel = Matpel::find($matpel);
                $users = Siswa::with('user')->whereIn('kelas_id', $kelass)->get()->pluck('user');
                if ($users->isNotEmpty()) {
                    Notification::sendNow($users, new NewMateriNotification($save));
                }
                if (is_array($kelass)) {
                    foreach ($kelass as $kelazz) {
                        Discusion::create([
                            'object_type' => 'materi',
                            'object_type_id' => $save->id,
                            'user_id' => $guru_id,
                            'kelas_id' => $kelazz,
                            'matpel_kode' => $matpel->kode,
                            'description' => "Materi baru"
                        ]);
                    }
                } else {
                    Discusion::create([
                        'object_type' => 'materi',
                        'object_type_id' => $save->id,
                        'user_id' => $guru_id,
                        'kelas_id' => $kelas_kode,
                        'matpel_kode' => $matpel->kode,
                        'description' => "Materi baru"
                    ]);
                }
            }
            return $save;
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
