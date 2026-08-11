@extends('layouts.app')

@section('title', 'IPD Dashboard')
@section('page-title', 'IPD Dashboard')
@section('page-subtitle', 'In-Patient Department Management & Revenue Analytics')

@section('content')

<style>
    /* IPD Design Tokens (Indigo/Blue Theme) */
    :root {
        --ipd-primary: #4154f1;
        --ipd-dark: #012970;
        --ipd-light: #f6f9ff;
        --ipd-border: #e2e8f0;
    }
    
    /* Filter Panel */
    .ipd-filter-panel {
        background: #fff; border-radius: 14px; padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;
        border: 1px solid #f1f5f9;
    }
    .ipd-filter-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px; align-items: end;
    }
    .ipd-input {
        width: 100%; padding: 10px 12px; border-radius: 8px;
        border: 1px solid var(--ipd-border); background: #fff;
        font-size: 0.9rem; color: #1e293b;
    }
    .ipd-input:focus { border-color: var(--ipd-primary); outline: none; box-shadow: 0 0 0 3px rgba(65, 84, 241, 0.1); }
    
    /* Stats Grid */
    .ipd-stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px; margin-bottom: 20px;
    }
    .ipd-stat-card {
        background: #fff; border-radius: 12px; padding: 18px;
        display: flex; align-items: center; gap: 15px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border-left: 5px solid var(--ipd-primary);
    }
    .ipd-icon-box {
        width: 45px; height: 45px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #fff; flex-shrink: 0;
    }
    
    .ipd-panel {
        background: #fff; border-radius: 14px; padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;
    }
    .ipd-panel-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;
    }
    .ipd-panel-header h2 { margin: 0; font-size: 1.1rem; color: var(--ipd-dark); font-weight: 700; }
    
    .ipd-table { width: 100%; border-collapse: collapse; }
    .ipd-table th { text-align: left; font-size: 0.75rem; color: #64748b; padding: 12px; border-bottom: 2px solid #f1f5f9; }
    .ipd-table td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #444; }
    
    .btn-ipd {
        background: var(--ipd-primary); color: #fff; padding: 10px 20px;
        border-radius: 8px; border: none; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
        font-size: 0.9rem;
    }
    .btn-ipd:hover { background: var(--ipd-dark); }
    
    .badge-admitted { background: #D1FAE5; color: #065F46; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-discharged { background: #FEE2E2; color: #B91C1C; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
</style>

<!-- IPD Stats Cards (Visit Summary) -->
<div class="ipd-stats-grid">
    <div class="ipd-stat-card">
        <div class="ipd-icon-box" style="background: var(--ipd-primary);">
            <i class="fa-solid fa-procedures"></i>
        </div>
        <div>
            <div style="font-size: 1.3rem; font-weight: 800; color: var(--ipd-dark);">{{ $stats['total'] }}</div>
            <div style="font-size: 0.8rem; color: #64748b;">Total Admissions</div>
        </div>
    </div>
    
    <div class="ipd-stat-card" style="border-left-color: #22C55E;">
        <div class="ipd-icon-box" style="background: #22C55E;">
            <i class="fa-solid fa-bed"></i>
        </div>
        <div>
            <div style="font-size: 1.3rem; font-weight: 800; color: var(--ipd-dark);">{{ $stats['admitted'] }}</div>
            <div style="font-size: 0.8rem; color: #64748b;">Currently Admitted</div>
        </div>
    </div>

    <div class="ipd-stat-card" style="border-left-color: #EF4444;">
        <div class="ipd-icon-box" style="background: #EF4444;">
            <i class="fa-solid fa-door-open"></i>
        </div>
        <div>
            <div style="font-size: 1.3rem; font-weight: 800; color: var(--ipd-dark);">{{ $stats['discharged'] }}</div>
            <div style="font-size: 0.8rem; color: #64748b;">Discharged</div>
        </div>
    </div>
</div>

<!-- Collection Breakdown (Revenue) -->
<div class="ipd-stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
    
    <!-- Cash -->
    <div class="ipd-stat-card" style="border-left-color: #16a34a;">
        <div class="ipd-icon-box" style="background: #16a34a;">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <div>
            <div style="font-size: 1.2rem; font-weight: 800; color: var(--ipd-dark);">₹{{ number_format($stats['cash'], 2) }}</div>
            <div style="font-size: 0.8rem; color: #64748b;">Cash Collected</div>
        </div>
    </div>
    
    <!-- UPI -->
    <div class="ipd-stat-card" style="border-left-color: #2563eb;">
        <div class="ipd-icon-box" style="background: #2563eb;">
            <i class="fa-solid fa-mobile-screen"></i>
        </div>
        <div>
            <div style="font-size: 1.2rem; font-weight: 800; color: var(--ipd-dark);">₹{{ number_format($stats['upi'], 2) }}</div>
            <div style="font-size: 0.8rem; color: #64748b;">UPI Collected</div>
        </div>
    </div>

    <!-- Other (Card/Cheque) -->
    <div class="ipd-stat-card" style="border-left-color: #7C5CFC;">
        <div class="ipd-icon-box" style="background: #7C5CFC;">
            <i class="fa-solid fa-credit-card"></i>
        </div>
        <div>
            <div style="font-size: 1.2rem; font-weight: 800; color: var(--ipd-dark);">₹{{ number_format($stats['other'], 2) }}</div>
            <div style="font-size: 0.8rem; color: #64748b;">Card / Cheque</div>
        </div>
    </div>

    <!-- Pending -->
    <div class="ipd-stat-card" style="border-left-color: #F59E0B;">
        <div class="ipd-icon-box" style="background: #F59E0B;">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div>
            <div style="font-size: 1.2rem; font-weight: 800; color: var(--ipd-dark);">₹{{ number_format($stats['pending'], 2) }}</div>
            <div style="font-size: 0.8rem; color: #64748b;">Pending Bills</div>
        </div>
    </div>

    <!-- Refund -->
    <div class="ipd-stat-card" style="border-left-color: #dc2626;">
        <div class="ipd-icon-box" style="background: #dc2626;">
            <i class="fa-solid fa-rotate-left"></i>
        </div>
        <div>
            <div style="font-size: 1.2rem; font-weight: 800; color: var(--ipd-dark);">₹{{ number_format($stats['refund'], 2) }}</div>
            <div style="font-size: 0.8rem; color: #64748b;">Refunded</div>
        </div>
    </div>

    <!-- Net Revenue -->
    <div class="ipd-stat-card" style="background: linear-gradient(135deg, var(--ipd-dark), var(--ipd-primary)); border: none;">
        <div class="ipd-icon-box" style="background: rgba(255,255,255,0.2); color: #fff;">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
        <div>
            <div style="font-size: 1.2rem; font-weight: 800; color: #fff;">₹{{ number_format($stats['revenue'], 2) }}</div>
            <div style="font-size: 0.8rem; color: rgba(255,255,255,0.8);">Net Revenue</div>
        </div>
    </div>
</div>

<!-- Filter Panel -->
<div class="ipd-filter-panel">
    <form action="{{ route('ipd.dashboard') }}" method="GET">
        <div class="ipd-filter-grid">
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#64748b;">From Date</label>
                <input type="date" name="from_date" class="ipd-input" value="{{ $fromDate }}">
            </div>
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#64748b;">To Date</label>
                <input type="date" name="to_date" class="ipd-input" value="{{ $toDate }}">
            </div>
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#64748b;">Doctor</label>
                <select name="doctor_id" class="ipd-input">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}" @selected($doctorId == $d->id)>{{ $d->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#64748b;">Status</label>
                <select name="status" class="ipd-input">
                    <option value="All" @selected($status == 'All')>All Status</option>
                    <option value="Admitted" @selected($status == 'Admitted')>Admitted</option>
                    <option value="Discharged" @selected($status == 'Discharged')>Discharged</option>
                </select>
            </div>
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#64748b;">Search</label>
                <input type="text" name="search" class="ipd-input" placeholder="IPD Code / Name" value="{{ $search }}">
            </div>
            <div>
                <button type="submit" class="btn-ipd" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-filter"></i> Apply
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Admitted Patients List -->
<div class="ipd-panel">
    <div class="ipd-panel-header">
        <h2><i class="fa-solid fa-list-check" style="color: var(--ipd-primary);"></i> IPD Records (Filtered)</h2>
        <a href="{{ route('ipd.create') }}" class="btn-ipd" style="padding: 8px 16px; font-size: 0.85rem;">
            <i class="fa-solid fa-user-plus"></i> Add New Patient
        </a>
    </div>
    <div style="overflow-x: auto;">
    <table class="ipd-table">
        <thead>
            <tr>
                <th>IPD Code</th>
                <th>Patient Name</th>
                <th>Doctor</th>
                <th>Admitted On</th>
                <th>Advance</th>
                <th>Method</th>
                <th>Status</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admissions as $admission)
            <tr>
                <td><strong>{{ $admission->ipd_code }}</strong></td>
                <td>
                    <a href="{{ route('ipd.show', $admission->id) }}" style="color: var(--ipd-primary); font-weight: 600; text-decoration: none;">
                        {{ $admission->p_name ?? $admission->patient->full_name ?? 'N/A' }}
                    </a>
                </td>
                <td>{{ $admission->doctor->full_name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($admission->admission_date)->format('d M Y h:i A') }}</td>
                <td>₹{{ number_format($admission->advance_paid, 2) }}</td>
                <td>{{ $admission->payment_method ?? 'N/A' }}</td>
                <td>
                    @if($admission->status == 'Admitted')
                        <span class="badge-admitted">{{ $admission->status }}</span>
                    @else
                        <span class="badge-discharged">{{ $admission->status }}</span>
                    @endif
                </td>
                <td style="text-align: center; white-space: nowrap;">
                    <a href="{{ route('ipd.show', $admission->id) }}" class="btn-ipd" style="padding: 6px 10px; font-size: 0.8rem; background: #0d6efd; margin-right: 4px;" title="View Details">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <!-- NEW BED ALLOCATE BUTTON -->
                    <a href="{{ route('ipd.allocate', $admission->id) }}" class="btn-ipd" style="padding: 6px 10px; font-size: 0.8rem; background: #7C5CFC; margin-right: 4px;" title="Allocate Bed">
                        <i class="fa-solid fa-bed"></i>
                    </a>
                    <a href="{{ route('ipd.edit', $admission->id) }}" class="btn-ipd" style="padding: 6px 10px; font-size: 0.8rem; background: #22C55E; margin-right: 4px;" title="Edit">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form action="{{ route('ipd.destroy', $admission->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ipd" style="padding: 6px 10px; font-size: 0.8rem; background: #EF4444;" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">
                    <i class="fa-solid fa-bed" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                    No records found for the selected filters.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@endsection