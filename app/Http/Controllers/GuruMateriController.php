<?php

namespace App\Http\Controllers;

use App\Models\Discusion;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Siswa;
use App\Service\Contract\KelasServiceInterface;
use App\Service\Contract\MatpelServiceInterface;
use App\Service\MateriService;
use App\Service\Contract\MateriServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuruMateriController extends Controller
{
    /**
     * Handle the incoming request to retrieve learning materials for a specific class and subject.
     *
     * This method checks if a class code is provided. If it is, it retrieves the corresponding 
     * subject materials created by the teacher associated with the given class code. It filters 
     * the materials based on the class IDs and returns them along with the subject information 
     * to the 'guru/materi' view. If no class code is provided, it retrieves the classes 
     * associated with the teacher and returns them to the 'guru/pilih-kelas' view.
     *
     * @param Request $request The incoming request containing user and role information.
     * @param KelasServiceInterface $kelasService Service for retrieving class information.
     * @param MateriServiceInterface $materiService Service for handling learning materials.
     * @param MatpelServiceInterface $matpelService Service for retrieving subject information.
     * @param string|null $kelas_kode The class code to filter materials (optional).
     *
     * @return \Inertia\Response The response containing either the materials view or the class selection view.
     */

    public function materi(
        Request $request,
        KelasServiceInterface $kelasService,
        MatpelServiceInterface $matpelService,
        MateriService $materiService,
        string|null $kelas_kode = null
    ) {
        $nipGuru = $request->role_id;
        $userId = $request->user()->id;
        if ($kelas_kode) {
            $kodeMatpel = $request->kode_matpel;
            $matpel = $matpelService->getMatpelByKelasAndGuru(
                $kelas_kode,
                $nipGuru
            );
            if (!empty($kodeMatpel)) {
                $materi = $materiService->getMateriByGuruDanKelas(
                    $kodeMatpel,
                    $userId,
                    $kelas_kode
                )->map(function ($item) {
                    return array_merge(
                        $item,
                        ['publish_date' => Carbon::parse($item['publish_date'])->format('d-M-y h:i:s')],
                        ['is_published' => Carbon::parse($item['publish_date'])->lessThanOrEqualTo(now())]
                    );
                });
            }
            if (count($matpel) == 1 && empty($kodeMatpel)) {
                return redirect()->route('guru.materi', [
                    'kelas_kode' => $kelas_kode,
                    'kode_matpel' => $matpel[0]->kode_matpel,
                ]);
            }
            return inertia('guru/materi', [
                'matpels' => $matpel,
                'kelas_kode' => $kelas_kode,
                'kode_matpel' => $kodeMatpel,
                'materials' => $materi ?? null,
                'matpel_count' => count($matpel),
            ]);
        }
        $kelas = $kelasService->getKelasByGuru($nipGuru);
        return inertia('guru/pilih-kelas', [
            'kelas' => $kelas,
        ]);
    }
    /**
     * Menambahkan materi untuk kelas tertentu.
     *
     * @param string $kelas_kode Kode kelas yang akan ditambahkan materinya.
     * @param Request $request Objek request yang berisi data yang diperlukan.
     * @param MatpelServiceInterface $matpelService Layanan untuk mengambil data mata pelajaran.
     * @param KelasServiceInterface $kelasService Layanan untuk mengambil data kelas.
     *
     * @return \Inertia\Response Mengembalikan respons Inertia dengan data yang diperlukan untuk tampilan.
     *
     * Fungsi ini mengambil data mata pelajaran berdasarkan kode kelas dan NIP guru,
     * serta mengambil data kelas yang diajarkan oleh guru tersebut. 
     * Kelas aktif kemudian disiapkan untuk ditampilkan dalam tampilan.
     */
    public function tambahMateri(
        string $kelas_kode,
        Request $request,
        MatpelServiceInterface $matpelService,
        KelasServiceInterface $kelasService
    ) {
        $nipGuru = $request->role_id;
        $matpels = $matpelService->getMatpelByKelasAndGuru($kelas_kode, $nipGuru);
        $kelas = $kelasService->getKelasByGuru($nipGuru);
        $kelasActive = [Kelas::find($kelas_kode)->id];
        //masuk diskusi

        return inertia('guru/tambah-materi', [
            'matpels' => $matpels,
            'kelas' => $kelas,
            'kelas_kode' => $kelas_kode,
            'active_kelas' => $kelasActive,
        ]);
    }
    /**
     * Menyimpan data materi pembelajaran baru
     *
     * Method ini menangani penyimpanan materi pembelajaran dengan validasi data input.
     * Data yang disimpan akan dikaitkan dengan kelas dan user yang sedang login.
     *
     * @param Request $request Object request yang berisi data input dari form
     * @param string $kelas_kode Kode kelas untuk mengidentifikasi kelas tujuan
     * @param MateriServiceInterface $materiService Service untuk menangani logika penyimpanan materi
     *
     * @return \Illuminate\Http\RedirectResponse Redirect kembali ke halaman sebelumnya dengan pesan success atau error
     *
     * @throws \Illuminate\Validation\ValidationException Jika validasi data gagal
     *
     * Validasi Input:
     * - title: string, wajib diisi
     * - matpel: wajib diisi (mata pelajaran)
     * - youtube_id: string, wajib diisi, harus URL valid
     * - kelas_ids: array, wajib diisi
     * - description: string, wajib diisi
     * - file_materi: file, opsional
     *
     * @note Terdapat bug pada pesan error - kedua kondisi menampilkan pesan yang sama
     */
    public function simpanMateri(
        Request $request,
        string $kelas_kode,
        MateriServiceInterface $materiService
    ) {
        $id = $request->user()->id;
        $data = $request->validate([
            'title' => 'string|required',
            'matpel' => "required",
            'youtube_id' => "string|required|url",
            'kelas_ids' => ['required'],
            'description' => "string|required",
            'publish_date' => ['nullable'],
            'file_materi' => ["nullable"]
        ]);
        if ($materiService->simpanMateri($data, $kelas_kode, $id)) {
            return redirect()->back()->withErrors([
                'success' => "Materil berhasil di tambahkan"
            ]);
        }

        return redirect()->back()->withErrors([
            'gagal' => "Materil berhasil di tambahkan"
        ]);
    }
    public function deleteMateri(
        Request $request,
        string $materi_id
    ) {
        $id = $request->user()->id;
        $materi = Materi::find($materi_id);
        if ($materi && $materi->created_by_user_id === $id && $materi->delete()) {
            return redirect()->back()->withErrors([
                'success' => "Materil berhasil di hapus"
            ]);
        }
        return redirect()->back()->withErrors([
            'gagal' => "Materil gagal di hapus"
        ]);
    }
    public function publishMateri(Request $request)
    {
        $id = $request->id ?? null;
        if (!$id) {
            return redirect()->back()->withErrors([
                'gagal' => "Materil gagal di Publish"
            ]);
        }
        $materi = Materi::find($id);
        if ($materi) {
            $materi->update([
                'publish_date' => now()
            ]);
            return redirect()->back()->withErrors([
                'success' => "Materil berhasil di Publish"
            ]);
        }
    }
}
