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

        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiM2E4NWFkN2FmNGI1ZTFmYjMyYTVhZmRhYmIyZTIzYzZmZjI2NTBiODE0NmZjMjhhY2RmODk0NjlhN2VkMTU3M2FlYzNjOGRhNjQxOGMwYzEiLCJpYXQiOjE3NTY2OTMwMTUuMjM2ODMzLCJuYmYiOjE3NTY2OTMwMTUuMjM2ODM2LCJleHAiOjE3NTY3Nzk0MTUuMjI0MzUsInN1YiI6ImJhZ2Fzc2FtdWRyYSIsInNjb3BlcyI6WyJvbGQtc3VwZXJhZG1pbiIsImNlbG9lLWRhc2hib2FyZCIsIm9sZC1kb3NlbiIsIm9sZC1iYWEiLCJhdHRlbmRhbmNlLWVtcGxveWVlIiwiZGFzaGJvYXJkLXVzZXIiLCJuZXdzLWFwcHJvdmVyIiwibmV3cy1jcmVhdG9yIiwibmV3LXNzbyIsIm9sZC1wZWdhd2FpIiwic3NvLW9wZW5saWIiLCJvbGQtYWRtaW4tZGF0YS1tYWhhc2lzd2EtZmFrdWx0YXMiLCJvbGQtc2lzZm8iLCJvbGQtYWRtaW4tbGFjIiwib2xkLWFkbWluLWxhYWsiLCJvbGQtYmstdGVsdSIsIm9sZC1rZWxvbXBvay1rZWFobGlhbiIsInRyYWluaW5nLWZzZHAtc3VwcG9ydCIsInRyYWluaW5nLXVzZXItZmFrdWx0YXMiLCJvbGQtYWRtaW4tYmsiLCJvbGQtZGV2dGVhbSIsImVtcGxveWVlLXN0cnVjdHVyYWwiLCJvbnNpdGVyZWdpc3RyYXRpb24tYWRtaW4iLCJpdGRvYy1tYXF1dGkiLCJpdGRvY19leHRhdWRpdG9yIl19.GsjLsHKqQAfUhgcqYnFvfHiTIstnkmOl_VMdlfRHkQWHg6FsKRs4txMiX7BHIPcD127Zbbr-eD-45UhEwARjKPUUT9kUqZXAfCDbwjdIoI4U4aLL6m6MiJEnLaDQp2kSqw6YKiFd18BSOZ9CrnKd1s3XYVP1Srh-R40cn042swj1-H1F7ZpKhzYk96UQPV0gux5TaFo86YJtLMRtiDdRX2ayiMiER2Sd9CyeX1uVgb3cJ9zmCmm_E5teNkQUPqR9fCyNPsJSXVgjN-0boVT3mXVvzQr1AFl_8pRKeUu6_zXWGPcBKb4mNPL_StJPFByaloleB0eHvbpm6FqgdeIWg9vfI4dUVcC1yXWhcgU047HOPWMmrD1_6Gf3QvgrHmz7hsdDVGURHCO-rnNd08r0LepceA9z3bOWq7kI5YjJKo9Pu6Wv4Cd0fh8HM46kVTnWtWQoznzRbFd0l2hPvoVoLWak8V1qcspi6qyS5NnsRON9Zq_xQ7Vv5P3Pvkcnw4L8iNebHuH5y4dWvJbYASJHClTFCmnixE7qWtyu33v7EiE5TH4fBAVdtCt_WV0NTH3SgwRsJNvn8aZaIp70Hv8iJbTOAR5qliPZnU1aTex4bOLvNiAXy-nudi3 _BWItHfVq6rXBC2s2At5H6Y6P5wCFHNPB1xX4siT3MMsDFd-jMo4';

        // ambil data dari API
        $response = Http::withToken($token)
            ->get('https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69');
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