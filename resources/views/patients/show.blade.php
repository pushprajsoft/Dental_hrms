@extends('layouts.app')

@section('title', 'Patient Details')
@section('page-title', 'Patient Details')
@section('page-subtitle', $patient->patient_code)

@section('content')

<style>
    .lifetime-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin: 20px 0 28px;
    }
    .stat-card-ph {
        border-radius: 14px;
        padding: 18px 20px;
        color: #fff;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    }
    .stat-card-ph .label {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        opacity: 0.9;
    }
    .stat-card-ph .value {
        font-family: 'Outfit', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 6px;
    }
    .stat-visits   { background: linear-gradient(135deg, #14b8a6, #0d9488); }
    .stat-billed   { background: linear-gradient(135deg, #a855f7, #7e22ce); }
    .stat-paid     { background: linear-gradient(135deg, #22c55e, #15803d); }
    .stat-refund   { background: linear-gradient(135deg, #ef4444, #b91c1c); }
    .stat-outstanding { background: linear-gradient(135deg, #f97316, #c2410c); }

    .visit-timeline-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1.15rem;
        color: var(--clr-primary, #123C3A);
        margin: 30px 0 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .visit-card {
        background: #fff;
        border-left: 5px solid #3FBFAD;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        padding: 18px 20px;
        margin-bottom: 16px;
    }
    .visit-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 12px;
    }
    .visit-code {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        color: var(--clr-primary, #123C3A);
        font-size: 1rem;
    }
    .visit-date {
        font-size: 0.82rem;
        color: var(--clr-muted, #64748b);
    }
    .badge-pill {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .badge-type-New       { background:#dbeafe; color:#1d4ed8; }
    .badge-type-Follow-up { background:#fef3c7; color:#b45309; }
    .badge-type-Revisit   { background:#ede9fe; color:#6d28d9; }

    .badge-status-Paid     { background:#dcfce7; color:#166534; }
    .badge-status-Partial  { background:#fef9c3; color:#854d0e; }
    .badge-status-Pending  { background:#fee2e2; color:#991b1b; }
    .badge-status-Refunded { background:#fde2ff; color:#86198f; }

    .visit-amounts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }
    .visit-amounts .amt-box {
        background: var(--clr-bg, #f6f9ff);
        border-radius: 8px;
        padding: 8px 12px;
    }
    .visit-amounts .amt-label {
        font-size: 0.72rem;
        color: var(--clr-muted, #64748b);
        text-transform: uppercase;
        font-weight: 600;
    }
    .visit-amounts .amt-value {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1rem;
        color: var(--clr-primary, #123C3A);
    }

    .payment-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }
    .payment-chip {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 999px;
    }
    .chip-Cash   { background:#dcfce7; color:#166534; }
    .chip-UPI    { background:#dbeafe; color:#1e40af; }
    .chip-Cheque { background:#ede9fe; color:#6d28d9; }
    .chip-Card   { background:#fce7f3; color:#a21caf; }
    .chip-Other  { background:#e5e7eb; color:#374151; }

    .visit-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 0.82rem;
        color: var(--clr-muted, #64748b);
    }
</style>

    <div class="panel">
        <div class="panel-header">
            <h2>
                <span class="avatar-chip">{{ strtoupper(substr($patient->full_name, 0, 1)) }}</span>
                {{ $patient->full_name }}
            </h2>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('patients.edit', $patient) }}" class="btn-outline-clinic">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
                <a href="{{ route('patients.index') }}" class="btn-outline-clinic">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="form-grid">
            <div><span class="form-label-clinic">Patient Code</span>{{ $patient->patient_code }}</div>
            <div><span class="form-label-clinic">Gender</span>{{ $patient->gender }}</div>
            <div><span class="form-label-clinic">Date of Birth</span>{{ $patient->date_of_birth ? $patient->date_of_birth->format('d M Y') . ' (' . $patient->age . ' yrs)' : '—' }}</div>
            <div><span class="form-label-clinic">Blood Group</span>{{ $patient->blood_group ?: '—' }}</div>
            <div><span class="form-label-clinic">Phone</span>{{ $patient->phone }}</div>
            <div><span class="form-label-clinic">Email</span>{{ $patient->email ?: '—' }}</div>
            <div><span class="form-label-clinic">Doctor Assigned</span>{{ $patient->doctor_name ?: '—' }}</div>
            <div><span class="form-label-clinic">Status</span>{{ $patient->status }}</div>
            <div class="full-span"><span class="form-label-clinic">Address</span>{{ $patient->address ?: '—' }}</div>
            <div class="full-span"><span class="form-label-clinic">Chief Complaint</span>{{ $patient->chief_complaint ?: '—' }}</div>
            <div class="full-span"><span class="form-label-clinic">Treatment Plan</span>{{ $patient->treatment_plan ?: '—' }}</div>
        </div>
    </div>

    {{-- ===== LIFETIME STATS ===== --}}
    <div class="lifetime-stats">
        <div class="stat-card-ph stat-visits">
            <div class="label">Total Visits</div>
            <div class="value">{{ $lifetime['totalVisits'] }}</div>
        </div>
        <div class="stat-card-ph stat-billed">
            <div class="label">Total Billed</div>
            <div class="value">₹{{ number_format($lifetime['totalBilled'], 2) }}</div>
        </div>
        <div class="stat-card-ph stat-paid">
            <div class="label">Total Paid</div>
            <div class="value">₹{{ number_format($lifetime['totalPaid'], 2) }}</div>
        </div>
        <div class="stat-card-ph stat-refund">
            <div class="label">Refunded</div>
            <div class="value">₹{{ number_format($lifetime['totalRefund'], 2) }}</div>
        </div>
        <div class="stat-card-ph stat-outstanding">
            <div class="label">Outstanding</div>
            <div class="value">₹{{ number_format($lifetime['outstanding'], 2) }}</div>
        </div>
    </div>

    {{-- ===== VISIT HISTORY TIMELINE ===== --}}
    <div class="visit-timeline-title">
        <i class="fa-solid fa-clock-rotate-left"></i> Visit History
    </div>

    @forelse ($visits as $visit)
        <div class="visit-card">
            <div class="visit-card-top">
                <div>
                    <div class="visit-code">Visit #{{ $visit->token_number ?? $visit->id }}</div>
                    <div class="visit-date">
                        <i class="fa-regular fa-calendar"></i>
                        {{ \Illuminate\Support\Carbon::parse($visit->visit_date)->format('d M Y') }}
                        @if($visit->doctor)
                            &nbsp;·&nbsp;<i class="fa-solid fa-user-doctor"></i> {{ $visit->doctor->name }}
                        @endif
                    </div>
                </div>
                <div style="display:flex; gap:8px;">
                    <span class="badge-pill badge-type-{{ $visit->visit_type }}">{{ $visit->visit_type }}</span>
                    <span class="badge-pill badge-status-{{ $visit->status }}">{{ $visit->status }}</span>
                </div>
            </div>

            <div class="visit-amounts">
                <div class="amt-box">
                    <div class="amt-label">Total</div>
                    <div class="amt-value">₹{{ number_format($visit->total_amount, 2) }}</div>
                </div>
                <div class="amt-box">
                    <div class="amt-label">Paid</div>
                    <div class="amt-value">₹{{ number_format($visit->amount_paid, 2) }}</div>
                </div>
                <div class="amt-box">
                    <div class="amt-label">Balance</div>
                    <div class="amt-value">₹{{ number_format(max(0, $visit->total_amount - $visit->amount_paid), 2) }}</div>
                </div>
            </div>

            @if($visit->payments && $visit->payments->count())
                <div class="payment-chips">
                    @foreach($visit->payments as $payment)
                        <span class="payment-chip chip-{{ $payment->method }}">
                            {{ $payment->method }}: ₹{{ number_format($payment->amount, 2) }}
                            @if($payment->reference_no)
                                ({{ $payment->reference_no }})
                            @endif
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="visit-card-footer">
                <div>
                    @if($visit->refund_amount > 0)
                        <i class="fa-solid fa-rotate-left"></i> Refunded: ₹{{ number_format($visit->refund_amount, 2) }}
                    @endif
                </div>
                <a href="{{ route('opd.show', $visit) }}" class="btn-outline-clinic">
                    <i class="fa-solid fa-eye"></i> View Full Visit
                </a>
            </div>
        </div>
    @empty
        <div class="panel" style="text-align:center; color: var(--clr-muted, #64748b);">
            No OPD visits recorded yet for this patient.
        </div>
    @endforelse

    <div style="margin-top:16px;">
        {{ $visits->links() }}
    </div>

@endsection