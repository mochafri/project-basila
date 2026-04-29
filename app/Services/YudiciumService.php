<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YudiciumService
{
    protected $token;
    protected $urlAcademic;
    protected $urlFaculty;
    protected $urlPrody;
    protected $urlSetAcademic;
    protected $urlAllAcademic;
    protected $urlResetAcademic;
    protected $urlPickAcademic;

    public function __construct()
    {
        $this->token = env('KEY_TOKEN');
        $this->urlAcademic = env('URL_ACADEMIC');
        $this->urlFaculty = env('URL_FACULTY');
        $this->urlPrody = env('URL_PRODY');
        $this->urlSetAcademic = trim(env('URL_SET_ACADEMIC'), " '\"");
        $this->urlAllAcademic = trim(env('URL_ALL_ACADEMIC'), " '\"");
        $this->urlPickAcademic = trim(env('URL_PICK_ACADEMIC'), " '\"");
        $this->urlResetAcademic = trim(env('URL_RESET_ACADEMIC'), " '\"");
    }

    public function getFaculties()
    {
        $response = Http::withToken($this->token)->get($this->urlFaculty);
        return $response->successful() ? collect($response->json()) : collect();
    }

    public function getPrody($facultyId)
    {
        $response = Http::withToken($this->token)->get($this->urlPrody . $facultyId);
        return $response->successful() ? collect($response->json()) : collect();
    }

    public function getAcademicData($prodiId)
    {
        $url = $this->urlAcademic . '&id=' . $prodiId;
        $response = Http::get($url);
        return $response->successful() ? $response->json() : [];
    }

    public function setAcademicStatus($nim, $date = '', $selected = 'Y')
    {
        // Persiapkan URL dengan mengganti placeholder dari .env
        $apiUrl = str_replace('NIM', $nim, $this->urlSetAcademic);
        $apiUrl = str_replace('TANGGAL', $date, $apiUrl);
        $apiUrl = str_replace('SELECTED', $selected, $apiUrl);


        try {
            // Mekanisme Retry: Coba 3x dengan jeda 100ms jika ada masalah koneksi
            $response = Http::retry(3, 100)->get($apiUrl);
            
            $status = $response->status();
            $body   = trim($response->body());


            return $response;
        } catch (\Exception $e) {
            Log::error("[API stt=8] Critical error during API call.", [
                'nim'   => $nim,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function resetAcademicStatus($nim, $periode = null)
    {
        // Jika periode tidak dikirim, gunakan tanggal hari ini sebagai fallback
        $periode = $periode ?: date('Y-m-d');

        // Persiapkan URL dengan mengganti placeholder NIM dan TANGGAL
        $apiUrl = str_replace(['NIM', 'TANGGAL'], [$nim, $periode], $this->urlResetAcademic);
        
        \Log::info("[API stt=11] Resetting NIM $nim with URL: $apiUrl");

        try {
            // Gunakan timeout 10 detik agar tidak "gantung" terlalu lama
            $response = Http::timeout(10)->get($apiUrl);
            
            // API ini sering mengembalikan string "true" atau "1" secara literal
            $body = strtolower(trim($response->body()));
            return ($body === 'true' || $body === '1');
            
        } catch (\Exception $e) {
            Log::error("[API stt=11] Critical error during reset API call.", [
                'nim'     => $nim,
                'periode' => $periode,
                'error'   => $e->getMessage()
            ]);
            // Jangan lempar exception agar proses hapus lokal di controller tetap lanjut
            return false;
        }
    }

    public function getAllAcademicData($prodiId, $tanggal)
    {
        $apiUrl = str_replace(['IDPRODI', 'TANGGAL'], [$prodiId, $tanggal], $this->urlAllAcademic);
        $response = Http::get($apiUrl);
        return $response->successful() ? $response->json() : [];
    }

    public function getPickAcademicData($prodiId, $tanggal)
    {
        $apiUrl = str_replace(['IDPRODI', 'TANGGAL'], [$prodiId, $tanggal], $this->urlPickAcademic);
        $response = Http::get($apiUrl);
        return $response->successful() ? $response->json() : [];
    }

    public function generatePeriodeDropdown()
    {
        $currentMonth = (int)date('n');
        $currentYear = (int)date('Y');
        
        // Tentukan "Tahun Awal" dari Tahun Ajaran berjalan
        // Jika bulan >= 8 (Agustus), maka kita ada di awal TA tahun ini.
        // Jika bulan < 8, maka kita ada di TA yang dimulai tahun lalu.
        $baseYear = ($currentMonth >= 8) ? $currentYear : $currentYear - 1;

        $periodes = [];

        // Kita tampilkan TA Depan (+1), TA Sekarang (0), dan TA Kemarin (-1)
        for ($i = 1; $i >= -1; $i--) {
            $year = $baseYear + $i;

            // Genap (Februari - Juli di tahun berikutnya dari awal TA)
            $periodes[] = [
                'value' => ($year + 1) . "-02-01",
                'label' => __('dashboard.even') . " {$year}/" . ($year + 1),
                'start' => ($year + 1) . "-02-01",
                'end' => ($year + 1) . "-07-31",
                'order' => 1
            ];

            // Ganjil (Agustus - Januari di tahun berikutnya dari awal TA)
            $periodes[] = [
                'value' => "{$year}-08-01",
                'label' => __('dashboard.odd') . " {$year}/" . ($year + 1),
                'start' => "{$year}-08-01",
                'end' => ($year + 1) . "-01-31",
                'order' => 2
            ];
        }

        // Urutkan agar yang terbaru ada di paling atas
        usort($periodes, function ($a, $b) {
            return strcmp($b['start'], $a['start']);
        });

        return $periodes;
    }

    public function getSemesterLabel($periode)
    {
        if (!$periode) {
            return 'Belum ditetapkan';
        }

        $bulan = date('m', strtotime($periode));
        $tahun = date('Y', strtotime($periode));

        if ($bulan >= 2 && $bulan <= 7) {
            return __('dashboard.even') . " {$tahun}/" . ($tahun + 1);
        }

        return __('dashboard.odd') . " {$tahun}/" . ($tahun + 1);
    }

    public function getFakultasInitial($facultyId)
    {
        $mappingFaculties = [
            3 => 'IT', 4 => 'IK', 5 => 'TE', 6 => 'RI', 
            7 => 'IF', 8 => 'EB', 9 => 'KB', 10 => 'SBY', 11 => 'PWT'
        ];
        return $mappingFaculties[$facultyId] ?? 'XX';
    }

    public function getWaitingYudiciums()
    {
        $yudicium = DB::table('yudiciums')
            ->select(
                'yudiciums.id as id',
                'yudiciums.no_yudicium as no_yudicium',
                'yudiciums.periode as periode',
                'yudiciums.fakultas_id as fakultas',
                'yudiciums.prodi_id as prodi',
                DB::raw('(SELECT COUNT(*) FROM mhs_yudiciums WHERE mhs_yudiciums.yudicium_id = yudiciums.id) as total_mhs')
            )
            ->where('yudiciums.approval_status', 'Waiting')
            ->get();

        $fakulties = $this->getFaculties();
        $prodyCache = [];

        $yudicium->transform(function ($item) use ($fakulties, &$prodyCache) {
            $faculty = $fakulties->firstWhere('facultyid', $item->fakultas);
            $item->fakultasname = $faculty['facultyname'] ?? 'Unknown';
            $facultyId = $faculty['facultyid'] ?? null;

            if ($facultyId) {
                if (!isset($prodyCache[$facultyId])) {
                    $prodyCache[$facultyId] = $this->getPrody($facultyId);
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
            $listSelected = $this->getSelectedAcademicData($item->prodi, $item->periode);
            $item->total_mhs = count($listSelected);

            return $item;
        });

        return $yudicium;
    }
    public function getSelectedAcademicData($prodiId, $tanggal)
    {
        $apiUrl = str_replace(['IDPRODI', 'TANGGAL'], [$prodiId, $tanggal], $this->urlPickAcademic);
        

        try {
            $response = Http::retry(3, 100)->get($apiUrl);
            $data = $response->json();


            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            Log::error("[API stt=10] Critical error fetching selected students.", [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function generateNoSk($id, $fakultasInitial)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $months[(int)date('n')];
        $year = date('Y');
        
        // Format ID jadi 2 digit (misal 6 jadi 06)
        $formattedId = str_pad($id, 2, '0', STR_PAD_LEFT);

        return "SK.No. {$formattedId}/Sidang Yudisium/{$fakultasInitial}/{$monthName}/{$year}";
    }
}
