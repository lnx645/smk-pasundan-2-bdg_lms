<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Guru</title>
    <style>
        /* Menggunakan CSS yang sama dengan Laporan Siswa agar konsisten */
        @page {
            margin: 1cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.4;
        }

        /* KOP SURAT */
        .kop-surat {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }

        .kop-surat td {
            border: none;
        }

        .institusi {
            text-align: center;
        }

        .institusi h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .institusi h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .alamat {
            font-size: 11px;
            font-style: italic;
        }

        /* JUDUL */
        .judul-laporan {
            text-align: center;
            margin-bottom: 15px;
        }

        .judul-laporan h3 {
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }

        /* TABEL DATA */
        .tabel-data {
            width: 100%;
            border-collapse: collapse;
        }

        .tabel-data th,
        .tabel-data td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            /* Agar teks mapel yang panjang tetap rapi di atas */
        }

        .tabel-data th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .tabel-data tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* TANDA TANGAN */
        .ttd-wrapper {
            width: 100%;
            margin-top: 40px;
        }

        .ttd {
            float: right;
            width: 30%;
            text-align: center;
        }
    </style>
</head>

<body>

    <table class="kop-surat">
        <tr>
            <td width="15%" align="center">
                <img width="80%" src="{{ public_path('logo.png') }}" class="logo" alt="Logo">
            </td>
            @include('pdf.kop_surat')

        </tr>
    </table>

    <div class="judul-laporan">
        <h3>LAPORAN DATA GURU DAN STAFF</h3>
        <p style="margin: 2px 0; font-size: 10px;">Dicetak pada: {{ $tanggal }}</p>
    </div>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="18%">NIP</th>
                <th>Nama Lengkap & Gelar</th>
                <th width="5%">L/P</th>
                <th width="25%">Mata Pelajaran Diampu</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_guru as $guru)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $guru->nip }}</td>
                    <td>
                        {{ $guru->gelar_depan ? $guru->gelar_depan . ' ' : '' }}
                        <strong>{{ $guru->user->name ?? '-' }}</strong>{{ $guru->gelar_belakang ? ', ' . $guru->gelar_belakang : '' }}
                    </td>
                    <td class="text-center">{{ $guru->jenis_kelamin }}</td>
                    <td>
                        {{-- LOGIKA BARU UNTUK MENAMPILKAN MAPEL & KELAS --}}
                        @if ($guru->pengajarans && $guru->pengajarans->count() > 0)
                            <ul style="margin: 0; padding-left: 15px;">
                                {{-- 1. Kelompokkan Pengajaran berdasarkan Nama Mapel --}}
                                @foreach ($guru->pengajarans->groupBy('matpel.nama') as $namaMapel => $listPengajaran)
                                    <li style="margin-bottom: 4px;">
                                        <strong>{{ $namaMapel ?? 'Mapel Tanpa Nama' }}</strong>
                                        <br>
                                        {{-- 2. Tampilkan list kelas untuk mapel tersebut --}}
                                        <span style="font-size: 10px; color: #555;">
                                            Kelas:
                                            @foreach ($listPengajaran as $p)
                                                {{ $p->kelas->nama ?? '-' }}@if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #888; font-style: italic;">- Tidak ada jadwal -</span>
                        @endif
                    </td>
                    <td class="text-center" style="text-transform: uppercase; font-size: 10px; font-weight: bold;">
                        {{ $guru->status }}
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 20px;">Data guru tidak tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="ttd-wrapper">
            <div class="ttd">
                <p>Bandung, {{ $tanggal }}</p>
                <p>Kepala Sekolah</p>
                <br><br><br><br>
                <p style="text-decoration: underline; font-weight: bold;">Nama Kepala Sekolah</p>
                <p>NIP. 19800101 200001 1 001</p>
            </div>
        </div>

    </body>

    </html>
