<?php

namespace App\Http\Controllers;

use App\Models\Yudicium;
use App\Models\MhsYud;
use Illuminate\Http\Request;

class UpdateYudiciumController extends Controller
{
    public function index(Request $request)
    {
        $routeName = $request->route()->getName();

        if (in_array($routeName, ['index5', 'index7'])) {
            return view("dashboard.$routeName");
        }
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
