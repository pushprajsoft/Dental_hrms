<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    use HasFactory;

    protected $fillable = ['is_enabled', 'backup_time', 'backup_path', 'last_backup_at'];

    // ADD THIS: Tells Laravel to treat 'is_enabled' as a true boolean
    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public static function current()
    {
        return self::firstOrCreate(['id' => 1], [
            'is_enabled' => false,
            'backup_time' => '02:00',
            'backup_path' => 'app-backups'
        ]);
    }
}