<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email}';
    protected $description = 'Reset user password';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found!");
            return 1;
        }

        $user->update([
            'password' => Hash::make('password')
        ]);

        $this->info("Password reset successfully for {$user->email}");
        $this->info("New Password: password");
        
        return 0;
    }
}