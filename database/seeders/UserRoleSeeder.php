<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Username yang valid — hapus semua user lain selain superadmin
        $validUsernames = [
            'superadmin',
            'admin',
            'laak01', 'laak02',
            'dekan_fit', 'dekan_fik', 'dekan_fte', 'dekan_fri', 'dekan_fif', 'dekan_feb', 'dekan_fkb',
            'dosen01', 'dosen02', 'dosen03', 'dosen04',
        ];

        // Hapus user lama yang tidak ada di daftar valid
        $oldUsers = User::whereNotIn('username', $validUsernames)->get();
        foreach ($oldUsers as $old) {
            UserRole::where('user_nib', $old->id)->delete();
            $old->delete();
        }

        $users = [
            // ─── ADMIN ───────────────────────────────────────────────
            [
                'username' => 'admin',
                'nip'      => '196503121990031002',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'fullname' => 'Budi Santoso',
                'role_id'  => 10000,
            ],

            // ─── LAAK ────────────────────────────────────────────────
            [
                'username' => 'laak01',
                'nip'      => '197204251998031003',
                'password' => Hash::make('password'),
                'role'     => 'laak',
                'fullname' => 'Siti Rahayu',
                'role_id'  => 50055,
            ],
            [
                'username' => 'laak02',
                'nip'      => '198001152005012004',
                'password' => Hash::make('password'),
                'role'     => 'laak',
                'fullname' => 'Agus Purnomo',
                'role_id'  => 50055,
            ],

            // ─── DEKAN ───────────────────────────────────────────────
            [
                'username'    => 'dekan_fit',
                'nip'         => '196812201994031001',
                'password'    => Hash::make('password'),
                'role'        => 'dekan',
                'fakultas_id' => 3,   // FIT - Fakultas Ilmu Terapan
                'fullname'    => 'Dedy Rahman Wijaya',
                'role_id'     => 34803,
            ],
            [
                'username'    => 'dekan_fik',
                'nip'         => '197505102001121002',
                'password'    => Hash::make('password'),
                'role'        => 'dekan',
                'fakultas_id' => 4,   // FIK - Fakultas Industri Kreatif
                'fullname'    => 'Dandi Yunidar',
                'role_id'     => 34803,
            ],
            [
                'username'    => 'dekan_fte',
                'nip'         => '196904151994031005',
                'password'    => Hash::make('password'),
                'role'        => 'dekan',
                'fakultas_id' => 5,   // FTE - Fakultas Teknik Elektro
                'fullname'    => 'Achmad Rizal',
                'role_id'     => 34803,
            ],
            [
                'username'    => 'dekan_fri',
                'nip'         => '197308221999031003',
                'password'    => Hash::make('password'),
                'role'        => 'dekan',
                'fakultas_id' => 6,   // FRI - Fakultas Rekayasa Industri
                'fullname'    => 'Muhammad Iqbal',
                'role_id'     => 34803,
            ],
            [
                'username'    => 'dekan_fif',
                'nip'         => '197601102003121001',
                'password'    => Hash::make('password'),
                'role'        => 'dekan',
                'fakultas_id' => 7,   // FIF - Fakultas Informatika
                'fullname'    => 'Kemas Muslim Lhaksmana',
                'role_id'     => 34803,
            ],
            [
                'username'    => 'dekan_feb',
                'nip'         => '196711281993032002',
                'password'    => Hash::make('password'),
                'role'        => 'dekan',
                'fakultas_id' => 8,   // FEB - Fakultas Ekonomi dan Bisnis
                'fullname'    => 'Farida Titik Kristanti',
                'role_id'     => 34803,
            ],
            [
                'username'    => 'dekan_fkb',
                'nip'         => '197209141998022001',
                'password'    => Hash::make('password'),
                'role'        => 'dekan',
                'fakultas_id' => 9,   // FKB - Fakultas Komunikasi dan Bisnis
                'fullname'    => 'Lis Kurnia Nurhayati',
                'role_id'     => 34803,
            ],

            // ─── DOSEN ───────────────────────────────────────────────
            [
                'username' => 'dosen01',
                'nip'      => '198203142006041001',
                'password' => Hash::make('password'),
                'role'     => 'dosen',
                'fullname' => 'Rizky Firmansyah',
                'role_id'  => 30055,
            ],
            [
                'username' => 'dosen02',
                'nip'      => '197911082004122003',
                'password' => Hash::make('password'),
                'role'     => 'dosen',
                'fullname' => 'Dewi Kusumawati',
                'role_id'  => 30055,
            ],
            [
                'username' => 'dosen03',
                'nip'      => '198507252010011005',
                'password' => Hash::make('password'),
                'role'     => 'dosen',
                'fullname' => 'Hendra Gunawan',
                'role_id'  => 30055,
            ],
            [
                'username' => 'dosen04',
                'nip'      => '198001302008012002',
                'password' => Hash::make('password'),
                'role'     => 'dosen',
                'fullname' => 'Nurul Hidayah',
                'role_id'  => 30055,
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'nip'         => $data['nip'],
                    'password'    => $data['password'],
                    'role'        => $data['role'],
                    'fakultas_id' => $data['fakultas_id'] ?? null,
                ]
            );

            UserRole::updateOrCreate(
                ['user_nib' => $user->id],
                [
                    'user_fullname' => $data['fullname'],
                    'role_id'       => $data['role_id'],
                ]
            );
        }

        $this->command->info('✅ Users dan User Roles berhasil dibuat!');
        $this->command->newLine();
        $this->command->info('📋 Daftar User:');
        $this->command->info(str_repeat('─', 90));
        $this->command->info(sprintf('%-4s %-15s %-20s %-10s %-8s %s', 'ID', 'Username', 'NIP', 'Role', 'Role ID', 'Nama'));
        $this->command->info(str_repeat('─', 90));

        User::with('userRole')->orderBy('id')->get()->each(function ($u) {
            $this->command->info(sprintf(
                '%-4d %-15s %-20s %-10s %-8s %s',
                $u->id,
                $u->username,
                $u->nip ?? '-',
                $u->role,
                $u->userRole?->role_id ?? '-',
                $u->userRole?->user_fullname ?? '-'
            ));
        });

        $this->command->info(str_repeat('─', 90));
        $this->command->newLine();
        $this->command->info('🔑 Password semua user: password');
        $this->command->info('📊 Total: ' . User::count() . ' users');
    }
}
