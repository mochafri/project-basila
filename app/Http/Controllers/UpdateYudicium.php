<?php

namespace App\Http\Controllers;

use App\Models\Yudicium;
use App\Models\MhsYud;
use Illuminate\Http\Request;

class UpdateYudicium extends Controller
{
    public function index()
    {
        return view('dashboard.index7');
    }

    public function updateYudicium(Request $request)
    {
        $validate = $request->validate([
            'id' => 'integer|required'
        ]);

        $yudicium = Yudicium::find($request->id);
        $yudicium->approval_status = 'Waiting';
        $yudicium->save();

        MhsYud::where('yudicium_id', $request->id)
            ->where('status', 'Tidak Eligible')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data diperbarui'
        ]);
    }
}
