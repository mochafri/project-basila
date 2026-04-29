<?php

namespace App\Http\Controllers;

use App\Models\MhsYud;
use App\Models\Yudicium;
use App\Models\Post;
use App\Services\YudiciumService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YudiciumDashboardController extends Controller
{
    protected $yudiciumService;

    public function __construct(YudiciumService $yudiciumService)
    {
        $this->yudiciumService = $yudiciumService;
    }

    public function index(Request $request)
    {
        $data = $this->getCommonDashboardData($request);
        $totalMhsYud = $data['totalMhsYud'];
        $predikatCounts = $data['predikatCounts'];
        $fakultasCounts = $data['fakultasCounts'];
        $faculties = $data['faculties'];

        // 1️⃣ Hitung jumlah per predikat
        $predikatList = [
            'Sempurna (Summa Cumlaude)',
            'Dengan Pujian (Cumlaude)',
            'Sangat Memuaskan (Very Good)',
            'Memuaskan (Good)',
            'Tanpa Predikat (No Predicate)',
        ];

        $dataPredikat = [];
        foreach ($predikatList as $label) {
            $found = $predikatCounts->firstWhere('predikat', $label);
            $jumlah = $found ? $found->total : 0;
            $persen = $totalMhsYud > 0 ? round(($jumlah / $totalMhsYud) * 100, 1) : 0;

            $dataPredikat[] = [
                'label' => $label,
                'jumlah' => $jumlah,
                'persen' => $persen,
            ];
        }

        $dataFakultas = [];
        foreach ($faculties as $faculty) {
            $found = $fakultasCounts->firstWhere('fakultas_id', $faculty['facultyid']);
            $jumlah = $found ? $found->total : 0;
            $persen = $totalMhsYud > 0 ? round(($jumlah / $totalMhsYud) * 100, 1) : 0;

            $dataFakultas[] = [
                'label' => $faculty['facultyname'],
                'jumlah' => $jumlah,
                'persen' => $persen,
            ];
        }

        return view("dashboard.index", array_merge($data, [
            'dataPredikat' => $dataPredikat,
            'dataFakultas' => $dataFakultas,
        ]));
    }

    public function penetapan(Request $request)
    {
        $data = $this->getCommonDashboardData($request);
        $datas = $data['datas'];
        $faculties = $data['faculties'];
        $prodyCache = [];

        $datas->transform(function ($item) use ($faculties, &$prodyCache) {
            $faculty = $faculties->firstWhere('facultyid', $item->fakultas_id);
            $item->facultyname = $faculty['facultyname'] ?? 'Unknown';
            $facultyId = $faculty['facultyid'] ?? null;

            if ($facultyId) {
                if (!isset($prodyCache[$facultyId])) {
                    $prodyCache[$facultyId] = $this->yudiciumService->getPrody($facultyId);
                }

                $prody = $prodyCache[$facultyId];
                $item->prodyname = $prody->firstWhere('studyprogramid', (string) $item->prodi_id)['studyprogramname'] ?? 'Unknown';
            } else {
                $item->prodyname = 'Unknown';
            }

            $item->semester_label = $this->yudiciumService->getSemesterLabel($item->periode);
            return $item;
        });

        return view("dashboard.index2", $data);
    }

    public function approval(Request $request)
    {
        $data = $this->getCommonDashboardData($request);
        
        // Get waiting yudiciums via the dedicated method (which we will move to YudiciumApprovalController later or call directly)
        $yudicium = $this->yudiciumService->getWaitingYudiciums();

        return view("dashboard.index4", array_merge($data, [
            'yudicium' => $yudicium,
        ]));
    }

    public function laporan(Request $request)
    {
        $response = $this->index($request);
        return view("dashboard.index6", $response->getData());
    }

    protected function getCommonDashboardData(Request $request)
    {
        $periode = $request->query('periode');
        $periodes = $this->yudiciumService->generatePeriodeDropdown();
        $selectedPeriode = $periode ? collect($periodes)->firstWhere('value', $periode) : null;
        $periodeLabel = $selectedPeriode['label'] ?? null;

        $datasQuery = DB::table('yudiciums');
        if ($selectedPeriode) {
            $datasQuery->whereBetween('periode', [$selectedPeriode['start'], $selectedPeriode['end']]);
        }
        $datas = $datasQuery->get();

        $countApprovalQuery = DB::table('yudiciums')->where('approval_status', 'Approved');
        if ($selectedPeriode) {
            $countApprovalQuery->whereBetween('periode', [$selectedPeriode['start'], $selectedPeriode['end']]);
        }
        $countApproval = $countApprovalQuery->count();

        $totalMhsYudQuery = MhsYud::join('yudiciums', 'mhs_yudiciums.yudicium_id', '=', 'yudiciums.id')
            ->where('yudiciums.approval_status', 'approved');
        if ($selectedPeriode) {
            $totalMhsYudQuery->whereBetween('yudiciums.periode', [$selectedPeriode['start'], $selectedPeriode['end']]);
        }
        $totalMhsYud = $totalMhsYudQuery->count();

        $predikatCountsQuery = MhsYud::join('yudiciums', 'mhs_yudiciums.yudicium_id', '=', 'yudiciums.id')
            ->where('yudiciums.approval_status', 'approved')
            ->select('mhs_yudiciums.predikat', DB::raw('COUNT(*) as total'))
            ->groupBy('mhs_yudiciums.predikat');
        if ($selectedPeriode) {
            $predikatCountsQuery->whereBetween('yudiciums.periode', [$selectedPeriode['start'], $selectedPeriode['end']]);
        }
        $predikatCounts = $predikatCountsQuery->get();

        $fakultasCountsQuery = MhsYud::join('yudiciums', 'mhs_yudiciums.yudicium_id', '=', 'yudiciums.id')
            ->where('yudiciums.approval_status', 'approved')
            ->select('mhs_yudiciums.fakultas_id', DB::raw('COUNT(*) as total'))
            ->groupBy('mhs_yudiciums.fakultas_id');
        if ($selectedPeriode) {
            $fakultasCountsQuery->whereBetween('yudiciums.periode', [$selectedPeriode['start'], $selectedPeriode['end']]);
        }
        $fakultasCounts = $fakultasCountsQuery->get();

        $postCount = Post::count();
        $approvalWaiting = Yudicium::where('approval_status', 'Waiting')->count();
        $faculties = $this->yudiciumService->getFaculties();

        return [
            'datas' => $datas,
            'postCount' => $postCount,
            'totalMhsYud' => $totalMhsYud,
            'countApproval' => $countApproval,
            'waitingApproval' => $approvalWaiting,
            'periode' => $periode,
            'periodes' => $periodes,
            'periodeLabel' => $periodeLabel,
            'predikatCounts' => $predikatCounts,
            'fakultasCounts' => $fakultasCounts,
            'faculties' => $faculties,
        ];
    }
}
