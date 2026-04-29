<?php

namespace App\Http\Controllers;

use App\Models\MhsYud;
use App\Models\Yudicium;
use App\Models\Pejabat;
use App\Services\YudiciumService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class YudiciumPrintController extends Controller
{
    protected $yudiciumService;

    public function __construct(YudiciumService $yudiciumService)
    {
        $this->yudiciumService = $yudiciumService;
    }

    public function printPdf($id)
    {
        $yudicium = Yudicium::findOrFail($id);

        if ($yudicium->approval_status !== 'Approved') {
            abort(403, 'Yudisium belum disetujui');
        }

        $penandatangan = Pejabat::penandatanganYudisium($yudicium->fakultas_id)->first();
        $mahasiswas = MhsYud::where('yudicium_id', $id)->get();
        $faculties = $this->yudiciumService->getFaculties();
        $prodyCache = [];

        $mahasiswas->transform(function ($mhs) use ($faculties, &$prodyCache) {
            $faculty = $faculties->firstWhere('facultyid', (string) $mhs->fakultas_id);
            $mhs->facultyname = $faculty['facultyname'] ?? 'Unknown';
            $facultyId = $faculty['facultyid'] ?? null;

            if ($facultyId) {
                if (!isset($prodyCache[$facultyId])) {
                    $prodyCache[$facultyId] = $this->yudiciumService->getPrody($facultyId);
                }
                $prody = $prodyCache[$facultyId];
                $mhs->prodyname = $prody->firstWhere('studyprogramid', (string) $mhs->prody_id)['studyprogramname'] ?? 'Unknown';
            } else {
                $mhs->prodyname = 'Unknown';
            }

            if (!empty($mhs->id_smt_masuk)) {
                $yearStart = substr($mhs->id_smt_masuk, 0, 4);
                $semesterCode = substr($mhs->id_smt_masuk, 4, 1);
                $yearEnd = (int)$yearStart + 1;
                $semesterName = ($semesterCode == '1') ? 'Ganjil' : 'Genap';
                
                $mhs->tahun_masuk = "{$semesterName} {$yearStart}/{$yearEnd}";
            } else {
                $mhs->tahun_masuk = '-';
            }
            $mhs->pass_sks = $mhs->pass_sks ?? '-';

            return $mhs;
        });

        $pdf = Pdf::loadView('dashboard.yudiciumPrint', [
            'yudicium' => $yudicium,
            'mahasiswas' => $mahasiswas,
            'penandatangan' => $penandatangan
        ])->setPaper('A4', 'landscape');

        $filename = 'LAMPIRAN YUDISIUM - ' . preg_replace('/[^A-Za-z0-9\-]/', '-', $yudicium->no_yudicium) . '.pdf';
        return $pdf->stream($filename);
    }

    public function printRekapPdf(Request $request)
    {
        $validate = $request->validate([
            'fakultas_id' => 'required|integer',
            'periode' => 'required|date',
        ]);

        $periodes = $this->yudiciumService->generatePeriodeDropdown();
        $selectedPeriode = collect($periodes)->firstWhere('value', $validate['periode']);

        if (!$selectedPeriode) {
            abort(404, 'Periode tidak valid');
        }

        $mahasiswas = MhsYud::join('yudiciums', 'mhs_yudiciums.yudicium_id', '=', 'yudiciums.id')
            ->where('yudiciums.approval_status', 'Approved')
            ->where('mhs_yudiciums.fakultas_id', $validate['fakultas_id'])
            ->whereBetween('yudiciums.periode', [$selectedPeriode['start'], $selectedPeriode['end']])
            ->select('mhs_yudiciums.*', 'yudiciums.no_yudicium', 'yudiciums.periode')
            ->orderBy('yudiciums.no_yudicium')
            ->get();

        if ($mahasiswas->isEmpty()) {
            abort(404, 'Tidak ada data mahasiswa yudisium');
        }

        $faculties = $this->yudiciumService->getFaculties();
        $prodyCache = [];

        $mahasiswas->transform(function ($mhs) use ($faculties, &$prodyCache) {
            $faculty = $faculties->firstWhere('facultyid', (string) $mhs->fakultas_id);
            $mhs->facultyname = $faculty['facultyname'] ?? '-';

            if ($faculty) {
                $facultyId = $faculty['facultyid'];
                if (!isset($prodyCache[$facultyId])) {
                    $prodyCache[$facultyId] = $this->yudiciumService->getPrody($facultyId);
                }
                $prody = $prodyCache[$facultyId];
                $mhs->prodyname = $prody->firstWhere('studyprogramid', (string) $mhs->prody_id)['studyprogramname'] ?? '-';
            } else {
                $mhs->prodyname = '-';
            }

            return $mhs;
        });

        $pdf = Pdf::loadView('dashboard.yudiciumRekapPrint', [
            'mahasiswas' => $mahasiswas,
            'periodeLabel' => $selectedPeriode['label']
        ])->setPaper('A4', 'landscape');

        $periodeLabelSafe = preg_replace('/[^A-Za-z0-9\-\s]/', '', $selectedPeriode['label']);
        $filename = "REKAP YUDISIUM {$periodeLabelSafe}.pdf";

        return $pdf->stream($filename);
    }
}
