<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 1; $i <= 10; $i++) {
            $ipk = $faker->randomFloat(2, 2.5, 4.0);
            $predikat = ($ipk >= 3.51) ? 'Cumlaude' : (($ipk >= 3.00) ? 'Sangat Memuaskan' : 'Memuaskan');

            DB::table('mahasiswa')->insert([
                'STUDENTID' => '32' . $faker->numerify('########'),
                'FULLNAME' => $faker->name,
                'MASA_STUDI' => $faker->randomElement(['8 Semester', '9 Semester', '10 Semester']),
                'PASS_CREDIT' => $faker->numberBetween(140, 150),
                'GPA' => $ipk,
                'STATUS' => $faker->randomElement(['Aktif', 'Lulus']),
                'STUDYPROGRAMID' => 32, // D3 Rekayasa Perangkat Lunak Aplikasi
                'FACULTYID' => 3,       // ILMU TERAPAN
                'PREDIKAT' => $predikat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
