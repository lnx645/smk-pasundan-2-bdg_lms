<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KenaikanKelasController extends Controller
{

    public function index(Request $request)
    {
        $kelas_id = $request->query('kelas_id');

        $siswas = [];
        if ($kelas_id) {
            $siswas = Siswa::where('kelas_id', $kelas_id)
                ->with('user')
                ->where('status', 'aktif')
                ->get();
        }

        return inertia('admin/user-management/siswa/naik-kelas', [
            'list_kelas' => Kelas::all(),
            'siswas' => $siswas,
            'selected_kelas_id' => $kelas_id
        ]);
    }

    /**
     * Proses eksekusi naik kelas (Opsi B)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nis_list'     => 'required|array|min:1',
            'nis_list.*'   => 'exists:siswas,nis',
            'tingkat_baru' => 'required',
            'kelas_tujuan_id' => 'required_unless:tingkat_baru,13|nullable|exists:kelas,id',
        ], [
            'kelas_tujuan_id.required_unless' => 'Kelas tujuan harus dipilih kecuali siswa dinyatakan lulus.',
        ]);

        $updateData = [
            'tingkat' => $request->tingkat_baru,
        ];

        if ($request->tingkat_baru == '13') {
            $updateData['kelas_id'] = null;
            $updateData['status']   = 'lulus';
        } else {
            $updateData['kelas_id'] = $request->kelas_tujuan_id;
            $updateData['status']   = 'aktif';
        }

        Siswa::whereIn('nis', $request->nis_list)->update($updateData);
        return redirect()->back()->with('message', count($request->nis_list) . ' Siswa berhasil dinaikkan kelasnya!');
    }
}
