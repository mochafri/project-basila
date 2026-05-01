<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YudisiumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'no_yudicium' => 'YUD-2024-01',
                'no_sk' => 'SK-YUD-2024-01',
                'fakultas_id' => 1,
                'prodi_id' => 1,
                'periode' => '2024-01-01', // 2024 Ganjil
                'approval_status' => 'Approved',
                'catatan' => 'Yudisium Ganjil 2024',
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_yudicium' => 'YUD-2024-02',
                'no_sk' => 'SK-YUD-2024-02',
                'fakultas_id' => 1,
                'prodi_id' => 2,
                'periode' => '2024-07-01', // 2024 Genap
                'approval_status' => 'Waiting',
                'catatan' => 'Yudisium Genap 2024',
                'approved_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('yudiciums')->insert($data);
    }
}
