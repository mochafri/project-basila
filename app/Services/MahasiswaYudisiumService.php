<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\MhsYud;
use App\Models\Mahasiswa;
use Exception;

class MahasiswaYudisiumService
{
    /**
     * Filter mahasiswa untuk yudisium (stt=7 API atau database fallback)
     * 
     * @param string $prodi
     * @param string $periode
     * @param string|null $fakultas
     * @return array
     */
    public function filterMahasiswa($prodi, $periode, $fakultas = null)
    {
        Log::info('Filter Mahasiswa Yudisium', [
            'prodi' => $prodi,
            'periode' => $periode,
            'fakultas' => $fakultas
        ]);

        // A. Coba ambil dari API (stt=7)
        try {
            // Gunakan URL_ACADEMIC yang sudah ada (stt=7)
            $apiUrl = env('URL_ACADEMIC');
            $timeout = env('API_YUDISIUM_TIMEOUT', 10);
            
            $response = Http::timeout($timeout)->get($apiUrl, [
                'id' => $prodi,
                'periode' => $periode
            ]);

            if ($response->successful()) {
                $apiData = $response->json();
                
                // Jika API berhasil dan ada data
                if (!empty($apiData) && is_array($apiData)) {
                    Log::info('Data berhasil diambil dari API', ['count' => count($apiData)]);
                    
                    // Format data API dengan source marker dan field lengkap
                    $formattedData = collect($apiData)->map(function ($item) {
                        return [
                            'nim' => $item['nim'] ?? $item['STUDENTID'] ?? null,
                            'name' => $item['nama'] ?? $item['FULLNAME'] ?? null,
                            'nama' => $item['nama'] ?? $item['FULLNAME'] ?? null,
                            'study_period' => $item['masa_studi'] ?? $item['MASA_STUDI'] ?? null,
                            'masa_studi' => $item['masa_studi'] ?? $item['MASA_STUDI'] ?? null,
                            'sks_lulus' => $item['sks_lulus'] ?? $item['PASS_CREDIT'] ?? null,
                            'ipk' => $item['ipk'] ?? $item['GPA'] ?? null,
                            'predikat' => $item['predikat'] ?? $item['PREDIKAT'] ?? null,
                            'status' => $item['status'] ?? $item['STATUS'] ?? null,
                            'prodi' => $item['prodi'] ?? $item['STUDYPROGRAMNAME'] ?? null,
                            'fakultas' => $item['fakultas'] ?? $item['FACULTYNAME'] ?? null,
                            // Field detail untuk modal
                            'SMT_CURRENT' => $item['SMT_CURRENT'] ?? $item['semester_lulus'] ?? null,
                            'STATUS' => $item['STATUS'] ?? $item['status_mk'] ?? null,
                            'BAHASA_ASING' => $item['BAHASA_ASING'] ?? $item['bahasa_asing'] ?? null,
                            'PUBLIKASI' => $item['PUBLIKASI'] ?? $item['publikasi'] ?? null,
                            'TAK' => $item['TAK'] ?? $item['tak'] ?? null,
                            'ADMINISTRATIF' => $item['ADMINISTRATIF'] ?? $item['administratif'] ?? null,
                            'BPP' => $item['BPP'] ?? $item['bpp'] ?? null,
                            'OPENLIB' => $item['OPENLIB'] ?? $item['openlib'] ?? null,
                            'SANKSI' => $item['SANKSI'] ?? $item['sanksi'] ?? null,
                            'source' => 'api'
                        ];
                    })->toArray();

                    return [
                        'success' => true,
                        'source' => 'api',
                        'data' => $formattedData,
                        'message' => 'Data berhasil diambil dari API'
                    ];
                }
            }

            Log::warning('API response kosong atau tidak berhasil', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (Exception $e) {
            Log::error('API Error saat filter mahasiswa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // B. Fallback ke Database
        Log::info('Fallback ke database');
        
        $query = Mahasiswa::query();

        if ($fakultas) {
            $query->where('FACULTYID', $fakultas);
        }

        if ($prodi) {
            $query->where('STUDYPROGRAMID', $prodi);
        }

        $databaseData = $query->select(
            'STUDENTID as nim',
            'FULLNAME as name',
            'FULLNAME as nama',
            'MASA_STUDI as study_period',
            'MASA_STUDI as masa_studi',
            'PASS_CREDIT as sks_lulus',
            'GPA as ipk',
            'PREDIKAT as predikat',
            'STATUS as status',
            'STUDYPROGRAMID',
            'FACULTYID',
            // Field detail untuk modal (set default values karena tidak ada di database)
            DB::raw("CAST(SUBSTRING_INDEX(MASA_STUDI, ' ', 1) AS UNSIGNED) as SMT_CURRENT"),
            DB::raw("IF(STATUS = 'Eligible', 'LULUS', 'TIDAK LULUS') as STATUS_MK"),
            DB::raw("'YA' as BAHASA_ASING"),
            DB::raw("'YA' as PUBLIKASI"),
            DB::raw("'YA' as TAK"),
            DB::raw("'YA' as ADMINISTRATIF"),
            DB::raw("'YA' as BPP"),
            DB::raw("'YA' as OPENLIB"),
            DB::raw("'TIDAK' as SANKSI"),
            DB::raw("'database' as source")
        )->get()->map(function($item) {
            // Tambahkan nama prodi dan fakultas (bisa dari mapping atau API lain)
            $item->prodi = "Program Studi ID: " . $item->STUDYPROGRAMID;
            $item->fakultas = "Fakultas ID: " . $item->FACULTYID;
            unset($item->STUDYPROGRAMID);
            unset($item->FACULTYID);
            return $item;
        })->toArray();

        Log::info('Data berhasil diambil dari database', ['count' => count($databaseData)]);

        return [
            'success' => true,
            'source' => 'database',
            'data' => $databaseData,
            'message' => 'Data diambil dari database (fallback)'
        ];
    }

    /**
     * Simpan draft mahasiswa yudisium
     * 
     * @param array $mahasiswaList
     * @param int $yudisiumId
     * @param string $periode
     * @return array
     */
    public function simpanDraft($mahasiswaList, $yudisiumId, $periode)
    {
        Log::info('Simpan Draft Yudisium', [
            'yudisium_id' => $yudisiumId,
            'periode' => $periode,
            'mahasiswa_count' => count($mahasiswaList)
        ]);

        DB::beginTransaction();
        
        try {
            $apiSuccess = [];
            $dbSuccess = [];
            $errors = [];

            foreach ($mahasiswaList as $mahasiswa) {
                $source = $mahasiswa['source'] ?? 'database';
                $nim = $mahasiswa['nim'];

                // A. Jika source = 'api' → Hit API stt=8
                if ($source === 'api') {
                    try {
                        // Gunakan URL_SET_ACADEMIC yang sudah ada
                        $apiUrl = env('URL_SET_ACADEMIC');
                        $timeout = env('API_YUDISIUM_TIMEOUT', 10);
                        
                        // Replace placeholder dengan nilai sebenarnya
                        $apiUrl = str_replace('NIM', $nim, $apiUrl);
                        $apiUrl = str_replace('TANGGAL', $periode, $apiUrl);
                        $apiUrl = str_replace('SELECTED', 'Y', $apiUrl);
                        
                        $response = Http::timeout($timeout)->get($apiUrl);

                        if ($response->successful()) {
                            $apiSuccess[] = $nim;
                            Log::info("API stt=8 berhasil untuk NIM: {$nim}");
                        } else {
                            $errors[] = "API gagal untuk NIM {$nim}: " . $response->body();
                            Log::error("API stt=8 gagal untuk NIM: {$nim}", [
                                'response' => $response->body()
                            ]);
                        }
                    } catch (Exception $e) {
                        $errors[] = "API error untuk NIM {$nim}: " . $e->getMessage();
                        Log::error("API stt=8 error untuk NIM: {$nim}", [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                // B. Jika source = 'database' → Insert ke mhs_yudiciums
                else {
                    try {
                        // Cek duplicate
                        $exists = MhsYud::where('nim', $nim)
                            ->where('yudicium_id', $yudisiumId)
                            ->exists();

                        if (!$exists) {
                            MhsYud::create([
                                'nim' => $nim,
                                'yudicium_id' => $yudisiumId,
                                'name' => $mahasiswa['nama'] ?? null,
                                'study_period' => $mahasiswa['masa_studi'] ?? null,
                                'pass_sks' => $mahasiswa['sks_lulus'] ?? null,
                                'ipk' => $mahasiswa['ipk'] ?? null,
                                'predikat' => $mahasiswa['predikat'] ?? null,
                                'status' => 'draft'
                            ]);

                            $dbSuccess[] = $nim;
                            Log::info("Database insert berhasil untuk NIM: {$nim}");
                        } else {
                            Log::info("NIM {$nim} sudah ada di database, skip insert");
                        }
                    } catch (Exception $e) {
                        $errors[] = "Database error untuk NIM {$nim}: " . $e->getMessage();
                        Log::error("Database insert error untuk NIM: {$nim}", [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            DB::commit();

            return [
                'success' => true,
                'api_success' => $apiSuccess,
                'db_success' => $dbSuccess,
                'errors' => $errors,
                'message' => 'Draft berhasil disimpan'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error saat simpan draft', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menyimpan draft: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ambil data draft mahasiswa yudisium
     * 
     * @param int $yudisiumId
     * @param string $periode
     * @param string|null $source
     * @return array
     */
    public function getDraft($yudisiumId, $periode, $source = null)
    {
        Log::info('Get Draft Yudisium', [
            'yudisium_id' => $yudisiumId,
            'periode' => $periode,
            'source' => $source
        ]);

        // A. Jika mode API → gunakan stt=9 atau stt=10
        if ($source === 'api') {
            try {
                // Gunakan URL_ALL_ACADEMIC (stt=9) untuk semua data
                $apiUrl = env('URL_ALL_ACADEMIC');
                $timeout = env('API_YUDISIUM_TIMEOUT', 10);
                
                // Replace placeholder dengan nilai sebenarnya
                $apiUrl = str_replace('IDPRODI', $yudisiumId, $apiUrl);
                $apiUrl = str_replace('TANGGAL', $periode, $apiUrl);
                
                $response = Http::timeout($timeout)->get($apiUrl);

                if ($response->successful()) {
                    $apiData = $response->json();
                    
                    if (!empty($apiData) && is_array($apiData)) {
                        $formattedData = collect($apiData)->map(function ($item) {
                            return [
                                'nim' => $item['nim'] ?? null,
                                'nama' => $item['nama'] ?? null,
                                'masa_studi' => $item['masa_studi'] ?? null,
                                'sks_lulus' => $item['sks_lulus'] ?? null,
                                'ipk' => $item['ipk'] ?? null,
                                'predikat' => $item['predikat'] ?? null,
                                'status' => $item['status'] ?? null,
                                'selected' => $item['selected'] ?? null,
                                'source' => 'api'
                            ];
                        })->toArray();

                        return [
                            'success' => true,
                            'source' => 'api',
                            'data' => $formattedData
                        ];
                    }
                }
            } catch (Exception $e) {
                Log::error('API Error saat get draft', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // B. Mode database → ambil dari mhs_yudiciums
        $databaseData = MhsYud::where('yudicium_id', $yudisiumId)
            ->where('status', 'draft')
            ->select(
                'nim',
                'name as nama',
                'study_period as masa_studi',
                'pass_sks as sks_lulus',
                'ipk',
                'predikat',
                'status',
                DB::raw("'database' as source")
            )
            ->get()
            ->toArray();

        return [
            'success' => true,
            'source' => 'database',
            'data' => $databaseData
        ];
    }

    /**
     * Tetapkan yudisium (finalisasi)
     * 
     * @param int $yudisiumId
     * @param string $periode
     * @param string $source
     * @return array
     */
    public function tetapkanYudisium($yudisiumId, $periode, $source)
    {
        Log::info('Tetapkan Yudisium', [
            'yudisium_id' => $yudisiumId,
            'periode' => $periode,
            'source' => $source
        ]);

        DB::beginTransaction();

        try {
            // A. Jika source = 'api'
            if ($source === 'api') {
                // Ambil data dari API stt=10 (selected = Y)
                // Gunakan URL_PICK_ACADEMIC yang sudah ada
                $apiUrl = env('URL_PICK_ACADEMIC');
                $timeout = env('API_YUDISIUM_TIMEOUT', 10);
                
                // Replace placeholder dengan nilai sebenarnya
                $apiUrl = str_replace('IDPRODI', $yudisiumId, $apiUrl);
                $apiUrl = str_replace('TANGGAL', $periode, $apiUrl);
                
                $response = Http::timeout($timeout)->get($apiUrl);

                if (!$response->successful()) {
                    throw new Exception('Gagal mengambil data dari API stt=10');
                }

                $apiData = $response->json();

                if (empty($apiData)) {
                    throw new Exception('Tidak ada data selected dari API');
                }

                $inserted = 0;
                $skipped = 0;

                // Loop data dan mapping ke mhs_yudiciums
                foreach ($apiData as $item) {
                    $nim = $item['nim'] ?? $item['STUDENTID'] ?? null;

                    if (!$nim) {
                        continue;
                    }

                    // Cek duplicate
                    $exists = MhsYud::where('nim', $nim)
                        ->where('yudicium_id', $yudisiumId)
                        ->where('status', 'final')
                        ->exists();

                    if (!$exists) {
                        MhsYud::create([
                            'nim' => $nim,
                            'yudicium_id' => $yudisiumId,
                            'name' => $item['nama'] ?? $item['FULLNAME'] ?? null,
                            'study_period' => $item['masa_studi'] ?? $item['MASA_STUDI'] ?? null,
                            'pass_sks' => $item['sks_lulus'] ?? $item['PASS_CREDIT'] ?? null,
                            'ipk' => $item['ipk'] ?? $item['GPA'] ?? null,
                            'predikat' => $item['predikat'] ?? $item['PREDIKAT'] ?? null,
                            'status' => 'final'
                        ]);

                        $inserted++;
                        Log::info("Data final dari API berhasil disimpan untuk NIM: {$nim}");
                    } else {
                        $skipped++;
                        Log::info("NIM {$nim} sudah ada dengan status final, skip");
                    }
                }

                DB::commit();

                return [
                    'success' => true,
                    'source' => 'api',
                    'inserted' => $inserted,
                    'skipped' => $skipped,
                    'message' => "Yudisium berhasil ditetapkan dari API. {$inserted} data disimpan, {$skipped} data di-skip."
                ];
            }
            // B. Jika source = 'database'
            else {
                // Update status draft menjadi final
                $updated = MhsYud::where('yudicium_id', $yudisiumId)
                    ->where('status', 'draft')
                    ->update(['status' => 'final']);

                DB::commit();

                Log::info("Status draft berhasil diupdate ke final", ['count' => $updated]);

                return [
                    'success' => true,
                    'source' => 'database',
                    'updated' => $updated,
                    'message' => "Yudisium berhasil ditetapkan. {$updated} data diupdate ke status final."
                ];
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error saat tetapkan yudisium', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menetapkan yudisium: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Hapus draft mahasiswa dari API (uncheck)
     * 
     * @param string $nim
     * @param string $periode
     * @return array
     */
    public function hapusDraftApi($nim, $periode)
    {
        try {
            // Gunakan URL_RESET_ACADEMIC yang sudah ada (stt=11)
            $apiUrl = env('URL_RESET_ACADEMIC');
            $timeout = env('API_YUDISIUM_TIMEOUT', 10);
            
            // Replace placeholder dengan nilai sebenarnya
            $apiUrl = str_replace('NIM', $nim, $apiUrl);
            $apiUrl = str_replace('TANGGAL', $periode, $apiUrl);
            
            $response = Http::timeout($timeout)->get($apiUrl);

            if ($response->successful()) {
                Log::info("Draft API berhasil dihapus untuk NIM: {$nim}");
                return [
                    'success' => true,
                    'message' => 'Draft berhasil dihapus dari API'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menghapus draft dari API'
            ];
        } catch (Exception $e) {
            Log::error('Error hapus draft API', [
                'nim' => $nim,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
