<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MhsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        for ($i = 1; $i <= 5; $i++) {
            $study_period = rand(6, 8);
            $pass_sks = rand(110, 144);
            $ipk = number_format(rand(200, 395) / 100, 2);

            $data[] = [
                'nim'          => '608072' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'fakultas_id'  => 3,
                'prody_id'     => 32,
                'name'         => 'Mahasiswa ' . $i,
                'study_period' => $study_period,
                'pass_sks'     => $pass_sks,
                'ipk'          => $ipk,
                'predikat'     => null,
                'status' => (new Mahasiswa)->hitungStatus($study_period, $pass_sks, $ipk),
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
        DB::table('mahasiswa')->insert($data);
    }
}
