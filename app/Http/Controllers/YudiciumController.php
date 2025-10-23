<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MhsYud;
use App\Models\Yudicium;
use App\Models\TempStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\Mahasiswa;

class YudiciumController extends Controller
{
    public $url;

    public function __construct()
    {
        $this->token = env('KEY_TOKEN');
        $this->url = env('URL_ACADEMIC');
        $this->urlFakultas = env('URL_FACULTY');
        $this->urlProdi = env('URL_PRODY');
    }

    public function index(Request $request)
    {
        // 1️⃣ Ambil periode dari query string (misalnya ?periode=2025-02-01)
        $periode = $request->query('periode');

        // 2️⃣ Generate daftar dropdown periode otomatis (2 tahun terakhir)
        $periodes = $this->generatePeriodeDropdown();

        // Siapkan variabel default
        $selectedPeriode = null;

        if ($periode) {
            $selectedPeriode = collect($periodes)->firstWhere('value', $periode);
        }

        // 3️⃣ Ambil data yudicium sesuai periode (jika dipilih)
        $datasQuery = DB::table('yudiciums');
        if ($selectedPeriode) {
            $datasQuery->whereBetween('periode', [
                $selectedPeriode['start'],
                $selectedPeriode['end']
            ]);
        }
        $datas = $datasQuery->get();

        // 4️⃣ Hitung jumlah yudisium yang disetujui (Approved)
        $countApprovalQuery = DB::table('yudiciums')
            ->where('approval_status', 'Approved');
        if ($selectedPeriode) {
            $countApprovalQuery->whereBetween('periode', [
                $selectedPeriode['start'],
                $selectedPeriode['end']
            ]);
        }
        $countApproval = $countApprovalQuery->count();

        // 5️⃣ Hitung total mahasiswa yudisium (yang sudah disetujui)
        $totalMhsYudQuery = MhsYud::join('yudiciums', 'mhs_yudiciums.yudicium_id', '=', 'yudiciums.id')
            ->where('yudiciums.approval_status', 'approved');
        if ($selectedPeriode) {
            $totalMhsYudQuery->whereBetween('yudiciums.periode', [
                $selectedPeriode['start'],
                $selectedPeriode['end']
            ]);
        }
        $totalMhsYud = $totalMhsYudQuery->count();

        // 6️⃣ Hitung jumlah per predikat (mengikuti periode)
        $predikatCounts = MhsYud::join('yudiciums', 'mhs_yudiciums.yudicium_id', '=', 'yudiciums.id')
            ->where('yudiciums.approval_status', 'approved')
            ->select('mhs_yudiciums.predikat', DB::raw('COUNT(*) as total'))
            ->groupBy('mhs_yudiciums.predikat');

        if ($selectedPeriode) {
            $predikatCounts->whereBetween('yudiciums.periode', [
                $selectedPeriode['start'],
                $selectedPeriode['end']
            ]);
        }

        $predikatCounts = $predikatCounts->get();

        // 7️⃣ Hitung jumlah per fakultas (mengikuti periode)
        $fakultasCounts = MhsYud::join('yudiciums', 'mhs_yudiciums.yudicium_id', '=', 'yudiciums.id')
            ->where('yudiciums.approval_status', 'approved')
            ->select('mhs_yudiciums.fakultas_id', DB::raw('COUNT(*) as total'))
            ->groupBy('mhs_yudiciums.fakultas_id');

        if ($selectedPeriode) {
            $fakultasCounts->whereBetween('yudiciums.periode', [
                $selectedPeriode['start'],
                $selectedPeriode['end']
            ]);
        }

        $fakultasCounts = $fakultasCounts->get();

        // 🔹 Data tambahan
        $routeName = $request->route()->getName();
        $postCount = Post::count();
        $approvalWaiting = Yudicium::where('approval_status', 'Waiting')->count();

        // 🔹 Ambil data fakultas dari API
        $facultyRes = Http::withToken($this->token)->get($this->urlFakultas);
        $faculties = $facultyRes->successful() ? collect($facultyRes->json()) : collect();

        $prodyCache = [];

        // === ROUTE: index (Dashboard utama) ===
        if (in_array($routeName, ['index', 'index4', 'index6'])) {

            // ===============================
            // 1️⃣ Hitung jumlah per predikat
            // ===============================
            $predikatList = [
                'Sempurna (Summa Cumlaude)',
                'Dengan Pujian (Cumlaude)',
                'Sangat Memuaskan (Very Good)',
                'Memuaskan (Good)',
                'Tanpa Predikat',
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

            // ===============================
            // 2️⃣ Hitung jumlah per fakultas
            // ===============================
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

            return view("dashboard.$routeName", [
                'datas' => $datas,
                'postCount' => $postCount,
                'totalMhsYud' => $totalMhsYud,
                'dataPredikat' => $dataPredikat,
                'dataFakultas' => $dataFakultas,
                'countApproval' => $countApproval,
                'waitingApproval' => $approvalWaiting,
                'periode' => $periode,
                'periodes' => $periodes
            ]);
        }
        
        // === ROUTE: index2 ===
        elseif ($routeName === 'index2') {

            $datas->transform(function ($item) use ($faculties, &$prodyCache) {
                $faculty = $faculties->firstWhere('facultyid', $item->fakultas_id);
                $item->facultyname = $faculty['facultyname'] ?? 'Unknown';
                $facultyId = $faculty['facultyid'] ?? null;

                if ($facultyId) {
                    if (!isset($prodyCache[$facultyId])) {
                        $prodyRes = Http::withToken(env('KEY_TOKEN'))
                            ->get($this->urlProdi . $facultyId);
                        $prodyCache[$facultyId] = $prodyRes->successful() ? collect($prodyRes->json()) : collect();
                    }

                    $prody = $prodyCache[$facultyId];
                    $item->prodyname = $prody->firstWhere('studyprogramid', (string) $item->prodi_id)['studyprogramname'] ?? 'Unknown';
                } else {
                    $item->prodyname = 'Unknown';
                }

                return $item;
            });

            return view("dashboard.$routeName", [
                'datas' => $datas,
                'postCount' => $postCount,
                'totalMhsYud' => $totalMhsYud,
                'countApproval' => $countApproval,
                'periode' => $periode,
                'periodes' => $periodes
            ]);
        }
    }


    public function approve($id)
    {
        $yudicium = Yudicium::findOrFail($id);
        $yudicium->approval_status = 'approved';
        // $yudicium->approved_by = auth()->user()->id;
        $yudicium->approved_at = now();
        $yudicium->save();

        return redirect()->back()->with('success', 'Yudicium approved.');
    }

    // Button buat untuk membuat draft terlebih dahulu
    public function saveDraft(Request $request)
    {
        $validate = $request->validate([
            'fakultas_id' => 'required|integer',
            'prody_id' => 'required|integer',
            'alasan' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        \Log::info('Request from client side : ', $validate);

        $yudicium = Yudicium::create([
            'fakultas_id' => $validate['fakultas_id'],
            'prodi_id' => $validate['prody_id'],
            'periode' => null,
            'no_yudicium' => null,
        ]);

        // $prodi_id = $validate['prodi_id'];
        // $url = $this->url . '&id=' . $prodi_id;

        // $response = Http::get($url);
        // $listMahasiswa = $response->json();

        $mahasiswaDb = Mahasiswa::select()
            ->where('STUDYPROGRAMID', $validate['prody_id'])
            ->get()
            ->toArray();

        \Log::info('Data mahasiswa', $mahasiswaDb);

        if (empty($mahasiswaDb)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data mahasiswa, silahkan coba lagi.'
            ], 402);
        }

        $eligibleMhs = [];

        foreach ($mahasiswaDb as $mhs) {
            $tempStatus = TempStatus::select('status', 'alasan')
                ->where('nim', $mhs['STUDENTID']);

            $statusFromTemp = $tempStatus->value('status');
            $statusFromApi = ucfirst(strtolower($mhs['STATUS']));

            $finalStatus = !empty($statusFromTemp) ? $statusFromTemp : $statusFromApi;

            if ($finalStatus === 'Eligible') {
                $eligibleMhs[] = [
                    'nim' => $mhs['STUDENTID'],
                    'fakultas_id' => $mhs['FACULTYID'],
                    'prody_id' => $mhs['STUDYPROGRAMID'],
                    'name' => $mhs['FULLNAME'],
                    'study_period' => $mhs['MASA_STUDI'],
                    'pass_sks' => $mhs['PASS_CREDIT'],
                    'ipk' => $mhs['GPA'],
                    'status_otomatis' => ucfirst(strtolower($mhs['STATUS'])),
                    'status' => $statusFromTemp ?? null,
                    'predikat' => (new MhsYud)->getPredikat($mhs['GPA']),
                    'alasan_status' => $tempStatus->value('alasan') ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'yudicium_id' => $yudicium->id
                ];
            }
        }

        \Log::info('Eligible mhs', $eligibleMhs);

        if (!empty($eligibleMhs)) {
            MhsYud::insert($eligibleMhs);
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft Yudisium berhasil disimpan'
        ], 200);
    }

