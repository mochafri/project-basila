<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;
use App\Models\Yudicium;

class MhsYudisiumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswa = Mahasiswa::all();
        $yudiciums = Yudicium::all();

        if ($mahasiswa->isEmpty() || $yudiciums->isEmpty()) {
            return;
        }

        foreach ($mahasiswa as $index => $mhs) {
            // Bagi mahasiswa ke yudisium yang ada secara bergantian
            $yudisium = $yudiciums[$index % $yudiciums->count()];

            // Tentukan status eligible
            $status = 'Tidak Eligible';
            if ((int)$mhs->MASA_STUDI <= 8 && $mhs->PASS_CREDIT >= 144 && $mhs->GPA >= 2.50) {
                $status = 'Eligible';
            }

            DB::table('mhs_yudiciums')->insert([
                'nim' => $mhs->STUDENTID,
                'fakultas_id' => $mhs->FACULTYID,
                'prody_id' => $mhs->STUDYPROGRAMID,
                'name' => $mhs->FULLNAME,
                'study_period' => (int) filter_var($mhs->MASA_STUDI, FILTER_SANITIZE_NUMBER_INT),
                'pass_sks' => $mhs->PASS_CREDIT,
                'ipk' => $mhs->GPA,
                'predikat' => $mhs->PREDIKAT,
                'status' => $status,
                'yudicium_id' => $yudisium->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
