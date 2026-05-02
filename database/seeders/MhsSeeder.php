<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MhsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Standarisasi untuk S1 (Sarjana):
     * - Masa Studi Normal: 8 semester (4 tahun)
     * - Masa Studi Maksimal: 14 semester (7 tahun)
     * - SKS Lulus: 144-160 SKS
     */
    public function run(): void
    {
        $data = [];

        for ($i = 1; $i <= 5; $i++) {
            // S1: 8-14 semester, 144-160 SKS
            $study_period = rand(8, 14);
            $pass_sks = rand(144, 160);
            $ipk = number_format(rand(275, 400) / 100, 2); // IPK 2.75 - 4.00

            // Note: STATUS dan PREDIKAT akan digenerate dari fungsi, tidak disimpan di database
            $data[] = [
                'STUDENTID'     => '3101' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'FULLNAME'      => 'Mahasiswa Test ' . $i,
                'MASA_STUDI'    => $study_period . ' Semester',
                'PASS_CREDIT'   => $pass_sks,
                'GPA'           => $ipk,
                'STUDYPROGRAMID'=> 31, // S1 Informatika
                'FACULTYID'     => 7,  // Fakultas Informatika
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }
        DB::table('mahasiswa')->insert($data);
    }
}
