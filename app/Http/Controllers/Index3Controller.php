<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Mahasiswa;
use App\Models\Yudicium;
use App\Models\Post;

class Index3Controller extends Controller
{
    public function index(Request $request)
    {
        // cek route name
        $routeName = $request->route()->getName();

        $token = session('token');

        // ambil data fakultas dari API
        $response = Http::withToken($token)
            ->get('https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69');
        $faculties = $response->successful() ? $response->json() : [];

        // ambil data mahasiswa
        $mahasiswa = Mahasiswa::all();
        foreach ($mahasiswa as $mhs) {
            $mhs->save(); // trigger booted()
        }

        // Ambil kode jika ada hasil generate
        $kode = session('kode');

        $postCount = Post::count();


        if ($routeName === 'index3' || $routeName === 'index4') {
            return view("dashboard.$routeName", compact('faculties', 'mahasiswa', 'kode', 'postCount'));
        }

    }

    public function generate(Request $request)
    {
        $request->validate([
            'fakultas' => 'required',
            'semester' => 'required',
            'prodi' => 'required',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Ambil auto_increment terakhir
        $last = Yudicium::max('id') ?? 0;
        $nextNo = $last + 1;

        // Ambil jumlah mahasiswa dari input
        $jumlah = $request->jumlah;

        // Map fakultas ke inisial
        $map = [
            'Informatika' => 'IF',
            'Sistem Informasi' => 'SI',
            'Teknik Elektro' => 'TE',
            'Ilmu Terapan' => 'IT',
            'Ekonomi dan Bisnis' => 'EB',
            'Komunikasi dan Bisnis' => 'KB',
        ];
        $fakultasInitial = $map[$request->fakultas] ?? strtoupper(substr($request->fakultas, 0, 2));

        // Ambil tahun awal dari semester (contoh: "Ganjil 2024/2025" → 2024)
        preg_match('/\d{4}/', $request->semester, $match);
        $tahun = $match[0] ?? date('Y');

        // Susun nomor yudisium
        $kode = $nextNo . '/AKD' . $jumlah . '/' . $fakultasInitial . '-DEK/' . $tahun;

        // Redirect balik ke index3 dengan membawa kode
        return redirect()->route('index3')->with('kode', $kode);
    }


    /**
     * Simpan data mahasiswa baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:20|unique:mhs_yudiciums,nim',
            'name' => 'required|string|max:100',
            'study_period' => 'required|integer|min:1',
            'pass_sks' => 'required|integer|min:0',
            'ipk' => 'required|numeric|between:0,4.00',
        ]);

        // Predikat & Status otomatis dihitung oleh model (mutator setIpkAttribute)
        Mahasiswa::create([
            'nim' => $request->nim,
            'name' => $request->name,
            'study_period' => $request->study_period,
            'pass_sks' => $request->pass_sks,
            'ipk' => $request->ipk,
        ]);

        return redirect()->back()->with('success', 'Data mahasiswa berhasil ditambahkan');
    }

    /**
     * Update data mahasiswa
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'study_period' => 'required|integer|min:1',
            'pass_sks' => 'required|integer|min:0',
            'ipk' => 'required|numeric|between:0,4.00',
        ]);

        $mahasiswa = Mahasiswa::findOrFail($id);

        $mahasiswa->update([
            'name' => $request->name,
            'study_period' => $request->study_period,
            'pass_sks' => $request->pass_sks,
            'ipk' => $request->ipk, // otomatis trigger mutator -> predikat + status update
        ]);

        return redirect()->back()->with('success', 'Data mahasiswa berhasil diperbarui');
    }

    /**
     * Hapus data mahasiswa
     */
    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->delete();

        return redirect()->back()->with('success', 'Data mahasiswa berhasil dihapus');
    }

}
