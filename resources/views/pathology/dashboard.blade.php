@extends('layouts.app')

@section('title', 'Pathology Dashboard')
@section('page-title', 'Pathology Lab Dashboard')
@section('page-subtitle', 'Manage lab test requests, enter results, and print reports')

@section('content')

<style>
    :root {
        --path-primary: #2563eb;
        --path-dark: #1e3a8a;
        --path-light: #eff6ff;
    }
    .path-stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px; margin-bottom: 30px;
    }
    .path-stat-card {
        background: #fff; border-radius: 12px; padding: 20px;
        display: flex; align-items: center; gap: 15px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border-left: 5px solid var(--path-primary);
    }
    .path-icon-box {
        width: 50px; height: 50px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: #fff; flex-shrink: 0;
    }
    
    .path-panel {
        background: #fff; border-radius: 14px; padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;
    }
    .path-panel-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;
    }
    .path-panel-header h2 { margin: 0; font-size: 1.1rem; color: var(--path-dark); font-weight: 700; }
    
    .path-table { width: 100%; border-collapse: collapse; }
    .path-table th { text-align: left; font-size: 0.75rem; color: #64748b; padding: 12px; border-bottom: 2px solid #f1f5f9; }
    .path-table td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #444; }
    
    .btn-path {
        background: var(--path-primary); color: #fff; padding: 10px 20px;
        border-radius: 8px; border: none; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-size: 0.9rem;
    }
    .btn-path:hover { background: var(--path-dark); }
    
    .badge-pending { background: #FEF3C7; color: #92400E; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-collected { background: #DBEAFE; color: #1E40AF; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-completed { background: #D1FAE5; color: #065F46; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 8px; }
    .form-input { width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.95rem; }
    .form-input:focus { outline: none; border-color: var(--path-primary); background: #fff; }
</style>

@if(session('success'))
    <div class="alert-clinic" style="background: #D1FAE5; color: #065F46; margin-bottom: 20px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<!-- Pathology Stats Cards -->
<div class="path-stats-grid">
    <div class="path-stat-card">
        <div class="path-icon-box" style="background: #F59E0B;"><i class="fa-solid fa-hourglass-half"></i></div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--path-dark);">{{ $stats['pending'] }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Pending Tests</div>
        </div>
    </div>
    <div class="path-stat-card" style="border-left-color: #2563eb;">
        <div class="path-icon-box" style="background: #2563eb;"><i class="fa-solid fa-vial"></i></div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--path-dark);">{{ $stats['collected'] }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Samples Collected</div>
        </div>
    </div>
    <div class="path-stat-card" style="border-left-color: #22C55E;">
        <div class="path-icon-box" style="background: #22C55E;"><i class="fa-solid fa-check-circle"></i></div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--path-dark);">{{ $stats['completed'] }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Completed Tests</div>
        </div>
    </div>
    <div class="path-stat-card" style="border-left-color: #7C5CFC;">
        <div class="path-icon-box" style="background: #7C5CFC;"><i class="fa-solid fa-flask"></i></div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--path-dark);">{{ $stats['total'] }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Total Tests</div>
        </div>
    </div>
</div>

<!-- New Test Request Form -->
<div class="path-panel" id="new-test-form">
    <div class="path-panel-header">
        <h2><i class="fa-solid fa-plus-circle" style="color: var(--path-primary);"></i> New Test Request</h2>
    </div>
    <form action="{{ route('pathology.store') }}" method="POST">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Select Patient</label>
                <select name="patient_id" class="form-input" required>
                    <option value="">-- Choose Patient --</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->full_name }} ({{ $p->patient_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Referred By (Doctor)</label>
                <select name="doctor_id" class="form-input">
                    <option value="">-- Select Doctor --</option>
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}">{{ $d->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Test Name</label>
                <input type="text" name="test_name" class="form-input" placeholder="e.g., Complete Blood Count (CBC)" required>
            </div>
            <div class="form-group">
                <label>Notes / Clinical Summary</label>
                <input type="text" name="notes" class="form-input" placeholder="e.g., Routine checkup / Fever">
            </div>
        </div>
        <div style="text-align: right;">
            <button type="submit" class="btn-path"><i class="fa-solid fa-flask"></i> Request Test</button>
        </div>
    </form>
</div>

<!-- Recent & Pending Tests List -->
<div class="path-panel" id="pending-tests">
    <div class="path-panel-header">
        <h2><i class="fa-solid fa-list-check" style="color: var(--path-primary);"></i> Recent Lab Tests</h2>
    </div>
    <div style="overflow-x: auto;">
        <table class="path-table">
            <thead>
                <tr>
                    <th>Test Code</th>
                    <th>Patient Name</th>
                    <th>Test Name</th>
                    <th>Doctor</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tests as $test)
                <tr>
                    <td><strong>{{ $test->test_code }}</strong></td>
                    <td>{{ $test->patient->full_name ?? 'N/A' }}</td>
                    <td>{{ $test->test_name }}</td>
                    <td>{{ $test->doctor->full_name ?? 'N/A' }}</td>
                    <td>
                        @if($test->status == 'Pending')
                            <span class="badge-pending">{{ $test->status }}</span>
                        @elseif($test->status == 'Sample Collected')
                            <span class="badge-collected">{{ $test->status }}</span>
                        @else
                            <span class="badge-completed">{{ $test->status }}</span>
                        @endif
                    </td>
                    <td style="text-align:right; white-space: nowrap;">
                        @if($test->status != 'Completed')
                        <a href="{{ route('pathology.edit', $test->id) }}" class="btn-path" style="padding: 6px 12px; font-size: 0.8rem; background: #1e40af;">
                            <i class="fa-solid fa-pen"></i> Enter Results
                        </a>
                        @else
                        <a href="{{ route('pathology.report', $test->id) }}" target="_blank" class="btn-path" style="padding: 6px 12px; font-size: 0.8rem; background: #22C55E;">
                            <i class="fa-solid fa-file-pdf"></i> View Report
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">
                        <i class="fa-solid fa-flask" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                        No lab tests requested yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection