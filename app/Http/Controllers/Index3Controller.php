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
            $prodiId = $request->prodi;

            // 🔹 1. Ambil data dari API
            $url = "https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=7&id={$prodiId}";
            $response = Http::get($url);

            if ($response->successful()) {
                $students = $response->json();

                // 🔹 2. Sync data API ke DB
                foreach ($students as $item) {
                    Mahasiswa::updateOrInsert(
                        ['nim' => $item['STUDENTID']], // primary key
                        [
                            'name' => $item['FULLNAME'],
                            'study_period' => $item['MASA_STUDI'],
                            'pass_sks' => $item['PASS_CREDIT'],
                            'ipk' => $item['GPA'],
                            'predikat' => $this->getPredikat($item['GPA']),
                            'status' => ucfirst(strtolower($item['STATUS'])),
                            'alasan_status' => $item['STATUS'] === 'ELIGIBLE' ? null : 'Tidak memenuhi syarat',
                            'fakultas_id' => $item['FACULTYID'],
                            'prody_id' => $item['STUDYPROGRAMID']
                        ]
                    );
                }
            }

            // 🔹 3. Ambil data dari DB untuk ditampilkan
            $mahasiswa = Mahasiswa::select(
                'nim',
                'name',
                'study_period',
                'pass_sks',
                'ipk',
                'predikat',
                'status',
                'alasan_status'
            )
                ->where('fakultas_id', $request->fakultas)
                ->where('prody_id', $prodiId)
                ->get();

            return response()->json([
                'success' => true,
                'mahasiswa' => $mahasiswa
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    private function getPredikat($gpa)
    {
        if ($gpa >= 3.51) return 'Cumlaude';
        if ($gpa >= 3.00) return 'Sangat Memuaskan';
        if ($gpa >= 2.75) return 'Memuaskan';
        return 'Cukup';
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
            'name' => 'required|string|max:255',
            'study_period' => 'required|integer',
            'pass_sks' => 'required|integer',
            'ipk' => 'required|numeric',
        ]);

        try {
            $mahasiswa = Mahasiswa::findOrFail($id);
            $mahasiswa->update([
                'name' => $request->name,
                'study_period' => $request->study_period,
                'pass_sks' => $request->pass_sks,
                'ipk' => $request->ipk,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil diperbarui'
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
            'status' => 'required|string',
            'alasan' => 'nullable|string|max:255', // alasan bisa optional kalau status = Eligible
        ]);

        try {
            $mahasiswa = Mahasiswa::where('nim', $request->nim)->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa tidak ditemukan'
                ], 404);
            }

            // Normalisasi status (biar konsisten di DB)
            $status = strtolower($request->status) === 'eligible' ? 'Eligible' : 'Tidak Eligible';

            $mahasiswa->status = $status;
            $mahasiswa->alasan_status = $status === 'Eligible' ? null : $request->alasan;
            $mahasiswa->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'mahasiswa' => $mahasiswa
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
