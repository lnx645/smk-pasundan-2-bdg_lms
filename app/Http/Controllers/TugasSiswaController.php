<?php

namespace App\Http\Controllers;

use App\Models\JawabanTugas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Storage;

class TugasSiswaController extends Controller
{
    /**
     * Helper untuk mendeteksi disk mana yang digunakan (GCS atau Lokal)
     */
    private function getActiveDisk()
    {
        return config('filesystems.default') === 'gcs' ? 'gcs' : 'public';
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

        // Hapus file secara dinamis berdasarkan disk yang aktif
        $disk = $this->getActiveDisk();
        if ($jawaban->file && Storage::disk($disk)->exists($jawaban->file)) {
            Storage::disk($disk)->delete($jawaban->file);
        }

        $jawaban->delete();
        return back()->with('success', 'Pengumpulan tugas berhasil dibatalkan. Silakan upload ulang.');
    }

    public function kerjakanSimpan(Request $request)
    {
        $request->validate([
            'tugas_id' => 'required|exists:tugas,tugasID',
            'jawaban_text' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $tugas = Tugas::where('tugasID', $request->tugas_id)->firstOrFail();

        if (now()->greaterThan($tugas->deadline)) {
            return back()->with('error', 'Maaf, batas waktu pengumpulan tugas sudah berakhir. Anda tidak dapat mengirim jawaban.');
        }

        $path = null;
        $fileUrl = null;
        $disk = $this->getActiveDisk();

        if ($request->hasFile('file')) {
            // Simpan file ke folder 'jawaban-tugas' di disk yang aktif
            $path = $request->file('file')->store('jawaban-tugas', $disk);

            // Generate URL berdasarkan disk
            if ($disk === 'gcs') {
                $fileUrl = Storage::disk('gcs')->url($path);
            } else {
                $fileUrl = asset('storage/' . $path);
            }
        }

        JawabanTugas::updateOrCreate(
            [
                'tugas_id' => $request->tugas_id,
                'answered_by_id' => $request->user()->id,
            ],
            [
                'jawaban' => $request->jawaban_text,
                'file' => $path, // path relatif (misal: jawaban-tugas/abc.jpg)
                'file_url' => $fileUrl // URL lengkap (https://... atau http://...)
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
        $receivers = $tugas->receiver_type_id ?? [];

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
            abort(403, 'Maaf, tugas ini tidak ditugaskan kepada Anda atau kelas Anda.');
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
        $kelasId = $request->kelas['id'];

        $tugas = Tugas::query()->with('nilais')
            ->with(['user', 'matpel'])
            ->where(function ($q) use ($userId) {
                $q->where('receiver_type', 'siswa_id')
                    ->whereJsonContains('receiver_type_id', $userId);
            })
            ->orWhere(function ($q) use ($kelasId) {
                $q->where('receiver_type', 'class_id')
                    ->whereJsonContains('receiver_type_id', $kelasId);
            })
            ->get();

        $jawaban = JawabanTugas::with('nilai')->where('answered_by_id', $userId)
            ->get()
            ->keyBy('tugas_id');

        $tugas = $tugas->map(function ($item) use ($jawaban, $userId) {
            $nilai = collect($item->nilais)
                ->where('siswa_id', '=', $userId)
                ->where('tugas_id', '=', $item->tugasID)
                ->first();

            if ($nilai) {
                $item->nilai_siswa = $nilai->angka;
            }
            $item->is_dikerjakan = $jawaban->has($item->tugasID);
            $item->is_deadline_over = now()->greaterThan(SupportCarbon::parse($item->deadline));

            return $item;
        });

        return inertia('siswa/tugas', [
            'tugas' => $tugas->values(),
            'kelas' => $request->kelas,
        ]);
    }
}
