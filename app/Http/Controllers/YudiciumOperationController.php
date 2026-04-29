<?php

namespace App\Http\Controllers;

use App\Models\MhsYud;
use App\Models\Yudicium;
use App\Models\TempStatus;
use App\Services\YudiciumService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YudiciumOperationController extends Controller
{
    protected $yudiciumService;

    public function __construct(YudiciumService $yudiciumService)
    {
        $this->yudiciumService = $yudiciumService;
    }

    public function saveDraft(Request $request)
    {
        $validate = $request->validate([
            'fakultas_id'    => 'required|integer',
            'prodi_id'       => 'required|integer',
            'mahasiswa_nims' => 'array',
        ]);

        $nims = $validate['mahasiswa_nims'] ?? [];

        if (empty($nims)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada mahasiswa yang dipilih.'
            ], 403);
        }

        DB::beginTransaction();

        try {
            $currentDate = date('Y-m-d');

            $yudicium = Yudicium::create([
                'fakultas_id' => $validate['fakultas_id'],
                'prodi_id'    => $validate['prodi_id'],
                'periode'     => null,
                'no_yudicium' => null,
            ]);

            // Langsung set API untuk setiap NIM yang dipilih: SELECTED=Y, periode=tanggalHariIni
            foreach ($nims as $nim) {
                $apiResponse = $this->yudiciumService->setAcademicStatus($nim, $currentDate, 'Y');
                
                // Validasi Body (true, TRUE, atau 1)
                $body = strtolower(trim($apiResponse->body()));
                if (!$apiResponse->successful() || ($body !== 'true' && $body !== '1')) {
                    Log::error("[saveDraft] Failed to set status for NIM $nim. Body: " . $apiResponse->body());
                    throw new \Exception('Gagal mengubah data akademik untuk NIM ' . $nim . '. Response: ' . $apiResponse->body());
                }
            }

            // Delay 1.5 detik agar API Feeder sempat melakukan update internal
            usleep(1500000); 

            // Verifikasi perubahan via stt=9 (ALL_ACADEMIC)
            $verify = $this->yudiciumService->getAllAcademicData($validate['prodi_id'], $currentDate);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Draft Yudisium berhasil disimpan'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saveDraft: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDraft($id)
    {
        $mahasiswa = MhsYud::where('yudicium_id', $id)->get();

        foreach ($mahasiswa as $mhs) {
            $temp = TempStatus::where('nim', $mhs->nim)->first();
            if ($temp) {
                $mhs->status = $temp->status;
                $mhs->alasan_status = $temp->alasan;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $mahasiswa,
        ]);
    }

    public function edit($id)
    {
        $yudicium = Yudicium::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $yudicium,
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $yudicium = Yudicium::findOrFail($id);
            $prodiId = $yudicium->prodi_id;
            
            // Gunakan periode dari tabel, jika null pakai tanggal created_at
            $periode = $yudicium->periode ?: ($yudicium->created_at ? $yudicium->created_at->format('Y-m-d') : date('Y-m-d'));

            // 1. Ambil daftar mahasiswa yang terpilih dari API (stt=10) untuk prodi & tanggal ini
            // Ini penting karena untuk 'Draft', data NIM mungkin belum ada di mhs_yudiciums lokal
            $urlPick = "https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=10&id=$prodiId&periode=$periode";
            $responsePick = Http::get($urlPick);
            $listSelected = $responsePick->successful() ? $responsePick->json() : [];

            // 2. Reset status di API untuk setiap mahasiswa yang ditemukan
            if (!empty($listSelected)) {
                foreach ($listSelected as $mhs) {
                    $nim = $mhs['STUDENTID'] ?? null;
                    if ($nim) {
                        try {
                            $this->yudiciumService->resetAcademicStatus($nim, $periode);
                        } catch (\Exception $e) {
                            Log::warning("[destroy] Gagal reset API untuk NIM $nim: " . $e->getMessage());
                        }
                    }
                }
            }

            // 3. Hapus data lokal (mhs_yudiciums jika ada, dan yudicium)
            MhsYud::where('yudicium_id', $id)->delete();
            $yudicium->delete();

            DB::commit();

            // Reset auto-increment
            $maxId = Yudicium::max('id') ?: 0;
            DB::statement("ALTER TABLE yudiciums AUTO_INCREMENT = " . ($maxId + 1));

            return response()->json(['success' => true, 'message' => 'Yudisium berhasil dihapus dan disinkronisasi.']);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error destroy yudisium: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
