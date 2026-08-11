@extends('layouts.app')

@section('title', 'Patient Details')
@section('page-title', 'Patient Details & Visit History')
@section('page-subtitle', 'Comprehensive view of patient IPD records and payments')

@section('content')

<style>
    :root {
        --ipd-primary: #4154f1;
        --ipd-dark: #012970;
        --ipd-border: #e2e8f0;
        --ipd-text-muted: #334155;
        --ipd-purple: #7C5CFC;
        --ipd-orange: #FF8A5C;
    }
    .ipd-details-container { width: 100%; margin: 0; }
    
    /* Header */
    .details-header {
        background: linear-gradient(135deg, #012970, #4154f1);
        color: #fff; border-radius: 16px; padding: 30px;
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 24px; box-shadow: 0 10px 30px rgba(1, 41, 112, 0.2);
    }
    .details-header h2 { margin: 0; font-size: 1.8rem; font-weight: 700; font-family: 'Outfit', sans-serif; }
    .details-header p { margin: 5px 0 0 0; opacity: 0.8; font-size: 1rem; }
    
    .btn-back { background: rgba(255,255,255,0.2); color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s; }
    .btn-back:hover { background: rgba(255,255,255,0.3); }
    .btn-edit { background: #22C55E; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-left: 10px; }
    
    /* Cards */
    .ipd-card { background: #fff; border-radius: 14px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #f1f5f9; }
    .ipd-card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
    .ipd-card-header h3 { margin: 0; font-size: 1.1rem; color: var(--ipd-dark); font-weight: 700; }
    .ipd-card-header .icon-box { width: 36px; height: 36px; border-radius: 8px; background: #f6f9ff; color: var(--ipd-primary); display: flex; align-items: center; justify-content: center; font-size: 16px; }
    
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
    .info-box { background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px solid #e2e8f0; }
    .info-box strong { display: block; color: var(--ipd-text-muted); font-size: 0.8rem; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-box span { font-size: 1.1rem; font-weight: 600; color: var(--ipd-dark); }
    
    /* Financial Stats */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 24px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 18px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border-left: 5px solid var(--ipd-primary); }
    .stat-icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fff; flex-shrink: 0; }
    
    /* Table */
    .ipd-table { width: 100%; border-collapse: collapse; }
    .ipd-table th { text-align: left; font-size: 0.75rem; color: #64748b; padding: 12px; border-bottom: 2px solid #f1f5f9; }
    .ipd-table td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #444; }
    
    .badge-admitted { background: #D1FAE5; color: #065F46; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-discharged { background: #FEE2E2; color: #B91C1C; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
</style>

<div class="ipd-details-container">

    <!-- Header -->
    <div class="details-header">
        <div>
            <h2>{{ $patient->full_name }}</h2>
            <p>{{ $patient->patient_code }} · {{ $patient->phone ?? 'No Phone' }}</p>
        </div>
        <div>
            <a href="{{ route('ipd.dashboard') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="{{ route('patients.edit', $patient->id) }}" class="btn-edit">
                <i class="fa-solid fa-pen"></i> Edit Patient
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="stats-grid">
        <div class="stat-card" style="border-left-color: var(--ipd-primary);">
            <div class="stat-icon" style="background: var(--ipd-primary);"><i class="fa-solid fa-procedures"></i></div>
            <div>
                <div style="font-size: 1.3rem; font-weight: 800; color: var(--ipd-dark);">{{ $lifetime['totalVisits'] }}</div>
                <div style="font-size: 0.8rem; color: #64748b;">Total IPD Visits</div>
            </div>
        </div>
        <div class="stat-card" style="border-left-color: #16a34a;">
            <div class="stat-icon" style="background: #16a34a;"><i class="fa-solid fa-sack-dollar"></i></div>
            <div>
                <div style="font-size: 1.3rem; font-weight: 800; color: var(--ipd-dark);">₹{{ number_format($lifetime['totalPaid'], 2) }}</div>
                <div style="font-size: 0.8rem; color: #64748b;">Total Collected</div>
            </div>
        </div>
        <div class="stat-card" style="border-left-color: #dc2626;">
            <div class="stat-icon" style="background: #dc2626;"><i class="fa-solid fa-rotate-left"></i></div>
            <div>
                <div style="font-size: 1.3rem; font-weight: 800; color: var(--ipd-dark);">₹{{ number_format($lifetime['totalRefund'], 2) }}</div>
                <div style="font-size: 0.8rem; color: #64748b;">Total Refunded</div>
            </div>
        </div>
        <div class="stat-card" style="border-left-color: #F59E0B;">
            <div class="stat-icon" style="background: #F59E0B;"><i class="fa-solid fa-clock"></i></div>
            <div>
                <div style="font-size: 1.3rem; font-weight: 800; color: var(--ipd-dark);">₹{{ number_format($lifetime['outstanding'], 2) }}</div>
                <div style="font-size: 0.8rem; color: #64748b;">Outstanding</div>
            </div>
        </div>
    </div>

    <!-- Patient Details -->
    <div class="ipd-card">
        <div class="ipd-card-header">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <h3>Patient Personal Details</h3>
        </div>
        <div class="info-grid">
            <div class="info-box"><strong>Gender</strong><span>{{ $patient->gender ?? 'N/A' }}</span></div>
            <div class="info-box"><strong>Date of Birth</strong><span>{{ $patient->date_of_birth ? $patient->date_of_birth->format('d M Y') : 'N/A' }}</span></div>
            <div class="info-box"><strong>Age</strong><span>{{ $patient->age ?? 'N/A' }}</span></div>
            <div class="info-box"><strong>Blood Group</strong><span>{{ $patient->blood_group ?? 'N/A' }}</span></div>
            <div class="info-box"><strong>Mobile Number</strong><span>{{ $patient->phone ?? 'N/A' }}</span></div>
            <div class="info-box"><strong>Aadhar Number</strong><span>{{ $patient->aadhar ?? 'N/A' }}</span></div>
            <div class="info-box" style="grid-column: span 2;"><strong>Address</strong><span>{{ $patient->address ?? 'N/A' }}</span></div>
        </div>
    </div>

    <!-- IPD Visit & Payment History -->
    <div class="ipd-card">
        <div class="ipd-card-header">
            <div class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <h3>IPD Visit & Payment History</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="ipd-table">
                <thead>
                    <tr>
                        <th>IPD Code</th>
                        <th>Admission Date</th>
                        <th>Doctor</th>
                        <th>Bed Allotted</th>
                        <th>Amount Paid</th>
                        <th>Method</th>
                        <th>Refund</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ipdVisits as $visit)
                    <tr>
                        <td><strong>{{ $visit->ipd_code }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($visit->admission_date)->format('d M Y h:i A') }}</td>
                        <td>{{ $visit->doctor->full_name ?? 'N/A' }}</td>
                        <td>{{ $visit->bed->bed_number ?? 'Not Assigned' }}</td>
                        <td>₹{{ number_format($visit->advance_paid, 2) }}</td>
                        <td>{{ $visit->payment_method ?? 'N/A' }}</td>
                        <td>₹{{ number_format($visit->refund_amount, 2) }}</td>
                        <td>
                            @if($visit->status == 'Admitted')
                                <span class="badge-admitted">{{ $visit->status }}</span>
                            @else
                                <span class="badge-discharged">{{ $visit->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            No IPD visit history found for this patient.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection