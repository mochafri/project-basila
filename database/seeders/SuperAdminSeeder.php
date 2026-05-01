<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeder user admin lokal yang bypass SSO dan mendapatkan akses SUPERADMIN
        $user = User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'nip' => '999999',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Create user role for superadmin
        UserRole::updateOrCreate(
            ['user_nib' => $user->id],
            [
                'user_fullname' => 'Super Administrator',
                'role_id' => 10000,
            ]
        );
    }
}
