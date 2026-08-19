@extends('layouts.app')

@section('title', 'Billing & Invoices')
@section('page-title', 'Billing Management')
@section('page-subtitle', 'Manage all clinic invoices and transactions')

@section('content')
<style>
    .billing-toolbar { background: #fff; border: 1px solid #e5e9f0; border-radius: 16px; padding: 20px; margin-bottom: 24px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
    .filter-form { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
    .btn-pdf { background: #EF4444; color: #fff; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .btn-pdf:hover { background: #B91C1C; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { text-align: left; font-size: 0.75rem; text-transform: uppercase; color: #64748b; padding: 16px; border-bottom: 2px solid #e5e9f0; background: #f8fafc; }
    .data-table td { padding: 14px 16px; border-bottom: 1px solid #e5e9f0; font-size: 0.9rem; }
</style>

<div class="billing-toolbar">
    <form action="{{ route('billing.index') }}" method="GET" class="filter-form">
        <div>
            <label style="font-size:0.75rem; font-weight:600; color:#64748b;">From Date</label><br>
            <input type="date" name="from_date" value="{{ $fromDate }}" style="padding:10px; border-radius:10px; border:1px solid #e5e9f0;">
        </div>
        <div>
            <label style="font-size:0.75rem; font-weight:600; color:#64748b;">To Date</label><br>
            <input type="date" name="to_date" value="{{ $toDate }}" style="padding:10px; border-radius:10px; border:1px solid #e5e9f0;">
        </div>
        <button type="submit" class="btn-clinic" style="margin-top:22px;">Filter</button>
        <a href="{{ route('billing.index') }}" class="btn-clinic" style="margin-top:22px; background:transparent; color:#64748b; border:1px solid #e5e9f0;">Reset</a>
    </form>
    <a href="{{ route('billing.settings') }}" class="btn-clinic" style="background: #7C5CFC; margin-top:22px;">
        <i class="fa-solid fa-gear"></i> Print Layout & GST
    </a>
</div>

<div class="dash-panel" style="padding: 0; overflow: hidden;">
    <div style="padding: 24px; display: flex; justify-content: space-between; border-bottom: 1px solid #e5e9f0;">
        <h2 style="margin:0; font-family:'Outfit';"><i class="fa-solid fa-file-invoice-dollar" style="color:#3FBFAD;"></i> All Invoices</h2>
        <h3 style="margin:0; color:#15803D;">Total Collected: ₹{{ number_format($totalRevenue, 2) }}</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice Date</th>
                    <th>Visit Code</th>
                    <th>Patient Name</th>
                    <th>Amount Paid</th>
                    <th>Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($bill->visit_date)->format('d M Y') }}</td>
                    <td><span style="font-family:monospace; background:#f1f5f9; padding:4px 8px; border-radius:6px;">{{ $bill->visit_code }}</span></td>
                    <td>{{ $bill->patient->full_name ?? 'N/A' }}</td>
                    <td style="font-weight:600; color:#15803D;">₹{{ number_format($bill->amount_paid, 2) }}</td>
                    <td><span style="padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; background:#D1FAE5; color:#065F46;">{{ $bill->status }}</span></td>
                    <td style="text-align:center;">
                        <a href="{{ route('billing.invoice', $bill->id) }}" target="_blank" class="btn-pdf" style="padding:8px 14px; font-size:0.85rem;">
                            <i class="fa-solid fa-file-pdf"></i> View PDF
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:#64748b;">No invoices found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection