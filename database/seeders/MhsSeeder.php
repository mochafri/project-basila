<?php

namespace Database\Seeders;

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

        for ($i = 1; $i <= 10; $i++) {
            $ipk = number_format(rand(200, 395) / 100, 2);
            $pass_sks = rand(110, 144);
            $study_period = rand(6, 8);

            $data[] = [
                'nim'          => '608072' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'fakultas_id'  => 3,
                'prody_id'     => 32,
                'name'         => 'Mahasiswa ' . $i,
                'study_period' => $study_period,
                'pass_sks'     => $pass_sks,
                'ipk'          => $ipk,
                'predikat'     => null,
                'status_otomatis' => $this->hitungStatus(
                    $study_period,
                    $pass_sks,
                    $ipk
                ),
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
        DB::table('mahasiswa')->insert($data);
    }

    public function hitungStatus($studyPeriod, $sks, $ipk)
    {
        if ($studyPeriod <= 8 && $sks >= 110 && $ipk >= 3.01) {
            return 'Eligible';
        }
        return 'Tidak Eligible';
    }
}