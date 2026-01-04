<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Matpel;
use App\Models\Pengajaran;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AkademikController extends Controller
{
    public function create()
    {
        return Inertia::render('admin/akademik/create', [
            'matpels' => Matpel::select('kode', 'nama')->orderBy('nama')->get(),

            'gurus' => Guru::with(['user', 'spesialisMatpel'])
                ->has('user')
                ->get()
                ->map(function ($guru) {
                    return [
                        'nip' => $guru->nip,
                        'nama' => $guru->user?->nama ?? $guru->user?->name ?? 'Tanpa Nama',
                        'keahlian_utama' => $guru?->spesialisMatpel?->nama ?? '',
                        'keahlian_utama_kode' =>  $guru?->spesialisMatpel?->kode ?? null,
                    ];
                }),

            'kelas_group' => Kelas::orderBy('tingkat')->orderBy('nama')->get()->groupBy('tingkat'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'matpel_kode' => 'required',
            'guru_nip' => 'required',
            'kelas_ids' => 'required|array|min:1',
        ]);

        $countBerhasil = 0;
        $countSkip = 0;

        foreach ($request->kelas_ids as $kelasId) {
            $data = Pengajaran::firstOrCreate(
                [
                    'kelas_id' => $kelasId,
                    'matpel_kode' => $request->matpel_kode,
                ],
                [
                    'guru_nip' => $request->guru_nip
                ]
            );

            if ($data->wasRecentlyCreated) {
                $countBerhasil++;
            } else {
                $countSkip++;
            }
        }

        $message = "Selesai! $countBerhasil kelas berhasil ditambahkan.";
        if ($countSkip > 0) {
            $message .= " ($countSkip kelas dilewati karena sudah memiliki mapel ini).";
        }

        return redirect()->back()->with('success', $message);
    }
}
