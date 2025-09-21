<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Yudicium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use Carbon\Carbon;

class YudiciumController extends Controller
{
    public function index(Request $request)
    {
        $datas = DB::table('yudiciums')->get();

        $routeName = $request->route()->getName();
        $postCount = Post::count();

        if ($routeName === 'index' || $routeName === 'index4' || $routeName === 'index6') {
            return view("dashboard.$routeName", [
                'datas' => $datas,
                'postCount' => $postCount
            ]);
        } elseif ($routeName === 'index2') {
            $datas = DB::table('yudiciums')
                ->whereNotNull('no_yudicium')
                ->get();

            return view("dashboard.$routeName", [
                'datas' => $datas,
                'postCount' => $postCount
            ]);
        }
    }

    public function approve($id)
    {
        $yudicium = Yudicium::findOrFail($id);
        $yudicium->approval_status = 'approved';
        // $yudicium->approved_by = auth()->user()->id;
        $yudicium->approved_at = now();
        $yudicium->save();

        return redirect()->back()->with('success', 'Yudicium approved.');
    }

    public function generateCode(Request $request)
    {
        try {
            $validate = $request->validate([
                'fakultas' => 'required|integer',
                'prodi' => 'required|integer',
            ]);

            $periode = Carbon::now()->format('d-m-y');

            $yudicium = Yudicium::create([
                'fakultas' => $validate['fakultas'],
                'prodi' => $validate['prodi'],
                'periode' => $periode,
            ]);

            $mappingFaculties = [3 => 'IT', 4 => 'IK', 5 => 'TE', 6 => 'RI', 7 => 'IF', 8 => 'EB', 9 => 'KB', 10 => 'SBY', 11 => 'PWT'];
            $fakultasInitial = $mappingFaculties[$validate['fakultas']] ?? 'XX';
            $tahun = date('Y');

            $nomorYudisium = $yudicium->id . '/AKD100/' . $fakultasInitial . '-DEK/' . $tahun;
            $yudicium->update(['no_yudicium' => $nomorYudisium]);

            // return redirect()->back()->with('success', 'Yudicium berhasil ditetapkan');

            return response()->json([
                'success' => true,
                'nomor_yudisium' => $nomorYudisium
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