    // Buttton tetap kan
    public function generateCode(Request $request)
    {
        try {
            $validate = $request->validate([
                'id' => 'required|integer',
                'facultyId' => 'required|integer'
            ]);

            $id = $validate['id'];
            $mappingFaculties = [3 => 'IT', 4 => 'IK', 5 => 'TE', 6 => 'RI', 7 => 'IF', 8 => 'EB', 9 => 'KB', 10 => 'SBY', 11 => 'PWT'];
            $fakultasInitial = $mappingFaculties[$validate['facultyId']] ?? 'XX';
            $tahun = date('d-m-Y');
            $nomorYudisium = $id . '/AKD100/' . $fakultasInitial . '/' . $tahun;

            $yudicium = Yudicium::where('id', $validate['id'])
                ->update([
                    'no_yudicium' => $nomorYudisium,
                    'periode' => $tahun,
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

    public function getDraft($id)
    {
        $mahasiswa = MhsYud::select()
            ->where('yudicium_id', $id)
            ->get();

        foreach ($mahasiswa as $mhs) {
            $temp = TempStatus::select('status', 'alasan')
                ->where('nim', $mhs->nim)
                ->get();

            $mhs->status = $temp->status;
            $mhs->alasan_status = $temp->alasan;
        }

        return response()->json([
            'success' => true,
            'data' => $mahasiswa,
        ]);
    }

    public function getMahasiswa($id)
    {
        $mahasiswa = MhsYud::select('nim', 'name', 'study_period', 'pass_sks', 'ipk', 'predikat', 'status', 'status_otomatis', 'alasan_status')
            ->where('yudicium_id', $id)
            ->get();

        foreach ($mahasiswa as $mhs) {
            $mhs->predikat = (new MhsYud)->getPredikat($mhs->ipk);
            if (empty($mhs->alasan_status)) {
                $mhs->alasan_status = '-';
            }

            if (empty($mhs->status)) {
                $mhs->status = $mhs->status_otomatis;
            }
        }

        $yudisium = Yudicium::select('approval_status')
            ->where('id', $id)
            ->get();

        return response()->json([
            'success' => true,
            'mahasiswa' => $mahasiswa,
            'yudisium' => $yudisium
        ]);
    }

    public function getAllYudicium()
    {
        $yudicium = DB::table('yudiciums')
            ->join('mhs_yudiciums', 'yudiciums.id', '=', 'mhs_yudiciums.yudicium_id')
            ->select(
                'yudiciums.id as id',
                'yudiciums.no_yudicium as no_yudicium',
                'yudiciums.periode as periode',
                'yudiciums.fakultas_id as fakultas',
                'yudiciums.prodi_id as prodi',
                DB::raw('COUNT(mhs_yudiciums.id) as total_mhs')
            )
            ->where('yudiciums.approval_status', 'Waiting')
            ->groupBy('yudiciums.id', 'yudiciums.no_yudicium')
            ->get();

        $fakultasResponse = Http::withToken($this->token)
            ->get($this->urlFakultas);
        $fakulties = collect($fakultasResponse->successful() ? $fakultasResponse->json() : []);

        $prodyCache = [];

        $yudicium->transform(function ($item) use ($fakulties, &$prodyCache) {
            $faculty = $fakulties->firstWhere('facultyid', $item->fakultas);
            $item->fakultasname = $faculty['facultyname'] ?? 'Unknown';

            $facultyId = $faculty['facultyid'] ?? null;

            if ($facultyId) {
                if (!isset($prodyCache[$facultyId])) {
                    $prodyRes = Http::withToken($this->token)
                        ->get($this->urlProdi . $facultyId);

                    $prodyCache[$facultyId] = collect($prodyRes->successful() ? $prodyRes->json() : []);
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

            return $item;
        });

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

        \Log::info('Fakultas ID' . $validate['fakultas_id']);

        if (empty($validate['fakultas_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yudisium di fakultas ini'
            ]);
        }

        $fakultasResponse = Http::withToken($this->token)
            ->get($this->urlFakultas);

        $yudiciums = DB::table('yudiciums')
            ->join('mhs_yudiciums', 'yudiciums.id', '=', 'mhs_yudiciums.yudicium_id')
            ->select(
                'yudiciums.id as id',
                'yudiciums.no_yudicium as no_yudicium',
                'yudiciums.periode as periode',
                'yudiciums.fakultas_id as fakultas',
                'yudiciums.prodi_id as prodi',
                DB::raw('COUNT(mhs_yudiciums.id) as total_mhs')
            )
            ->where('yudiciums.fakultas_id', $validate['fakultas_id'])
            ->where('yudiciums.approval_status', 'Waiting')
            ->groupBy('yudiciums.id', 'yudiciums.no_yudicium')
            ->get();

        $fakulties = collect($fakultasResponse->successful() ? $fakultasResponse->json() : []);

        $prodyCache = [];

        $yudiciums->transform(function ($item) use ($fakulties, &$prodyCache) {
            $faculty = $fakulties->firstWhere('facultyid', $item->fakultas);
            $item->fakultasname = $faculty['facultyname'] ?? 'Unknown';

            $facultyId = $faculty['facultyid'] ?? null;

            if ($facultyId) {
                if (!isset($prodyCache[$facultyId])) {
                    $prodyRes = Http::withToken($this->token)
                        ->get($this->urlProdi . $facultyId);

                    $prodyCache[$facultyId] = collect($prodyRes->successful() ? $prodyRes->json() : []);
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
                'catatan' => 'string|required',
            ]);

            \Log::info('Request from client side : ', $validate);
            \Log::info('Incoming request:', $request->all());

            $yudisium = Yudicium::where('id', $request->yudisium_id)
                ->update([
                    'approval_status' => $validate['approval_status'],
                    'catatan' => $validate['catatan']
                ]);

            if (!$yudisium) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yudisium not found'
                ], 403);
            }

            $yudisium = Yudicium::find($request->yudisium_id);
            \Log::info('Update status : ' . $yudisium);

            return response()->json([
                'success' => true,
                'data' => $yudisium->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    private function generatePeriodeDropdown()
    {
        $currentYear = date('Y');
        $periodes = [];

        for ($year = $currentYear; $year >= $currentYear - 2; $year--) {
            // Semester Genap: Februari–Juli
            $periodes[] = [
                'value' => "{$year}-02-01",
                'label' => "Genap {$year}/" . ($year + 1),
                'start' => "{$year}-02-01",
                'end' => "{$year}-07-31"
            ];

            // Semester Ganjil: Agustus–Januari
            $periodes[] = [
                'value' => "{$year}-08-01",
                'label' => "Ganjil {$year}/" . ($year + 1),
                'start' => "{$year}-08-01",
                'end' => ($year + 1) . "-01-31"
            ];
        }

        usort($periodes, fn($a, $b) => strcmp($b['value'], $a['value']));
        return $periodes;
    }

    public function edit($id)
    {
        if (!$id) {
            \Log::info('ID not found');
            return response()->json([
                'success' => false,
                'message' => 'ID not found'
            ]);
        }

        $mahasiswa = MhsYud::select('nim', 'name', 'fakultas_id', 'prody_id', 'study_period', 'pass_sks', 'ipk', 'predikat', 'status', 'status_otomatis', 'alasan_status')
            ->where('yudicium_id', $id)
            ->get();
        $nim = $mahasiswa->pluck('nim');

        $result = [];

        $result = collect($mahasiswa ?? [])
            ->map(function ($mhs) {
                $tempStatus = TempStatus::select('status', 'alasan')
                    ->where('nim', $mhs['nim'])
                    ->get();
                $statusFromTemp = $tempStatus->value('status');
                $alasanFromTemp = $tempStatus->value('alasan');

                $finalStatus = !empty($statusFromTemp) ? $statusFromTemp : $mhs['status_otomatis'];

                return [
                    'nim' => $mhs['nim'],
                    'name' => $mhs['name'],
                    'fakultas' => $mhs['fakultas_id'],
                    'prodi' => $mhs['prody_id'],
                    'study_period' => $mhs['study_period'],
                    'pass_sks' => $mhs['pass_sks'],
                    'ipk' => $mhs['ipk'] ?? '-',
                    'predikat' => $mhs['predikat'],
                    'status' => $finalStatus,
                    'alasan_status' => $alasanFromTemp ?? '-'
                ];
            });

        \Log::info('NIM : ', $nim->toArray());
        \Log::info('Mhs Yud : ', $mahasiswa->toArray());
        \Log::info('Result : ', $result->toArray());

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
