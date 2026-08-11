@extends('layouts.app')

@section('title', 'OPD Visit Details')
@section('page-title', 'OPD Visit Details')
@section('page-subtitle', $visit->visit_code)

@section('content')

    <div class="panel">
        <div class="panel-header">
            <h2>
                <span class="avatar-chip">{{ strtoupper(substr($visit->patient->full_name ?? '?', 0, 1)) }}</span>
                {{ $visit->patient->full_name ?? 'Deleted Patient' }}
            </h2>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('opd.edit', $visit) }}" class="btn-outline-clinic">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
                <a href="{{ route('opd.index') }}" class="btn-outline-clinic">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="form-grid">
            <div><span class="form-label-clinic">Visit Code</span>{{ $visit->visit_code }}</div>
            <div><span class="form-label-clinic">Patient</span>{{ $visit->patient->full_name ?? '—' }} ({{ $visit->patient->patient_code ?? '—' }})</div>
            <div><span class="form-label-clinic">Doctor</span>{{ $visit->doctor->full_name ?? '—' }}</div>
            <div><span class="form-label-clinic">Visit Date</span>{{ $visit->visit_date->format('d M Y') }}</div>
            <div><span class="form-label-clinic">Visit Type</span>{{ $visit->visit_type }}</div>
            <div><span class="form-label-clinic">Status</span>{{ $visit->status }}</div>
            <div><span class="form-label-clinic">Consultation Fee</span>₹{{ number_format($visit->consultation_fee, 2) }}</div>
            <div><span class="form-label-clinic">Other Charges</span>₹{{ number_format($visit->other_charges, 2) }}</div>
            <div><span class="form-label-clinic">Discount</span>₹{{ number_format($visit->discount, 2) }}</div>
            <div><span class="form-label-clinic">Total Amount</span>₹{{ number_format($visit->total_amount, 2) }}</div>
            <div><span class="form-label-clinic">Amount Paid</span>₹{{ number_format($visit->amount_paid, 2) }}</div>
            <div><span class="form-label-clinic">Payment Method</span>{{ $visit->payment_method }}</div>
            <div><span class="form-label-clinic">Payment Date</span>{{ $visit->payment_date ? $visit->payment_date->format('d M Y') : '—' }}</div>
            <div><span class="form-label-clinic">Refund Amount</span>₹{{ number_format($visit->refund_amount, 2) }}</div>
            <div><span class="form-label-clinic">Balance Due</span>₹{{ number_format($visit->balance_due, 2) }}</div>
            <div class="full-span"><span class="form-label-clinic">Chief Complaint</span>{{ $visit->chief_complaint ?: '—' }}</div>
            <div class="full-span"><span class="form-label-clinic">Notes</span>{{ $visit->notes ?: '—' }}</div>
        </div>
    </div>

@endsection