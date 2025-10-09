<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Yudicium;
use App\Models\MhsYud;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

class YudiciumController extends Controller
{
    public $url;

    public function __construct()
    {
        $this->token = env('KEY_TOKEN');
        $this->url = 'https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=7';
    }

    public function index(Request $request)
    {
        $datas = DB::table('yudiciums')->get();

        $routeName = $request->route()->getName();
        $postCount = Post::count();
        $totalMhsYud = MhsYud::count();

        // Ambil data fakultas dari API
        $facultyRes = Http::withToken($this->token)
            ->get('https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69');
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
                            ->get("https://gateway.telkomuniversity.ac.id/b2ac79622cd60bce8dc5a1a7171bfc9c/{$facultyId}");

                        $prodyCache[$facultyId] = $prodyRes->successful() ? collect($prodyRes->json()) : collect();
                    }

                    $prody = $prodyCache[$facultyId];
                    if ($prody->isNotEmpty()) {
                        $program = $prody->firstWhere('studyprogramid', (string) $item->prodi_id);
                        $item->prodyname = $program['studyprogramname'] ?? 'Unknown';
                    } else {
                        $item->prodyname = 'Unknown';
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
                'status' => 'string|nullable',
                'alasan' => 'string|nullable'
            ]);

            $lastId = Yudicium::max('id');
            $nextId = $lastId ? $lastId + 1 : 1;
            $mappingFaculties = [3 => 'IT', 4 => 'IK', 5 => 'TE', 6 => 'RI', 7 => 'IF', 8 => 'EB', 9 => 'KB', 10 => 'SBY', 11 => 'PWT'];
            $fakultasInitial = $mappingFaculties[$validate['fakultas_id']] ?? 'XX';
            $tahun = date('Y');
            $nomorYudisium = $nextId . '/AKD100/' . $fakultasInitial . '/' . $tahun;

            // Consume data api academic
            $prodi_id = $validate['prodi_id'];
            $url = $this->url .  '&id=' . $prodi_id;

            $response = Http::get($url);
            \Log::info('Response : ' . $response->body());
            $listMahasiswa = $response->json();
            \Log::info('List Mahasiswa : ', $listMahasiswa);

            // Cek dlu buat status yang bakal di pakai
            $finalStatus = !empty($validate['status']) ? $validate['status'] : ucfirst(strtolower($listMahasiswa[0]['STATUS']));

            \Log::info('Nomor Yudisium : ' . $nomorYudisium);
            \Log::info('Periode : ' . $tahun);
            \Log::info('Map Fakultas : ', $mappingFaculties);
            \Log::info('Fakultas id : ' . $validate['fakultas_id']);

            if ($finalStatus === 'Tidak Eligible') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada mahasiswa yang Eligible,Silahkan coba lagi.'
                ], 403);
            }

            if ($finalStatus === 'Eligible') {

                // Buat untuk row yudicium terlebih dahulu
                $yudicium = Yudicium::create([
                    'fakultas_id' => $validate['fakultas_id'],
                    'prodi_id' => $validate['prodi_id'],
                    'periode' => $tahun,
                    'no_yudicium' => $nomorYudisium
                ]);

                // Baru data mahasiswa setiap mahasiswa yang ikut yudicium
                $eligibleMhs = [];
                foreach ($listMahasiswa as $mhs) {
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
                            'status' => $validate['status'] ?? null,
                            'predikat' => (new MhsYud)->getPredikat($mhs['GPA']),
                            'alasan_status' => $validate['alasan'],
                            'yudicium_id' => $yudicium->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (!empty($eligibleMhs)) {
                    MhsYud::insert($eligibleMhs);
                }

                \Log::info('Yudicium : ', $yudicium->toArray());
            }
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

        return response()->json([
            'success' => true,
            'mahasiswa' => $mahasiswa
        ]);
    }
}
