<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Nilai</title>
    <style>
        /* SETUP HALAMAN & FONT (Sama dengan Siswa/Guru/Kelas) */
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

        .text-bold {
            font-weight: bold;
        }

        /* Zebra Striping */
        .tabel-data tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Styling Khusus Nilai */
        .nilai-bagus {
            color: black;
        }

        .nilai-jelek {
            color: red;
            font-style: italic;
        }

        /* Contoh jika nilai < 75 */

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
                <h2 style="border: 2px solid black; padding: 5px;">
                    <img src="{{ base_path('resources/img/log.png') }}" alt="">
                </h2>
            </td>
            @include('pdf.kop_surat')
        </tr>
    </table>

    <div class="judul-laporan">
        <h3>REKAPITULASI NILAI SISWA</h3>
        <p style="margin: 2px 0; font-size: 10px;">Dicetak pada: {{ $tanggal }}</p>
    </div>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Siswa</th>
                <th width="30%">Tugas / Matpel</th>
                <th width="10%">Nilai</th>
                <th>Komentar Guru</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_nilai as $nilai)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <span class="text-bold">{{ $nilai->siswa->name ?? 'Siswa Terhapus' }}</span>
                        <br>
                        <span style="font-size: 10px; color: #666;">
                            {{ $nilai->siswa->email ?? '' }}
                        </span>
                    </td>
                    <td>
                        {{ $nilai->tugas->title ?? ($nilai->tugas->nama ?? 'Tugas #' . $nilai->tugas_id) }}
                        /
                        {{ $nilai->tugas->matpel->nama }}
                    </td>
                    <td class="text-center text-bold" style="font-size: 14px;">
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
                    <td colspan="5" class="text-center" style="padding: 20px;">Belum ada data nilai yang masuk.</td>
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
