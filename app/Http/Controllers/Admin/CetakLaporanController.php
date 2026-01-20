<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Matpel;
use App\Models\Nilai;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CetakLaporanController extends Controller
{
    public function cetakKelas(Request $request)
    {
        $query = Kelas::withCount('siswa')
            ->with(['pengajarans.guru.user', 'pengajarans.matpel']);

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
    public function __invoke()
    {
        return inertia('admin/laporan/index', [
            'list_kelas' => Kelas::orderBy('tingkat')->orderBy('nama')->get(['id', 'nama', 'tingkat']),
            'list_matpel' => Matpel::orderBy('nama')->get(['nama', 'kode']),
        ]);
    }

    // 1. CETAK SISWA (Order by Nama Siswa A-Z)
    public function cetakLaporanSiswa(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $query = Siswa::query()
            ->join('users', 'siswas.user_id', '=', 'users.id')
            ->with(['kelas'])
            ->select('siswas.*'); // Agar ID tidak tertukar dengan ID User

        if ($request->filled('kelas_id') && $request->kelas_id != 'all') {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Urutkan berdasarkan kolom name di tabel users
        $siswa = $query->orderBy('users.name', 'asc')->get();

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

        return $pdf->setPaper('a4', 'portrait')->stream('Laporan-Siswa.pdf');
    }

    // 2. CETAK GURU (Order by Nama Guru A-Z)
    public function cetakGuru(Request $request)
    {
        $query = Guru::query()
            ->join('users', 'gurus.user_id', '=', 'users.id')
            ->with(['user', 'pengajarans.matpel', 'pengajarans.kelas'])
            ->select('gurus.*');

        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Urutkan berdasarkan nama guru
        $guru = $query->orderBy('users.name', 'asc')->get();

        $pdf = Pdf::loadView('pdf.laporan_guru', [
            'data_guru' => $guru,
            'tanggal' => now()->translatedFormat('d F Y')
        ]);

        return $pdf->setPaper('a4', 'portrait')->stream('Laporan-Guru.pdf');
    }

    // 3. CETAK NILAI (Order by Nama Siswa A-Z)
    public function cetakNilai(Request $request)
    {
        $query = Nilai::query()
            ->with(['siswa.kelas', 'siswa', 'siswa.user', 'tugas.matpel'])
            ->select('nilais.*'); // Pastikan hanya kolom dari tabel nilais yang diambil

        // Filter Kelas
        if ($request->filled('kelas_id') && $request->kelas_id != 'all') {
            $query->where('siswas.kelas_id', $request->kelas_id);

            $query->whereHas('tugas', function ($q) use ($request) {
                $q->whereJsonContains('receiver_type_id', $request->kelas_id);
            });
        }

        // Filter Mapel
        if ($request->filled('matpel_kode') && $request->matpel_kode != 'all') {
            $query->whereHas('tugas', function ($q) use ($request) {
                $q->where('matpel_kode', $request->matpel_kode);
            });
        }

        // 3. Order By Nama di tabel users (A-Z)
        $nilai = $query->get();

        $pdf = Pdf::loadView('pdf.laporan_nilai', [
            'data_nilai' => $nilai,
            'tanggal' => now()->translatedFormat('d F Y'),
            'kelas_terpilih' => $request->kelas_id,
            'mapel_terpilih' => $request->matpel_kode
        ]);

        return $pdf->setPaper('a4', 'portrait')->stream('Laporan-Nilai.pdf');
    }
}
