<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // This ensures the Super Admin is ALWAYS created when the database is fresh.
        User::updateOrCreate(
            ['email' => 'admin@dentalhrms.com'],
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'password' => 'password123', // Automatically hashed by your User model!
                'role' => 'super_admin',
                'status' => 'active',
                'security_question' => 'What is your clinic name?',
                'security_answer' => 'dentacare'
            ]
        );
    }
}