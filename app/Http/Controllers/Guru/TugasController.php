<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Discusion;
use App\Models\JawabanTugas;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tugas;
use App\Models\User;
use App\Notifications\NewTugasNotification;
use App\Service\Contract\KelasServiceInterface;
use App\Service\Contract\MatpelServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TugasController extends Controller
{
    public function tambah(
        Request $request,
        MatpelServiceInterface $matpelService
    ) {
        $matpel = $matpelService->getMatpelByGuru($request->role_id);
        return inertia("guru/tugas/tambah-tugas", [
            'matpels' => $matpel,
        ]);
    }
    public function getKelasByMatpel(Request $request)
    {
        $matpels = $request->post('matpel_kode');
        return Kelas::withCount('siswa')
            ->join('pengajarans', 'pengajarans.kelas_id', '=', 'kelas.id')
            ->join('gurus', 'gurus.nip', '=', 'pengajarans.guru_nip')
            ->where('pengajarans.guru_nip', '=', $request->role_id)
            ->where('pengajarans.matpel_kode', '=', $matpels)
            ->select([
                'kelas.nama as nama_kelas',
                'kelas.id as id_kelas',
                DB::raw("(SELECT COUNT(*) FROM siswas WHERE siswas.kelas_id = pengajarans.kelas_id AND siswas.status='aktif') as jumlah_siswa")
            ])->groupBy('kelas.id', 'kelas.nama')
            ->get();
    }

    public function index(Request $request, string $kelas_id, MatpelServiceInterface $matpelService)
    {
        $guruId = $request->user()->id;
        $tugas = Tugas::query()
            ->where('created_by_user_id', $guruId)
            ->join('matpels', 'matpels.kode', '=', 'tugas.matpel_kode')
            ->select(['tugas.*', 'matpels.nama as nama_matpel'])
            ->when($request->keywords, function ($q) use ($request) {
                $keywords = "%{$request->keywords}%";
                $q->where(function ($q) use ($keywords) {
                    $q->where('tugas.title', 'LIKE', $keywords)
                        ->orWhere('matpels.nama', 'LIKE', $keywords);
                });
            })
            ->get();
        $filtered = $tugas->filter(
            fn($item) =>
            $item->receiver_type !== 'class_id' ||
                collect($item->receiver_type_id)->contains($kelas_id)
        );
        $jawabanTugas = JawabanTugas::whereIn('tugas_id', $filtered->pluck('tugasID'))
            ->get()
            ->groupBy('tugas_id');

        $result = $filtered->map(function ($item) use ($jawabanTugas, $kelas_id) {
            $receiverUserList = [];
            $persentase = [];
            if ($item->receiver_type === "siswa_id") {
                $targetUserIds = $item->receiver_type_id;
                $jawaban = $jawabanTugas->get($item->tugasID, collect());
                $jumlahSubmit = $jawaban->whereIn('answered_by_id', $targetUserIds)->count();
                $total = count($targetUserIds);

                $persentase = [
                    'jumlah_submit' => $jumlahSubmit,
                    'total_siswa'   => $total,
                    'persen_submit' => $total > 0 ? round(($jumlahSubmit / $total) * 100, 2) : 0
                ];

                $users = User::whereIn('id', $targetUserIds)
                    ->with(['siswa.kelas'])
                    ->get();

                $receiverUserList = $users->map(function ($u) use ($jawaban) {
                    $isDikerjakan = $jawaban->where('answered_by_id', $u->id)->first();

                    return [
                        'dikerjakan' => $isDikerjakan !== null,
                        'id'         => $u->id,
                        'name'       => $u->name,
                        'kelas'      => $u->siswa?->kelas?->nama ?? '-',
                    ];
                });
            } else {
                $targetKelasId = $item->receiver_type_id;
                $targetUserIds = User::whereHas('siswa', function ($query) use ($targetKelasId) {
                    $query->where('kelas_id', $targetKelasId);
                })->pluck('id');
                $jawaban = $jawabanTugas->get($item->tugasID, collect());
                $jumlahSubmit = $jawaban->whereIn('answered_by_id', $targetUserIds)->count();
                $totalSiswa = $targetUserIds->count();

                $persentase = [
                    'jumlah_submit' => $jumlahSubmit,
                    'total_siswa'   => $totalSiswa,
                    'persen_submit' => $totalSiswa > 0 ? round(($jumlahSubmit / $totalSiswa) * 100, 2) : 0
                ];

                $users = User::whereIn('id', $targetUserIds)
                    ->with(['siswa.kelas'])
                    ->get();

                $receiverUserList = $users->map(function ($u) use ($jawaban) {
                    $isDikerjakan = $jawaban->where('answered_by_id', $u->id)->first();

                    return [
                        'dikerjakan' => $isDikerjakan !== null,
                        'id'         => $u->id,
                        'name'       => $u->name,
                        'kelas'      => $u->siswa?->kelas?->nama ?? '-',
                    ];
                });
            }

            return array_merge(
                $item->toArray(),
                $persentase,
                ['receiver_users' => $receiverUserList]
            );
        });
        $infoKelas = Kelas::find($kelas_id);
        $matpel = $matpelService->getMatpelByKelasAndGuru($kelas_id, $request->role_id);
        return inertia('guru/tugas', [
            'info_kelas' => $infoKelas,
            'search_current_terms' => [
                'keywords' => $request->keywords,
                'matpels' => $request->kode_matpel,
            ],
            'tugas' => $result->values(),
            'kelas_id' => $kelas_id,
            'matpels' => $matpel,
        ]);
    }

    public function __invoke(
        Request $request,
        KelasServiceInterface $kelasService
    ) {
        if (!($request->role == 'guru')) {
            return to_route('home');
        }
        $kelas = $kelasService->getKelasByGuru($request->role_id);
        return inertia('guru/tugas', [
            'kelas' => $kelas,
        ]);
    }

    public function simpanTugas(Request $request)
    {
        try {
            $data = $request->validate([
                'matpel' => ['required', 'string'],
                'judul' => ['required'],
                'deskripsi' => ['required'],
                'mode_pengumpulan' => ['required'],
                'deadline' => ['required'],
                'receiver_type_id' => ['required'],
                'receiver_type' => ['required']
            ]);
            $tugas =   Tugas::create([
                'matpel_kode'       => $data['matpel'],
                'receiver_type_id' => $data['receiver_type_id'],
                'receiver_type' => $data['receiver_type'],
                'title'             => $data['judul'],
                'content'           => $data['deskripsi'],
                'mode_pengumpulan'  => $data['mode_pengumpulan'],
                'deadline'          => $data['deadline'],
                'publish_date'      => now(), // atau isi sesuai kebutuhan
                'created_by_user_id' => $request->user()->id,
            ]);
            //masuk ke forum juga jika tugas

            if ($data['receiver_type'] == 'class_id') {

                $classIds = $data['receiver_type_id'];
                if (!is_array($classIds)) {
                    $classIds = [$classIds];
                }
                if ($tugas && is_array($classIds)) {
                    foreach ($classIds as $id) {
                        Discusion::create([
                            'object_type_id' => $tugas->tugasID,
                            'object_type' => 'tugas',
                            'user_id' => $tugas->created_by_user_id,
                            'kelas_id' => $id,
                            'description' => $tugas->content,
                            'matpel_kode' => $tugas->matpel_kode,
                        ]);
                    }
                }
                $receivers = User::whereHas('siswa', function ($query) use ($classIds) {
                    $query->whereIn('kelas_id', $classIds);
                })->get();
                if ($receivers->count() > 0) {
                    Notification::send($receivers, new NewTugasNotification($tugas, $request->user()));
                }
                return  redirect()->back()->withErrors([
                    'success' => "Tugas Berhasil di simpan!"
                ]);
            }
        } catch (\Throwable $th) {
            return  redirect()->back()->withErrors([
                'gagal' => "Tugas gagal di simpan!"
            ]);
        }
    }
    public function getSiswa(Request $request)
    {
        $key = $request->keywords ?? null;

        $data = Siswa::with(['kelas', 'user'])
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    $q->where('nis', 'like', "%{$key}%")

                        ->orWhereHas('kelas', function ($qKelas) use ($key) {
                            $qKelas->where('nama', 'like', "%{$key}%");
                        })

                        ->orWhereHas('user', function ($qUser) use ($key) {
                            $qUser->where('name', 'like', "%{$key}%");
                        });
                });
            })
            ->get();

        return $data->map(function ($item) {
            return [
                'nis' => $item->nis,
                'user' => [
                    'id' => $item->user->id,
                    'name' => $item->user ? $item->user->name : 'Tanpa Nama',
                ],
                'kelas' => [
                    'nama' => $item->kelas ? $item->kelas->nama : 'Tanpa Kelas',
                ]
            ];
        });
    }
    public function periksaTugas(Request $request, string|null $id = null)
    {

        $jawaban = JawabanTugas::query()
            ->with('nilai') // Load relasi nilai
            ->select([
                'jawaban_tugas.jawabanID',
                'jawaban_tugas.jawabanID as jawaban_id',
                'jawaban_tugas.file_url',
                'jawaban_tugas.created_at',
                'jawaban_tugas.tugas_id',
                'jawaban_tugas.answered_by_id',

                // Data Join
                'users.id as user_id',
                'users.name as user_name',
                'siswas.nis',
                'kelas.nama as kelas_nama',
            ])
            ->join('users', 'users.id', '=', 'jawaban_tugas.answered_by_id')
            ->join('siswas', 'siswas.user_id', '=', 'users.id')
            ->join('kelas', 'kelas.id', '=', 'siswas.kelas_id')
            ->where('jawaban_tugas.tugas_id', $id)
            ->get();

        return inertia('guru/tugas/periksa-tugas', [
            'jawaban' => $jawaban,
            'tugas_id' => $id,
        ]);
    }
    public function editTugas(Request $request, string|null $id = null, MatpelServiceInterface $matpelService)
    {
        $tugas = Tugas::find($id);
        $matpel = $matpelService->getMatpelByGuru($request->role_id);
        return inertia('guru/tugas/edit-tugas', [
            'tugas' => $tugas,
            'matpels' => $matpel,
        ]);
    }
    public function updateTugas(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'matpel' => ['required', 'string'],
                'judul' => ['required'],
                'deskripsi' => ['required'],
                'mode_pengumpulan' => ['required'],
                'deadline' => ['required'],
                'receiver_type_id' => ['required'],
                'receiver_type' => ['required']
            ]);
            $tugas = Tugas::findOrFail($id);
            $tugas->update([
                'matpel_kode'        => $data['matpel'],
                'receiver_type_id'   => $data['receiver_type_id'],
                'receiver_type'      => $data['receiver_type'],
                'title'              => $data['judul'],
                'content'            => $data['deskripsi'],
                'mode_pengumpulan'   => $data['mode_pengumpulan'],
                'deadline'           => $data['deadline'],
            ]);

            return redirect()->back()->withErrors([
                'success' => "Tugas Berhasil di update!"
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors([
                'gagal' => "Tugas gagal di update!"
            ]);
        }
    }
    public function deleteTugas(Request $request, string $id)
    {
        try {
            $guru_id = $request->user()->id;

            $tugas = Tugas::where('tugasID', $id)
                ->where('created_by_user_id', $guru_id)
                ->first();

            if (! $tugas) {
                return redirect()->back()->withErrors([
                    'gagal' => "Opps tugas gagal dihapus!",
                ]);
            };
            if (!$tugas->delete()) {
                return redirect()->back()->withErrors([
                    'gagal' => "Opps tugas gagal dihapus!",
                ]);
            }
            return redirect()->back()->withErrors([
                'success' => "Opps tugas berhasil dihapus!",
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors([
                'error' => "Opps tugas gagal dihapus!",
            ]);
        }
    }
}
