<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YudiciumController extends Controller
{
    function index()
    {
        $datas = DB::table('yudiciums')->get();
        return view('dashboard.index2', ['datas' => $datas]);
    }
}
