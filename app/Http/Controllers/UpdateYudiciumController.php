<?php

namespace App\Http\Controllers;

use App\Models\Yudicium;
use App\Models\MhsYud;
use App\Models\TempStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\YudiciumApprovalController;
use App\Services\YudiciumService;

class UpdateYudiciumController extends Controller
{
    public function index(Request $request)
    {
        $routeName = $request->route()->getName();
        $yudiciumId = $request->id;

        if ($routeName === 'index5') {
            $yudicium = Yudicium::find($yudiciumId);
            if (!$yudicium) {
                abort(404, 'Yudisium not found');
            }

            $tanggal = $yudicium->created_at ? $yudicium->created_at->format('Y-m-d') : date('Y-m-d');
            $prodiId = $yudicium->prodi_id;
            $fakultasId = $yudicium->fakultas_id;

            // Cek apakah ini penetapan ulang (Rejected) atau penetapan pertama kali
            $isRejected = $yudicium->approval_status === 'Rejected';
            
            // A. Jika Rejected, langsung ambil dari mhs_yudiciums (skip API)
            if ($isRejected) {
                Log::info('Penetapan ulang (Rejected), ambil dari mhs_yudiciums', ['yudicium_id' => $yudiciumId]);
                
                $mahasiswaFromMhsYud = MhsYud::where('yudicium_id', $yudiciumId)->get();
                
                $datas = $mahasiswaFromMhsYud->map(function($mhs) {
                    return (object) [
                        'nim' => $mhs->nim,
                        'name' => $mhs->name,
                        'fakultas_id' => $mhs->fakultas_id,
                        'prodi_id' => $mhs->prody_id,
                        'fakultas_name' => '-',
                        'prodi_name' => '-',
                        'study_period' => $mhs->study_period,
                        'pass_sks' => $mhs->pass_sks,
                        'ipk' => $mhs->ipk,
                        'status_otomatis' => $mhs->status_otomatis ?? 'Eligible',
                        'status' => $mhs->status,
                        'predikat' => $mhs->predikat,
                        'alasan_status' => $mhs->alasan_status,
                        'selected' => true, // Auto-select semua
                        'id_smt_masuk' => $mhs->id_smt_masuk,
                        'bahasa_asing' => null,
                        'publikasi' => null,
                        'tak' => null,
                        'administratif' => null,
                        'bpp' => null,
                        'openlib' => null,
                        'sanksi' => null,
                        'smt_current' => null,
                        'source' => 'mhs_yudiciums'
                    ];
                });
                
                $source = 'mhs_yudiciums';
                Log::info('Data berhasil diambil dari mhs_yudiciums', ['count' => $datas->count()]);
                
            } else {
                // B. Penetapan pertama kali: Coba ambil dari API (stt=9 - ALL_ACADEMIC)
                $datas = [];
                $source = 'database'; // default fallback
                
                try {
                    $urlAllAcademic = trim(env('URL_ALL_ACADEMIC'), " '\"");
                    $apiUrl = str_replace(['IDPRODI', 'TANGGAL'], [$prodiId, $tanggal], $urlAllAcademic);

                    Log::info('Fetching from API', ['url' => $apiUrl]);
                    $response = Http::timeout(10)->get($apiUrl);
                    $listMahasiswa = $response->json();
                    
                    if (!empty($listMahasiswa) && is_array($listMahasiswa)) {
                        $source = 'api';
                        Log::info('Data berhasil diambil dari API', ['count' => count($listMahasiswa)]);
                        
                        // Ambil daftar NIM yang sudah ada di mhs_yudiciums
                        $existingNims = MhsYud::pluck('nim')->toArray();
                        
                        foreach ($listMahasiswa as $mhs) {
                            $nim = $mhs['STUDENTID'];
                            
                            // Skip mahasiswa yang sudah ada di mhs_yudiciums
                            if (in_array($nim, $existingNims)) {
                                Log::info('Skipping mahasiswa yang sudah ada di mhs_yudiciums', ['nim' => $nim]);
                                continue;
                            }
                            
                            $tempStatus = TempStatus::select('status', 'alasan')
                                ->where('nim', $nim);

                            $statusFromTemp = $tempStatus->value('status');
                            $statusFromApi = ucfirst(strtolower($mhs['STATUS']));
                            $finalStatus = !empty($statusFromTemp) ? $statusFromTemp : $statusFromApi;

                            $datas[] = (object) [
                                'nim' => $nim,
                                'name' => $mhs['FULLNAME'],
                                'fakultas_id' => $mhs['FACULTYID'],
                                'prodi_id' => $mhs['STUDYPROGRAMID'],
                                'fakultas_name' => $mhs['FACULTYNAME'] ?? '-',
                                'prodi_name' => $mhs['STUDYPROGRAMNAME'] ?? '-',
                                'study_period' => $mhs['MASA_STUDI'],
                                'pass_sks' => $mhs['PASS_CREDIT'],
                                'ipk' => $mhs['GPA'],
                                'status_otomatis' => ucfirst(strtolower($mhs['STATUS'])),
                                'status' => $finalStatus,
                                'predikat' => (new MhsYud)->getPredikat($mhs['GPA']),
                                'alasan_status' => $tempStatus->value('alasan') ?? null,
                                // Auto-select jika: selected = 'Y' DAN periode tidak null
                                'selected' => (($mhs['SELECTED'] ?? 'N') === 'Y') && !empty($tanggal),
                                'periode' => $tanggal,
                                'id_smt_masuk' => $mhs['ID_SMT_MASUK'] ?? null,
                                'bahasa_asing' => $mhs['BAHASA_ASING'] ?? null,
                                'publikasi' => $mhs['PUBLIKASI'] ?? null,
                                'tak' => $mhs['TAK'] ?? null,
                                'administratif' => $mhs['ADMINISTRATIF'] ?? null,
                                'bpp' => $mhs['BPP'] ?? null,
                                'openlib' => $mhs['OPENLIB'] ?? null,
                                'sanksi' => $mhs['SANKSI'] ?? null,
                                'smt_current' => $mhs['SMT_CURRENT'] ?? null,
                                'source' => 'api'
                            ];
                        }
                    } else {
                        Log::warning('API response kosong, fallback ke database');
                    }
                } catch (\Exception $e) {
                    Log::error('API Error, fallback ke database', [
                        'error' => $e->getMessage()
                    ]);
                }

                // C. Fallback ke mhs_yudiciums jika API gagal atau kosong
                if (empty($datas)) {
                    Log::info('Menggunakan data dari mhs_yudiciums (fallback)');
                    
                    $mahasiswaFromMhsYud = MhsYud::where('yudicium_id', $yudiciumId)->get();
                    
                    if ($mahasiswaFromMhsYud->isNotEmpty()) {
                        $datas = $mahasiswaFromMhsYud->map(function($mhs) {
                            return (object) [
                                'nim' => $mhs->nim,
                                'name' => $mhs->name,
                                'fakultas_id' => $mhs->fakultas_id,
                                'prodi_id' => $mhs->prody_id,
                                'fakultas_name' => '-',
                                'prodi_name' => '-',
                                'study_period' => $mhs->study_period,
                                'pass_sks' => $mhs->pass_sks,
                                'ipk' => $mhs->ipk,
                                'status_otomatis' => $mhs->status_otomatis ?? 'Eligible',
                                'status' => $mhs->status,
                                'predikat' => $mhs->predikat,
                                'alasan_status' => $mhs->alasan_status,
                                'selected' => true,
                                'id_smt_masuk' => $mhs->id_smt_masuk,
                                'bahasa_asing' => null,
                                'publikasi' => null,
                                'tak' => null,
                                'administratif' => null,
                                'bpp' => null,
                                'openlib' => null,
                                'sanksi' => null,
                                'smt_current' => null,
                                'source' => 'mhs_yudiciums'
                            ];
                        })->toArray();
                        
                        $source = 'mhs_yudiciums';
                    }
                    
                    Log::info('Data berhasil diambil dari mhs_yudiciums', ['count' => count($datas)]);
                }
                
                $datas = collect($datas);
            }
            
            // Log source yang digunakan
            Log::info('Data source', ['source' => $source, 'count' => $datas->count()]);
            
        } else {
            $mhsYud = (new YudiciumApprovalController(new YudiciumService()))->getMahasiswa($yudiciumId);
            $datas = collect($mhsYud->getData()->mahasiswa);
        }

        if (in_array($routeName, ['index5', 'index7'])) {
            return view("dashboard.$routeName", [
                "datas"=> $datas,
                "yudicium_id" => $yudiciumId,
                "source" => $source ?? 'database'
            ]);
        }
    }

