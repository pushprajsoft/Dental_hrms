@extends('layouts.app')

@section('title', 'OPD Dashboard')
@section('page-title', 'OPD Dashboard')
@section('page-subtitle', 'Out-patient visits, revenue & collections')

@section('content')

<style>
    .opd-collection-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    @media (max-width: 1200px) {
        .opd-collection-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 700px) {
        .opd-collection-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .opd-coll-card {
        background: var(--clr-surface, #fff);
        border: 1px solid var(--clr-border, #e5e9f0);
        border-radius: var(--radius-lg, 12px);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
    }
    .opd-coll-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0;
    }
    .opd-coll-label {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .04em; color: var(--clr-muted, #64748b); margin-bottom: 2px;
    }
    .opd-coll-value { font-size: 1.35rem; font-weight: 800; color: var(--clr-primary, #123C3A); }

    .opd-coll-breakdown {
        margin-top: 4px;
        font-size: 11px;
        color: var(--clr-muted, #64748b);
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .opd-coll-breakdown span {
        background: var(--clr-bg, #f6f9ff);
        border: 1px solid var(--clr-border, #e5e9f0);
        border-radius: 6px;
        padding: 1px 6px;
        white-space: nowrap;
    }

    .opd-filter-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr) auto;
        gap: 14px;
        align-items: end;
        margin-bottom: 24px;
    }
    @media (max-width: 1200px) {
        .opd-filter-row { grid-template-columns: repeat(3, 1fr); }
    }

    .opd-doctor-card {
        background: var(--clr-surface, #fff);
        border: 1px solid var(--clr-border, #e5e9f0);
        border-radius: var(--radius-lg, 12px);
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .opd-doctor-name { font-weight: 700; color: var(--clr-primary, #123C3A); }
    .opd-doctor-meta { font-size: 0.8rem; color: var(--clr-muted, #64748b); }
    .opd-doctor-amount { font-weight: 800; color: #17847A; font-size: 1.05rem; }

    .text-end { text-align: right; }

    .opd-patient-link {
        display: flex;
        align-items: center;
        gap: 8px;
        color: inherit;
        text-decoration: none;
    }
    .opd-patient-link:hover .opd-patient-name {
        color: #17847A;
        text-decoration: underline;
    }
    .opd-patient-name { font-weight: 600; }
    
    /* ==========================================
       NATIVE BOOTSTRAP DROPUP STYLING
       ========================================== */
    
    .dropdown-menu {
        display: none; 
        position: absolute;
        bottom: 100%; 
        top: auto;    
        margin-bottom: 5px; 
    }
    .dropdown-menu.show {
        display: block; 
    }

    .pdf-action-menu {
        background-color: #ffffff; 
        border: 1px solid #e2e8f0; 
        border-radius: 10px; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
        padding: 8px; 
        min-width: 240px; 
        z-index: 1050;
    }
    .pdf-action-menu .dropdown-item {
        padding: 10px 12px; 
        font-size: 0.9rem; 
        color: #333333; 
        font-weight: 500;
        border-radius: 6px; 
        display: flex; 
        align-items: center; 
        gap: 10px;
        text-decoration: none; 
        transition: background 0.2s;
    }
    .pdf-action-menu .dropdown-item:hover { 
        background-color: #f1f5f9; 
        color: #123C3A; 
    }
</style>

<div class="alert-clinic" style="background:#FFF4E5; color:#B45309; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-info"></i>
    Showing OPD data from <strong>{{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}</strong>
    to <strong>{{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</strong>.
    Refunded amounts are deducted from Net Collection but visits still count in the totals above.
</div>

{{-- Visit Summary --}}
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-label">Total Visits</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">New Patients</div>
        <div class="stat-value">{{ $stats['new'] }}</div>
    </div>
    <div class="stat-card accent-warn">
        <div class="stat-label">Follow-up</div>
        <div class="stat-value">{{ $stats['followup'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Revisit</div>
        <div class="stat-value">{{ $stats['revisit'] }}</div>
    </div>
</div>

{{-- Collection Breakdown --}}
<div class="opd-collection-grid">
    <div class="opd-coll-card">
        <div class="opd-coll-icon" style="background:rgba(34,197,94,.12); color:#16a34a;"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
            <div class="opd-coll-label">Cash Collected</div>
            <div class="opd-coll-value">₹{{ number_format($collection['cash'], 2) }}</div>
        </div>
    </div>
    <div class="opd-coll-card">
        <div class="opd-coll-icon" style="background:rgba(59,130,246,.12); color:#2563eb;"><i class="fa-solid fa-mobile-screen"></i></div>
        <div>
            <div class="opd-coll-label">UPI Collected</div>
            <div class="opd-coll-value">₹{{ number_format($collection['upi'], 2) }}</div>
        </div>
    </div>
    <div class="opd-coll-card">
        <div class="opd-coll-icon" style="background:rgba(124,92,252,.12); color:#7C5CFC;"><i class="fa-solid fa-layer-group"></i></div>
        <div>
            <div class="opd-coll-label">Other</div>
            <div class="opd-coll-value">₹{{ number_format($collection['other'], 2) }}</div>
            @if($collection['other'] > 0)
                <div class="opd-coll-breakdown">
                    @if($otherBreakdown['cheque'] > 0)
                        <span><i class="fa-solid fa-money-check"></i> Cheque ₹{{ number_format($otherBreakdown['cheque'], 2) }}</span>
                    @endif
                    @if($otherBreakdown['card'] > 0)
                        <span><i class="fa-solid fa-credit-card"></i> Card ₹{{ number_format($otherBreakdown['card'], 2) }}</span>
                    @endif
                    @if($otherBreakdown['other'] > 0)
                        <span><i class="fa-solid fa-circle-dot"></i> Other ₹{{ number_format($otherBreakdown['other'], 2) }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="opd-coll-card">
        <div class="opd-coll-icon" style="background:rgba(239,68,68,.12); color:#dc2626;"><i class="fa-solid fa-rotate-left"></i></div>
        <div>
            <div class="opd-coll-label">Refunds</div>
            <div class="opd-coll-value">₹{{ number_format($collection['refund'], 2) }}</div>
        </div>
    </div>
    <div class="opd-coll-card" style="background:linear-gradient(135deg,#3FBFAD,#17847A); border:none;">
        <div class="opd-coll-icon" style="background:rgba(255,255,255,.25); color:#fff;"><i class="fa-solid fa-sack-dollar"></i></div>
        <div>
            <div class="opd-coll-label" style="color:rgba(255,255,255,.85);">Net Collection</div>
            <div class="opd-coll-value" style="color:#fff;">₹{{ number_format($collection['net'], 2) }}</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="panel" style="margin-bottom:24px;">
    <div class="panel-header">
        <h2><i class="fa-solid fa-filter" style="color:var(--clr-accent, #3FBFAD);"></i> Filters</h2>
        <a href="{{ route('opd.create') }}" class="btn-clinic">
            <i class="fa-solid fa-plus"></i> Add OPD Visit
        </a>
    </div>

    <form method="GET" action="{{ route('opd.index') }}" class="opd-filter-row" style="padding: 0 20px 20px;">
        <div>
            <label class="form-label-clinic">From Date</label>
            <input type="date" name="from_date" class="form-control-clinic" value="{{ $fromDate }}">
        </div>
        <div>
            <label class="form-label-clinic">To Date</label>
            <input type="date" name="to_date" class="form-control-clinic" value="{{ $toDate }}">
        </div>
        <div>
            <label class="form-label-clinic">Doctor</label>
            <select name="doctor_id" class="form-control-clinic">
                <option value="">All Doctors</option>
                @foreach($doctors as $d)
                    <option value="{{ $d->id }}" @selected($doctorId == $d->id)>{{ $d->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label-clinic">Visit Type</label>
            <select name="visit_type" class="form-control-clinic">
                <option value="">All Types</option>
                @foreach(['New', 'Follow-up', 'Revisit'] as $t)
                    <option value="{{ $t }}" @selected($visitType == $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label-clinic">Search</label>
            <input type="text" name="search" class="form-control-clinic" value="{{ $search }}" placeholder="Patient, code, phone...">
        </div>
        <div>
            <button type="submit" class="btn-clinic" style="width:100%; justify-content:center;">
                <i class="fa-solid fa-magnifying-glass"></i> Apply
            </button>
        </div>
    </form>
</div>

{{-- Doctor Revenue --}}
@if($doctorRevenue->isNotEmpty())
<div class="panel" style="margin-bottom:24px;">
    <div class="panel-header">
        <h2><i class="fa-solid fa-user-doctor" style="color:#7C5CFC;"></i> Doctor-wise Collection</h2>
    </div>
    <div style="padding: 4px 20px 20px;">
        @foreach($doctorRevenue as $row)
            <div class="opd-doctor-card">
                <div>
                    <div class="opd-doctor-name">{{ $row->doctor->full_name ?? 'Unassigned' }}</div>
                    <div class="opd-doctor-meta">{{ $row->total_visits }} visit(s)</div>
                </div>
                <div class="opd-doctor-amount">₹{{ number_format($row->total_collected, 2) }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Visits Table --}}
<div class="panel">
    <div class="panel-header">
        <h2><i class="fa-solid fa-table text-primary"></i> OPD Visit Records</h2>
    </div>

    @if($visits->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-hospital-user" style="font-size:2.4rem; color: var(--clr-accent);"></i>
            <p style="margin-top:14px;">No OPD visits found for the selected filters.</p>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="clinic-table">
                <thead>
                    <tr>
                        <th>Visit Code</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-end">Total (₹)</th>
                        <th class="text-end">Paid (₹)</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visits as $visit)
                        <tr>
                            <td>{{ $visit->visit_code }}</td>
                            <td>
                                @if($visit->patient)
                                    <a href="{{ route('patients.show', $visit->patient) }}" class="opd-patient-link" title="View patient history">
                                        <span class="avatar-chip">{{ strtoupper(substr($visit->patient->full_name, 0, 1)) }}</span>
                                        <span class="opd-patient-name">{{ $visit->patient->full_name }}</span>
                                    </a>
                                @else
                                    <span class="avatar-chip">?</span> Deleted Patient
                                @endif
                            </td>
                            <td>{{ $visit->doctor->full_name ?? '—' }}</td>
                            <td>{{ $visit->visit_date->format('d M Y') }}</td>
                            <td>{{ $visit->visit_type }}</td>
                            <td class="text-end">{{ number_format($visit->total_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($visit->amount_paid, 2) }}</td>
                            <td>{{ $visit->payment_method }}</td>
                            <td>
                                @php
                                    $badgeClass = match($visit->status) {
                                        'Paid' => 'badge-active',
                                        'Refunded' => 'badge-completed',
                                        default => 'badge-follow',
                                    };
                                @endphp
                                <span class="badge-status {{ $badgeClass }}">{{ $visit->status }}</span>
                            </td>
                            <td style="text-align:right; white-space: nowrap;">
                                
                                {{-- FORCED HORIZONTAL ALIGNMENT FLEX CONTAINER --}}
                                <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px;">
                                    
                                    {{-- View Button --}}
                                    <a href="{{ route('opd.show', $visit) }}" class="btn-outline-clinic" title="View" style="display: inline-flex; align-items: center; justify-content: center; width: auto;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    
                                    {{-- PDF Invoice Dropup --}}
                                    <div class="btn-group dropup">
                                        <a href="#" class="btn-outline-clinic" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="PDF Options" style="color: #EF4444; border-color: #FEE2E2; display: inline-flex; align-items: center; justify-content: center; width: auto;">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end pdf-action-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('billing.invoice', $visit->id) }}?type=plain" target="_blank">
                                                    <i class="fa-solid fa-file" style="color: #64748b; width: 16px;"></i> Plain PDF
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('billing.invoice', $visit->id) }}?type=cash" target="_blank">
                                                    <i class="fa-solid fa-file-invoice-dollar" style="color: #16a34a; width: 16px;"></i> OPD Cash Bill PDF
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('billing.invoice', $visit->id) }}?type=prescription" target="_blank">
                                                    <i class="fa-solid fa-prescription-bottle-medical" style="color: #7C5CFC; width: 16px;"></i> Prescription PDF
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('billing.invoice', $visit->id) }}?type=plain_rx" target="_blank">
                                                    <i class="fa-solid fa-notes-medical" style="color: #3FBFAD; width: 16px;"></i> Plain Prescription PDF
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Edit Button --}}
                                    <a href="{{ route('opd.edit', $visit) }}" class="btn-outline-clinic" title="Edit" style="display: inline-flex; align-items: center; justify-content: center; width: auto;">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    
                                    {{-- Delete Button --}}
                                    <form action="{{ route('opd.destroy', $visit) }}" method="POST" onsubmit="return confirm('Delete this OPD visit permanently?');" style="display: inline-block; margin: 0; padding: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-warn" title="Delete" style="display: inline-flex; align-items: center; justify-content: center; width: auto;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px;">
            {{ $visits->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection