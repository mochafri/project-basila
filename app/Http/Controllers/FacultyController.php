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

        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiYWVjMTQ5YmQyNWNhZDEwZGFhYTI5OGFlNWZiNDdmYjkwOTU4MzYwMmU4OGU4NjY1MjRkMDY1YjMzYmM4MzFkMzJhNmFjZWRhNTlmM2YyMTAiLCJpYXQiOjE3NTY3Nzc5NTYuMjk5MzI2LCJuYmYiOjE3NTY3Nzc5NTYuMjk5MzI4LCJleHAiOjE3NTY4NjQzNTYuMjgzODQ0LCJzdWIiOiJiYWdhc3NhbXVkcmEiLCJzY29wZXMiOlsib2xkLXN1cGVyYWRtaW4iLCJjZWxvZS1kYXNoYm9hcmQiLCJvbGQtZG9zZW4iLCJvbGQtYmFhIiwiYXR0ZW5kYW5jZS1lbXBsb3llZSIsImRhc2hib2FyZC11c2VyIiwibmV3cy1hcHByb3ZlciIsIm5ld3MtY3JlYXRvciIsIm5ldy1zc28iLCJvbGQtcGVnYXdhaSIsInNzby1vcGVubGliIiwib2xkLWFkbWluLWRhdGEtbWFoYXNpc3dhLWZha3VsdGFzIiwib2xkLXNpc2ZvIiwib2xkLWFkbWluLWxhYyIsIm9sZC1hZG1pbi1sYWFrIiwib2xkLWJrLXRlbHUiLCJvbGQta2Vsb21wb2sta2VhaGxpYW4iLCJ0cmFpbmluZy1mc2RwLXN1cHBvcnQiLCJ0cmFpbmluZy11c2VyLWZha3VsdGFzIiwib2xkLWFkbWluLWJrIiwib2xkLWRldnRlYW0iLCJlbXBsb3llZS1zdHJ1Y3R1cmFsIiwib25zaXRlcmVnaXN0cmF0aW9uLWFkbWluIiwiaXRkb2MtbWFxdXRpIiwiaXRkb2NfZXh0YXVkaXRvciJdfQ.SRiH0BdiQQczteu2spjSUPNjcwikK5k9x3j8GUWfQoiNRw8GamawwjTdprDqcZEKLNK60ZHho9t-XB9bI2naUQiWapR4JjfYAJB08wqhF-r5aJq49ygDRHDhLH_LwQ2GPU0C7dmI6F_JVc0N_G2knSkTXcsb2l5IOsj0qLY0ZzFJTWYDBi1kF1Qi5bWjfsIvB7u-Sz5fsYZCBOzsQXxj3O9t6zGex7RlKBj9oKNsAGo2Bw3I55erGVFNsTy2UrsSsgyxwgk9TOydUElD-KPKb5JdZ7fZGjFScLB_ntzQXqlrHm2hLZwi4HKQsxDqYoQWi6r_gvjc4dslnf8Wx_Khrxtrv1HtDP1RKZMa9oeuzQh_zIpu25hgwyssa6i0aDJGazrLX7LF529vy6m3UuQUBAaoWXN0BwJ56x_x6ZwMbolUaydVnIp6qwjA6TtEGOgqVG8sJsEXWoc6bEWBISFTxeTrzNgWaqKJ-xtsw6Kz8P24sGmkUXSkpDbiv3wqasMoe755EqwFRCDwlZXyTFcVksguxfSzVcP2uEDwr1IazAUfznXSumw8wk97JubctHw_XM0rRnN7-x6oZKQb-vUOxxB515Cu4I9jqWjQP1uUmVf9GOgfM2i4QY57nRuCml-icxZfeLY8jOHc35O0cljPkZKHt7ziVSMCsxKV2b5diSY';

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
