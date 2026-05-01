<?php

namespace Database\Seeders;

use App\Models\Pejabat;
use Illuminate\Database\Seeder;

class PejabatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pejabats = [
            // Rektor (Level Universitas)
            [
                'fakultas_id' => null,
                'nama' => 'Adiwijaya',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.Si., M.Si.',
                'jabatan' => 'Rektor',
                'level' => 'universitas',
                'aktif' => true,
            ],
            
            // Dekan Fakultas Ilmu Terapan (FIT) - ID 3
            [
                'fakultas_id' => 3,
                'nama' => 'Dedy Rahman Wijaya',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.T., M.T.',
                'jabatan' => 'Dekan',
                'level' => 'fakultas',
                'aktif' => true,
            ],
            
            // Dekan Fakultas Industri Kreatif (FIK) - ID 4
            [
                'fakultas_id' => 4,
                'nama' => 'Dandi Yunidar',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Sn., M.Ds., Ph.D.',
                'jabatan' => 'Dekan',
                'level' => 'fakultas',
                'aktif' => true,
            ],
            
            // Dekan Fakultas Teknik Elektro (FTE) - ID 5
            [
                'fakultas_id' => 5,
                'nama' => 'Achmad Rizal',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.T., M.T.',
                'jabatan' => 'Dekan',
                'level' => 'fakultas',
                'aktif' => true,
            ],
            
            // Dekan Fakultas Rekayasa Industri (FRI) - ID 6
            [
                'fakultas_id' => 6,
                'nama' => 'Muhammad Iqbal',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.T., M.M.',
                'jabatan' => 'Dekan',
                'level' => 'fakultas',
                'aktif' => true,
            ],
            
            // Dekan Fakultas Informatika (FIF) - ID 7
            [
                'fakultas_id' => 7,
                'nama' => 'Kemas Muslim Lhaksmana',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => null,
                'jabatan' => 'Dekan',
                'level' => 'fakultas',
                'aktif' => true,
            ],
            
            // Dekan Fakultas Ekonomi dan Bisnis (FEB) - ID 8
            [
                'fakultas_id' => 8,
                'nama' => 'Farida Titik Kristanti',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.E., M.Si.',
                'jabatan' => 'Dekan',
                'level' => 'fakultas',
                'aktif' => true,
            ],
            
            // Dekan Fakultas Komunikasi dan Sosial (FKB) - ID 9
            [
                'fakultas_id' => 9,
                'nama' => 'Lis Kurnia Nurhayati',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.S., M.Hum.',
                'jabatan' => 'Dekan',
                'level' => 'fakultas',
                'aktif' => true,
            ],
        ];

        foreach ($pejabats as $pejabat) {
            Pejabat::updateOrCreate(
                [
                    'fakultas_id' => $pejabat['fakultas_id'],
                    'jabatan' => $pejabat['jabatan'],
                ],
                $pejabat
            );
        }

        $this->command->info('✅ Data Pejabat (Dekan) berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Daftar Dekan per Fakultas:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Rektor:');
        $this->command->info('  Prof. Dr. Adiwijaya, S.Si., M.Si.');
        $this->command->info('');
        $this->command->info('Fakultas Ilmu Terapan (FIT - ID 3):');
        $this->command->info('  Prof. Dr. Dedy Rahman Wijaya, S.T., M.T.');
        $this->command->info('');
        $this->command->info('Fakultas Industri Kreatif (FIK - ID 4):');
        $this->command->info('  Dandi Yunidar, S.Sn., M.Ds., Ph.D.');
        $this->command->info('');
        $this->command->info('Fakultas Teknik Elektro (FTE - ID 5):');
        $this->command->info('  Prof. Dr. Achmad Rizal, S.T., M.T.');
        $this->command->info('');
        $this->command->info('Fakultas Rekayasa Industri (FRI - ID 6):');
        $this->command->info('  Dr. Muhammad Iqbal, S.T., M.M.');
        $this->command->info('');
        $this->command->info('Fakultas Informatika (FIF - ID 7):');
        $this->command->info('  Dr. Kemas Muslim Lhaksmana');
        $this->command->info('');
        $this->command->info('Fakultas Ekonomi dan Bisnis (FEB - ID 8):');
        $this->command->info('  Prof. Dr. Farida Titik Kristanti, S.E., M.Si.');
        $this->command->info('');
        $this->command->info('Fakultas Komunikasi dan Sosial (FKB - ID 9):');
        $this->command->info('  Dr. Lis Kurnia Nurhayati, S.S., M.Hum.');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
        $this->command->info('Total: ' . Pejabat::count() . ' pejabat');
    }
}
