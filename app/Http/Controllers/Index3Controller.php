<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Yudicium;
use App\Models\TempStatus;
use App\Models\Post;

class Index3Controller extends Controller
{
    public $url;

    public function __construct()
    {
        $this->url = 'https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=7';
    }

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

            $url = $this->url . '&id=' . $prodiId;
            $response = Http::get($url);

            $mahasiswa = [];

            if ($response->successful()) {
                $data = $response->json();

                \Log::info('Data mahasiswa', $data);

                $mahasiswa = collect($data ?? [])
                    ->filter(fn($mhs) => $mhs['STUDYPROGRAMID'] == $prodiId)
                    ->map(function ($mhs) {
                        $tempStatus = TempStatus::select('status','alasan')
                            ->where('nim', $mhs['STUDENTID']);

                        $statusFromTemp = $tempStatus->value('status');
                        $alasanFromTemp = $tempStatus->value('alasan');

                        $statusFromApi = ucfirst(strtolower($mhs['STATUS']));

                        return [
                            'nim' => $mhs['STUDENTID'] ?? '-',
                            'name' => $mhs['FULLNAME'] ?? '-',
                            'study_period' => $mhs['MASA_STUDI'] ?? '-',
                            'pass_sks' => $mhs['PASS_CREDIT'] ?? '-',
                            'ipk' => $mhs['GPA'] ?? '-',
                            'predikat' => $this->getPredikat($mhs['GPA']),
                            'status' => !empty($statusFromTemp) ? $statusFromTemp : $statusFromApi,
                            'alasan_status' => !empty($alasanFromTemp) ? $alasanFromTemp : '-',
                        ];
                    })
                    ->toArray();

            \Log::info('Data mahasiswa', $mahasiswa);

            }
            // $mahasiswa = Mahasiswa::select('nim', 'name', 'study_period', 'pass_sks', 'ipk', 'predikat', 'status', 'alasan_status')
            //     ->where('fakultas_id', $request->fakultas)
            //     ->where('prody_id', $request->prodi)
            //     ->get();

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

    public function getAllMhs()
    {
        $url = $this->url;

        $response = Http::get($url);

        $mahasiswa = [];
        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 400);
        }

        $data = $response->json();
        $mahasiswa = collect($data ?? [])
            ->map(function ($mhs) {
                return [
                    'nim' => $mhs['STUDENTID'] ?? '-',
                    'name' => $mhs['FULLNAME'] ?? '-',
                    'study_period' => $mhs['MASA_STUDI'] ?? '-',
                    'pass_sks' => $mhs['PASS_CREDIT'] ?? '-',
                    'ipk' => $mhs['GPA'] ?? '-',
                    'predikat' => $this->getPredikat($mhs['GPA']),
                    'status' => ucfirst(strtolower($mhs['STATUS'])),
                    'alasan_status' => $mhs['STATUS'] === 'ELIGIBLE' ? null : 'Tidak memenuhi syarat',
                ];
            });

        return response()->json([
            'success' => true,
            'mahasiswa' => $mahasiswa
        ], 200);
    }

    private function getPredikat($gpa)
    {
        if ($gpa >= 3.51)
            return 'Cumlaude';
        if ($gpa >= 3.00)
            return 'Sangat Memuaskan';
        if ($gpa >= 2.75)
            return 'Memuaskan';
        return 'Cukup';
    }
}