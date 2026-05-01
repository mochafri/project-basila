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

            // A. Coba ambil dari API (stt=9 - ALL_ACADEMIC)
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
                    
                    foreach ($listMahasiswa as $mhs) {
                        $tempStatus = TempStatus::select('status', 'alasan')
                            ->where('nim', $mhs['STUDENTID']);

                        $statusFromTemp = $tempStatus->value('status');
                        $statusFromApi = ucfirst(strtolower($mhs['STATUS']));
                        $finalStatus = !empty($statusFromTemp) ? $statusFromTemp : $statusFromApi;

                        $datas[] = (object) [
                            'nim' => $mhs['STUDENTID'],
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
                            'selected' => ($mhs['SELECTED'] ?? 'N') === 'Y',
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

            // B. Fallback ke Database jika API gagal atau kosong
            if (empty($datas)) {
                Log::info('Menggunakan data dari database');
                
                // Ambil dari tabel mahasiswa berdasarkan fakultas dan prodi
                $mahasiswaFromDb = DB::table('mahasiswa')
                    ->where('STUDYPROGRAMID', $prodiId)
                    ->select(
                        'STUDENTID as nim',
                        'FULLNAME as name',
                        'MASA_STUDI',
                        'PASS_CREDIT as pass_sks',
                        'GPA as ipk',
                        'PREDIKAT as predikat',
                        'STATUS as status',
                        'FACULTYID as fakultas_id'
                    )
                    ->get();

                foreach ($mahasiswaFromDb as $mhs) {
                    $tempStatus = TempStatus::select('status', 'alasan')
                        ->where('nim', $mhs->nim);

                    $statusFromTemp = $tempStatus->value('status');
                    $finalStatus = !empty($statusFromTemp) ? $statusFromTemp : $mhs->status;

                    // Extract numeric value from "10 Semester" format
                    $studyPeriod = 0;
                    if (preg_match('/(\d+)/', $mhs->MASA_STUDI, $matches)) {
                        $studyPeriod = (int)$matches[1];
                    }

                    $datas[] = (object) [
                        'nim' => $mhs->nim,
                        'name' => $mhs->name,
                        'fakultas_id' => $mhs->fakultas_id ?? $fakultasId,
                        'prodi_id' => $prodiId,
                        'fakultas_name' => '-',
                        'prodi_name' => '-',
                        'study_period' => $studyPeriod,
                        'pass_sks' => (int)$mhs->pass_sks,
                        'ipk' => (float)$mhs->ipk,
                        'status_otomatis' => $mhs->status,
                        'status' => $finalStatus,
                        'predikat' => $mhs->predikat,
                        'alasan_status' => $tempStatus->value('alasan') ?? null,
                        'selected' => false, // default tidak terpilih untuk data database
                        'id_smt_masuk' => null,
                        'bahasa_asing' => null,
                        'publikasi' => null,
                        'tak' => null,
                        'administratif' => null,
                        'bpp' => null,
                        'openlib' => null,
                        'sanksi' => null,
                        'smt_current' => null,
                        'source' => 'database'
                    ];
                }
                
                Log::info('Data berhasil diambil dari database', ['count' => count($datas)]);
            }
            
            $datas = collect($datas);
            
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
                            'GPA',
                            'PREDIKAT',
                            'STATUS'
                        )
                        ->get();

                    foreach ($mahasiswaFromDb as $mhs) {
                        // Extract numeric value from "10 Semester" format
                        $studyPeriod = 0;
                        if (preg_match('/(\d+)/', $mhs->MASA_STUDI, $matches)) {
                            $studyPeriod = (int)$matches[1];
                        }
                        
                        $listMahasiswa[] = [
                            'STUDENTID' => $mhs->STUDENTID,
                            'FULLNAME' => $mhs->FULLNAME,
                            'MASA_STUDI' => $studyPeriod,
                            'PASS_CREDIT' => (int)$mhs->PASS_CREDIT,
                            'GPA' => (float)$mhs->GPA,
                            'PREDIKAT' => $mhs->PREDIKAT,
                            'STATUS' => $mhs->STATUS
                        ];
                    }
                    
                    $source = 'database';
                    Log::info('Data berhasil diambil dari database', ['count' => count($listMahasiswa)]);
                }
            }

            if (empty($listMahasiswa)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data mahasiswa yang dipilih.'
                ], 402);
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
                        'status' => 'final' // Status final untuk penetapan
                    ];
                    
                    Log::info('Inserting mahasiswa', [
                        'nim' => $nim,
                        'data' => $dataToInsert
                    ]);
                    
                    MhsYud::create($dataToInsert);
                    $inserted++;
                    
                    Log::info('Successfully inserted', ['nim' => $nim]);
                } else {
                    Log::info('Mahasiswa already exists, updating to final', ['nim' => $nim]);
                    
                    // Update ke final jika sudah ada
                    MhsYud::where('nim', $nim)
                        ->where('yudicium_id', $validate['id'])
                        ->update(['status' => 'final']);
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

    public function updateYudicium(Request $request)
    {
        $validate = $request->validate([
            'id' => 'integer|required'
        ]);

        $yudicium = Yudicium::find($request->id);
        $yudicium->approval_status = 'Waiting';
        $yudicium->save();

        MhsYud::where('yudicium_id', $request->id)
            ->where('status', 'Tidak Eligible')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data diperbarui'
        ]);
    }
}
