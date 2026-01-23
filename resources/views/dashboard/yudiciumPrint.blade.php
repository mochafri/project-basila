<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Lampiran Yudisium</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.4;
        }

        .title {
            font-weight: bold;
            text-align: left;
        }

        .centerFont {
            font-size: 12pt;
            line-height: 0.5;
        }

        .center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mt-30 {
            margin-top: 30px;
        }

        .mt-50 {
            margin-top: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11pt;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: middle;
        }

        th {
            text-align: center;
            font-weight: bold;
        }

        .no-border td {
            border: none;
            padding: 3px;
        }
    </style>
</head>

<body>

    @php
        $faculty = $mahasiswas->first()->facultyname ?? '-';
        $prodi = $mahasiswas->first()->prodyname ?? '-';
    @endphp

    <!-- HEADER -->
    <p class="title">
        Lampiran 1 Keputusan Rektor Universitas Telkom tentang Penetapan Lulusan
        Program Studi {{ strtoupper($prodi) }}
        Fakultas {{ strtoupper($faculty) }}
        Universitas Telkom periode {{ $yudicium->periode }}
    </p>

    <!-- JUDUL -->
    <div class="center centerFont mt-30">
        <p><b>DAFTAR LULUSAN</b></p>
        <p><b>PROGRAM STUDI {{ strtoupper($prodi) }}</b></p>
        <p><b>FAKULTAS {{ strtoupper($faculty) }}</b></p>
        <p><b>UNIVERSITAS TELKOM</b></p>
        <p><b>PERIODE {{ strtoupper($yudicium->periode) }}</b></p>
    </div>

    <!-- TABEL MAHASISWA -->
    <table class="mt-20">
        <thead>
            <tr>
                <th>NO</th>
                <th>NIM</th>
                <th>NAMA</th>
                <th>TMP LAHIR</th>
                <th>TGL LAHIR</th>
                <th>THN MASUK</th>
                <th>LULUS</th>
                <th>IPK</th>
                <th>SKS</th>
                <th>YUDISIUM</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mahasiswas as $i => $mhs)
                <tr>
                    <td align="center">{{ $i + 1 }}</td>
                    <td align="center">{{ $mhs->nim }}</td>
                    <td>{{ $mhs->name }}</td>
                    <td></td>
                    <td align="center">

                    </td>
                    <td align="center">{{ $mhs->tahun_masuk ?? '-' }}</td>
                    <td align="center">
                        {{ date('d F Y', strtotime($yudicium->periode)) }}
                    </td>
                    <td align="center">{{ $mhs->ipk }}</td>
                    <td align="center">{{ $mhs->pass_sks ?? '-' }}</td>
                    <td align="center">{{ $mhs->predikat ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <table class="no-border mt-50">
        <tr>
            <td width="60%"></td>
            <td class="center">
                Ditetapkan di : Bandung<br>
                Pada tanggal : Sesuai pengesahan sistem<br><br>
                <b>UNIVERSITAS TELKOM</b><br><br><br><br>
                <b><u>{{ $penandatangan->nama_lengkap }}</u></b><br>
                {{ $penandatangan->jabatan }}
            </td>
        </tr>
    </table>

</body>

</html>