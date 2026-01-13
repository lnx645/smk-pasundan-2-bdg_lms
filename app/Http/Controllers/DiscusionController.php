<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Discusion;
use App\Models\DiscusionComment;
use App\Models\Matpel;
use App\Service\Contract\KelasServiceInterface;
use App\Service\Contract\MatpelServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscusionController extends Controller
{
    public function comments(string $kelas_id, string $matpel_kode, string $id)
    {
        $disc = Discusion::with([
            'user',
            'comments' => function ($query) {
                $query->latest();
            },
            'comments.user'
        ])->where([
            'kelas_id'    => $kelas_id,
            'matpel_kode' => $matpel_kode,
            'id'          => $id,
        ])
            ->firstOrFail();
        return inertia('discusion/comment', [
            'discusion' => $disc,
            'comments' => $disc->comments, // Kirim comments yang sudah ada user-nya
        ]);
    }
    public function postComment(Request $request)
    {
        $validated = $request->validate([
            'discusion_id' => 'required|exists:discusions,id',
            'text' => 'required|string',
        ]);

        DiscusionComment::create([
            'discusion_id' => $validated['discusion_id'],
            'user_id' => Auth::user()->id, // Ambil ID dari user yang login
            'text' => $validated['text'],
        ]);

        return back(); // Kembali ke halaman diskusi
    }
    public function indexDiskusiGuru(
        Request $request,
        KelasServiceInterface $kelasService
    ) {
        $kelas = $kelasService->getKelasByGuru($request->role_id);
        return inertia('guru/diskusi_index', ['kelas' => $kelas]);
    }
    public function index(Request $request, KelasServiceInterface $kelasService, string $kelas_id = null)
    {
        $id = $request->role_id;
        $kelas = $request->kelas['id'] ?? $kelas_id;
        $matpels = $kelasService->get_matpels($kelas);
        $role = $request->role;
        if ($role == 'guru') {
            $matpels =  $matpels->filter(function ($kelas) use ($id) {
                return $kelas->guru_nip === $id;
            });
        }
        return inertia('siswa/discusion/index', [
            'matpels' => $matpels,
            'kelas_id' => $kelas,
        ]);
    }

    public function show(Request $request, KelasServiceInterface $kelasService, string $kelas_id, string $matpels_id)
    {
        $user = Auth::user();

        if ($user->role == 'siswa') {
            $user->with(['siswa.kelas']);
            if ($user->siswa->kelas->id != $kelas_id) {
                return abort(404);
            }
        } else if ($user->role == 'guru') {
            $kelas = $kelasService->getKelasByGuru($request->role_id);
            $isAuthorized = $kelas->contains('id_kelas', $kelas_id);
            if (!$isAuthorized) {
                return abort(404);
            }
        }
        $discussions = Discusion::with(['user', 'matpel', 'comments', 'linkedObject'])
            ->where('kelas_id', $kelas_id)
            ->where('matpel_kode', $matpels_id)
            ->latest()
            ->get()->map(function ($item) {
                $item->created_at_human = $item->created_at->diffForHumans();
                return $item;
            });

        return inertia('siswa/discusion/show', [
            'discussions' => $discussions,
            'kelas_id' => $kelas_id,
            'matpels' => Matpel::find($matpels_id)->first(),
            'matpel_kode' => $matpels_id,
        ]);
    }
    public function like(Discusion $discussion)
    {
        $discussion->increment('likes');

        return redirect()->back()->with('message', 'Disukai!');
    }
    public function delete(Request $request, string $discussion)
    {
        $user = Auth::user();

        $discusion = Discusion::findOrFail($discussion);
        if ($discusion) {
            $discusion->delete();
        }
        if ($discusion->user_id == $user->id || $user->role == 'guru') {
            return back();
        } else {
            return redirect()->back()->withErrors([
                'gagal' => "Data gagal dihapus"
            ]);
        }
    }
    public function store(Request $request, String $kelas_id, string $matpel_kode)
    {

        $user = Auth::user();
        $isGuru = $user->role === 'guru';
        $validated = $request->validate([
            'description' => 'required|string|min:3',
            'kelas_id'    => 'required|integer',
            'matpel_kode' => 'required|string',
            'object_type' => 'nullable|string'
        ]);

        $type = $isGuru ? ($request->object_type ?? 'forum') : 'forum';
        Discusion::create([
            'description'    => $validated['description'],
            'object_type'    => $type,
            'object_type_id' => null,
            'user_id'        => $user->id,
            'kelas_id'       => $validated['kelas_id'],
            'matpel_kode'    => $validated['matpel_kode'],
        ]);

        return redirect()->back()->with('message', 'Diskusi berhasil dikirim');
    }
}
