<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NoYudiciumController extends Controller
{
    public function generateNomorYudisium($facultyName, $tahun, $lastNumber = null)
    {
        // Mapping fakultas ke kode
        $facultyMap = [
            'ILMU TERAPAN' => 'IL-DEK',
            'REKAYASA INDUSTRI' => 'RI-DEK',
            'TEKNIK ELEKTRO' => 'TE-DEK',
            'INDUSTRI KREATIF' => 'IK-DEK',
            'INFORMATIKA' => 'IF-DEK',
            'EKONOMI DAN BISNIS' => 'EB-DEK',
            'KOMUNIKASI DAN ILMU SOSIAL' => 'KI-DEK',
            'DIREKTORAT KAMPUS SURABAYA' => 'DKS-DEK',
            'DIREKTORAT KAMPUS PURWOKERTO' => 'DKP-DEK',
        ];

        // Ambil nomor urut terakhir (misalnya dari DB), kalau null default 1
        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;
        $nomorUrut = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        // Cari kode fakultas dari mapping
        $kodeFakultas = $facultyMap[strtoupper($facultyName)] ?? 'UNK';

        // Format akhir: 01/AKD15/IF-DEK/2024
        $nomorYudisium = "{$nomorUrut}/AKD15/{$kodeFakultas}/{$tahun}";

        return $nomorYudisium;
    }

    public function index(Request $request)
{
    $faculty = $request->faculty; // dari dropdown
    $prodi   = $request->prodi;
    $semester = $request->semester;
    $tahun = date('Y');

    // Ambil daftar mahasiswa eligible (sesuai logic kamu)
    $mahasiswa = []; // query atau API

    // Cari nomor terakhir di DB
    $lastYudisium = \DB::table('yudisiums')
        ->whereYear('created_at', $tahun)
        ->orderBy('id', 'desc')
        ->first();

    $lastNumber = 0;
    if ($lastYudisium && $lastYudisium->nomor_yudisium) {
        // Ambil 2 digit pertama sebelum '/'
        $lastNumber = (int) explode('/', $lastYudisium->nomor_yudisium)[0];
    }

    // Generate nomor baru
    $nomorYudisium = $this->generateNomorYudisium($faculty, $tahun, $lastNumber);

    return view('yudisium.index', compact(
        'mahasiswa',
        'faculty',
        'prodi',
        'semester',
        'nomorYudisium'
    ));
}

public function ajaxGenerateNomor(Request $request)
{
    $faculty = $request->faculty;
    $tahun   = date('Y');

    // Cari nomor terakhir dari DB
    $lastYudisium = \DB::table('mhs_yudiciums')
        ->whereYear('created_at', $tahun)
        ->orderBy('id', 'desc')
        ->first();

    $lastNumber = 0;
    if ($lastYudisium && $lastYudisium->nomor_yudisium) {
        $lastNumber = (int) explode('/', $lastYudisium->nomor_yudisium)[0];
    }

    // Panggil function generate nomor
    $nomorYudisium = $this->generateNomorYudisium($faculty, $tahun, $lastNumber);

    return response()->json([
        'nomor_yudisium' => $nomorYudisium
    ]);
}

}
