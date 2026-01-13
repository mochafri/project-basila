<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Yudisium {{ $yudicium->no_yudicium }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f2f2f2; text-align: center; }
        h2, h4 { text-align: center; margin: 0; }
    </style>
</head>
<body>

<h2>DAFTAR MAHASISWA YUDISIUM</h2>
<h4>{{ $yudicium->no_yudicium }}</h4>
<h4>Periode: {{ $yudicium->periode }}</h4>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>IPK</th>
            <th>Predikat</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($mahasiswas as $index => $mhs)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>{{ $mhs->nim }}</td>
                <td>{{ $mhs->name }}</td>
                <td align="center">{{ $mhs->ipk }}</td>
                <td>{{ $mhs->predikat }}</td>
                <td align="center">Lulus</td>
            </tr>
        @endforeach
    </tbody>
</table>

<br><br>

<table width="100%" style="border: none">
    <tr>
        <td style="border:none; width:60%"></td>
        <td style="border:none; text-align:center">
            Mengetahui,<br>
            Ketua Sidang Yudisium<br><br><br>
            <b>(_____________________)</b>
        </td>
    </tr>
</table>

</body>
</html>
