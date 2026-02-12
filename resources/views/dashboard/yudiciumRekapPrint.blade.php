<!DOCTYPE html>
<html>

<head>

    <title>REKAP YUDISIUM {{ strtoupper($periodeLabel) }}</title>
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

    <div class="center centerFont mt-30">
        <p><b>DAFTAR LULUSAN</b></p>
        <p><b>FAKULTAS {{ strtoupper($faculty) }}</b></p>
        <p><b>UNIVERSITAS TELKOM</b></p>
        <p><b>PERIODE {{ strtoupper($periodeLabel) }}</b></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Yudisium</th>
                <th>Nama</th>
                <th>Fakultas</th>
                <th>Prodi</th>
                <th>IPK</th>
                <th>Predikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mahasiswas as $i => $mhs)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $mhs->no_yudicium }}</td>
                    <td>{{ $mhs->name }}</td>
                    <td>{{ $mhs->facultyname }}</td>
                    <td>{{ $mhs->prodyname }}</td>
                    <td>{{ $mhs->ipk }}</td>
                    <td>{{ $mhs->predikat }}</td>
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
                <b><u>Prof.Dr.Suyanto,S.T.,M.Sc.</u></b><br>
                Rektor
            </td>
        </tr>
    </table>

</body>

</html>