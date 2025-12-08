<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tugas;
use App\Service\Contract\KelasServiceInterface;
use App\Service\Contract\MatpelServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TugasController extends Controller
{
    public function tambah(Request $request, KelasServiceInterface $kelasService, MatpelServiceInterface $matpelService)
    {
        $matpel = $matpelService->getMatpelByGuru($request->role_id);
        return inertia("guru/tugas/tambah-tugas", [
            'matpels' => $matpel,
        ]);
    }
    public function getKelasByMatpel(Request $request, KelasServiceInterface $kelasService)
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
        $guru = $request->user()->id;
        $tugas = Tugas::where('created_by_user_id', $guru)
            ->join('matpels', 'matpels.kode', '=', 'tugas.matpel_kode')
            ->select(['tugas.*', 'matpels.nama as nama_matpel'])
            ->when($request->keywords, function ($q) use ($request) {
                $q->where('tugas.title', 'LIKE', "%{$request->keywords}%")
                    ->orWhere('matpels.nama', 'LIKE', "%{$request->keywords}%");
            })
            ->get();
        $tug = $tugas->filter(function ($item) use ($kelas_id) {
            if ($item->receiver_type === 'class_id') {
                return collect($item->receiver_type_id)->contains($kelas_id);
            }
            return true;
        });

        $info_kelas = Kelas::find($kelas_id);
        $matpel = $matpelService->getMatpelByKelasAndGuru($kelas_id, $request->role_id);
        return inertia('guru/tugas', [
            'info_kelas' => $info_kelas,
            'search_current_terms' => [
                'keywords' => $request->keywords,
                'matpels' => $request->kode_matpel,
            ],
            'tugas' => $tug->values(),
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
            $deadline = Carbon::parse($data['deadline'])->format('Y-m-d H:i:s');
            Tugas::create([
                'matpel_kode'       => $data['matpel'],
                'receiver_type_id' => $data['receiver_type_id'],
                'receiver_type' => $data['receiver_type'],
                'title'             => $data['judul'],
                'content'           => $data['deskripsi'],
                'mode_pengumpulan'  => $data['mode_pengumpulan'],
                'deadline'          => $deadline,
                'publish_date'      => now(), // atau isi sesuai kebutuhan
                'created_by_user_id' => $request->user()->id,
            ]);
            return  redirect()->back()->withErrors([
                'success' => "Tugas Berhasil di simpan!"
            ]);
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
                    'name' => $item->user ? $item->user->name : 'Tanpa Nama',
                ],
                'kelas' => [
                    'nama' => $item->kelas ? $item->kelas->nama : 'Tanpa Kelas',
                ]
            ];
        });
    }
    public function periksaTugas(Request $request, string $id = null)
    {

        return inertia('guru/tugas/periksa-tugas');
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

            $deadline = Carbon::parse($data['deadline'])->format('Y-m-d H:i:s');

            $tugas = Tugas::findOrFail($id);

            $tugas->update([
                'matpel_kode'        => $data['matpel'],
                'receiver_type_id'   => $data['receiver_type_id'],
                'receiver_type'      => $data['receiver_type'],
                'title'              => $data['judul'],
                'content'            => $data['deskripsi'],
                'mode_pengumpulan'   => $data['mode_pengumpulan'],
                'deadline'           => $deadline,
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
}
