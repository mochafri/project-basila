<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MhsYud;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        // Total mahasiswa yudisium
        $totalMahasiswa = MhsYud::count();

        // Hitung jumlah mahasiswa per predikat
        $predikatCounts = MhsYud::select('predikat', DB::raw('COUNT(*) as total'))
            ->groupBy('predikat')
            ->get();

        // Urutan predikat yang akan ditampilkan
        $predikatList = [
            'Istimewa (Summa Cumlaude)',
            'Dengan Pujian (Cumlaude)',
            'Sangat Memuaskan (Very Good)',
            'Memuaskan (Good)',
            'Tanpa Predikat',
        ];

        // Susun data akhir
        $dataPredikat = [];
        foreach ($predikatList as $label) {
            $found = $predikatCounts->firstWhere('predikat', $label);
            $jumlah = $found ? $found->total : 0;
            $persen = $totalMahasiswa > 0 ? round(($jumlah / $totalMahasiswa) * 100, 1) : 0;

            $dataPredikat[] = [
                'label' => $label,
                'jumlah' => $jumlah,
                'persen' => $persen,
            ];
        }

        // Contoh variabel lain
        $postCount = MhsYud::distinct('yudicium_id')->count('yudicium_id');

        // Kirim ke view
       return view('dashboard.index', compact('dataPredikat', 'totalMahasiswa', 'postCount'));
    }
}
