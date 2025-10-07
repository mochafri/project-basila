<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AproveYudicium extends Controller
{
    public function __construct()
    {
        $this-> token = env('KEY_TOKEN');
        $this-> urlFakultas = 'https://gateway.telkomuniversity.ac.id/2def2c126fd225c3eaa77e20194b9b69';
        $this-> urlProdi = 'https://gateway.telkomuniversity.ac.id/b2ac79622cd60bce8dc5a1a7171bfc9c/';
    }

    public function filterYudisium(Request $request)
    {
        $validate = $request->validate([
            'fakultas_id' => 'required|integer'
        ]);

        \Log::info('Fakultas ID' . $validate['fakultas_id']);

        if (empty($validate['fakultas_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yudisium di fakultas ini'
            ]);
        }

        $fakultasResponse = Http::withToken($this->token)
            ->get($this->urlFakultas);

        $yudiciums = DB::table('yudiciums')
            ->join('mhs_yudiciums', 'yudiciums.id', '=', 'mhs_yudiciums.yudicium_id')
            ->select(
                'yudiciums.id as id',
                'yudiciums.no_yudicium as no_yudicium',
                'yudiciums.periode as periode',
                'yudiciums.fakultas_id as fakultas',
                'yudiciums.prodi_id as prodi',
                DB::raw('COUNT(mhs_yudiciums.id) as total_mhs')
            )
            ->where('yudiciums.fakultas_id', $validate['fakultas_id'])
            ->groupBy('yudiciums.id', 'yudiciums.no_yudicium')
            ->get();

        $fakulties = collect($fakultasResponse->successful() ? $fakultasResponse->json() : []);

        $prodyCache = [];

        $yudiciums->transform(function ($item) use ($fakulties, &$prodyCache) {
            $faculty = $fakulties->firstWhere('facultyid', $item->fakultas);
            $item->fakultasname = $faculty['facultyname'] ?? 'Unknown';

            $facultyId = $faculty['facultyid'] ?? null;

            if ($facultyId) {
                if (!isset($prodyCache[$facultyId])) {
                    $prodyRes = Http::withToken($this->token)
                        ->get($this->urlProdi . $facultyId);

                    $prodyCache[$facultyId] = collect($prodyRes->successful() ? $prodyRes->json() : []);
                }
                $prody = $prodyCache[$facultyId];

                if($prody->isNotEmpty()){
                    $prodi = $prody->firstWhere('studyprogramid', $item->prodi);
                    $item->prodiname = $prodi['studyprogramname'] ?? 'Unknown';
                } else {
                    $item->prodyname = 'Unknown';
                }
            }

            return $item ;
        });

        return response()->json([
            'success' => true,
            'data' => $yudiciums
        ]);
    }
}
