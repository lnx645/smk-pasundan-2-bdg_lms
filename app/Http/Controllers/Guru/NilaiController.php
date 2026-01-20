<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\User;
use App\Service\Contract\MatpelServiceInterface;
use App\Service\MatpelService;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    /**
     * 1. Menampilkan Halaman (View)
     * Route: GET /guru/nilai
     */
    public function kelola_nilai(Request $request,  MatpelServiceInterface $matpelService)
    {
        $id = $request->role_id;
        $matpel = $matpelService->getMatpelByGuru($id);
        return inertia('guru/nilai/kelola', [
            'matpel' => $matpel,
        ]);
    }


    public function index(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        // Query dasar dengan relasi
        $query = Nilai::with(['siswa', 'tugas.matpel']);

        // Filter: Jika ada siswa_id yang dikirim dari frontend
        if ($request->filled('siswa_id')) {
            $query->where('siswa_id', $request->siswa_id);
        }
        // Ambil data urut terbaru
        $nilai = $query->latest()->get();

        $listSiswa = User::whereHas('nilais')
            ->select('id', 'name')
            ->distinct()
            ->get();

        return response()->json([
            'nilai' => $nilai,
            'siswa' => $listSiswa
        ]);
    }

    /**
     * 3. Update Nilai (Edit)
     * Route: PUT /guru/nilai/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'angka'    => 'required|numeric|min:0|max:100',
            'komentar' => 'nullable|string|max:255',
        ]);

        $nilai = Nilai::findOrFail($id);

        $nilai->update([
            'angka'    => $request->angka,
            'komentar' => $request->komentar
        ]);

        return response()->json([
            'message' => 'Nilai berhasil diperbarui',
            'data'    => $nilai
        ]);
    }

    /**
     * 4. Hapus Nilai
     * Route: DELETE /guru/nilai/{id}
     */
    public function destroy($id)
    {
        $nilai = Nilai::findOrFail($id);
        $nilai->delete();

        return response()->json([
            'message' => 'Nilai berhasil dihapus'
        ]);
    }

    /**
     * 5. Simpan Nilai Baru (Create/Update)
     * Route: POST /guru/nilai/simpan
     */
    public function simpan(Request $request)
    {
        $validated = $request->validate([
            'tugas_id'   => 'required|exists:tugas,tugasID', // Pastikan nama kolom primary key di tabel tugas benar
            'siswa_id'   => 'required|exists:users,id',
            'jawaban_id' => 'nullable', // Boleh null jika guru input manual tanpa jawaban siswa
            'nilai'      => 'required|numeric|min:0|max:100',
            'komentar'   => 'nullable|string',
        ]);

        // Menggunakan updateOrCreate agar tidak ada duplikat nilai 
        // untuk 1 siswa pada 1 tugas yang sama.
        Nilai::updateOrCreate(
            [
                'tugas_id' => $validated['tugas_id'],
                'siswa_id' => $validated['siswa_id'],
            ],
            [
                'jawaban_id' => $validated['jawaban_id'] ?? null,
                'angka'      => $validated['nilai'], // Mapping: Input 'nilai' -> DB 'angka'
                'komentar'   => $validated['komentar'] ?? null,
            ]
        );

        return back()->with('success', 'Nilai berhasil disimpan!');
    }
}
