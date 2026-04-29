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

        if ($yudicium->approval_status === 'Waiting') {
            // Fetch from URL_PICK_ACADEMIC using the set periode
            $prodiId  = $yudicium->prodi_id;
            $tanggal  = $yudicium->periode ?? $yudicium->created_at->format('Y-m-d');
            $list     = $this->yudiciumService->getSelectedAcademicData($prodiId, $tanggal);

            $mahasiswa = collect($list ?? [])->map(function ($mhs) {
                $tempStatus = TempStatus::where('nim', $mhs['STUDENTID'])->first();
                return [
                    'nim'            => $mhs['STUDENTID'],
                    'name'           => $mhs['FULLNAME'],
                    'study_period'   => $mhs['MASA_STUDI'] ?? '-',
                    'pass_sks'       => $mhs['PASS_CREDIT'] ?? '-',
                    'ipk'            => $mhs['GPA'] ?? '0',
                    'predikat'       => (new MhsYud)->getPredikat($mhs['GPA'] ?? 0),
                    'status'         => $tempStatus ? $tempStatus->status : ucfirst(strtolower($mhs['STATUS'] ?? '-')),
                    'alasan_status'  => $tempStatus ? $tempStatus->alasan : '-',
                    'status_otomatis'=> ucfirst(strtolower($mhs['STATUS'] ?? '-')),
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

        } elseif ($yudicium->approval_status === 'Draft' || empty($yudicium->approval_status)) {
            // Draft: fetch from URL_ALL_ACADEMIC (SELECTED=Y only)
            $prodiId = $yudicium->prodi_id;
            $tanggal = $yudicium->created_at ? $yudicium->created_at->format('Y-m-d') : date('Y-m-d');
            $list    = $this->yudiciumService->getAllAcademicData($prodiId, $tanggal);

            $mahasiswa = collect($list ?? [])
                ->filter(fn($mhs) => ($mhs['SELECTED'] ?? 'N') === 'Y')
                ->map(function ($mhs) {
                    $tempStatus = TempStatus::where('nim', $mhs['STUDENTID'])->first();
                    return [
                        'nim'            => $mhs['STUDENTID'],
                        'name'           => $mhs['FULLNAME'],
                        'study_period'   => $mhs['MASA_STUDI'] ?? '-',
                        'pass_sks'       => $mhs['PASS_CREDIT'] ?? '-',
                        'ipk'            => $mhs['GPA'] ?? '0',
                        'predikat'       => (new MhsYud)->getPredikat($mhs['GPA'] ?? 0),
                        'status'         => $tempStatus ? $tempStatus->status : ucfirst(strtolower($mhs['STATUS'] ?? '-')),
                        'alasan_status'  => $tempStatus ? $tempStatus->alasan : '-',
                        'status_otomatis'=> ucfirst(strtolower($mhs['STATUS'] ?? '-')),
                        'fakultas_id'    => $mhs['FACULTYID'] ?? null,
                    ];
                })->values();

        } else {
            // Approved / Rejected: from local mhs_yudiciums
            $mahasiswa = MhsYud::select('nim','name','study_period','pass_sks','fakultas_id','ipk','predikat','status','status_otomatis','alasan_status')
                ->where('yudicium_id', $id)
                ->get()
                ->each(function ($mhs) {
                    if (empty($mhs->alasan_status)) $mhs->alasan_status = '-';
                    if (empty($mhs->status))        $mhs->status = $mhs->status_otomatis;
                    $mhs->predikat = (new MhsYud)->getPredikat($mhs->ipk);
                });
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
                            'predikat'       => (new MhsYud)->getPredikat($mhs['GPA'] ?? ($mhs['ipk'] ?? 0)),
                            'status_otomatis'=> ucfirst(strtolower($mhs['STATUS'] ?? ($mhs['status_otomatis'] ?? ''))),
                            'status'         => $tempStatus ? $tempStatus->status : null,
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
