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

        // ============================================================
        // AMBIL DATA LANGSUNG DARI DATABASE (mhs_yudiciums)
        // TIDAK MENGGUNAKAN API
        // ============================================================
        Log::info("getMahasiswa: mengambil data dari database mhs_yudiciums", ['id' => $id]);

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

            // Jika Approved: generate SK dan update status
            // TIDAK perlu mengambil data dari API lagi karena data sudah ada di mhs_yudiciums
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

                // TIDAK perlu delete dan re-insert data dari API
                // Data mahasiswa sudah ada di mhs_yudiciums dari proses penetapan sebelumnya
                // Cukup update status mahasiswa menjadi 'Eligible' (final)
                MhsYud::where('yudicium_id', $yudicium->id)
                    ->update(['status' => 'Eligible']);
                
                Log::info('Yudicium approved, status mahasiswa updated to Eligible', [
                    'yudicium_id' => $yudicium->id,
                    'total_mahasiswa' => MhsYud::where('yudicium_id', $yudicium->id)->count()
                ]);
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
