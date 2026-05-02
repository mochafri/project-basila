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

class YudiciumApprovalController extends Controller
{
    protected $yudiciumService;

    public function __construct(YudiciumService $yudiciumService)
    {
        $this->yudiciumService = $yudiciumService;
    }

    public function generateCode(Request $request)
    {
        try {
            $validate = $request->validate([
                'id' => 'required|integer',
                'facultyId' => 'required|integer'
            ]);

            $id = $validate['id'];
            $fakultasInitial = $this->yudiciumService->getFakultasInitial($validate['facultyId']);
            $tahun = date('Y-m-d');
            $nomorYudisium = $id . '/AKD100/' . $fakultasInitial . '/' . $tahun;

            Yudicium::where('id', $validate['id'])
                ->update([
                    'no_yudicium' => $nomorYudisium,
                    'periode'     => $tahun,
                    'approval_status' => 'Waiting'
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Nomor yudisium berhasil dibuat'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function getMahasiswa($id)
    {
        $yudicium = Yudicium::findOrFail($id);

        $prodiId = $yudicium->prodi_id;
        $tanggal = $yudicium->periode ?? $yudicium->created_at->format('Y-m-d');
        $mahasiswa = collect();

        // ============================================================
        // PRIORITAS 1: API (untuk semua status: Draft, Waiting, Approved, Rejected)
        // PRIORITAS 2: Database fallback (mhs_yudiciums)
        // ============================================================
        try {
            if ($yudicium->approval_status === 'Draft' || empty($yudicium->approval_status)) {
                // Draft → URL_ALL_ACADEMIC, filter SELECTED=Y
                $list = $this->yudiciumService->getAllAcademicData($prodiId, $tanggal);
                $list = collect($list ?? [])->filter(fn($m) => ($m['SELECTED'] ?? 'N') === 'Y')->values()->all();
            } else {
                // Waiting / Approved / Rejected / Final → URL_PICK_ACADEMIC
                $list = $this->yudiciumService->getSelectedAcademicData($prodiId, $tanggal);
            }

            if (!empty($list)) {
                Log::info("getMahasiswa: data dari API", ['id' => $id, 'count' => count($list)]);

                $mahasiswa = collect($list)->map(function ($mhs) {
                    $tempStatus = TempStatus::where('nim', $mhs['STUDENTID'])->first();
                    
                    // Generate predikat dari fungsi
                    $predikat = (new MhsYud)->getPredikat($mhs['GPA'] ?? 0);
                    
                    // Generate status dari fungsi hitungStatus
                    // Extract numeric value from "10 Semester" format
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
                    
                    return [
                        'nim'            => $mhs['STUDENTID'],
                        'name'           => $mhs['FULLNAME'],
                        'study_period'   => $mhs['MASA_STUDI'] ?? '-',
                        'pass_sks'       => $mhs['PASS_CREDIT'] ?? '-',
                        'ipk'            => $mhs['GPA'] ?? '0',
                        'predikat'       => $predikat,
                        'status'         => $finalStatus,
                        'alasan_status'  => $tempStatus ? $tempStatus->alasan : '-',
                        'status_otomatis'=> $computedStatus,
                        'fakultas_id'    => $mhs['FACULTYID'] ?? null,
                        'prody_id'       => $mhs['STUDYPROGRAMID'] ?? null,
                        'BAHASA_ASING'   => $mhs['BAHASA_ASING'] ?? null,
                        'PUBLIKASI'      => $mhs['PUBLIKASI'] ?? null,
                        'TAK'            => $mhs['TAK'] ?? null,
                        'ADMINISTRATIF'  => $mhs['ADMINISTRATIF'] ?? null,
                        'BPP'            => $mhs['BPP'] ?? null,
                        'OPENLIB'        => $mhs['OPENLIB'] ?? null,
                        'SANKSI'         => $mhs['SANKSI'] ?? null,
                    ];
                })->values();
            } else {
                Log::warning("getMahasiswa: API kosong", ['id' => $id, 'status' => $yudicium->approval_status]);
            }
        } catch (\Exception $e) {
            Log::error("getMahasiswa: API error, fallback ke database", [
                'id'    => $id,
                'error' => $e->getMessage()
            ]);
        }

        // ============================================================
        // PRIORITAS 2: Database fallback jika API kosong / gagal
        // ============================================================
        if ($mahasiswa->isEmpty()) {
            Log::info("getMahasiswa: fallback ke database mhs_yudiciums", ['id' => $id]);

            $mahasiswa = MhsYud::select(
                    'nim','name','study_period','pass_sks',
                    'fakultas_id','prody_id','ipk','predikat','status',
                    'status_otomatis','alasan_status'
                )
                ->where('yudicium_id', $id)
                ->get()
                ->map(function ($mhs) {
                    // Generate predikat dari fungsi (bukan dari database)
                    $predikat = (new MhsYud)->getPredikat($mhs->ipk);
                    
                    // Generate status dari fungsi hitungStatus
                    $mahasiswaModel = new \App\Models\Mahasiswa();
                    $computedStatus = $mahasiswaModel->hitungStatus(
                        (int)$mhs->study_period, 
                        (int)$mhs->pass_sks, 
                        (float)$mhs->ipk,
                        (int)$mhs->prody_id
                    );
                    
                    // Prioritas: status dari mhs_yudiciums > computed status
                    $finalStatus = $mhs->status ?: $computedStatus;
                    
                    return [
                        'nim'            => $mhs->nim,
                        'name'           => $mhs->name,
                        'study_period'   => $mhs->study_period,
                        'pass_sks'       => $mhs->pass_sks,
                        'ipk'            => $mhs->ipk,
                        'predikat'       => $predikat,
                        'status'         => $finalStatus,
                        'alasan_status'  => $mhs->alasan_status ?: '-',
                        'status_otomatis'=> $computedStatus,
                        'fakultas_id'    => $mhs->fakultas_id,
                        'prody_id'       => $mhs->prody_id,
                        'BAHASA_ASING'   => null,
                        'PUBLIKASI'      => null,
                        'TAK'            => null,
                        'ADMINISTRATIF'  => null,
                        'BPP'            => null,
                        'OPENLIB'        => null,
                        'SANKSI'         => null,
                    ];
                })->values();

            Log::info("getMahasiswa: database result", ['id' => $id, 'count' => $mahasiswa->count()]);
        }

        return response()->json([
            'success'  => true,
            'mahasiswa'=> $mahasiswa,
            'yudisium' => [$yudicium]
        ]);
    }

    public function getAllYudicium()
    {
        $yudicium = $this->yudiciumService->getWaitingYudiciums();

        return response()->json([
            'success' => true,
            'data' => $yudicium
        ]);
    }

    public function filterYudisium(Request $request)
    {
        $validate = $request->validate([
            'fakultas_id' => 'required|integer'
        ]);

        $yudiciums = DB::table('yudiciums')
            ->select(
                'yudiciums.id as id',
                'yudiciums.no_yudicium as no_yudicium',
                'yudiciums.periode as periode',
                'yudiciums.fakultas_id as fakultas',
                'yudiciums.prodi_id as prodi'
            )
            ->where('yudiciums.fakultas_id', $validate['fakultas_id'])
            ->where('yudiciums.approval_status', 'Waiting')
            ->get();

        $fakulties = $this->yudiciumService->getFaculties();
        $prodyCache = [];

        $yudiciums->transform(function ($item) use ($fakulties, &$prodyCache) {
            $faculty = $fakulties->firstWhere('facultyid', $item->fakultas);
            $item->fakultasname = $faculty['facultyname'] ?? 'Unknown';
            $facultyId = $faculty['facultyid'] ?? null;

            if ($facultyId) {
                if (!isset($prodyCache[$facultyId])) {
                    $prodyCache[$facultyId] = $this->yudiciumService->getPrody($facultyId);
                }
                $prody = $prodyCache[$facultyId];
                if ($prody->isNotEmpty()) {
                    $prodi = $prody->firstWhere('studyprogramid', $item->prodi);
                    $item->prodiname = $prodi['studyprogramname'] ?? 'Unknown';
                } else {
                    $item->prodiname = 'Unknown';
                }
            } else {
                $item->prodiname = 'Unknown';
            }

            // Ambil total mahasiswa dari API stt=10 (Official Picked)
            $listSelected = $this->yudiciumService->getSelectedAcademicData($item->prodi, $item->periode);
            $item->total_mhs = count($listSelected);

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $yudiciums
        ], 200);
    }

    public function updateStatus(Request $request)
    {
        try {
            $validate = $request->validate([
                'approval_status' => 'string|required',
                'catatan'         => 'string|nullable',
            ]);

            $yudicium = Yudicium::findOrFail($request->yudisium_id);
            $yudicium->update([
                'approval_status' => $validate['approval_status'],
                'catatan'         => $validate['catatan']
            ]);

            // Jika Approved: generate SK, ambil dari URL_PICK_ACADEMIC dan insert ke mhs_yudiciums
            if ($validate['approval_status'] === 'Approved') {
                // Generate Nomor SK sesuai format: SK.No. 07/Sidang Yudisium/FTE/Juli/2014
                $fakultasInitial = $this->yudiciumService->getFakultasInitial($yudicium->fakultas_id);
                // Tambahkan 'F' di depan jika inisial hanya 2 huruf (TE -> FTE)
                if (strlen($fakultasInitial) <= 2) {
                    $fakultasInitial = 'F' . $fakultasInitial;
                }

                $yudicium->update([
                    'no_sk' => $this->yudiciumService->generateNoSk($yudicium->id, $fakultasInitial),
                    'approved_at' => now()
                ]);

                $prodiId = $yudicium->prodi_id;
                $tanggal = $yudicium->periode;
                $list    = $this->yudiciumService->getSelectedAcademicData($prodiId, $tanggal);

                if (!empty($list)) {
                    MhsYud::where('yudicium_id', $yudicium->id)->delete(); // clear any stale

                    $rows = [];
                    foreach ($list as $mhs) {
                        $nim = $mhs['STUDENTID'] ?? ($mhs['nim'] ?? null);
                        if (!$nim) continue;

                        $tempStatus = TempStatus::where('nim', $nim)->first();
                        
                        // Generate predikat dari fungsi
                        $predikat = (new MhsYud)->getPredikat($mhs['GPA'] ?? ($mhs['ipk'] ?? 0));
                        
                        // Generate status dari fungsi hitungStatus
                        // Extract numeric value from "10 Semester" format
                        $studyPeriod = 0;
                        $masaStudi = $mhs['MASA_STUDI'] ?? ($mhs['study_period'] ?? '0');
                        if (preg_match('/(\d+)/', $masaStudi, $matches)) {
                            $studyPeriod = (int)$matches[1];
                        }
                        
                        $mahasiswaModel = new \App\Models\Mahasiswa();
                        $computedStatus = $mahasiswaModel->hitungStatus(
                            $studyPeriod, 
                            (int)($mhs['PASS_CREDIT'] ?? ($mhs['pass_sks'] ?? 0)), 
                            (float)($mhs['GPA'] ?? ($mhs['ipk'] ?? 0)),
                            (int)($mhs['STUDYPROGRAMID'] ?? ($mhs['prody_id'] ?? null))
                        );
                        
                        // Prioritas: temp_status > computed status
                        $finalStatus = $tempStatus ? $tempStatus->status : $computedStatus;
                        
                        $rows[] = [
                            'nim'            => $nim,
                            'id_smt_masuk'   => $mhs['ID_SMT_MASUK'] ?? null,
                            'fakultas_id'    => $mhs['FACULTYID'] ?? ($mhs['fakultas_id'] ?? 0),
                            'prody_id'       => $mhs['STUDYPROGRAMID'] ?? ($mhs['prody_id'] ?? 0),
                            'name'           => $mhs['FULLNAME'] ?? ($mhs['name'] ?? 'Unknown'),
                            'tmp_lahir'      => $mhs['TMP_LAHIR'] ?? null,
                            'tgl_lahir'      => $mhs['TGL_LAHIR'] ?? null,
                            'study_period'   => $mhs['MASA_STUDI'] ?? ($mhs['study_period'] ?? null),
                            'pass_sks'       => $mhs['PASS_CREDIT'] ?? ($mhs['pass_sks'] ?? null),
                            'ipk'            => $mhs['GPA'] ?? ($mhs['ipk'] ?? 0),
                            'predikat'       => $predikat,
                            'status_otomatis'=> $computedStatus,
                            'status'         => $finalStatus,
                            'alasan_status'  => $tempStatus ? $tempStatus->alasan : null,
                            'yudicium_id'    => $yudicium->id,
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    }
                    if (!empty($rows)) {
                        MhsYud::insert($rows);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data'    => $yudicium->fresh()
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    public function approve($id)
    {
        $yudicium = Yudicium::findOrFail($id);
        $yudicium->approval_status = 'Approved';
        $yudicium->approved_at     = now();
        $yudicium->save();

        return redirect()->back()->with('success', 'Yudicium approved.');
    }
}
