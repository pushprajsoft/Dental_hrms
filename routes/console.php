<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;
use App\Models\BackupSetting;

// Schedule the daily auto-backup
Schedule::call(function () {
    $settings = BackupSetting::current();
    
    // Double-check if backup is enabled before running
    if ($settings->is_enabled) {
        Artisan::call('db:backup');
    }
})->dailyAt(BackupSetting::current()->backup_time)
  ->name('daily-database-backup')
  ->withoutOverlapping(); // Prevents overlapping runs if the backup takes a long time