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
            $fakultasId = $request->fakultas;
            $prodiId = $request->prodi;
            $periode = $request->periode;

            // Pastikan wajib pilih Fakultas dan Program Studi
            if (empty($fakultasId) || empty($prodiId)) {
                return response()->json([
                    'success' => true,
                    'source' => 'none',
                    'mahasiswa' => []
                ], 200);
            }

            \Log::info("Filter:", [
                'fakultas' => $fakultasId,
                'prodi' => $prodiId,
                'semester' => $periode
            ]);

            // API stt=7: Panggil tanpa parameter (API akan return semua data)
            // Kita akan filter di backend
            \Log::info('Calling API stt=7 (no params)', [
                'url' => $this->url
            ]);
            
            $response = Http::timeout(15)->get($this->url);

            \Log::info('API Response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'raw_body' => $response->body(),
                'data_count' => is_array($response->json()) ? count($response->json()) : 0
            ]);

            $data = $response->json();
            $mahasiswa = [];
            $source = 'database'; // default fallback

            if ($response->successful() && !empty($data) && is_array($data)) {
                $source = 'api';
                
                \Log::info('API Response received', [
                    'total_from_api' => count($data),
                    'prodi' => $prodiId
                ]);
                
                // Ambil daftar NIM yang sudah ada di mhs_yudiciums (semua, tidak peduli status atau prodi)
                $existingNims = MhsYud::pluck('nim')->toArray();
                
                \Log::info('Existing NIMs in mhs_yudiciums (all)', [
                    'count' => count($existingNims)
                ]);
                
                // Cross-check dengan API stt=10 untuk mendapatkan daftar NIM yang sudah selected
                $selectedInApiStt10 = [];
                try {
                    \Log::info('Cross-checking with API stt=10 to get selected NIMs');
                    
                    // Panggil API stt=10 untuk prodi ini dengan tanggal hari ini
                    $urlPickAcademic = trim(env('URL_PICK_ACADEMIC'), " '\"");
                    $currentDate = date('Y-m-d');
                    $apiUrlStt10 = str_replace(['IDPRODI', 'TANGGAL'], [$prodiId, $currentDate], $urlPickAcademic);
                    
                    $responseStt10 = Http::timeout(10)->get($apiUrlStt10);
                    
                    if ($responseStt10->successful()) {
                        $dataStt10 = $responseStt10->json();
                        if (!empty($dataStt10) && is_array($dataStt10)) {
                            $selectedInApiStt10 = array_column($dataStt10, 'STUDENTID');
                            \Log::info('Found selected NIMs in API stt=10', [
                                'count' => count($selectedInApiStt10),
                                'sample' => array_slice($selectedInApiStt10, 0, 5)
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to check API stt=10', [
                        'error' => $e->getMessage()
                    ]);
                }
                
                $beforeFilter = count($data);
                
                // Untuk debugging: sample data sebelum filter
                $sampleBeforeFilter = array_slice($data, 0, 3);
                \Log::info('Sample data before filter', [
                    'sample' => array_map(function($m) {
                        return [
                            'nim' => $m['STUDENTID'],
                            'name' => $m['FULLNAME'],
                            'selected' => $m['SELECTED'] ?? 'NULL',
                            'periode' => $m['PERIODE'] ?? 'NULL'
                        ];
                    }, $sampleBeforeFilter)
                ]);
                
                $mahasiswa = collect($data ?? [])
                ->filter(function($mhs) use ($prodiId, $periode, $existingNims, $selectedInApiStt10) {
                    // Filter 1: Cek prodi
                    $matchProdi = empty($prodiId) || $mhs['STUDYPROGRAMID'] == $prodiId;
                    
                    // Filter 2: HANYA tampilkan mahasiswa yang SELECTED benar-benar NULL (bukan 'Y', bukan 'N')
                    $valSelected = $mhs['SELECTED'] ?? null;
                    $valPeriode  = $mhs['PERIODE'] ?? null;

                    // SELECTED harus benar-benar null atau string 'null' atau 'NULL' atau empty string
                    // TIDAK boleh 'Y' atau 'N'
                    $isSelectedNull = (
                        is_null($valSelected) || 
                        $valSelected === 'null' || 
                        $valSelected === 'NULL' || 
                        $valSelected === ''
                    );
                    
                    // PERIODE juga harus null atau empty
                    $isPeriodeNull = (
                        is_null($valPeriode) || 
                        $valPeriode === 'null' || 
                        $valPeriode === 'NULL' || 
                        $valPeriode === ''
                    );
                    
                    // Keduanya harus null
                    $notSelectedInApiStt7 = $isSelectedNull && $isPeriodeNull;
                    
                    // Filter 3: Cross-check dengan API stt=10 - exclude jika NIM ada di stt=10
                    $notInApiStt10 = !in_array($mhs['STUDENTID'], $selectedInApiStt10);
                    
                    // Filter 4: Exclude mahasiswa yang sudah ada di mhs_yudiciums (database lokal)
                    $notInMhsYud = !in_array($mhs['STUDENTID'], $existingNims);
                    
                    // Mahasiswa ditampilkan jika:
                    // 1. Match prodi
                    // 2. SELECTED = NULL di API stt=7
                    // 3. PERIODE = NULL di API stt=7
                    // 4. TIDAK ada di API stt=10 (belum selected)
                    // 5. Belum ada di database lokal (mhs_yudiciums)
                    return $matchProdi && $notSelectedInApiStt7 && $notInApiStt10 && $notInMhsYud;
                })
                ->map(function ($mhs) {
                    $tempStatus = TempStatus::select('status', 'alasan')
                        ->where('nim', $mhs['STUDENTID']);

                    $statusFromTemp = $tempStatus->value('status');
                    
                    // Generate predikat dari fungsi
                    $predikat = (new MhsYud)->getPredikat($mhs['GPA']);
                    
                    // Generate status dari fungsi hitungStatus jika ada di API
                    // Extract numeric value from "10 Semester" format
                    $studyPeriod = 0;
                    if (isset($mhs['MASA_STUDI']) && preg_match('/(\d+)/', $mhs['MASA_STUDI'], $matches)) {
                        $studyPeriod = (int)$matches[1];
                    }
                    
                    $mahasiswaModel = new \App\Models\Mahasiswa();
                    $computedStatus = $mahasiswaModel->hitungStatus(
                        $studyPeriod, 
                        (int)($mhs['PASS_CREDIT'] ?? 0), 
                        (float)($mhs['GPA'] ?? 0),
                        (int)($mhs['STUDYPROGRAMID'] ?? null)
                    );
                    
                    // Prioritas: temp_status > computed status
                    $statusFromApi = ucfirst(strtolower($mhs['STATUS']));
                    $finalStatus = !empty($statusFromTemp) ? $statusFromTemp : $computedStatus;

                    return [
                        'nim' => $mhs['STUDENTID'] ?? '-',
                        'name' => $mhs['FULLNAME'] ?? '-',
                        'study_period' => $mhs['MASA_STUDI'] ?? '-',
                        'sks_lulus' => $mhs['PASS_CREDIT'] ?? '-',
                        'ipk' => $mhs['GPA'] ?? '-',
                        'predikat' => $predikat,
                        'status' => $finalStatus,
                        
                        // Ekstra jika dibutuhkan js
                        'fakultas' => $mhs['FACULTYNAME'] ?? '-',
                        'prodi' => $mhs['STUDYPROGRAMNAME'] ?? '-',
                        'SMT_CURRENT' => $mhs['SMT_CURRENT'] ?? '-',
                        'alasan_status' => $tempStatus->value('alasan') ?? '-',
                        'source' => 'api' // Tambahkan source
                    ];
                })
                    ->toArray();
                    
                \Log::info('After filtering', [
                    'before_filter' => $beforeFilter,
                    'after_filter' => count($mahasiswa),
                    'source' => 'api',
                    'filtered_out' => $beforeFilter - count($mahasiswa),
                    'excluded_in_mhs_yud' => count($existingNims),
                    'excluded_in_api_stt10' => count($selectedInApiStt10),
                    'reason' => 'Excluded: already in mhs_yudiciums OR selected in API stt=7 OR found in API stt=10'
                ]);
            } else {
                \Log::warning('API failed or empty, using database fallback', [
                    'success' => $response->successful(),
                    'has_data' => !empty($data),
                    'is_array' => is_array($data)
                ]);
            }

            // Fallback ke database HANYA jika API benar-benar gagal atau tidak ada data
            if ($source === 'database') {
                
                // Alur API: Menampilkan mahasiswa eligible yang BELUM ada di mhs_yudiciums (semua, tidak peduli status atau prodi)
                $dbData = DB::table('mahasiswa')
                    ->when($fakultasId, fn($q) => $q->where('FACULTYID', $fakultasId))
                    ->when($prodiId, fn($q) => $q->where('STUDYPROGRAMID', $prodiId))
                    // Filter mahasiswa yang belum ada di tabel mhs_yudiciums
                    ->whereNotIn('STUDENTID', function($query) {
                        $query->select('nim')->from('mhs_yudiciums');
                    })
                    ->select(
                        'STUDENTID as nim',
                        'FULLNAME as name',
                        'MASA_STUDI as study_period',
                        'PASS_CREDIT as sks_lulus',
                        'GPA as ipk'
                    )
                    ->get();

                $mahasiswa = collect($dbData)->map(function($mhs) use ($prodiId) {
                    $tempStatus = TempStatus::select('status', 'alasan')
                        ->where('nim', $mhs->nim);
                    $statusFromTemp = $tempStatus->value('status');
                    
                    // Generate predikat dari fungsi
                    $predikat = (new MhsYud)->getPredikat($mhs->ipk);
                    
                    // Generate status dari fungsi hitungStatus
                    // Extract numeric value from "10 Semester" format
                    $studyPeriod = 0;
                    if (preg_match('/(\d+)/', $mhs->study_period, $matches)) {
                        $studyPeriod = (int)$matches[1];
                    }
                    
                    $mahasiswaModel = new \App\Models\Mahasiswa();
                    $computedStatus = $mahasiswaModel->hitungStatus(
                        $studyPeriod, 
                        (int)$mhs->sks_lulus, 
                        (float)$mhs->ipk,
                        (int)$prodiId
                    );
                    
                    // Prioritas: temp_status > computed status
                    $status = $statusFromTemp ?: $computedStatus;
                    
                    return [
                        'nim' => $mhs->nim,
                        'name' => $mhs->name,
                        'study_period' => $mhs->study_period,
                        'sks_lulus' => $mhs->sks_lulus,
                        'ipk' => $mhs->ipk,
                        'predikat' => $predikat,
                        'status' => $status,
                        
                        // Ekstra jika dibutuhkan js
                        'fakultas' => '-', 
                        'prodi' => '-',
                        'SMT_CURRENT' => '-', // Dummy karena di DB mahasiswa tidak ada SMT_CURRENT
                        'alasan_status' => $tempStatus->value('alasan') ?? '-',
                        'source' => 'database' // Tambahkan source
                    ];
                })->toArray();
            }

            \Log::info("Source: " . $source);

            // Format ulang agar tidak index asosiatif (opsional tapi aman)
            $mahasiswa = array_values($mahasiswa);

            return response()->json([
                'success' => true,
                'source' => $source,
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
