<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Kelas</title>
    <style>
        /* SETUP HALAMAN & FONT */
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

        /* JUDUL LAPORAN */
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

        /* LIST MAPEL (Biar rapi di dalam tabel) */
        ul.list-mapel {
            margin: 0;
            padding-left: 15px;
            list-style-type: square;
        }

        ul.list-mapel li {
            margin-bottom: 3px;
        }

        .nama-guru {
            font-size: 10px;
            color: #555;
            font-style: italic;
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
        <h3>REKAPITULASI DATA KELAS</h3>
        <p style="margin: 2px 0; font-size: 10px;">Dicetak pada: {{ $tanggal }}</p>
    </div>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Nama Kelas</th>
                <th width="10%">Tingkat</th>
                <th width="10%">Jml Siswa</th>
                <th>Daftar Mata Pelajaran & Pengajar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_kelas as $kelas)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $kelas->nama }}</td>
                    <td class="text-center">{{ $kelas->tingkat }}</td>
                    <td class="text-center">
                        {{ $kelas->siswa_count }} Orang
                    </td>
                    <td>
                        @if ($kelas->pengajarans && $kelas->pengajarans->count() > 0)
                            <ul class="list-mapel">
                                @foreach ($kelas->pengajarans as $p)
                                    <li>
                                        <strong>{{ $p->matpel->nama ?? $p->matpel_kode }}</strong>
                                        <br>
                                        <span class="nama-guru">
                                            Guru: {{ $p->guru->user->name ?? ($p->guru_nip ?? '-') }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: red; font-size: 10px; font-style:italic;">Belum ada jadwal mapel</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">Belum ada data kelas yang dibuat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-wrapper">
        <div class="ttd">
            <p>Bandung, {{ $tanggal }}</p>
            <p>Waka Kurikulum</p>
            <br><br><br><br>
            <p style="text-decoration: underline; font-weight: bold;">Nama Pejabat</p>
            <p>NIP. .......................</p>
        </div>
    </div>

</body>

</html>
