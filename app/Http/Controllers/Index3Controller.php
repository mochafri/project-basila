<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\MhsYud;
use App\Models\Mahasiswa;
use App\Models\TempStatus;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class Index3Controller extends Controller
{
    public function __construct()
    {
        $this->url = trim(env('URL_ACADEMIC'), " '\"");
    }

    public function index(Request $request)
    {
        // cek route name
        $routeName = $request->route()->getName();

        $token = session('token');

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

        // ambil data fakultas dari API
        // $response = Http::withToken($token)
        //     ->get('https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69');
        // $faculties = $response->successful() ? $response->json() : [];

        // ambil data mahasiswa
        // $mahasiswa = Mahasiswa::all();
        // foreach ($mahasiswa as $mhs) {
        //     $mhs->save(); // trigger booted()
        // }

        // Ambil kode jika ada hasil generate
        $kode = session('kode');

        $postCount = Post::count();


        if ($routeName === 'index3' || $routeName === 'index4'|| $routeName === 'index6') {
            return view("dashboard.$routeName", compact('kode', 'postCount', 'periodes', 'periode'));
        }
    }

    public function filterMhs(Request $request)
    {
        try {
            $prodiId = $request->prodi;
            // stt=7 requires a periode parameter to return selection info (SELECTED/PERIODE)
            $url = $this->url . '&periode=' . date('Y-m-d') . '&t=' . time();
            $response = Http::get($url);
            $data = $response->json();

            $mahasiswa = [];
            $periode = $request->periode;
            $smtFilter = $request->periode; // Use the dropdown value directly if it's a semester code (e.g. 20252)

            if ($response->successful()) {
                $mahasiswa = collect($data ?? [])
                ->filter(function($mhs) use ($prodiId, $smtFilter) {
                    $matchProdi   = $mhs['STUDYPROGRAMID'] == $prodiId;
                    
                    // Hanya tampilkan mahasiswa yang belum dipilih (SELECTED dan PERIODE harus kosong/null)
                    $valSelected = $mhs['SELECTED'] ?? null;
                    $valPeriode  = $mhs['PERIODE'] ?? null;

                    $notSelected = (is_null($valSelected) || $valSelected === 'null' || $valSelected === 'NULL' || $valSelected === 'N' || empty($valSelected)) &&
                                   (is_null($valPeriode) || $valPeriode === 'null' || $valPeriode === 'NULL' || empty($valPeriode));
                    
                    return $matchProdi && $notSelected;
                })
                ->map(function ($mhs) {
                    $tempStatus = TempStatus::select('status', 'alasan')
                        ->where('nim', $mhs['STUDENTID']);

                        $statusFromTemp = $tempStatus->value('status');
                        $alasanFromTemp = $tempStatus->value('alasan');

                        $statusFromApi = ucfirst(strtolower($mhs['STATUS']));

                        return [
                            'fakultas' => $mhs['FACULTYNAME'] ?? '-',
                            'prodi' => $mhs['STUDYPROGRAMNAME'] ?? '-',
                            'nim' => $mhs['STUDENTID'] ?? '-',
                            'name' => $mhs['FULLNAME'] ?? '-',
                            'study_period' => $mhs['MASA_STUDI'] ?? '-',
                            'pass_sks' => $mhs['PASS_CREDIT'] ?? '-',
                            'ipk' => $mhs['GPA'] ?? '-',
                            'predikat' => (new MhsYud)->getPredikat($mhs['GPA']),
                            'status' => !empty($statusFromTemp) ? $statusFromTemp : $statusFromApi,
                            'alasan_status' => !empty($alasanFromTemp) ? $alasanFromTemp : '-',
                            'SMT_CURRENT' => $mhs['SMT_CURRENT'] ?? '-',
                        ];
                    })
                    ->toArray();

                \Log::info('Data mahasiswa', $mahasiswa);
            }

            return response()->json([
                'success' => true,
                'mahasiswa' => $mahasiswa
            ], 200);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }
    
    public function getSemesters($prodiId)
    {
        try {
            $url = $this->url . '&id=' . $prodiId;
            $response = Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                $semesters = collect($data ?? [])
                    ->filter(fn($mhs) => $mhs['STUDYPROGRAMID'] == $prodiId)
                    ->pluck('SMT_CURRENT')
                    ->unique()
                    ->sortDesc()
                    ->map(function ($smt) {
                        if (empty($smt) || $smt == '-') return null;
                        
                        $year = substr($smt, 0, 4);
                        $term = substr($smt, 4, 1);
                        $label = ($term == '1' ? 'Ganjil' : ($term == '2' ? 'Genap' : 'Semester ' . $term)) . " {$year}/" . ($year + 1);
                        return [
                            'value' => $smt,
                            'label' => $label
                        ];
                    })
                    ->filter()
                    ->values();

                return response()->json([
                    'success' => true,
                    'semesters' => $semesters
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function generatePeriodeDropdown()
    {
        return app(\App\Services\YudiciumService::class)->generatePeriodeDropdown();
    }
}
