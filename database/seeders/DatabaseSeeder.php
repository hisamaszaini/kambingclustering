<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@kambingmonitoring.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // User account
        User::updateOrCreate(
            ['username' => 'user'],
            [
                'name' => 'Petugas',
                'email' => 'user@kambingmonitoring.com',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );
    }
}
