@extends('layouts.app')

@section('title', 'Backup & Restore')
@section('page-title', 'System Backup & Restore')
@section('page-subtitle', 'Schedule auto-backups, export data, and restore your database safely')

@section('content')

<style>
    .backup-container { max-width: 900px; margin: 0 auto; }
    .backup-card { background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #f1f5f9; }
    .backup-card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
    .backup-card-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; color: #123C3A; font-family: 'Outfit', sans-serif; }
    .backup-card-header .icon-box { width: 40px; height: 40px; border-radius: 10px; background: #f0fdfa; color: #3FBFAD; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 8px; }
    .form-input { width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.95rem; }
    .form-input:focus { outline: none; border-color: #3FBFAD; background: #fff; }
    
    /* Animated Toggle Switch */
    .switch { position: relative; display: inline-block; width: 50px; height: 24px; margin-top: 5px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 24px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #22C55E; }
    input:checked + .slider:before { transform: translateX(26px); }
    
    /* Animated Buttons */
    .btn-backup { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer; font-size: 1rem; transition: all 0.2s; text-decoration: none; }
    .btn-save { background: #3FBFAD; color: #fff; }
    .btn-save:hover { background: #17847A; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(63,191,173,0.2); }
    .btn-run { background: #2563eb; color: #fff; }
    .btn-run:hover { background: #1e40af; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(37,99,235,0.2); }
    .btn-import { background: #7C5CFC; color: #fff; }
    .btn-import:hover { background: #4B2ED8; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(124,92,252,0.2); }
    .btn-download { background: #22C55E; color: #fff; padding: 8px 14px; font-size: 0.85rem; }
    .btn-download:hover { background: #15803D; transform: translateY(-1px); }
    
    .upload-zone { border: 2px dashed #e2e8f0; background: #f8fafc; padding: 30px; border-radius: 12px; text-align: center; }
    .upload-zone i { font-size: 2rem; color: #7C5CFC; margin-bottom: 10px; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
    th { background: #f8fafc; color: #64748b; font-weight: 600; }
</style>

<div class="backup-container">

    @if(session('success'))
        <div class="alert-clinic" style="background: #D1FAE5; color: #065F46; margin-bottom: 20px; border: 1px solid #A7F3D0;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-clinic" style="background: #FEF2F2; color: #B91C1C; margin-bottom: 20px; border: 1px solid #FCA5A5;">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-clinic" style="background: #FEF2F2; color: #B91C1C; margin-bottom: 20px; border: 1px solid #FCA5A5;">
            <i class="fa-solid fa-triangle-exclamation"></i> 
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-family: 'Outfit'; color: #123C3A; margin: 0;">Backup & Restore</h1>
            <p style="color: #64748b; margin: 5px 0 0 0;">Last Auto-Backup: <strong>{{ $settings->last_backup_at ? \Carbon\Carbon::parse($settings->last_backup_at)->diffForHumans() : 'Never' }}</strong></p>
        </div>
        <form action="{{ route('backup.run') }}" method="POST">
            @csrf
            <button type="submit" class="btn-backup btn-run"><i class="fa-solid fa-play"></i> Run Backup Now</button>
        </form>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        
        <!-- LEFT COLUMN: Settings & Export -->
        <div>
            <!-- Settings Card -->
            <div class="backup-card">
                <div class="backup-card-header">
                    <div class="icon-box"><i class="fa-solid fa-gear"></i></div>
                    <h3>Scheduling Settings</h3>
                </div>
                <form action="{{ route('backup.settings') }}" method="POST">
                    @csrf
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Enable Auto-Backup</label>
                        <label class="switch">
                            <input type="checkbox" name="is_enabled" {{ $settings->is_enabled ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Daily Backup Time</label>
                        <input type="time" name="backup_time" class="form-input" value="{{ $settings->backup_time }}" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Custom Storage Path (Server)</label>
                        <input type="text" name="backup_path" class="form-input" value="{{ $settings->backup_path }}" required>
                        <small style="color: #94a3b8; margin-top: 5px;">
                            <i class="fa-solid fa-circle-info"></i> You can use nested folders (e.g., <strong>my_backups/daily</strong>).<br>
                            Files save securely on the server at: <strong>storage/app/your_folder</strong>.<br>
                            Use the <strong>Download</strong> button below to save them to your local computer.
                        </small>
                    </div>
                    <button type="submit" class="btn-backup btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Settings</button>
                </form>
            </div>

            <!-- Export Card -->
            <div class="backup-card">
                <div class="backup-card-header">
                    <div class="icon-box" style="color: #22C55E; background: #f0fdf4;"><i class="fa-solid fa-file-export"></i></div>
                    <h3>Existing Backups</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                        <tr>
                            <td>{{ $backup['name'] }}</td>
                            <td>{{ $backup['size'] }} KB</td>
                            <td>{{ $backup['date'] }}</td>
                            <td>
                                <a href="{{ route('backup.download', $backup['name']) }}" class="btn-backup btn-download"><i class="fa-solid fa-download"></i> Download</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8;">No backups found yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT COLUMN: Import -->
        <div>
            <div class="backup-card">
                <div class="backup-card-header">
                    <div class="icon-box" style="color: #7C5CFC; background: #f5f3ff;"><i class="fa-solid fa-file-import"></i></div>
                    <h3>Restore Database</h3>
                </div>
                <div class="upload-zone">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <h4 style="margin: 0 0 5px 0; color: #123C3A;">Upload .SQL File</h4>
                    <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 20px;">Select a backup file to restore the database.</p>
                    <form action="{{ route('backup.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="sql_file" accept=".sql,.txt" required style="margin-bottom: 20px;">
                        <br>
                        <button type="submit" class="btn-backup btn-import"><i class="fa-solid fa-upload"></i> Upload & Restore</button>
                    </form>
                </div>
                <div style="margin-top: 20px; background: #fff7ed; border: 1px solid #ffedd5; color: #c2410c; padding: 15px; border-radius: 10px; font-size: 0.85rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <strong>Warning:</strong> Restoring a database will overwrite all current data. Ensure you have a recent backup before proceeding.
                </div>
            </div>
        </div>

    </div>
</div>

@endsection