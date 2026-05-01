<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data STUDYPROGRAMID dari API Telkom University
     * 
     * Kriteria Eligible untuk Yudisium:
     * 1. IPK minimal 2.75
     * 2. Masa studi tidak melebihi batas maksimal:
     *    - D3: Maksimal 8 semester
     *    - D4/S1: Maksimal 14 semester
     *    - S2: Maksimal 8 semester
     *    - S3: Maksimal 10 semester
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Definisi Program Studi per Fakultas (dari API response)
        // Format: [STUDYPROGRAMID, FACULTYID, Nama Prodi, Jenjang]
        $programStudi = [
            // Fakultas Ilmu Terapan (FIT) - ID 3
            [51, 3, 'D3 Manajemen Pemasaran', 'D3'],
            [143, 3, 'S2 Terapan Rekayasa Teknologi Informasi', 'S2'],
            [32, 3, 'D3 Rekayasa Perangkat Lunak Aplikasi', 'D3'],
            [73, 3, 'D3 Sistem Informasi Akuntansi', 'D3'],
            [54, 3, 'D3 Perhotelan', 'D3'],
            [72, 3, 'D3 Sistem Informasi', 'D3'],
            [124, 3, 'D4 Sistem Informasi Kota Cerdas', 'D4'],
            [14, 3, 'D3 Teknologi Telekomunikasi', 'D3'],
            [71, 3, 'D3 Teknologi Komputer', 'D3'],
            [33, 3, 'D4 Teknologi Rekayasa Multimedia', 'D4'],
            
            // Fakultas Industri Kreatif (FIK) - ID 4
            [94, 4, 'S1 Desain Produk', 'S1'],
            [93, 4, 'S1 Desain Interior', 'S1'],
            [95, 4, 'S1 Seni Rupa', 'S1'],
            [91, 4, 'S1 Desain Komunikasi Visual', 'S1'],
            [92, 4, 'S1 Kriya', 'S1'],
            [57, 4, 'S2 Desain', 'S2'],
            [98, 4, 'S1 Film', 'S1'],
            [97, 4, 'S1 Desain Komunikasi Visual (International Class)', 'S1'],
            
            // Fakultas Teknik Elektro (FTE) - ID 5
            [29, 5, 'S1 Teknik Biomedis', 'S1'],
            [101, 5, 'S1 Teknik Sistem Energi', 'S1'],
            [111, 5, 'S3 Teknik Elektro', 'S3'],
            [11, 5, 'S1 Teknik Telekomunikasi', 'S1'],
            [12, 5, 'S1 Teknik Elektro', 'S1'],
            [78, 5, 'S2 Teknik Elektro', 'S2'],
            [13, 5, 'S1 Teknik Komputer', 'S1'],
            [62, 5, 'S1 Teknik Fisika', 'S1'],
            
            // Fakultas Rekayasa Industri (FRI) - ID 6
            [22, 6, 'S1 Sistem Informasi', 'S1'],
            [60, 6, 'S1 Teknik Logistik', 'S1'],
            [21, 6, 'S1 Teknik Industri', 'S1'],
            [10, 6, 'S2 Sistem Informasi', 'S2'],
            [80, 6, 'S2 Teknik Industri', 'S2'],
            [139, 6, 'S1 Manajemen Rekayasa', 'S1'],
            
            // Fakultas Informatika (FIF) - ID 7
            [89, 7, 'S1 PJJ Informatika', 'S1'],
            [31, 7, 'S1 Informatika', 'S1'],
            [49, 7, 'S2 Ilmu Forensik', 'S2'],
            [52, 7, 'S3 Informatika', 'S3'],
            [61, 7, 'S1 Rekayasa Perangkat Lunak', 'S1'],
            [30, 7, 'S1 Sains Data', 'S1'],
            [38, 7, 'S1 Teknologi Informasi', 'S1'],
            
            // Fakultas Ekonomi dan Bisnis (FEB) - ID 8
            [79, 8, 'S2 Manajemen', 'S2'],
            [46, 8, 'S1 Administrasi Bisnis (International Class)', 'S1'],
            [41, 8, 'S1 Manajemen (Manajemen Bisnis Telekomunikasi & Informatika)', 'S1'],
            [58, 8, 'S1 Akuntansi (International Class)', 'S1'],
            [103, 8, 'S1 Manajemen Bisnis Rekreasi', 'S1'],
            [110, 8, 'S2 Akuntansi', 'S2'],
            [108, 8, 'S2 Administrasi Bisnis', 'S2'],
            [42, 8, 'S1 Akuntansi', 'S1'],
            [112, 8, 'S3 Manajemen', 'S3'],
            [44, 8, 'S1 Administrasi Bisnis', 'S1'],
            [85, 8, 'S2 Manajemen PJJ', 'S2'],
            
            // Fakultas Komunikasi dan Bisnis (FKB) - ID 9
            [48, 9, 'S1 Ilmu Komunikasi (International Class)', 'S1'],
            [99, 9, 'S2 Ilmu Komunikasi', 'S2'],
            [102, 9, 'S1 Penyiaran Konten Digital', 'S1'],
            [43, 9, 'S1 Ilmu Komunikasi', 'S1'],
            [47, 9, 'S1 Hubungan Masyarakat', 'S1'],
            [141, 9, 'S1 Psikologi', 'S1'],
        ];

        $this->command->info('🎓 Generating Mahasiswa Data...');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $totalMahasiswa = 0;
        $fakultasStats = [
            3 => ['nama' => 'FIT', 'count' => 0],
            4 => ['nama' => 'FIK', 'count' => 0],
            5 => ['nama' => 'FTE', 'count' => 0],
            6 => ['nama' => 'FRI', 'count' => 0],
            7 => ['nama' => 'FIF', 'count' => 0],
            8 => ['nama' => 'FEB', 'count' => 0],
            9 => ['nama' => 'FKB', 'count' => 0],
        ];

        foreach ($programStudi as $prodi) {
            [$studyProgramId, $facultyId, $namaProdi, $jenjang] = $prodi;
            
            // Random jumlah mahasiswa per prodi (1-10)
            $jumlahMahasiswa = $faker->numberBetween(1, 10);
            
            $this->command->info("Generating {$jumlahMahasiswa} mahasiswa untuk: {$namaProdi} (ID: {$studyProgramId})");

            for ($i = 1; $i <= $jumlahMahasiswa; $i++) {
                // Generate NIM berdasarkan study program ID dan tahun
                $tahun = $faker->randomElement(['2019', '2020', '2021', '2022']);
                $nim = str_pad($studyProgramId, 3, '0', STR_PAD_LEFT) . $tahun . $faker->numerify('####');
                
                // Generate IPK dengan distribusi realistis
                $ipk = $faker->randomFloat(2, 2.5, 4.0);
                
                // Tentukan predikat berdasarkan IPK
                if ($ipk >= 3.51) {
                    $predikat = 'Cumlaude';
                } elseif ($ipk >= 3.00) {
                    $predikat = 'Sangat Memuaskan';
                } else {
                    $predikat = 'Memuaskan';
                }
                
                // Generate masa studi berdasarkan jenjang
                if ($jenjang === 'D3') {
                    $masaStudi = $faker->randomElement(['6 Semester', '7 Semester', '8 Semester']);
                    $passCredit = $faker->numberBetween(110, 120);
                } elseif ($jenjang === 'D4') {
                    $masaStudi = $faker->randomElement(['8 Semester', '9 Semester', '10 Semester']);
                    $passCredit = $faker->numberBetween(140, 150);
                } elseif ($jenjang === 'S2') {
                    $masaStudi = $faker->randomElement(['4 Semester', '5 Semester', '6 Semester']);
                    $passCredit = $faker->numberBetween(40, 50);
                } elseif ($jenjang === 'S3') {
                    $masaStudi = $faker->randomElement(['6 Semester', '7 Semester', '8 Semester']);
                    $passCredit = $faker->numberBetween(40, 50);
                } else { // S1
                    $masaStudi = $faker->randomElement(['8 Semester', '9 Semester', '10 Semester', '11 Semester']);
                    $passCredit = $faker->numberBetween(140, 150);
                }
                
                // Generate nama dengan gender
                $gender = $faker->randomElement(['male', 'female']);
                $fullName = $gender === 'male' 
                    ? $faker->firstNameMale . ' ' . $faker->lastName
                    : $faker->firstNameFemale . ' ' . $faker->lastName;

                // Tentukan status eligible berdasarkan kriteria
                // Eligible jika: IPK >= 2.75 dan Masa Studi <= batas maksimal
                $isEligible = true;
                
                // Cek IPK minimal
                if ($ipk < 2.75) {
                    $isEligible = false;
                }
                
                // Cek masa studi maksimal berdasarkan jenjang
                $masaSemester = (int) filter_var($masaStudi, FILTER_SANITIZE_NUMBER_INT);
                if ($jenjang === 'D3' && $masaSemester > 8) {
                    $isEligible = false;
                } elseif (($jenjang === 'D4' || $jenjang === 'S1') && $masaSemester > 14) {
                    $isEligible = false;
                } elseif ($jenjang === 'S2' && $masaSemester > 8) {
                    $isEligible = false;
                } elseif ($jenjang === 'S3' && $masaSemester > 10) {
                    $isEligible = false;
                }
                
                $status = $isEligible ? 'Eligible' : 'Tidak Eligible';

                DB::table('mahasiswa')->insert([
                    'STUDENTID' => $nim,
                    'FULLNAME' => $fullName,
                    'MASA_STUDI' => $masaStudi,
                    'PASS_CREDIT' => $passCredit,
                    'GPA' => $ipk,
                    'STATUS' => $status,
                    'STUDYPROGRAMID' => $studyProgramId,
                    'FACULTYID' => $facultyId,
                    'PREDIKAT' => $predikat,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalMahasiswa++;
                $fakultasStats[$facultyId]['count']++;
            }
        }

        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
        $this->command->info('✅ Mahasiswa Seeder Completed!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info("   Total Program Studi: " . count($programStudi));
        $this->command->info("   Total Mahasiswa: {$totalMahasiswa}");
        $this->command->info("   Mahasiswa per Prodi: Random 1-10");
        $this->command->info('');
        $this->command->info('📋 Fakultas Breakdown:');
        foreach ($fakultasStats as $id => $stat) {
            $this->command->info("   {$stat['nama']} (ID {$id}): {$stat['count']} mahasiswa");
        }
        $this->command->info('');
        $this->command->info('💡 Note: Data STUDYPROGRAMID diambil dari API Telkom University');
        $this->command->info('   Jumlah mahasiswa per prodi: Random 1-10');
    }
}
