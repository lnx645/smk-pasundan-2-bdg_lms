<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CetakLaporanController extends Controller
{
    public function __invoke()
    {
        return inertia('admin/laporan/index');
    }
    public function cetakLaporanSiswa()
    {
        $siswa = Siswa::with(['user', 'kelas'])
            ->get()
            ->sortBy(function ($q) {
                return $q->kelas->nama ?? 'Unassigned';
            });

        $pdf = Pdf::loadView('pdf.laporan_siswa', [
            'data_siswa' => $siswa,
            'tanggal' => now()->translatedFormat('d F Y')
        ]);

        // Atur kertas di sini
        $pdf->setPaper('a4', 'portrait');

        // Tampilkan (Stream)
        return $pdf->stream('Laporan-Siswa.pdf');
    }

    // 2. CETAK GURU
    public function cetakGuru()
    {
        // Kita eager load 'matpels' juga supaya bisa menampilkan mapel yang diajar
        $guru = Guru::with(['user', 'matpels'])->get();

        $pdf = Pdf::loadView('pdf.laporan_guru', [
            'data_guru' => $guru,
            'tanggal' => now()->translatedFormat('d F Y')
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Guru.pdf');
    }
    // 3. CETAK KELAS
    public function cetakKelas()
    {
        // 1. Ambil data Kelas
        // 2. Hitung jumlah siswa (siswa_count)
        // 3. Eager load 'pengajarans' beserta 'guru.user' dan 'matpel' untuk detail kurikulum
        $kelas = Kelas::withCount('siswa')
            ->with(['pengajarans.guru.user', 'pengajarans.matpel'])
            ->orderBy('tingkat') // Urutkan dari kelas 10, 11, 12
            ->orderBy('nama')    // Lalu urutkan nama kelas
            ->get();

        $pdf = Pdf::loadView('pdf.laporan_kelas', [
            'data_kelas' => $kelas,
            'tanggal' => now()->translatedFormat('d F Y')
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Data-Kelas.pdf');
    }
    // 4. CETAK NILAI
    public function cetakNilai()
    {
        // Ambil data nilai
        // with(['siswa', 'tugas']) -> Agar nama siswa dan judul tugas terbawa
        // latest() -> Urutkan dari yang terbaru
        // limit(100) -> Batasi 100 data agar tidak berat (Opsional, bisa dihapus jika ingin semua)
        $nilai = Nilai::with(['siswa', 'tugas'])
            ->latest()
            ->limit(100)
            ->get();

        $pdf = Pdf::loadView('pdf.laporan_nilai', [
            'data_nilai' => $nilai,
            'tanggal' => now()->translatedFormat('d F Y')
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Nilai.pdf');
    }
}
