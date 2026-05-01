<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class MahasiswaYudisiumService
{
    /**
     * Get data mahasiswa yudisium dari API, dengan fallback ke Database.
     *
     * @param int|string $fakultas
     * @param int|string $prodi
     * @param string $semester
     * @return array
     */
    public function getMahasiswaYudisium($fakultas, $prodi, $semester)
    {
        $cacheKey = "yudisium_api_{$fakultas}_{$prodi}_{$semester}";

        try {
            // Ambil dari API dengan cache 5 menit (300 detik)
            $apiData = Cache::remember($cacheKey, 300, function () use ($fakultas, $prodi, $semester) {
                // TODO: Sesuaikan URL API Eksternal
                $response = Http::timeout(5)->get(env('API_YUDISIUM_URL', 'https://api.example.com/yudisium'), [
                    'fakultas' => $fakultas,
                    'prodi' => $prodi,
                    'semester' => $semester
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data)) {
                        return $data;
                    }
                }
                return null;
            });

            if (!empty($apiData)) {
                return [
                    'source' => 'api',
                    'data' => $apiData
                ];
            }
        } catch (Exception $e) {
            // Logging jika API gagal/timeout
            Log::error('API Yudisium Error: ' . $e->getMessage());
        }

        // Fallback: Query Database
        // Ambil dari mhs_yudiciums join ke mahasiswa dan yudiciums (untuk filter periode/semester)
        $databaseData = DB::table('mhs_yudiciums')
            ->join('mahasiswa', 'mhs_yudiciums.nim', '=', 'mahasiswa.STUDENTID')
            ->join('yudiciums', 'mhs_yudiciums.yudicium_id', '=', 'yudiciums.id')
            ->where('mhs_yudiciums.fakultas_id', $fakultas)
            ->where('mhs_yudiciums.prody_id', $prodi)
            ->where('yudiciums.periode', $semester)
            ->select(
                'mhs_yudiciums.nim',
                'mahasiswa.FULLNAME as nama',
                'mahasiswa.MASA_STUDI as masa_studi',
                'mahasiswa.PASS_CREDIT as sks_lulus',
                'mahasiswa.GPA as ipk',
                'mahasiswa.PREDIKAT as predikat',
                'mahasiswa.STATUS as status'
            )
            ->get();

        return [
            'source' => 'database',
            'data' => $databaseData
        ];
    }
}
