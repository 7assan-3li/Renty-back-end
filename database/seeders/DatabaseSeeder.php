<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);

        // --- مستخدم أحمد ---
        User::factory()->create([
            'name'     => 'Ahmed',
            'email'    => 'Ahmed@gmail.com',
            'phone'    => '77019481',
            'password' => \Illuminate\Support\Facades\Hash::make('ahmed0073'),
            'role'     => \App\Constants\UserRole::ADMIN,
        ]);

        $this->call(AdminUserSeeder::class);
        $this->call(CategoryCarSeeder::class);
    }
}
