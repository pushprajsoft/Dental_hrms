<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;
use App\Models\BackupSetting;

// Get backup time safely (use default if table doesn't exist yet)
 $backupTime = '02:00';
try {
    $backupTime = BackupSetting::current()->backup_time;
} catch (\Exception $e) {
    // Table doesn't exist yet during composer install - use default
}

// Schedule the daily auto-backup
Schedule::call(function () {
    $settings = BackupSetting::current();
    
    // Double-check if backup is enabled before running
    if ($settings->is_enabled) {
        Artisan::call('db:backup');
    }
})->dailyAt($backupTime)
  ->name('daily-database-backup')
  ->withoutOverlapping();