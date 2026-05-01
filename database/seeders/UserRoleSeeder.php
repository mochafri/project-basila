<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data users dengan struktur seperti screenshot
        $users = [
            // Administrator
            [
                'username' => 'admin',
                'nip' => '000000',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'user_role' => [
                    'user_fullname' => 'Administrator',
                    'role_id' => 10000,
                ],
            ],
            
            // Kamar Begian
            [
                'username' => 'kamar',
                'nip' => '111111',
                'password' => Hash::make('kamar123'),
                'role' => 'laak',
                'user_role' => [
                    'user_fullname' => 'Kamar Begian',
                    'role_id' => 50055,
                ],
            ],
            
            // Kaur Akademik 1
            [
                'username' => 'kaur1',
                'nip' => '198704321',
                'password' => Hash::make('kaur123'),
                'role' => 'dekan',
                'user_role' => [
                    'user_fullname' => 'Kaur Akademik 1',
                    'role_id' => 34803,
                ],
            ],
            
            // Kaur Akademik 2
            [
                'username' => 'kaur2',
                'nip' => '198766432',
                'password' => Hash::make('kaur123'),
                'role' => 'dekan',
                'user_role' => [
                    'user_fullname' => 'Kaur Akademik 2',
                    'role_id' => 30057,
                ],
            ],
            
            // Staff A1
            [
                'username' => 'staffa1',
                'nip' => '2001001',
                'password' => Hash::make('staff123'),
                'role' => 'kaprodi',
                'user_role' => [
                    'user_fullname' => 'Staff A1',
                    'role_id' => 30056,
                ],
            ],
            
            // Staff A2
            [
                'username' => 'staffa2',
                'nip' => '2001002',
                'password' => Hash::make('staff123'),
                'role' => 'kaprodi',
                'user_role' => [
                    'user_fullname' => 'Staff A2',
                    'role_id' => 30060,
                ],
            ],
            
            // Staff B1
            [
                'username' => 'staffb1',
                'nip' => '2002001',
                'password' => Hash::make('staff123'),
                'role' => 'dosen',
                'user_role' => [
                    'user_fullname' => 'Staff B1',
                    'role_id' => 30055,
                ],
            ],
            
            // Staff B2
            [
                'username' => 'staffb2',
                'nip' => '2002002',
                'password' => Hash::make('staff123'),
                'role' => 'dosen',
                'user_role' => [
                    'user_fullname' => 'Staff B2',
                    'role_id' => 30056,
                ],
            ],
        ];

        foreach ($users as $userData) {
            // Create or update user (tanpa force ID)
            $user = User::updateOrCreate(
                ['username' => $userData['username']],
                [
                    'nip' => $userData['nip'],
                    'password' => $userData['password'],
                    'role' => $userData['role'],
                ]
            );

            // Create or update user role
            if (isset($userData['user_role'])) {
                UserRole::updateOrCreate(
                    ['user_nib' => $user->id],
                    [
                        'user_fullname' => $userData['user_role']['user_fullname'],
                        'role_id' => $userData['user_role']['role_id'],
                    ]
                );
            }
        }

        $this->command->info('✅ Users dan User Roles berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Daftar User:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        // Display actual data from database
        $allUsers = User::with('userRole')->get();
        foreach ($allUsers as $user) {
            $roleId = $user->userRole ? $user->userRole->role_id : 'N/A';
            $fullname = $user->userRole ? $user->userRole->user_fullname : 'N/A';
            $this->command->info(sprintf(
                '%2d | %-10s | %-10s | %-10s | %-8s | %s',
                $user->id,
                $user->username,
                $user->nip,
                $user->role,
                $roleId,
                $fullname
            ));
        }
        
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('');
        $this->command->info('🔑 Default Passwords:');
        $this->command->info('  admin/kamar: admin123/kamar123');
        $this->command->info('  kaur1/kaur2: kaur123');
        $this->command->info('  staff*: staff123');
    }
}
