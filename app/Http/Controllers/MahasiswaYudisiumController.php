<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MahasiswaYudisiumService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MahasiswaYudisiumController extends Controller
{
    protected $yudisiumService;

    public function __construct(MahasiswaYudisiumService $yudisiumService)
    {
        $this->yudisiumService = $yudisiumService;
    }

    /**
     * Menampilkan halaman penetapan yudisium
     */
    public function index(Request $request)
    {
        return view('yudisium.penetapan');
    }

    /**
     * Filter mahasiswa untuk yudisium (API primary, Database fallback)
     * Endpoint: POST /yudisium/filter-mahasiswa
     */
    public function filterMhs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prodi' => 'required',
            'periode' => 'required',
            'fakultas' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $prodi = $request->input('prodi');
        $periode = $request->input('periode');
        $fakultas = $request->input('fakultas');

        try {
            $result = $this->yudisiumService->filterMahasiswa($prodi, $periode, $fakultas);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error di filterMhs controller', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan draft mahasiswa yudisium
     * Endpoint: POST /yudisium/simpan-draft
     */
    public function simpanDraft(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'yudicium_id' => 'required|integer',
            'periode' => 'required',
            'mahasiswa' => 'required|array',
            'mahasiswa.*.nim' => 'required',
            'mahasiswa.*.source' => 'required|in:api,database'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $yudisiumId = $request->input('yudicium_id');
        $periode = $request->input('periode');
        $mahasiswaList = $request->input('mahasiswa');

        try {
            $result = $this->yudisiumService->simpanDraft($mahasiswaList, $yudisiumId, $periode);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error di simpanDraft controller', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil data draft mahasiswa yudisium
     * Endpoint: GET /yudisium/get-draft
     */
    public function getDraft(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'yudicium_id' => 'required|integer',
            'periode' => 'required',
            'source' => 'nullable|in:api,database'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $yudisiumId = $request->input('yudicium_id');
        $periode = $request->input('periode');
        $source = $request->input('source');

        try {
            $result = $this->yudisiumService->getDraft($yudisiumId, $periode, $source);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error di getDraft controller', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tetapkan yudisium (finalisasi)
     * Endpoint: POST /yudisium/tetapkan
     */
    public function tetapkanYudisium(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'yudicium_id' => 'required|integer',
            'periode' => 'required',
            'source' => 'required|in:api,database'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $yudisiumId = $request->input('yudicium_id');
        $periode = $request->input('periode');
        $source = $request->input('source');

        try {
            $result = $this->yudisiumService->tetapkanYudisium($yudisiumId, $periode, $source);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error di tetapkanYudisium controller', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus draft mahasiswa dari API (uncheck)
     * Endpoint: POST /yudisium/hapus-draft-api
     */
    public function hapusDraftApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required',
            'periode' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $nim = $request->input('nim');
        $periode = $request->input('periode');

        try {
            $result = $this->yudisiumService->hapusDraftApi($nim, $periode);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error di hapusDraftApi controller', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
