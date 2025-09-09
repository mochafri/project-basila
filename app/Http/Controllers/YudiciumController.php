<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Yudicium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

class YudiciumController extends Controller
{
    public function index(Request $request)
    {
        $datas = DB::table('yudiciums')->get();

        $routeName = $request->route()->getName();
        $postCount = Post::count();

        if ($routeName === 'index2' || $routeName === 'index' || $routeName === 'index4' || $routeName === 'index6') {
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

}
