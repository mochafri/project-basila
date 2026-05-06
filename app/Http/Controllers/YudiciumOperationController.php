<?php

namespace App\Http\Controllers;

use App\Models\MhsYud;
use App\Models\Yudicium;
use App\Models\TempStatus;
use App\Services\YudiciumService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YudiciumOperationController extends Controller
{
    protected $yudiciumService;

    public function __construct(YudiciumService $yudiciumService)
    {
        $this->yudiciumService = $yudiciumService;
    }

    public function saveDraft(Request $request)
    {
        $validate = $request->validate([
            'fakultas_id'    => 'required|integer',
            'prodi_id'       => 'required|integer',
            'mahasiswa_nims' => 'array',
            'source'         => 'nullable|string', // 'api' atau 'database'
        ]);

        $nims = $validate['mahasiswa_nims'] ?? [];
        $source = $validate['source'] ?? 'api'; // default api

        Log::info('saveDraft called', [
            'nims_count' => count($nims),
            'source' => $source,
            'fakultas_id' => $validate['fakultas_id'],
            'prodi_id' => $validate['prodi_id']
        ]);

        if (empty($nims)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada mahasiswa yang dipilih.'
            ], 403);
        }

        DB::beginTransaction();

        try {
            $currentDate = date('Y-m-d');

            $yudicium = Yudicium::create([
                'fakultas_id' => $validate['fakultas_id'],
                'prodi_id'    => $validate['prodi_id'],
                'periode'     => null,
                'no_yudicium' => null,
                'approval_status' => 'Draft',
                'created_by'  => auth()->user()?->nip ?: auth()->user()?->username,
            ]);

            // Kondisi 1: Jika data dari API - hit stt=8 dulu, lalu insert ke mhs_yudiciums
            if ($source === 'api') {
                Log::info('saveDraft: Data dari API, trying to hit stt=8 then inserting to mhs_yudiciums', [
                    'nims' => $nims,
                    'currentDate' => $currentDate,
                    'fakultas_id' => $validate['fakultas_id'],
                    'prodi_id' => $validate['prodi_id']
                ]);
                
                // Step 1: Coba hit API stt=8 untuk setiap NIM (optional, tidak wajib berhasil)
                $apiSetResults = [];
                foreach ($nims as $nim) {
                    try {
                        Log::info("[saveDraft] Trying to call API stt=8 for NIM $nim", [
                            'nim' => $nim,
                            'date' => $currentDate,
                            'selected' => 'Y'
                        ]);
                        
                        $apiResponse = $this->yudiciumService->setAcademicStatus($nim, $currentDate, 'Y');
                        $body = strtolower(trim($apiResponse->body()));
                        
                        $apiSetResults[$nim] = [
                            'success' => $apiResponse->successful() && ($body === 'true' || $body === '1'),
                            'status' => $apiResponse->status(),
                            'body' => $body
                        ];
                        
                        Log::info("[saveDraft] API stt=8 response for NIM $nim", $apiSetResults[$nim]);
                    } catch (\Exception $e) {
                        $apiSetResults[$nim] = [
                            'success' => false,
                            'error' => $e->getMessage()
                        ];
                        Log::warning("[saveDraft] API stt=8 failed for NIM $nim", [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                Log::info('[saveDraft] API stt=8 results summary', [
                    'total' => count($apiSetResults),
                    'success' => count(array_filter($apiSetResults, fn($r) => $r['success'])),
                    'failed' => count(array_filter($apiSetResults, fn($r) => !$r['success']))
                ]);
                
                // Step 2: Ambil data mahasiswa dari API stt=7 (untuk data lengkap)
                Log::info('[saveDraft] Fetching complete data from API stt=7');
                
                $apiUrl = trim(env('URL_ACADEMIC'), " '\"");
                $response = Http::timeout(15)->get($apiUrl);
                
                if (!$response->successful()) {
                    throw new \Exception('Gagal mengambil data dari API stt=7');
                }
                
                $allMahasiswa = $response->json();
                
                if (empty($allMahasiswa) || !is_array($allMahasiswa)) {
                    throw new \Exception('Data mahasiswa dari API kosong');
                }
                
                Log::info('[saveDraft] Fetched data from API stt=7', [
                    'total_count' => count($allMahasiswa)
                ]);
                
                // Step 3: Filter hanya mahasiswa yang dipilih (berdasarkan NIM)
                $selectedMahasiswa = collect($allMahasiswa)
                    ->filter(function($mhs) use ($nims) {
                        return in_array($mhs['STUDENTID'], $nims);
                    });
                
                Log::info('[saveDraft] Filtered selected mahasiswa', [
                    'selected_count' => $selectedMahasiswa->count()
                ]);
                
                if ($selectedMahasiswa->isEmpty()) {
                    throw new \Exception('Tidak ada data mahasiswa yang ditemukan di API untuk NIM yang dipilih');
                }
                
                // Step 4: Insert ke mhs_yudiciums (tidak peduli hasil API stt=8)
                foreach ($selectedMahasiswa as $mhs) {
                    $nim = $mhs['STUDENTID'];
                    
                    // Cek temp_status
                    $tempStatus = TempStatus::where('nim', $nim)->first();
                    
                    // Generate predikat
                    $predikat = (new MhsYud)->getPredikat($mhs['GPA'] ?? 0);
                    
                    // Generate status dari fungsi hitungStatus
                    $studyPeriod = 0;
                    if (isset($mhs['MASA_STUDI']) && preg_match('/(\d+)/', $mhs['MASA_STUDI'], $matches)) {
                        $studyPeriod = (int)$matches[1];
                    }
                    
                    $mahasiswaModel = new \App\Models\Mahasiswa();
                    $computedStatus = $mahasiswaModel->hitungStatus(
                        $studyPeriod, 
                        (int)($mhs['PASS_CREDIT'] ?? 0), 
                        (float)($mhs['GPA'] ?? 0),
                        (int)($mhs['STUDYPROGRAMID'] ?? null)
                    );
                    
                    // Prioritas: temp_status > computed status
                    $finalStatus = $tempStatus ? $tempStatus->status : $computedStatus;
                    
                    // Insert ke mhs_yudiciums
                    MhsYud::create([
                        'nim' => $nim,
                        'yudicium_id' => $yudicium->id,
                        'id_smt_masuk' => $mhs['ID_SMT_MASUK'] ?? null,
                        'fakultas_id' => $mhs['FACULTYID'] ?? $validate['fakultas_id'],
                        'prody_id' => $mhs['STUDYPROGRAMID'] ?? $validate['prodi_id'],
                        'name' => $mhs['FULLNAME'] ?? 'Unknown',
                        'tmp_lahir' => $mhs['TMP_LAHIR'] ?? null,
                        'tgl_lahir' => $mhs['TGL_LAHIR'] ?? null,
                        'study_period' => $studyPeriod,
                        'pass_sks' => (int)($mhs['PASS_CREDIT'] ?? 0),
                        'ipk' => (float)($mhs['GPA'] ?? 0),
                        'predikat' => $predikat,
                        'status_otomatis' => $computedStatus,
                        'status' => $finalStatus,
                        'alasan_status' => $tempStatus ? $tempStatus->alasan : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    Log::info("[saveDraft] Inserted NIM $nim to mhs_yudiciums", [
                        'api_stt8_success' => $apiSetResults[$nim]['success'] ?? false
                    ]);
                }
                
                Log::info('[saveDraft] Successfully inserted all selected mahasiswa to mhs_yudiciums', [
                    'count' => $selectedMahasiswa->count(),
                    'api_stt8_summary' => $apiSetResults
                ]);
                
            } else {
                // Kondisi 2: Jika data dari Database, insert ke mhs_yudiciums
                Log::info('saveDraft: Data dari Database, insert ke mhs_yudiciums', ['nims' => $nims]);
                
                // Ambil data mahasiswa dari database
                $mahasiswaData = DB::table('mahasiswa')
                    ->whereIn('STUDENTID', $nims)
                    ->where('STUDYPROGRAMID', $validate['prodi_id'])
                    ->get();
                
                foreach ($mahasiswaData as $mhs) {
                    // Extract numeric value from "10 Semester" format
                    $studyPeriod = 0;
                    if (preg_match('/(\d+)/', $mhs->MASA_STUDI, $matches)) {
                        $studyPeriod = (int)$matches[1];
                    }
                    
                    // Generate predikat dan status
                    $predikat = (new MhsYud)->getPredikat($mhs->GPA);
                    
                    $mahasiswaModel = new \App\Models\Mahasiswa();
                    $status = $mahasiswaModel->hitungStatus(
                        $studyPeriod, 
                        (int)$mhs->PASS_CREDIT, 
                        (float)$mhs->GPA,
                        (int)$mhs->STUDYPROGRAMID
                    );
                    
                    // Insert ke mhs_yudiciums
                    MhsYud::create([
                        'nim' => $mhs->STUDENTID,
                        'yudicium_id' => $yudicium->id,
                        'fakultas_id' => $validate['fakultas_id'],
                        'prody_id' => $validate['prodi_id'],
                        'name' => $mhs->FULLNAME,
                        'study_period' => $studyPeriod,
                        'pass_sks' => (int)$mhs->PASS_CREDIT,
                        'ipk' => (float)$mhs->GPA,
                        'predikat' => $predikat,
                        'status' => $status,
                    ]);
                }
                
                Log::info('saveDraft: Berhasil insert ke mhs_yudiciums', ['count' => $mahasiswaData->count()]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Draft Yudisium berhasil disimpan'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saveDraft: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDraft($id)
    {
        $mahasiswa = MhsYud::where('yudicium_id', $id)->get();

        foreach ($mahasiswa as $mhs) {
            $temp = TempStatus::where('nim', $mhs->nim)->first();
            if ($temp) {
                $mhs->status = $temp->status;
                $mhs->alasan_status = $temp->alasan;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $mahasiswa,
        ]);
    }

    public function edit($id)
    {
        $yudicium = Yudicium::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $yudicium,
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $yudicium = Yudicium::findOrFail($id);
            $prodiId = $yudicium->prodi_id;
            
            // Gunakan periode dari tabel, jika null pakai tanggal created_at
            $periode = $yudicium->periode ?: ($yudicium->created_at ? $yudicium->created_at->format('Y-m-d') : date('Y-m-d'));

            // 1. Ambil daftar mahasiswa yang terpilih dari API (stt=10) untuk prodi & tanggal ini
            // Ini penting karena untuk 'Draft', data NIM mungkin belum ada di mhs_yudiciums lokal
            $urlPick = "https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=10&id=$prodiId&periode=$periode";
            $responsePick = Http::get($urlPick);
            $listSelected = $responsePick->successful() ? $responsePick->json() : [];

            // 2. Reset status di API untuk setiap mahasiswa yang ditemukan
            if (!empty($listSelected)) {
                foreach ($listSelected as $mhs) {
                    $nim = $mhs['STUDENTID'] ?? null;
                    if ($nim) {
                        try {
                            $this->yudiciumService->resetAcademicStatus($nim, $periode);
                        } catch (\Exception $e) {
                            Log::warning("[destroy] Gagal reset API untuk NIM $nim: " . $e->getMessage());
                        }
                    }
                }
            }

            // 3. Hapus data lokal (mhs_yudiciums jika ada, dan yudicium)
            MhsYud::where('yudicium_id', $id)->delete();
            $yudicium->delete();

            DB::commit();

            // Reset auto-increment
            $maxId = Yudicium::max('id') ?: 0;
            DB::statement("ALTER TABLE yudiciums AUTO_INCREMENT = " . ($maxId + 1));

            return response()->json(['success' => true, 'message' => 'Yudisium berhasil dihapus dan disinkronisasi.']);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error destroy yudisium: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
