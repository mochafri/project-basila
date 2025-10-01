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
    public function __construct()
    {
        $this->token = env('KEY_TOKEN');
    }

    public function index(Request $request)
    {
        $datas = DB::table('yudiciums')->get();

        $routeName = $request->route()->getName();
        $postCount = Post::count();

        $facultyRes = Http::withToken($this->token)
            ->get('https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69');
        $faculties = $facultyRes->successful() ? collect($facultyRes->json()) : collect();

        $prodyCache = [];

        if ($routeName === 'index' || $routeName === 'index4' || $routeName === 'index6') {
            return view("dashboard.$routeName", [
                'datas' => $datas,
                'postCount' => $postCount
            ]);
        } elseif ($routeName === 'index2') {

            $datas->transform(function ($item) use ($faculties, &$prodyCache) {

                $faculty = $faculties->firstWhere('facultyid', $item->fakultas);
                $item->facultyname = $faculty['facultyname'] ?? 'Unknown';
                $facultyId = $faculty['facultyid'] ?? null;

                if ($facultyId) {
                    if (!isset($prodyCache[$facultyId])) {
                        $prodyRes = Http::withToken(env('KEY_TOKEN'))
                            ->get("https://gateway.telkomuniversity.ac.id/b2ac79622cd60bce8dc5a1a7171bfc9c/{$facultyId}");

                        $prodyCache[$facultyId] = $prodyRes->successful() ? collect($prodyRes->json()) : collect();
                    }

                    $prody = $prodyCache[$facultyId];
                    
                    if ($prody->isNotEmpty()) {
                        $program = $prody->firstWhere('studyprogramid', (string) $item->prodi);
                        $item->prodyname = $program['studyprogramname'] ?? 'Unknown';
                    } else {
                        $item->prodyname = 'Unknown';
                    }
                }
                return $item;
            });

            return view("dashboard.$routeName", [
                'datas' => $datas,
                'postCount' => $postCount
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
                'fakultas' => 'required|integer',
                'prodi' => 'required|integer',
            ]);

            $lastId = Yudicium::max('id');
            $nextId = $lastId ? $lastId + 1 : 1;
            $mappingFaculties = [3 => 'IT', 4 => 'IK', 5 => 'TE', 6 => 'RI', 7 => 'IF', 8 => 'EB', 9 => 'KB', 10 => 'SBY', 11 => 'PWT'];
            $fakultasInitial = $mappingFaculties[$validate['fakultas']] ?? 'XX';
            $tahun = date('Y');
            $nomorYudisium = $nextId . '/AKD100/' . $fakultasInitial . '/' . $tahun;

            $yudicium = Yudicium::create([
                'fakultas' => $validate['fakultas'],
                'prodi' => $validate['prodi'],
                'periode' => $tahun,
                'no_yudicium' => $nomorYudisium
            ]);

            $listMahasiswa = Mahasiswa::where('fakultas_id', $validate['fakultas'])
                ->where('prody_id', $validate['prodi'])
                ->get();

            $eligibleMhs = [];

            foreach ($listMahasiswa as $mhs) {
                $status = (new Mahasiswa)->hitungStatus($mhs->study_period, $mhs->pass_sks, $mhs->ipk);

                if ($status === "Eligible") {
                    $eligibleMhs[] = [
                        'nim' => $mhs->nim,
                        'fakultas_id' => $mhs->fakultas_id,
                        'prody_id' => $mhs->prody_id,
                        'name' => $mhs->name,
                        'study_period' => $mhs->study_period,
                        'pass_sks' => $mhs->pass_sks,
                        'ipk' => $mhs->ipk,
                        'status' => $mhs->status,
                        'yudicium_id' => $yudicium->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($eligibleMhs)) {
                MhsYud::insert($eligibleMhs);
            }

            // return redirect()->back()->with('success', 'Yudicium berhasil ditetapkan');

            return response()->json([
                'success' => true,
                'nomor_yudisium' => $nomorYudisium
            ]);
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
        $mahasiswa = MhsYud::select('nim', 'name', 'study_period', 'pass_sks', 'ipk', 'predikat', 'status')
            ->where('yudicium_id', $id)
            ->get();

        foreach ($mahasiswa as $mhs) {
            $mhs->predikat = (new MhsYud)->hitungPredikat($mhs->ipk);
        }

        return response()->json([
            'success' => true,
            'mahasiswa' => $mahasiswa
        ]);
    }
}
