<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateTestUser extends Command
{
    protected $signature = 'user:create-test';
    protected $description = 'Create or show test admin user';

    public function handle()
    {
        // Check by username
        $userByUsername = User::where('username', 'admin')->first();
        
        // Check by email
        $userByEmail = User::where('email', 'admin@dentacare.com')->first();

        if ($userByUsername || $userByEmail) {
            $user = $userByUsername ?? $userByEmail;
            $this->warn('User already exists!');
            $this->newLine();
            $this->info("ID: {$user->id}");
            $this->info("Name: {$user->name}");
            $this->info("Username: {$user->username}");
            $this->info("Email: {$user->email}");
            $this->info("Role: {$user->role}");
            $this->newLine();
            $this->warn('Login Credentials:');
            $this->info("Email: admin@dentacare.com");
            $this->info("Password: password (or your existing password)");
            return 0;
        }

        // Create new user
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@dentacare.com',
            'phone' => '9999999999',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'status' => 'Active',
        ]);

        $this->info('Test user created successfully!');
        $this->newLine();
        $this->info("Email: admin@dentacare.com");
        $this->info("Password: password");
        
        return 0;
    }
}