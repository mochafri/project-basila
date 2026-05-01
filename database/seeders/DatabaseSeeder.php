<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\YudiciumSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Commented out - kolom name dan email sudah tidak ada
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            SuperAdminSeeder::class,
            UserRoleSeeder::class,
            PejabatSeeder::class,
            MahasiswaSeeder::class,
            YudisiumSeeder::class,
            MhsYudisiumSeeder::class,
        ]);
    }
}