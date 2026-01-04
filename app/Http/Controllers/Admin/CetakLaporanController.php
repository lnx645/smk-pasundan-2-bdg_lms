<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Matpel; // Pastikan model Matpel di-import
use App\Models\Nilai;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CetakLaporanController extends Controller
{
    // Halaman Utama Laporan
    public function __invoke()
    {
        return inertia('admin/laporan/index', [
            // Kirim data untuk dropdown filter
            'list_kelas' => Kelas::orderBy('tingkat')->orderBy('nama')->get(['id', 'nama', 'tingkat']),
            'list_matpel' => Matpel::orderBy('nama')->get(['nama', 'kode']),
        ]);
    }

    // 1. CETAK SISWA (Filter: Kelas, Status)
    public function cetakLaporanSiswa(Request $request)
    {
        $query = Siswa::with(['user', 'kelas']);

        // Filter Kelas
        if ($request->has('kelas_id') && $request->kelas_id != 'all') {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter Status (Default: Aktif)
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $siswa = $query->get()->sortBy(function ($q) {
            return $q->kelas->nama ?? 'Unassigned';
        });

        // Judul Dinamis
        $judul = 'Laporan Data Siswa';
        if ($request->kelas_id && $request->kelas_id != 'all') {
            $kelas = Kelas::find($request->kelas_id);
            $judul .= ' - Kelas ' . ($kelas->nama ?? '');
        }

        $pdf = Pdf::loadView('pdf.laporan_siswa', [
            'data_siswa' => $siswa,
            'judul' => $judul,
            'tanggal' => now()->translatedFormat('d F Y')
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan-Siswa.pdf');
    }

    // 2. CETAK GURU (Filter: Status)
    public function cetakGuru(Request $request)
    {

        $query = Guru::with(['user', 'pengajarans.matpel', 'pengajarans.kelas']);

        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $guru = $query->get();

        $pdf = Pdf::loadView('pdf.laporan_guru', [
            'data_guru' => $guru,
            'tanggal' => now()->translatedFormat('d F Y')
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan-Guru.pdf');
    }

    // 3. CETAK KELAS (Filter: Tingkat)
    public function cetakKelas(Request $request)
    {
        $query = Kelas::withCount('siswa')
            ->with(['pengajarans.guru.user', 'pengajarans.matpel']);

        // Filter Tingkat
        if ($request->has('tingkat') && $request->tingkat != 'all') {
            $query->where('tingkat', $request->tingkat);
        }

        $kelas = $query->orderBy('tingkat')->orderBy('nama')->get();

        $pdf = Pdf::loadView('pdf.laporan_kelas', [
            'data_kelas' => $kelas,
            'tanggal' => now()->translatedFormat('d F Y')
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan-Data-Kelas.pdf');
    }

    public function cetakNilai(Request $request)
    {
        // 1. Eager Loading (Sudah benar)
        // Memuat relasi siswa->kelas dan tugas->matpel untuk performa query
        $query = Nilai::with(['siswa.kelas', 'tugas.matpel']);

        // 2. Filter Kelas (IMPLEMENTASI JSON DISINI)
        if ($request->filled('kelas_id') && $request->kelas_id != 'all') {

            // A. Filter Siswanya:
            // Hanya ambil nilai dari siswa yang memang berada di kelas tersebut
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });

            // B. Filter Tugasnya (Logic Target Kelas JSON):
            // Hanya ambil tugas yang kolom 'receiver_type_id'-nya mengandung ID kelas yang dipilih.
            // Ini memastikan tugas yang tampil memang ditujukan untuk kelas itu.
            $query->whereHas('tugas', function ($q) use ($request) {
                // 'receiver_type_id' adalah nama kolom JSON di tabel tugas
                $q->whereJsonContains('receiver_type_id', $request->kelas_id);
            });
        }

        // 3. Filter Mapel
        if ($request->filled('matpel_kode') && $request->matpel_kode != 'all') {
            $query->whereHas('tugas', function ($q) use ($request) {
                $q->where('matpel_kode', $request->matpel_kode);
            });
        }

        // Eksekusi Query
        $nilai = $query->latest()->get();

        // Generate PDF
        $pdf = Pdf::loadView('pdf.laporan_nilai', [
            'data_nilai' => $nilai,
            'tanggal' => now()->translatedFormat('d F Y'),
            // Opsional: Kirim info kelas/mapel terpilih ke view untuk judul laporan
            'kelas_terpilih' => $request->kelas_id,
            'mapel_terpilih' => $request->matpel_kode
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan-Nilai.pdf');
    }
}
