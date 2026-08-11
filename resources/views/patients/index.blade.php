@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of all patient records')

@section('content')

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Patients</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Male Patients</div>
            <div class="stat-value">{{ $stats['male'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Female Patients</div>
            <div class="stat-value">{{ $stats['female'] }}</div>
        </div>
        <div class="stat-card accent-warn">
            <div class="stat-label">New This Month</div>
            <div class="stat-value">{{ $stats['thisMonth'] }}</div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Patient Records</h2>

            <div style="display:flex; gap:12px; align-items:center;">
                <form method="GET" action="{{ route('patients.index') }}" class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name, code, phone...">
                </form>
                <a href="{{ route('patients.create') }}" class="btn-clinic">
                    <i class="fa-solid fa-plus"></i> Add New Patient
                </a>
            </div>
        </div>

        @if($patients->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-tooth" style="font-size:2.4rem; color: var(--clr-accent);"></i>
                <p style="margin-top:14px;">No patient records yet. Add your first patient to get started.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="clinic-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Code</th>
                            <th>Phone</th>
                            <th>Doctor</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr>
                                <td>
                                    <a href="{{ route('patients.show', $patient) }}" style="display:flex; align-items:center; gap:8px; color: inherit; text-decoration:none;">
                                        <span class="avatar-chip">{{ strtoupper(substr($patient->full_name, 0, 1)) }}</span>
                                        <span style="font-weight:600;">{{ $patient->full_name }}</span>
                                    </a>
                                </td>
                                <td>{{ $patient->patient_code }}</td>
                                <td>{{ $patient->phone }}</td>
                                <td>{{ $patient->doctor_name ?: '—' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($patient->status) {
                                            'Active' => 'badge-active',
                                            'Completed' => 'badge-completed',
                                            default => 'badge-follow',
                                        };
                                    @endphp
                                    <span class="badge-status {{ $badgeClass }}">{{ $patient->status }}</span>
                                </td>
                                <td>{{ $patient->created_at->format('d M Y') }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ $whatsapp->thankYouLinkFor($patient->phone, $patient->full_name) }}"
                                       target="_blank" class="btn-outline-clinic" title="Send Thank You on WhatsApp"
                                       style="color:#25D366; border-color:#BFEAD3;">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                    <a href="{{ route('patients.show', $patient) }}" class="btn-outline-clinic" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="btn-outline-clinic" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Delete this patient record permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-warn" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:20px;">
                {{ $patients->links() }}
            </div>
        @endif
    </div>

@endsection