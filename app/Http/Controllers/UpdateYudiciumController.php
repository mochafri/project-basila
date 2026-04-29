<?php

namespace App\Http\Controllers;

use App\Models\Yudicium;
use App\Models\MhsYud;
use App\Models\TempStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
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

            $urlAllAcademic = trim(env('URL_ALL_ACADEMIC'), " '\"");
            $apiUrl = str_replace(['IDPRODI', 'TANGGAL'], [$prodiId, $tanggal], $urlAllAcademic);

            $response = Http::get($apiUrl);
            $listMahasiswa = $response->json();
            
            $datas = [];
            if (!empty($listMahasiswa)) {
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
                        'ID_SMT_MASUK' => $mhs['ID_SMT_MASUK'] ?? null,
                        'BAHASA_ASING' => $mhs['BAHASA_ASING'] ?? null,
                        'PUBLIKASI' => $mhs['PUBLIKASI'] ?? null,
                        'TAK' => $mhs['TAK'] ?? null,
                        'ADMINISTRATIF' => $mhs['ADMINISTRATIF'] ?? null,
                        'BPP' => $mhs['BPP'] ?? null,
                        'OPENLIB' => $mhs['OPENLIB'] ?? null,
                        'SANKSI' => $mhs['SANKSI'] ?? null,
                        'SMT_CURRENT' => $mhs['SMT_CURRENT'] ?? null,
                    ];
                }
            }
            $datas = collect($datas);
        } else {
            $mhsYud = (new YudiciumApprovalController(new YudiciumService()))->getMahasiswa($yudiciumId);
            $datas = collect($mhsYud->getData()->mahasiswa);
        }

        if (in_array($routeName, ['index5', 'index7'])) {
            return view("dashboard.$routeName", [
                "datas"=> $datas,
                "yudicium_id" => $yudiciumId
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

        DB::beginTransaction();

        try {
            $yudicium = Yudicium::findOrFail($validate['id']);
            $prodiId = $yudicium->prodi_id;
            $tanggal = $yudicium->created_at ? $yudicium->created_at->format('Y-m-d') : date('Y-m-d');
            
            $urlAllAcademic = trim(env('URL_ALL_ACADEMIC'), " '\"");
            $apiUrl = str_replace(['IDPRODI', 'TANGGAL'], [$prodiId, $tanggal], $urlAllAcademic);

            $response = Http::get($apiUrl);
            $listMahasiswa = $response->json();

            if (empty($listMahasiswa)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data mahasiswa dari API.'
                ], 402);
            }

            // Generate nomor yudisium
            $mappingFaculties = [3 => 'IT', 4 => 'IK', 5 => 'TE', 6 => 'RI', 7 => 'IF', 8 => 'EB', 9 => 'KB', 10 => 'SBY', 11 => 'PWT'];
            $fakultasInitial  = $mappingFaculties[$yudicium->fakultas_id] ?? 'XX';
            $tahun            = date('Y-m-d');
            $nomorYudisium    = $yudicium->id . '/AKD100/' . $fakultasInitial . '/' . $tahun;

            $yudicium->no_yudicium    = $nomorYudisium;
            $yudicium->periode        = $tahun;
            $yudicium->approval_status = 'Waiting';
            $yudicium->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Yudisium berhasil ditetapkan'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error tetapkanYudisium: ' . $e->getMessage());
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
