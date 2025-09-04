<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Mahasiswa;

class Index3Controller extends Controller
{
    public function index(Request $request)
    {
        // cek route name
        $routeName = $request->route()->getName();

        $token = session('token');

        // ambil data dari API
        $response = Http::withToken($token)
            ->get('https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69');
        $faculties = $response->successful() ? $response->json() : [];

        //menampilkan data mahasiswa
        $mahasiswa = Mahasiswa::all();
        foreach ($mahasiswa as $mhs) {
            $mhs->save(); // ini akan trigger booted() → isi predikat & status
        }
        // tentukan view sesuai route
        if ($routeName === 'index3') {
            return view('dashboard.index3', compact('faculties', 'mahasiswa'));
        } elseif ($routeName === 'index4') {
            return view('dashboard.index4', compact('faculties', 'mahasiswa'));
        }

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
