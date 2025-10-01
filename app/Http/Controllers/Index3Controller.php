<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
        // $response = Http::withToken($token)
        //     ->get('https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69');
        // $faculties = $response->successful() ? $response->json() : [];

        // ambil data mahasiswa
        $mahasiswa = Mahasiswa::all();
        foreach ($mahasiswa as $mhs) {
            $mhs->save(); // trigger booted()
        }

        // Ambil kode jika ada hasil generate
        $kode = session('kode');

        $postCount = Post::count();


        if ($routeName === 'index3' || $routeName === 'index4') {
            return view("dashboard.$routeName", compact('mahasiswa', 'kode', 'postCount'));
        }

    }

    public function filterMhs(Request $request)
    {
        try {
            $mahasiswa = Mahasiswa::select('nim', 'name', 'study_period', 'pass_sks', 'ipk', 'predikat', 'status','alasan_status')
                ->where('fakultas_id', $request->fakultas)
                ->where('prody_id', $request->prodi)
                ->get();

            return response()->json([
                'success' => true,
                'mahasiswa' => $mahasiswa
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
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

    public function ubahStatus(Request $request)
    {
    $request->validate([
        'nim' => 'required|string',
        'status' => 'required|in:Eligible,Tidak Eligible',
        'alasan' => 'required|string|max:255',
    ]);

        try {
            $mahasiswa = Mahasiswa::where('nim', $request->nim)->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa tidak ditemukan'
                ], 404);
            }

            $mahasiswa->status = $request->status;
            $mahasiswa->alasan_status = $request->alasan;
            $mahasiswa->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
