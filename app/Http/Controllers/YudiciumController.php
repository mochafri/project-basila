<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Yudicium;
use App\Models\MhsYud;
use App\Models\TempStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use Symfony\Component\Yaml\Yaml;

class YudiciumController extends Controller
{
    public $url;

    public function __construct()
    {
        $this->token = env('KEY_TOKEN');
        $this->url = env('URL_ACADEMIC');
        $this-> urlFakultas = env('URL_FACULTY');
        $this-> urlProdi = env('URL_PRODY');
    }

    public function index(Request $request)
    {
        $datas = DB::table('yudiciums')->get();

        $routeName = $request->route()->getName();
        $postCount = Post::count();
        $totalMhsYud = MhsYud::count();

        // Ambil data fakultas dari API
        $facultyRes = Http::withToken($this->token)
            ->get($this->urlFakultas);
        $faculties = $facultyRes->successful() ? collect($facultyRes->json()) : collect();

        $prodyCache = [];

        // === ROUTE: index (Dashboard utama) ===
        if ($routeName === 'index' || $routeName === 'index4' || $routeName === 'index6') {

            // ===============================
            // 1️⃣ Hitung jumlah per predikat
            // ===============================
            $predikatCounts = MhsYud::select('predikat', DB::raw('COUNT(*) as total'))
                ->groupBy('predikat')
                ->get();

            $predikatList = [
                'Istimewa (Summa Cumlaude)',
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
            // 2️⃣ Hitung jumlah yudisium per fakultas
            // ===============================
            $fakultasCounts = MhsYud::select('fakultas_id', DB::raw('COUNT(*) as total'))
                ->groupBy('fakultas_id')
                ->get();

            $countApproval = Yudicium::where('approval_status', 'Waiting')->count();

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

            // // Urutkan fakultas berdasarkan jumlah terbanyak
            // usort($dataFakultas, fn($a, $b) => $b['jumlah'] <=> $a['jumlah']);

            // ===============================
            // Return ke view dashboard
            // ===============================
            return view("dashboard.$routeName", [
                'datas' => $datas,
                'postCount' => $postCount,
                'totalMhsYud' => $totalMhsYud,
                'dataPredikat' => $dataPredikat,
                'dataFakultas' => $dataFakultas,
                'countApproval' => $countApproval,
            ]);
        }

        // === ROUTE: index2 (data mahasiswa dengan fakultas/prodi) ===
        elseif ($routeName === 'index2') {

            $datas->transform(function ($item) use ($faculties, &$prodyCache) {

                // Cocokkan fakultas dari API
                $faculty = $faculties->firstWhere('facultyid', $item->fakultas_id);
                $item->facultyname = $faculty['facultyname'] ?? 'Unknown';
                $facultyId = $faculty['facultyid'] ?? null;

                if ($facultyId) {
                    // Cache agar tidak panggil API berulang
                    if (!isset($prodyCache[$facultyId])) {
                        $prodyRes = Http::withToken(env('KEY_TOKEN'))
                            ->get($this->urlProdi . $facultyId);

                        $prodyCache[$facultyId] = $prodyRes->successful() ? collect($prodyRes->json()) : collect();
                    }

                    $prody = $prodyCache[$facultyId];
                    if ($prody->isNotEmpty()) {
                        $program = $prody->firstWhere('studyprogramid', (string) $item->prodi_id);
                        $item->prodyname = $program['studyprogramname'] ?? 'Unknown';
                    }
                } else {
                    $item->prodyname = 'Unknown';
                }

                return $item;
            });

            return view("dashboard.$routeName", [
                'datas' => $datas,
                'postCount' => $postCount,
                'totalMhsYud' => $totalMhsYud,
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

    public function generateCode(Request $request)
    {
        try {
            $validate = $request->validate([
                'fakultas_id' => 'required|integer',
                'prodi_id' => 'required|integer',
            ]);

            // Consume data api academic
            $prodi_id = $validate['prodi_id'];
            $url = $this->url . '&id=' . $prodi_id;

            $response = Http::get($url);
            \Log::info('Response : ' . $response->body());
            $listMahasiswa = $response->json();
            \Log::info('List Mahasiswa : ', $listMahasiswa);

            // Baru data mahasiswa setiap mahasiswa yang ikut yudicium
            $eligibleMhs = [];

            foreach ($listMahasiswa as $mhs) {
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
                    ];
                }
            }

            \Log::info('Eligible Mhs : ', $eligibleMhs);

            if (empty($eligibleMhs)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada mahasiswa yang Eligible, silahkan coba lagi.'
                ], 403);
            }

            $lastId = Yudicium::max('id');
            $nextId = $lastId ? $lastId + 1 : 1;
            $mappingFaculties = [3 => 'IT', 4 => 'IK', 5 => 'TE', 6 => 'RI', 7 => 'IF', 8 => 'EB', 9 => 'KB', 10 => 'SBY', 11 => 'PWT'];
            $fakultasInitial = $mappingFaculties[$validate['fakultas_id']] ?? 'XX';
            $tahun = date('Y');
            $nomorYudisium = $nextId . '/AKD100/' . $fakultasInitial . '/' . $tahun;

            \Log::info('Nomor Yudisium : ' . $nomorYudisium);
            \Log::info('Periode : ' . $tahun);
            \Log::info('Map Fakultas : ', $mappingFaculties);
            \Log::info('Fakultas id : ' . $validate['fakultas_id']);

            $yudicium = Yudicium::create([
                'fakultas_id' => $validate['fakultas_id'],
                'prodi_id' => $validate['prodi_id'],
                'periode' => $tahun,
                'no_yudicium' => $nomorYudisium
            ]);

            foreach ($eligibleMhs as &$mhs) {
                $mhs['yudicium_id'] = $yudicium->id;
            }
            unset($mhs);

            if (!empty($eligibleMhs)) {
                MhsYud::insert($eligibleMhs);
            }

            \Log::info('Yudicium : ', $yudicium->toArray());

            // return redirect()->back()->with('success', 'Yudicium berhasil ditetapkan');

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
                }
            } else {
                $item->prodiname = 'Unknown';
            }

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $yudiciums
        ],200);
    }

    public function updateStatus(Request $request)
    {
        try{

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

            if(!$yudisium){
                return response()->json([
                    'success' => false,
                    'message' => 'Yudisium not found'
                ],403);
            }

            $yudisium = Yudicium::find($request->yudisium_id);
            \Log::info('Update status : '. $yudisium);

            return response()->json([
                'success' => true,
                'data' => $yudisium->fresh()
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ],500);
        }
    }
}
