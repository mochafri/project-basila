<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MahasiswaYudisiumService;

class MahasiswaYudisiumController extends Controller
{
    protected $yudisiumService;

    public function __construct(MahasiswaYudisiumService $yudisiumService)
    {
        $this->yudisiumService = $yudisiumService;
    }

    /**
     * Menampilkan halaman dan data mahasiswa yudisium.
     */
    public function index(Request $request)
    {
        $fakultas = $request->input('fakultas');
        $prodi = $request->input('prodi');
        $semester = $request->input('semester');

        $result = null;

        // Tampilkan data jika parameter filter telah diisi
        if ($fakultas && $prodi && $semester) {
            $result = $this->yudisiumService->getMahasiswaYudisium($fakultas, $prodi, $semester);
        }

        return view('yudisium.mahasiswa', compact('result', 'fakultas', 'prodi', 'semester'));
    }
}
