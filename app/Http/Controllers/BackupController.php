<?php

namespace App\Http\Controllers;

use App\Models\BackupSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function index()
    {
        $settings = BackupSetting::current();
        $backups = [];
        
        // Native PHP path to storage/app/your_folder
        $dirPath = storage_path('app/' . $settings->backup_path);
        
        // Read files directly from the server filesystem
        if (is_dir($dirPath)) {
            $files = array_diff(scandir($dirPath), ['.', '..']);
            foreach ($files as $file) {
                $filePath = $dirPath . '/' . $file;
                if (is_file($filePath)) {
                    $backups[] = [
                        'name' => $file,
                        'size' => round(filesize($filePath) / 1024, 2), // KB
                        'date' => date('Y-m-d H:i:s', filemtime($filePath))
                    ];
                }
            }
        }
        
        // Sort by date descending
        usort($backups, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return view('backup.index', compact('settings', 'backups'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'backup_time' => 'required|date_format:H:i',
            'backup_path' => 'required|string|regex:/^[a-zA-Z0-9\-_\/]+$/'
        ]);

        $settings = BackupSetting::current();
        $path = rtrim($request->input('backup_path'), '/');
        
        // Explicitly check if the checkbox was submitted (returns 1 or 0)
        $isEnabled = $request->has('is_enabled') ? 1 : 0;
        
        $settings->update([
            'is_enabled' => $isEnabled,
            'backup_time' => $request->input('backup_time'),
            'backup_path' => $path
        ]);

        return redirect()->back()->with('success', 'Backup settings updated! Auto-backup is now ' . ($isEnabled ? 'ENABLED' : 'DISABLED') . '.');
    }

    public function runNow()
    {
        $settings = BackupSetting::current();
        $path = $settings->backup_path;
        
        // BULLETPROOF FOLDER CREATION
        $dirPath = storage_path('app/' . $path);
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true); 
        }

        $filename = 'backup-' . now()->format('Y-m-d_His') . '.sql';
        $fullPath = $dirPath . '/' . $filename;
        
        // Start writing the SQL file directly from PHP
        $handle = fopen($fullPath, 'w');
        
        fwrite($handle, "-- ShubhHMS Database Backup\n");
        fwrite($handle, "-- Generated: " . now()->toDateTimeString() . "\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = DB::select('SHOW TABLES');
        
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            
            $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createTableSql = $createTableResult[0]->{'Create Table'};
            
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($handle, $createTableSql . ";\n\n");
            
            $rows = DB::table($tableName)->get();
            
            foreach ($rows as $row) {
                $rowArray = (array)$row;
                $columns = array_keys($rowArray);
                $values = array_values($rowArray);
                
                $escapedValues = array_map(function($value) {
                    if (is_null($value)) return 'NULL';
                    if (is_numeric($value) && !is_string($value)) return $value;
                    return "'" . addslashes($value) . "'";
                }, $values);
                
                $columnList = '`' . implode('`, `', $columns) . '`';
                $valueList = implode(', ', $escapedValues);
                
                fwrite($handle, "INSERT INTO `{$tableName}` ({$columnList}) VALUES ({$valueList});\n");
            }
            fwrite($handle, "\n");
        }
        
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
        
        $settings->update(['last_backup_at' => now()]);

        return redirect()->back()->with('success', 'Manual backup created successfully! Click Download to save it to your computer.');
    }

    public function download($filename)
    {
        $settings = BackupSetting::current();
        
        // Native PHP path to the file
        $filePath = storage_path('app/' . $settings->backup_path . '/' . $filename);
        
        if (!file_exists($filePath)) {
            abort(404, 'Backup file not found.');
        }

        // Force download using native PHP
        return response()->download($filePath);
    }

    public function import(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt'
        ]);

        $file = $request->file('sql_file');
        $sql = file_get_contents($file->getRealPath());

        try {
            DB::unprepared($sql);
            return redirect()->back()->with('success', 'Database restored successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
}