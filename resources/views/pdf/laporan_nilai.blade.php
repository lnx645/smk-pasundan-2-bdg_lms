<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Nilai</title>
    <style>
        /* SETUP HALAMAN */
        @page {
            margin: 1cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.3;
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
            vertical-align: middle;
        }

        /* JUDUL LAPORAN */
        .judul-laporan {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul-laporan h3 {
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
            font-size: 14px;
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
            text-align: left;
            /* Default rata kiri */
        }

        .tabel-data th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }

        /* Mencegah baris tabel terpotong saat ganti halaman */
        .tabel-data tr {
            page-break-inside: avoid;
        }

        /* Utility Classes */
        .text-center {
            text-align: center !important;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #666;
            font-size: 10px;
        }

        /* Zebra Striping */
        .tabel-data tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Styling Nilai */
        .nilai-bagus {
            color: black;
        }

        .nilai-jelek {
            color: red;
            font-weight: bold;
        }

        /* TANDA TANGAN */
        .ttd-wrapper {
            width: 100%;
            margin-top: 40px;
        }

        .ttd {
            float: right;
            width: 35%;
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
        <h3>REKAPITULASI NILAI SISWA</h3>
        @if (isset($kelas_terpilih) && $kelas_terpilih != 'all')
            <p style="margin: 2px 0;">Kelas: {{ $data_nilai->first()->siswa->kelas->nama ?? $kelas_terpilih }}</p>
        @endif
        <p style="margin: 2px 0; font-size: 10px;">Dicetak pada: {{ $tanggal }}</p>
    </div>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Siswa</th>
                <th width="35%">Detail Tugas</th>
                <th width="10%">Nilai</th>
                <th>Komentar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_nilai as $nilai)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <span class="text-bold">{{ $nilai->siswa->name ?? 'Siswa Terhapus' }}</span>
                        <br>
                        <span class="text-muted">
                            {{ $nilai->siswa->kelas->nama ?? '-' }} | {{ $nilai->siswa->nis ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <span class="text-bold">{{ $nilai->tugas?->title ?? 'Tugas dihapus' }}</span>
                        <br>
                        <span class="text-muted">
                            Mapel: {{ $nilai->tugas?->matpel?->nama ?? '-' }}
                        </span>
                    </td>
                    <td class="text-center text-bold" style="font-size: 13px;">
                        <span class="{{ $nilai->angka < 75 ? 'nilai-jelek' : 'nilai-bagus' }}">
                            {{ $nilai->angka }}
                        </span>
                    </td>
                    <td>
                        {{ $nilai->komentar ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">
                        <i>Belum ada data nilai yang sesuai filter.</i>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-wrapper">
        <div class="ttd">
            <p>Bandung, {{ $tanggal }}</p>
            <p>Mengetahui,</p>
            <p>Waka Kurikulum</p>
            <br><br><br>
            <p style="text-decoration: underline; font-weight: bold;">(Nama Pejabat)</p>
            <p>NIP. .......................</p>
        </div>
    </div>

</body>

</html>