    public function tetapkanYudisium(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required|integer',
            'mahasiswa_nims' => 'array',
        ]);
        
        if (!isset($validate['mahasiswa_nims'])) {
            $validate['mahasiswa_nims'] = [];
        }

        // Log request data
        Log::info('=== TETAPKAN YUDISIUM START ===', [
            'request_data' => $request->all(),
            'validated' => $validate
        ]);

        DB::beginTransaction();

        try {
            $yudicium = Yudicium::findOrFail($validate['id']);
            $prodiId = $yudicium->prodi_id;
            $fakultasId = $yudicium->fakultas_id;
            $tanggal = $yudicium->created_at ? $yudicium->created_at->format('Y-m-d') : date('Y-m-d');
            
            Log::info('Yudicium info', [
                'yudicium_id' => $validate['id'],
                'prodi_id' => $prodiId,
                'fakultas_id' => $fakultasId,
                'tanggal' => $tanggal,
                'mahasiswa_nims_count' => count($validate['mahasiswa_nims'])
            ]);
            
            $listMahasiswa = [];
            $source = 'database'; // default
            
            // A. Coba ambil dari API (stt=10 - PICK_ACADEMIC untuk selected=Y)
            try {
                $urlPickAcademic = trim(env('URL_PICK_ACADEMIC'), " '\"");
                $apiUrl = str_replace(['IDPRODI', 'TANGGAL'], [$prodiId, $tanggal], $urlPickAcademic);

                Log::info('Tetapkan Yudisium - Fetching from API stt=10', ['url' => $apiUrl]);
                $response = Http::timeout(10)->get($apiUrl);
                
                if ($response->successful()) {
                    $apiData = $response->json();
                    
                    if (!empty($apiData) && is_array($apiData)) {
                        $listMahasiswa = $apiData;
                        $source = 'api';
                        Log::info('Data berhasil diambil dari API stt=10', ['count' => count($listMahasiswa)]);
                    } else {
                        Log::warning('API stt=10 response kosong, fallback ke database');
                    }
                } else {
                    Log::warning('API stt=10 gagal, fallback ke database', [
                        'status' => $response->status()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('API Error stt=10, fallback ke database', [
                    'error' => $e->getMessage()
                ]);
            }

            // B. Fallback ke Database jika API gagal atau kosong
            if (empty($listMahasiswa)) {
                Log::info('Menggunakan data dari database untuk tetapkan yudisium');
                
                // Ambil dari tabel mahasiswa yang dipilih (dari checkbox)
                if (!empty($validate['mahasiswa_nims'])) {
                    $mahasiswaFromDb = DB::table('mahasiswa')
                        ->whereIn('STUDENTID', $validate['mahasiswa_nims'])
                        ->where('STUDYPROGRAMID', $prodiId)
                        ->select(
                            'STUDENTID',
                            'FULLNAME',
                            'MASA_STUDI',
                            'PASS_CREDIT',
                            'GPA'
                        )
                        ->get();

                    foreach ($mahasiswaFromDb as $mhs) {
                        // Extract numeric value from "10 Semester" format
                        $studyPeriod = 0;
                        if (preg_match('/(\d+)/', $mhs->MASA_STUDI, $matches)) {
                            $studyPeriod = (int)$matches[1];
                        }
                        
                        // Generate predikat using MhsYud model function
                        $predikat = (new MhsYud)->getPredikat($mhs->GPA);
                        
                        // Generate status using Mahasiswa model function
                        $mahasiswaModel = new \App\Models\Mahasiswa();
                        $status = $mahasiswaModel->hitungStatus(
                            $studyPeriod, 
                            (int)$mhs->PASS_CREDIT, 
                            (float)$mhs->GPA,
                            (int)$prodiId
                        );
                        
                        $listMahasiswa[] = [
                            'STUDENTID' => $mhs->STUDENTID,
                            'FULLNAME' => $mhs->FULLNAME,
                            'MASA_STUDI' => $studyPeriod,
                            'PASS_CREDIT' => (int)$mhs->PASS_CREDIT,
                            'GPA' => (float)$mhs->GPA,
                            'PREDIKAT' => $predikat,
                            'STATUS' => $status
                        ];
                    }
                    
                    $source = 'database';
                    Log::info('Data berhasil diambil dari database', ['count' => count($listMahasiswa)]);
                }
            } else {
                // Jika data dari API, filter hanya yang ada di mahasiswa_nims (yang di-check)
                if (!empty($validate['mahasiswa_nims'])) {
                    $listMahasiswa = array_filter($listMahasiswa, function($mhs) use ($validate) {
                        return in_array($mhs['STUDENTID'], $validate['mahasiswa_nims']);
                    });
                    Log::info('Filtered API data by checked NIMs', [
                        'original_count' => count($listMahasiswa),
                        'filtered_count' => count($listMahasiswa)
                    ]);
                }
            }

            if (empty($listMahasiswa)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data mahasiswa yang dipilih.'
                ], 402);
            }

            // PENTING: Hapus mahasiswa yang TIDAK ada di mahasiswa_nims (yang di-uncheck)
            // Ini memastikan mahasiswa yang di-uncheck tidak muncul lagi
            $checkedNims = $validate['mahasiswa_nims'];
            $deleted = MhsYud::where('yudicium_id', $validate['id'])
                ->whereNotIn('nim', $checkedNims)
                ->delete();
            
            if ($deleted > 0) {
                Log::info('Deleted unchecked mahasiswa from mhs_yudiciums', [
                    'deleted_count' => $deleted,
                    'yudicium_id' => $validate['id']
                ]);
            }

            // Simpan ke mhs_yudiciums dengan status final
            $inserted = 0;
            $skipped = 0;
            
            Log::info('Starting insert loop', ['total_mahasiswa' => count($listMahasiswa)]);
            
            foreach ($listMahasiswa as $mhs) {
                $nim = $mhs['STUDENTID'];
                
                // Cek duplicate
                $exists = MhsYud::where('nim', $nim)
                    ->where('yudicium_id', $validate['id'])
                    ->exists();

                Log::info('Checking mahasiswa', [
                    'nim' => $nim,
                    'exists' => $exists,
                    'yudicium_id' => $validate['id']
                ]);

                if (!$exists) {
                    $dataToInsert = [
                        'nim' => $nim,
                        'yudicium_id' => $validate['id'],
                        'fakultas_id' => $fakultasId,
                        'prody_id' => $prodiId,
                        'name' => $mhs['FULLNAME'] ?? null,
                        'study_period' => (int)($mhs['MASA_STUDI'] ?? 0),
                        'pass_sks' => (int)($mhs['PASS_CREDIT'] ?? 0),
                        'ipk' => (float)($mhs['GPA'] ?? 0),
                        'predikat' => $mhs['PREDIKAT'] ?? (new MhsYud)->getPredikat($mhs['GPA'] ?? 0),
                        'status' => 'Eligible' // Status Eligible untuk penetapan
                    ];
                    
                    Log::info('Inserting mahasiswa', [
                        'nim' => $nim,
                        'data' => $dataToInsert
                    ]);
                    
                    MhsYud::create($dataToInsert);
                    $inserted++;
                    
                    Log::info('Successfully inserted', ['nim' => $nim]);
                } else {
                    Log::info('Mahasiswa already exists, keeping status as is', ['nim' => $nim]);
                    
                    // Tidak perlu update status jika sudah ada
                    $skipped++;
                }
            }
            
            Log::info('Insert loop completed', [
                'inserted' => $inserted,
                'skipped' => $skipped
            ]);

            // Generate nomor yudisium
            $mappingFaculties = [
                3 => 'IT', 4 => 'IK', 5 => 'TE', 6 => 'RI', 
                7 => 'IF', 8 => 'EB', 9 => 'KB', 10 => 'SBY', 11 => 'PWT'
            ];
            $fakultasInitial = $mappingFaculties[$yudicium->fakultas_id] ?? 'XX';
            $tahun = date('Y');
            $nomorYudisium = $yudicium->id . '/AKD100/' . $fakultasInitial . '/' . $tahun;

            $yudicium->no_yudicium = $nomorYudisium;
            $yudicium->periode = $tanggal;
            $yudicium->approval_status = 'Waiting';
            $yudicium->save();

            DB::commit();

            Log::info('Yudisium berhasil ditetapkan', [
                'source' => $source,
                'inserted' => $inserted,
                'skipped' => $skipped,
                'yudicium_id' => $validate['id']
            ]);

            return response()->json([
                'success' => true,
                'message' => "Yudisium berhasil ditetapkan dari {$source}. {$inserted} data disimpan, {$skipped} data di-skip.",
                'source' => $source,
                'inserted' => $inserted,
                'skipped' => $skipped
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error tetapkanYudisium: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function hapusMahasiswa(Request $request)
    {
        $request->validate([
            'nim'         => 'required|string',
            'yudicium_id' => 'required|integer',
        ]);

        $deleted = MhsYud::where('nim', $request->nim)
            ->where('yudicium_id', $request->yudicium_id)
            ->delete();

        if ($deleted) {
            Log::info('Mahasiswa dihapus dari daftar yudisium', [
                'nim' => $request->nim,
                'yudicium_id' => $request->yudicium_id
            ]);
            return response()->json(['success' => true, 'message' => 'Mahasiswa berhasil dihapus dari daftar.']);
        }

        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
    }

    public function uncheckMahasiswa(Request $request)
    {
        $request->validate([
            'yudicium_id' => 'required|integer',
            'unchecked_mahasiswa' => 'required|array',
            'unchecked_mahasiswa.*.nim' => 'required|string',
            'unchecked_mahasiswa.*.source' => 'required|in:api,database,mhs_yudiciums',
        ]);

        $yudiciumId = $request->yudicium_id;
        $uncheckedList = $request->unchecked_mahasiswa;
        
        $yudicium = Yudicium::findOrFail($yudiciumId);
        $tanggal = $yudicium->created_at ? $yudicium->created_at->format('Y-m-d') : date('Y-m-d');
        
        $results = [
            'api_reset' => [],
            'database_deleted' => [],
            'errors' => []
        ];

        foreach ($uncheckedList as $mhs) {
            $nim = $mhs['nim'];
            $source = $mhs['source'];

            try {
                // SELALU hapus dari database terlebih dahulu (jika ada)
                $deleted = MhsYud::where('nim', $nim)
                    ->where('yudicium_id', $yudiciumId)
                    ->delete();
                
                if ($deleted) {
                    $results['database_deleted'][] = $nim;
                    Log::info('Berhasil hapus dari mhs_yudiciums untuk NIM: ' . $nim);
                }
                
                // Jika source dari API, hit API stt=11 untuk reset status
                if ($source === 'api') {
                    $apiUrl = trim(env('URL_RESET_ACADEMIC'), " '\"");
                    $apiUrl = str_replace(['NIM', 'TANGGAL'], [$nim, $tanggal], $apiUrl);
                    
                    Log::info('Uncheck mahasiswa dari API, hit stt=11', [
                        'nim' => $nim,
                        'url' => $apiUrl
                    ]);
                    
                    $response = Http::timeout(10)->get($apiUrl);
                    
                    if ($response->successful()) {
                        $body = strtolower(trim($response->body()));
                        if ($body === 'true' || $body === '1') {
                            $results['api_reset'][] = $nim;
                            Log::info('Berhasil reset API untuk NIM: ' . $nim);
                        } else {
                            $results['errors'][] = "NIM {$nim}: API response tidak valid ({$response->body()})";
                            Log::warning('API reset gagal untuk NIM: ' . $nim, ['response' => $response->body()]);
                        }
                    } else {
                        $results['errors'][] = "NIM {$nim}: API error (status {$response->status()})";
                        Log::error('API reset error untuk NIM: ' . $nim, ['status' => $response->status()]);
                    }
                }
                
            } catch (\Exception $e) {
                $results['errors'][] = "NIM {$nim}: {$e->getMessage()}";
                Log::error('Error uncheck mahasiswa', [
                    'nim' => $nim,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $totalProcessed = count($results['database_deleted']);
        $totalErrors = count($results['errors']);

        return response()->json([
            'success' => $totalErrors === 0,
            'message' => "Berhasil menghapus {$totalProcessed} mahasiswa dari database" . 
                        ($totalErrors > 0 ? ", {$totalErrors} error" : ""),
            'results' => $results
        ]);
    }

    public function updateYudicium(Request $request)
    {
        $request->validate([
            'id'             => 'required|integer',
            'mahasiswa_nims' => 'nullable|array',
            'remove_nims'    => 'nullable|array',
        ]);

        $yudiciumId   = $request->id;
        $keepNims     = $request->input('mahasiswa_nims', []);
        $removeNims   = $request->input('remove_nims', []);

        DB::beginTransaction();
        try {
            $yudicium = Yudicium::findOrFail($yudiciumId);

            // 1. Hapus mahasiswa yang tidak dicentang (remove_nims)
            if (!empty($removeNims)) {
                MhsYud::where('yudicium_id', $yudiciumId)
                    ->whereIn('nim', $removeNims)
                    ->delete();
                Log::info('Hapus mahasiswa tidak dicentang', ['count' => count($removeNims)]);
            }

            // 2. Update status mahasiswa yang dicentang → Eligible (bukan final)
            if (!empty($keepNims)) {
                MhsYud::where('yudicium_id', $yudiciumId)
                    ->whereIn('nim', $keepNims)
                    ->update(['status' => 'Eligible']);
                Log::info('Update status ke Eligible', ['count' => count($keepNims)]);
            }

            // 3. Set approval_status kembali ke Waiting
            $yudicium->approval_status = 'Waiting';
            $yudicium->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Yudisium berhasil ditetapkan ulang. ' . count($keepNims) . ' mahasiswa ditetapkan, ' . count($removeNims) . ' dihapus.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('updateYudicium error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
