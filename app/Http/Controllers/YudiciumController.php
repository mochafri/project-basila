<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Yudicium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

class YudiciumController extends Controller
{
    public function index()
    {
        $datas = DB::table('yudiciums')->get();
        return view('dashboard.index2', ['datas' => $datas]);

    }

    public function showPostCount()
    {
        $postCount = Post::count();
        return view('dashboard.index', compact('postCount'));
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
