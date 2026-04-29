<?php
namespace Database\Seeders;

use App\Models\User;
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
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'password' => Hash::make('password123'),
            ]
        );
    }
}
