@extends('layouts.app')

@section('title', 'IPD Details')
@section('page-title', 'IPD Details')
@section('page-subtitle', 'Comprehensive view of admission, patient details, and visit history')

@section('content')

<style>
    /* ==========================================
       1. DESIGN TOKENS & FOUNDATIONS
       ========================================== */
    :root {
        /* Colors */
        --color-text-primary: #444444;
        --color-text-secondary: #212529;
        --color-text-tertiary: #012970;
        --color-text-inverse: #4154f1;
        --color-surface-muted: #ffffff;
        --color-surface-raised: #f8f9fa;
        --color-surface-strong: #f6f9ff;
        --color-border: #e1ecea;
        
        /* Typography */
        --font-primary: 'Open Sans', sans-serif;
        --font-size-base: 14px;
        --font-size-xs: 12.25px;
        --font-size-md: 15px;
        --font-size-lg: 17.5px;
        --font-size-xl: 18px;
        
        /* Spacing */
        --space-2: 5px;
        --space-4: 7px;
        --space-5: 10px;
        --space-6: 10.5px;
        --space-7: 12px;
        --space-8: 14px;
        
        /* Radius & Shadows */
        --radius-sm: 5px;
        --radius-md: 8px;
        --shadow-3: rgba(1, 41, 112, 0.1) 0px 2px 20px 0px;
        --shadow-4: rgba(1, 41, 112, 0.1) 0px 0px 20px 0px;
    }

    .ipd-show-container { 
        width: 100%; margin: 0; font-family: var(--font-primary); 
        background-color: var(--color-surface-strong); 
    }

    /* ==========================================
       2. COMPONENT ANATOMY & STATES
       ========================================== */

    /* Header */
    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 24px;
    }
    .page-header h2 { margin: 0; font-size: var(--font-size-xl); color: var(--color-text-tertiary); font-weight: 700; }
    .btn { 
        padding: 8px 16px; border-radius: var(--radius-sm); text-decoration: none; 
        font-weight: 600; display: inline-flex; align-items: center; gap: 8px; 
        transition: all 150ms ease; border: none; cursor: pointer; font-size: 13px;
    }
    .btn-back { background: #fff; color: var(--color-text-tertiary); border: 1px solid var(--color-border); box-shadow: var(--shadow-4); }
    .btn-back:hover { background: var(--color-surface-raised); }
    .btn-edit { background: var(--color-text-inverse); color: #fff; }
    .btn-edit:hover { background: var(--color-text-tertiary); }

    /* Layout Grid */
    .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    @media (max-width: 992px) { .details-grid { grid-template-columns: 1fr; } }

    /* Cards */
    .card { 
        background: var(--color-surface-muted); border-radius: var(--radius-md); 
        padding: 20px; box-shadow: var(--shadow-3); margin-bottom: 20px; 
        border: 1px solid var(--color-border);
    }
    .card-header { 
        display: flex; align-items: center; gap: 10px; margin-bottom: 20px; 
        border-bottom: 1px solid var(--color-border); padding-bottom: 12px; 
    }
    .card-header h3 { margin: 0; font-size: var(--font-size-md); color: var(--color-text-tertiary); font-weight: 700; }
    .card-header i { color: var(--color-text-inverse); font-size: 16px; }

    /* Info Grid */
    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-label { font-size: var(--font-size-xs); color: #6c757d; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
    .info-value { font-size: var(--font-size-base); color: var(--color-text-secondary); font-weight: 600; word-wrap: break-word; }

    /* Financial Stats */
    .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 24px; }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    .stat-card { 
        background: var(--color-surface-muted); border-radius: var(--radius-md); padding: 18px; 
        text-align: center; box-shadow: var(--shadow-4); border-top: 4px solid var(--color-text-inverse);
    }
    .stat-card h4 { margin: 0 0 5px 0; font-size: var(--font-size-lg); font-weight: 800; color: var(--color-text-tertiary); }
    .stat-card p { margin: 0; font-size: var(--font-size-xs); color: #6c757d; text-transform: uppercase; font-weight: 600; }

    /* Table */
    .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .data-table th { text-align: left; font-size: var(--font-size-xs); color: #6c757d; padding: 12px; border-bottom: 2px solid var(--color-border); text-transform: uppercase; }
    .data-table td { padding: 12px; border-bottom: 1px solid var(--color-border); font-size: var(--font-size-base); color: var(--color-text-secondary); vertical-align: top; }
    
    /* Badges */
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-block; }
    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-danger { background: #fee2e2; color: #b91c1c; }
    
    /* Accessibility Focus */
    a:focus-visible, button:focus-visible {
        outline: 2px solid var(--color-text-inverse);
        outline-offset: 2px;
    }
</style>

<div class="ipd-show-container">

    <!-- Page Header -->
    <div class="page-header">
        <h2>IPD Details - {{ $ipdAdmission->ipd_code }}</h2>
        <div>
            <a href="{{ route('ipd.dashboard') }}" class="btn btn-back">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('ipd.edit', $ipdAdmission->id) }}" class="btn btn-edit" style="margin-left: 8px;">
                <i class="fa-solid fa-pen"></i> Edit / Discharge
            </a>
        </div>
    </div>

    

    <!-- Details Grid (Left & Right) -->
    <div class="details-grid">
        
        <!-- Left Column -->
        <div>
            <!-- Admission Details -->
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-hospital"></i>
                    <h3>Admission Details</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">IPD Code</span><span class="info-value">{{ $ipdAdmission->ipd_code }}</span></div>
                    <div class="info-item"><span class="info-label">Admission Date & Time</span><span class="info-value">{{ \Carbon\Carbon::parse($ipdAdmission->admission_date)->format('d/m/Y h:i A') }}</span></div>
                    <div class="info-item"><span class="info-label">Registered Type</span><span class="info-value">{{ $ipdAdmission->registered_type }}</span></div>
                    <div class="info-item"><span class="info-label">Scheme Type</span><span class="info-value">{{ $ipdAdmission->scheme_type ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Scheme Name</span><span class="info-value">{{ $ipdAdmission->scheme_name ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Case Type</span><span class="info-value">{{ $ipdAdmission->case_type ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Bill Category</span><span class="info-value">{{ $ipdAdmission->bill_category ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Corporate</span><span class="info-value">{{ $ipdAdmission->corporate ? 'Yes' : 'No' }}</span></div>
                    <div class="info-item"><span class="info-label">ESIC No</span><span class="info-value">{{ $ipdAdmission->esic_no ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">URN No</span><span class="info-value">{{ $ipdAdmission->urn_no ?? 'N/A' }}</span></div>
                    <div class="info-item" style="grid-column: span 2;"><span class="info-label">Admission Note</span><span class="info-value">{{ $ipdAdmission->admission_note ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Referral Doctor</span><span class="info-value">{{ $ipdAdmission->referral_doctor ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Remark</span><span class="info-value">{{ $ipdAdmission->remark ?? 'N/A' }}</span></div>
                </div>
            </div>

            <!-- Consultant Details -->
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-user-doctor"></i>
                    <h3>Consultant Details</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Incharge Consultant</span><span class="info-value">{{ $ipdAdmission->doctor->full_name ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Attending Consultant</span><span class="info-value">{{ $ipdAdmission->attendingDoctor->full_name ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Advance Paid</span><span class="info-value">₹{{ number_format($ipdAdmission->advance_paid, 2) }}</span></div>
                    <div class="info-item"><span class="info-label">Payment Method</span><span class="info-value">{{ $ipdAdmission->payment_method ?? 'N/A' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Patient Details -->
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-user"></i>
                    <h3>Patient Details</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Patient Name</span><span class="info-value">{{ $ipdAdmission->patient->full_name ?? $ipdAdmission->p_name ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Gender</span><span class="info-value">{{ $ipdAdmission->patient->gender ?? $ipdAdmission->p_gender ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">DOB</span><span class="info-value">{{ isset($ipdAdmission->patient->date_of_birth) ? $ipdAdmission->patient->date_of_birth->format('d/m/Y') : ($ipdAdmission->p_dob ?? 'N/A') }}</span></div>
                    <div class="info-item"><span class="info-label">Age</span><span class="info-value">{{ $ipdAdmission->patient->age ?? $ipdAdmission->p_age ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Mobile Number</span><span class="info-value">{{ $ipdAdmission->patient->phone ?? $ipdAdmission->p_mobile ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Aadhar Number</span><span class="info-value">{{ $ipdAdmission->patient->aadhar ?? $ipdAdmission->p_aadhar ?? 'N/A' }}</span></div>
                    <div class="info-item" style="grid-column: span 2;"><span class="info-label">Address</span><span class="info-value">{{ $ipdAdmission->patient->address ?? $ipdAdmission->p_address ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">MLC</span><span class="info-value">{{ $ipdAdmission->patient->mlc ?? $ipdAdmission->p_mlc ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Father Name</span><span class="info-value">{{ $ipdAdmission->patient->fh_name ?? $ipdAdmission->p_fh_name ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Mother Name</span><span class="info-value">{{ $ipdAdmission->patient->mother_name ?? $ipdAdmission->p_mother_name ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Marital Status</span><span class="info-value">{{ $ipdAdmission->patient->marital_status ?? $ipdAdmission->p_marital_status ?? 'N/A' }}</span></div>
                </div>
            </div>

            <!-- Patient Relative Details -->
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-users"></i>
                    <h3>Patient Relative Details</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Relative Name</span><span class="info-value">{{ $ipdAdmission->rel_name ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Relation</span><span class="info-value">{{ $ipdAdmission->rel_relation ?? 'N/A' }}</span></div>
                    <div class="info-item"><span class="info-label">Contact No.</span><span class="info-value">{{ $ipdAdmission->rel_contact ?? 'N/A' }}</span></div>
                    <div class="info-item" style="grid-column: span 2;"><span class="info-label">Address</span><span class="info-value">{{ $ipdAdmission->rel_address ?? 'N/A' }}</span></div>
                </div>
            </div>

            <!-- Discharge Details -->
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-door-open"></i>
                    <h3>Discharge Details</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Status</span><span class="info-value">
                        @if($ipdAdmission->status == 'Admitted')
                            <span class="badge badge-success">Admitted</span>
                        @else
                            <span class="badge badge-danger">Discharged</span>
                        @endif
                    </span></div>
                    <div class="info-item"><span class="info-label">Discharge Date</span><span class="info-value">{{ $ipdAdmission->discharge_date ? \Carbon\Carbon::parse($ipdAdmission->discharge_date)->format('d/m/Y') : 'N/A' }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <h4>{{ $lifetime['totalVisits'] }}</h4>
            <p>Total Visits</p>
        </div>
        <div class="stat-card" style="border-top-color: #012970;">
            <h4>₹{{ number_format($lifetime['totalBilled'], 2) }}</h4>
            <p>Total Billed</p>
        </div>
        <div class="stat-card" style="border-top-color: #198754;">
            <h4>₹{{ number_format($lifetime['totalPaid'], 2) }}</h4>
            <p>Total Paid</p>
        </div>
        <div class="stat-card" style="border-top-color: #dc3545;">
            <h4>₹{{ number_format($lifetime['refunded'], 2) }}</h4>
            <p>Refunded</p>
        </div>
        <div class="stat-card" style="border-top-color: #ffc107;">
            <h4>₹{{ number_format($lifetime['outstanding'], 2) }}</h4>
            <p>Outstanding</p>
        </div>
    </div>
    
    <!-- Visit History -->
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <h3>Visit History</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Visit #</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Payment Breakdown</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $visit)
                    <tr>
                        <td><strong>{{ $loop->iteration }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($visit->admission_date)->format('d M Y') }}</td>
                        <td>{{ $visit->registered_type }}</td>
                        <td>
                            @if($visit->status == 'Admitted')
                                <span class="badge badge-success">Admitted</span>
                            @else
                                <span class="badge badge-danger">Discharged</span>
                            @endif
                        </td>
                        <td>₹{{ number_format($visit->advance_paid, 2) }}</td>
                        <td>₹{{ number_format($visit->advance_paid, 2) }}</td>
                        <td>₹0.00</td>
                        <td>
                            @if($visit->payment_method)
                                <span class="badge badge-warning">{{ $visit->payment_method }}: ₹{{ number_format($visit->advance_paid, 2) }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px; color: #6c757d;">
                            No visit history found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection