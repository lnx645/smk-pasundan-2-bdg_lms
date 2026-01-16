<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Siswa</title>
    <style>
        /* Pengaturan Halaman */
        @page {
            margin: 1cm 1.5cm;
            /* Atas Bawah 1cm, Kiri Kanan 1.5cm */
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            /* Font formal */
            font-size: 12px;
            line-height: 1.4;
        }

        /* Styling Kop Surat */
        .kop-surat {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            /* Garis ganda di bawah kop */
            padding-bottom: 10px;
        }

        .kop-surat td {
            border: none;
            /* Hapus border tabel di kop */
        }

        .logo {
            width: 80px;
            height: auto;
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

        /* Styling Judul Laporan */
        .judul-laporan {
            text-align: center;
            margin-bottom: 15px;
        }

        .judul-laporan h3 {
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }

        /* Styling Tabel Data (Inti Masalah Kamu Tadi) */
        .tabel-data {
            width: 100%;
            border-collapse: collapse;
            /* Supaya garis menyatu */
        }

        .tabel-data th,
        .tabel-data td {
            border: 1px solid #000;
            /* Garis hitam tegas */
            padding: 6px 8px;
            /* Jarak teks ke garis */
        }

        .tabel-data th {
            background-color: #e0e0e0;
            /* Warna abu muda untuk header */
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Zebra Striping (Baris selang-seling warna) */
        .tabel-data tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Styling Tanda Tangan */
        .ttd-wrapper {
            width: 100%;
            margin-top: 40px;
        }

        .ttd {
            float: right;
            width: 30%;
            /* Lebar area tanda tangan */
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
        <h3>LAPORAN DATA SISWA</h3>
        <p style="margin: 2px 0; font-size: 10px;">Dicetak pada: {{ $tanggal }}</p>
    </div>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIS</th>
                <th>Nama Lengkap</th>
                <th width="5%">L/P</th>
                <th width="15%">Kelas</th>
                <th width="12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_siswa as $siswa)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $siswa->nis }}</td>
                    <td>{{ $siswa->user->nama ?? '-' }}</td>
                    <td class="text-center">{{ $siswa->jenis_kelamin }}</td>
                    <td class="text-center">{{ $siswa->kelas->nama ?? '-' }}</td>
                    <td class="text-center" style="text-transform: uppercase; font-size: 10px;">
                        {{ $siswa->status }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Data siswa tidak tersedia.</td>
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
