<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacultyController extends Controller
{
    private $token ;

    public function __construct()
    {
        $this->token = env('KEY_TOKEN');
    }

    public function faculty()
    {
        try {
            $response = Http::withToken($this-> token )
                ->get('https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69');

            if ($response->successful()) {
                $faculties = $response->json();
                \Log::info('Faculties data', $faculties);

                return response()->json([
                    'status' => 'success',
                    'data' => $faculties,
                ]);
            } elseif ($response->status() === 403) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token tidak valid atau akses ditolak',
                ], 403);
            } else {
                \Log::warning('API gagal', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengambil data fakultas',
                ], $response->status());
            }
        } catch (\Exception $e) {
            \Log::error('API Exception', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function prody($id)
    {
        try {
            $response = Http::withToken($this -> token)
                ->get("https://gateway.telkomuniversity.ac.id/b2ac79622cd60bce8dc5a1a7171bfc9c/{$id}");

            if ($response->successful() === 403) {
                Log::info('Token tidak valid');
                return response()->json([
                    'success' => 'failed',
                    'message' => 'Token tidak valid'
                ], 403);
            }

            $prody = $response->json();

            return response()->json([
                'success' => 'success',
                'data' => $prody
            ], 200);
        } catch (\Exception $e) {
            Log::error('API Exception', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }
}
