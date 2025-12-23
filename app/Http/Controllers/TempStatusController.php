<?php

namespace App\Http\Controllers;

use App\Models\MhsYud;
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
        
        \Log::info("Validate masuk", $validate);

        $status = TempStatus::updateOrCreate(
            ['nim' => $validate['nim']],
            [
                'status' => $validate['status'],
                'alasan' => $validate['alasan'],
            ]
        );

        $mhsYud = MhsYud::select('status', 'alasan_status')
            ->where('nim', $validate['nim'])
            ->get();
            
        if(count($mhsYud) > 0) {
            MhsYud::where('nim', $validate['nim'])
                ->update([
                    'status' => $validate['status'],
                    'alasan_status' => $validate['alasan'],
                ]);
        }

        session()->flash('success', 'Status berhasil disimpan');

        \Log::info('Status berhasil disimpan', $status->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil disimpan',
        ]);
    }
}
