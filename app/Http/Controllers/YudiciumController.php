<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
        $postTotalMahasiswa = 245;
        $postCount = Post::count();
        return view('dashboard.index', compact('postCount', 'postTotalMahasiswa'));
    }
}