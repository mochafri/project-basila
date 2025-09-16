<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacultyController extends Controller
{
    public function index()
    {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiMDQ3OTdhOTU4NzZjMzAxN2RjODliZmViZmVmNDI5ZjI0NDAzMTk1ZDcxOWI2YmMwMWE1NGFlZThlNTk4YWI4OTE0M2Y3MTEyZGJjOWEzNGQiLCJpYXQiOjE3NTc5ODY4MzEuODY0MjgxLCJuYmYiOjE3NTc5ODY4MzEuODY0MjgzLCJleHAiOjE3NTgwNzMyMzEuODUyNDE2LCJzdWIiOiJiYWdhc3NhbXVkcmEiLCJzY29wZXMiOlsib2xkLXN1cGVyYWRtaW4iLCJjZWxvZS1kYXNoYm9hcmQiLCJvbGQtZG9zZW4iLCJvbGQtYmFhIiwiYXR0ZW5kYW5jZS1lbXBsb3llZSIsImRhc2hib2FyZC11c2VyIiwibmV3cy1hcHByb3ZlciIsIm5ld3MtY3JlYXRvciIsIm5ldy1zc28iLCJvbGQtcGVnYXdhaSIsInNzby1vcGVubGliIiwib2xkLWFkbWluLWRhdGEtbWFoYXNpc3dhLWZha3VsdGFzIiwib2xkLXNpc2ZvIiwib2xkLWFkbWluLWxhYyIsIm9sZC1hZG1pbi1sYWFrIiwib2xkLWJrLXRlbHUiLCJvbGQta2Vsb21wb2sta2VhaGxpYW4iLCJ0cmFpbmluZy1mc2RwLXN1cHBvcnQiLCJ0cmFpbmluZy11c2VyLWZha3VsdGFzIiwib2xkLWFkbWluLWJrIiwib2xkLWRldnRlYW0iLCJlbXBsb3llZS1zdHJ1Y3R1cmFsIiwib25zaXRlcmVnaXN0cmF0aW9uLWFkbWluIiwiaXRkb2MtbWFxdXRpIiwiaXRkb2NfZXh0YXVkaXRvciJdfQ.PzhpyM8Dc3v2K7hjr1SGcFjWT5yZkk1x1Vi_VjwNHNnkoYoffZQFbZFj5LLjXmeZjX6esjatPBS1yUUNQjM5x_c9YqQgNTCmcnQ26a3Hx_DwTLB7CjTeNck1VkzuUIUja75qjSqIcc6QISzMhC6suzlD_djT2ltmhOgDPJBYeVX8rlw19hY_QLfztsmkXI7_zKPFCnyfBDIAZTxU7UsLz2q3WpMa8j9Hw-IsnPy0h9VrLmufmrMsIlnRLKD1KPfko-XcfsW6r-KLmbGlx90XI2aHYZs9plDV4te783ZzkMnN5NgUpziqXhPI7Os0PgIxjEPzf2jsjowiOeZlkc6yAsEcFXZYqtSSzmBDRZa_B1V9KcSjQAne6Wfd1SWn8LF2bOkvrOG5PG1JFlf3Jm4_PO2PKruSGl7LfJOoMMV3MlBtarR6WLMaD429d3vu-QPDnRiUFcLmHxtSyiprXFPmtpJySkBlKgToeYqFP9eSmNQjbimup-S7Gyr2FV5LiiiWfjDjh0j0nDIlQex_4iaruoQreDXyorG51d7H3BZfnhPPQoNoi3pJgpVj7XGVlNGfEBhm2deWASnxIwRJlJCn15MThbF6KtNeAzVgfigz1s5AD4KXjCgpKI1ezt-eS5d0xVf4vdzhp9vMskIbnmDJYHWodHHpWtcuhNV6F5HCtU0";

        try {
            $response = Http::withToken($token)
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
}