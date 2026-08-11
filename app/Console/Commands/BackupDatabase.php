<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\BackupSetting;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the MySQL database to a SQL file using pure PHP';

    public function handle()
    {
        $settings = BackupSetting::current();
        
        if (!$settings->is_enabled) {
            $this->info('Auto-backup is disabled. Exiting...');
            return;
        }

        $this->info('Starting database backup (Pure PHP)...');

        $filename = 'backup-' . now()->format('Y-m-d_His') . '.sql';
        $path = $settings->backup_path;
        
        // Ensure directory exists
        if (!Storage::disk('local')->exists($path)) {
            Storage::disk('local')->makeDirectory($path);
        }

        $fullPath = storage_path('app/' . $path . '/' . $filename);
        
        // Start writing the SQL file
        $handle = fopen($fullPath, 'w');
        
        // Add SQL headers
        fwrite($handle, "-- ShubhHMS Database Backup\n");
        fwrite($handle, "-- Generated: " . now()->toDateTimeString() . "\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        // Get all tables
        $tables = DB::select('SHOW TABLES');
        
        foreach ($tables as $table) {
            // Extract table name (handles different database connection names)
            $tableName = array_values((array)$table)[0];
            
            $this->info("Backing up table: {$tableName}");
            
            // Get Create Table statement
            $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createTableSql = $createTableResult[0]->{'Create Table'};
            
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($handle, $createTableSql . ";\n\n");
            
            // Get Data
            $rows = DB::table($tableName)->get();
            
            foreach ($rows as $row) {
                $rowArray = (array)$row;
                $columns = array_keys($rowArray);
                $values = array_values($rowArray);
                
                // Escape values
                $escapedValues = array_map(function($value) {
                    if (is_null($value)) {
                        return 'NULL';
                    }
                    if (is_numeric($value) && !is_string($value)) {
                        return $value;
                    }
                    return "'" . addslashes($value) . "'";
                }, $values);
                
                $columnList = '`' . implode('`, `', $columns) . '`';
                $valueList = implode(', ', $escapedValues);
                
                fwrite($handle, "INSERT INTO `{$tableName}` ({$columnList}) VALUES ({$valueList});\n");
            }
            
            fwrite($handle, "\n\n");
        }
        
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
        
        $settings->update(['last_backup_at' => now()]);
        $this->info("Backup created successfully: {$filename}");
    }
}