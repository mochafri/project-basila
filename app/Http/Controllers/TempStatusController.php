<?php

namespace App\Http\Controllers;

use App\Models\TempStatus;
use Illuminate\Http\Request;

class TempStatusController extends Controller
{
    public function postStatus(Request $request)
    {
        $validate = $request->validate([
            'status' => 'required|string',
            'alasan' => 'required|string',
            'nim' => 'required|string',
        ]);

        $status = TempStatus::create([
            'status' => $validate['status'],
            'alasan' => $validate['alasan'],
            'nim' => $validate['nim'],
        ]);

        \Log::info('Status berhasil disimpan', $status->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil disimpan',
        ]);
    }
}
