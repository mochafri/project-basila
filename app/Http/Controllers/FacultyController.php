<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        // cek route name
        $routeName = $request->route()->getName();

        // ambil data dari API
        $response = Http::get('https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69');
        $faculties = $response->successful() ? $response->json() : [];

        // tentukan view sesuai route
        if ($routeName === 'index3') {
            return view('dashboard.index3', compact('faculties'));
        } elseif ($routeName === 'index4') {
            return view('dashboard.index4', compact('faculties'));
        }

        abort(404);
    }
}
