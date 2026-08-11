@extends('layouts.app')

@section('title', 'Doctors')
@section('page-title', 'Doctors')
@section('page-subtitle', 'Manage your clinic doctor records')

@section('content')

    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="stat-card">
            <div class="stat-label">Total Doctors</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active</div>
            <div class="stat-value">{{ $stats['active'] }}</div>
        </div>
        <div class="stat-card accent-warn">
            <div class="stat-label">On Leave</div>
            <div class="stat-value">{{ $stats['onLeave'] }}</div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Doctor Records</h2>

            <div style="display:flex; gap:12px; align-items:center;">
                <form method="GET" action="{{ route('doctors.index') }}" class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name, code, specialization...">
                </form>
                <a href="{{ route('doctors.create') }}" class="btn-clinic">
                    <i class="fa-solid fa-plus"></i> Add New Doctor
                </a>
            </div>
        </div>

        @if($doctors->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-user-doctor" style="font-size:2.4rem; color: var(--clr-accent);"></i>
                <p style="margin-top:14px;">No doctor records yet. Add your first doctor to get started.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="clinic-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Code</th>
                            <th>Specialization</th>
                            <th>Phone</th>
                            <th>Experience</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctors as $doctor)
                            <tr>
                                <td>
                                    <span class="avatar-chip">{{ strtoupper(substr($doctor->full_name, 0, 1)) }}</span>
                                    {{ $doctor->full_name }}
                                </td>
                                <td>{{ $doctor->doctor_code }}</td>
                                <td>{{ $doctor->specialization }}</td>
                                <td>{{ $doctor->phone }}</td>
                                <td>{{ $doctor->experience_years ? $doctor->experience_years . ' yrs' : '—' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($doctor->status) {
                                            'Active' => 'badge-active',
                                            'Inactive' => 'badge-completed',
                                            default => 'badge-follow',
                                        };
                                    @endphp
                                    <span class="badge-status {{ $badgeClass }}">{{ $doctor->status }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <a href="{{ route('doctors.show', $doctor) }}" class="btn-outline-clinic" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('doctors.edit', $doctor) }}" class="btn-outline-clinic" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Delete this doctor record permanently?');">
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
                {{ $doctors->links() }}
            </div>
        @endif
    </div>

@endsection