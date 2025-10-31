<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
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
                'STUDENTID'     => '2101' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'FULLNAME'      => 'Mahasiswa ' . $i,
                'MASA_STUDI'    => $study_period,
                'PASS_CREDIT'   => $pass_sks,
                'GPA'           => $ipk,
                'STATUS'        => (new Mahasiswa)->hitungStatus($study_period, $pass_sks, $ipk),
                'STUDYPROGRAMID'=> 31,
                'FACULTYID'     => 7,
                'PREDIKAT'      => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }
        DB::table('mahasiswa')->insert($data);
    }
}
