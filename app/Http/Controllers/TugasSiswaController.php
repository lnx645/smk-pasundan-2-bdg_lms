<?php

namespace App\Http\Controllers;

use App\Models\JawabanTugas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Storage;
use Exception;

class TugasSiswaController extends Controller
{
    /**
     * Mengambil disk default dari config (gcs atau public)
     */
    private function getActiveDisk()
    {
        return env('FILESYSTEM_DISK') ? 'gcs' : 'public';
    }

    public function batalkanPengumpulan(Request $request, string $id)
    {
        $jawaban = JawabanTugas::where('jawabanID', $id)
            ->where('answered_by_id', $request->user()->id)
            ->with('tugas')
            ->firstOrFail();

        if ($jawaban->nilai !== null) {
            return back()->with('error', 'Tugas sudah dinilai guru, tidak bisa dibatalkan.');
        }

        if ($jawaban->tugas && now()->greaterThan($jawaban->tugas->deadline)) {
            return back()->with('error', 'Batas waktu pengumpulan telah berakhir.');
        }

        // Hapus file dari storage (GCS atau Lokal)
        if ($jawaban->file) {
            $disk = $this->getActiveDisk();
            try {
                if (Storage::disk($disk)->exists($jawaban->file)) {
                    Storage::disk($disk)->delete($jawaban->file);
                }
            } catch (\Throwable $th) {
            }
        }

        $jawaban->delete();
        return back()->with('success', 'Pengumpulan tugas berhasil dibatalkan.');
    }

    public function kerjakanSimpan(Request $request, string $id)
    {
        $request->validate([
            'tugas_id' => 'nullable|exists:tugas,tugasID',
            'jawaban_text' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // Max 10MB
        ]);

        $tugas_id = $request->tugas_id ?? $id;
        $tugas = Tugas::where('tugasID', $tugas_id)->firstOrFail();
        // Cek Deadline
        if (now()->greaterThan($tugas->deadline)) {
            return back()->with('error', 'Maaf, batas waktu pengumpulan tugas sudah berakhir.');
        }

        $path = null;
        $fileUrl = null;
        $disk = $this->getActiveDisk();

        if ($request->file('file')) {
            try {
                $path = $request->file('file')->store('jawaban-tugas', $disk);
                $fileUrl = Storage::disk($disk)->url($path);
            } catch (Exception $e) {
                return back()->with('error', 'Gagal upload ke Cloud Storage: ' . $e->getMessage());
            }
        }

        JawabanTugas::updateOrCreate(
            [
                'tugas_id' => $tugas_id,
                'answered_by_id' => $request->user()->id,
            ],
            [
                'jawaban' => $request->jawaban_text,
                'file' => $path,
                'file_url' => $fileUrl
            ]
        );

        return back()->with('success', 'Tugas berhasil dikirim.');
    }

    public function kerjakan(Request $request, string $id)
    {
        $user = $request->user();
        $siswa = $user->siswa;
        $tugas = Tugas::with(['user', 'matpel'])->findOrFail($id);

        $isAssigned = false;
        $receivers = is_array($tugas->receiver_type_id) ? $tugas->receiver_type_id : json_decode($tugas->receiver_type_id, true) ?? [];

        if ($tugas->receiver_type === 'class_id') {
            if ($siswa && in_array($siswa->kelas_id, $receivers)) {
                $isAssigned = true;
            }
        } elseif ($tugas->receiver_type === 'siswa_id') {
            if (in_array($user->id, $receivers)) {
                $isAssigned = true;
            }
        }

        if (!$isAssigned) {
            abort(403, 'Maaf, tugas ini tidak ditugaskan kepada Anda.');
        }

        $submission = JawabanTugas::with(['nilai'])->where('tugas_id', $id)
            ->where('answered_by_id', $user->id)
            ->first();

        return inertia('siswa/tugas/kerjakan', [
            'tugas' => $tugas,
            'submission' => $submission,
        ]);
    }

    public function showTugas(Request $request)
    {
        $userId  = $request->user()->id;
        $kelasId = $request->kelas['id'] ?? null;

        $tugas = Tugas::query()
            ->with(['user', 'matpel', 'nilais'])
            ->where(function ($q) use ($userId) {
                $q->where('receiver_type', 'siswa_id')
                    ->whereJsonContains('receiver_type_id', (int)$userId);
            });

        if ($kelasId) {
            $tugas->orWhere(function ($q) use ($kelasId) {
                $q->where('receiver_type', 'class_id')
                    ->whereJsonContains('receiver_type_id', (int)$kelasId);
            });
        }

        $tugasList = $tugas->get();

        $jawaban = JawabanTugas::where('answered_by_id', $userId)
            ->get()
            ->keyBy('tugas_id');

        $tugasList = $tugasList->map(function ($item) use ($jawaban, $userId) {
            $nilai = collect($item->nilais)
                ->where('siswa_id', $userId)
                ->first();

            $item->nilai_siswa = $nilai ? $nilai->angka : null;
            $item->is_dikerjakan = $jawaban->has($item->tugasID);
            $item->is_deadline_over = now()->greaterThan(SupportCarbon::parse($item->deadline));

            return $item;
        });

        return inertia('siswa/tugas', [
            'tugas' => $tugasList->values(),
            'kelas' => $request->kelas,
        ]);
    }
}
