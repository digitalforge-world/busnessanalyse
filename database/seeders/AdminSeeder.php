<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bi-analyzer.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'plan' => 'agency',
                'email_verified_at' => now(),
            ]
        );
    }
}
