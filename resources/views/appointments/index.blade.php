@extends('layouts.app')

@section('title', 'Appointments')
@section('page-title', 'Appointments')
@section('page-subtitle', 'Book, confirm, and manage patient appointments')

@section('content')

<style>
    .apt-stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:22px; }
    .apt-stat-card { border-radius:16px; padding:20px 22px; color:#fff; position:relative; overflow:hidden; }
    .apt-stat-card .stat-icon { width:44px; height:44px; border-radius:12px; background:rgba(255,255,255,.22); display:flex; align-items:center; justify-content:center; font-size:1.1rem; margin-bottom:10px; }
    .apt-stat-card .stat-label { font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; opacity:.9; }
    .apt-stat-card .stat-value { font-size:1.9rem; font-weight:700; font-family:'Outfit',sans-serif; }
    .apt-stat-today { background: linear-gradient(135deg,#3FBFAD,#12857A); }
    .apt-stat-upcoming { background: linear-gradient(135deg,#7C6CF0,#5847C9); }
    .apt-stat-completed { background: linear-gradient(135deg,#22C55E,#16803F); }
    .apt-stat-cancelled { background: linear-gradient(135deg,#F97066,#DC3B3B); }

    .apt-tabs { display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
    .apt-tab { padding:8px 16px; border-radius:20px; font-size:0.85rem; font-weight:600; text-decoration:none; color: var(--clr-muted,#64748b); background:#fff; border:1px solid var(--clr-border,#e5e9f0); }
    .apt-tab.active { background: var(--clr-primary,#123C3A); color:#fff; border-color: var(--clr-primary,#123C3A); }

    .apt-filters { display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap; }
    .apt-filters input, .apt-filters select { padding:9px 12px; border-radius:8px; border:1px solid var(--clr-border,#e5e9f0); font-size:0.85rem; }

    .apt-table { width:100%; border-collapse:collapse; }
    .apt-table th { text-align:left; font-size:0.75rem; text-transform:uppercase; letter-spacing:.04em; color:var(--clr-muted,#64748b); padding:10px 14px; border-bottom:1px solid var(--clr-border,#e5e9f0); }
    .apt-table td { padding:12px 14px; border-bottom:1px solid var(--clr-border,#e5e9f0); font-size:0.88rem; vertical-align:middle; }

    .apt-status-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; }
    .status-scheduled { background:#DBEAFE; color:#1E40AF; }
    .status-confirmed { background:#D1FAE5; color:#065F46; }
    .status-completed { background:#E0E7FF; color:#3730A3; }
    .status-cancelled { background:#FEE2E2; color:#B91C1C; }
    .status-noshow    { background:#FEF3C7; color:#92400E; }

    .apt-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; margin-right:4px; border:1px solid var(--clr-border,#e5e9f0); color:var(--clr-muted,#64748b); text-decoration:none; cursor:pointer; background:#fff; }
    .apt-action-btn:hover { background:var(--clr-bg,#f6f9ff); }
    .apt-action-btn.whatsapp { color:#25D366; }
    .apt-action-btn.whatsapp:hover { background:#E7FCEF; }
    .apt-action-btn.danger:hover { background:#FEE2E2; color:#B91C1C; }

    .apt-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:2000; align-items:center; justify-content:center; }
    .apt-modal-box { background:#fff; border-radius:16px; padding:26px 28px; width:420px; max-width:92vw; }
    .apt-modal-box h3 { margin:0 0 16px; font-family:'Outfit',sans-serif; color:var(--clr-primary,#123C3A); }
    .apt-modal-box .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
</style>

@if(session('success'))
    <div class="alert-clinic"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

<div class="apt-stats-grid">
    <div class="apt-stat-card apt-stat-today">
        <div class="stat-icon"><i class="fa-solid fa-calendar-day"></i></div>
        <div class="stat-label">Today's Appointments</div>
        <div class="stat-value">{{ $stats['today'] }}</div>
    </div>
    <div class="apt-stat-card apt-stat-upcoming">
        <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
        <div class="stat-label">Upcoming</div>
        <div class="stat-value">{{ $stats['upcoming'] }}</div>
    </div>
    <div class="apt-stat-card apt-stat-completed">
        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-label">Completed</div>
        <div class="stat-value">{{ $stats['completed'] }}</div>
    </div>
    <div class="apt-stat-card apt-stat-cancelled">
        <div class="stat-icon"><i class="fa-solid fa-circle-xmark"></i></div>
        <div class="stat-label">Cancelled / No-Show</div>
        <div class="stat-value">{{ $stats['cancelled'] }}</div>
    </div>
</div>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
    <div class="apt-tabs">
        <a href="{{ route('appointments.index', ['tab' => 'all']) }}" class="apt-tab {{ $statusTab == 'all' ? 'active' : '' }}">All</a>
        <a href="{{ route('appointments.index', ['tab' => 'today']) }}" class="apt-tab {{ $statusTab == 'today' ? 'active' : '' }}">Today</a>
        <a href="{{ route('appointments.index', ['tab' => 'upcoming']) }}" class="apt-tab {{ $statusTab == 'upcoming' ? 'active' : '' }}">Upcoming</a>
        <a href="{{ route('appointments.index', ['tab' => 'completed']) }}" class="apt-tab {{ $statusTab == 'completed' ? 'active' : '' }}">Completed</a>
        <a href="{{ route('appointments.index', ['tab' => 'cancelled']) }}" class="apt-tab {{ $statusTab == 'cancelled' ? 'active' : '' }}">Cancelled</a>
    </div>
    <a href="{{ route('appointments.create') }}" class="btn-clinic"><i class="fa-solid fa-calendar-plus"></i> Book Appointment</a>
</div>

<form method="GET" class="apt-filters">
    <input type="hidden" name="tab" value="{{ $statusTab }}">
    <input type="text" name="search" placeholder="Search patient or code..." value="{{ $search }}">
    <select name="doctor_id" onchange="this.form.submit()">
        <option value="">All Doctors</option>
        @foreach($doctors as $d)
            <option value="{{ $d->id }}" @selected($doctorId == $d->id)>{{ $d->full_name }}</option>
        @endforeach
    </select>
    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
    <button type="submit" class="btn-outline-clinic">Filter</button>
</form>

<div class="profile-card" style="padding:0; overflow-x:auto;">
    <table class="apt-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $apt)
                <tr>
                    <td>{{ $apt->appointment_code }}</td>
                    <td>
                        <a href="javascript:void(0)" onclick="showApptDetails({{ $apt->id }})" style="color:var(--clr-primary); font-weight:600; text-decoration:none;">
                            {{ $apt->patient->full_name }}
                        </a>
                    </td>
                    <td>{{ $apt->doctor->full_name ?? '—' }}</td>
                    <td>{{ $apt->appointment_date->format('d M Y') }} · {{ $apt->formatted_time }}</td>
                    <td><span class="apt-status-badge {{ $apt->status_color }}">{{ $apt->status }}</span></td>
                    <td>
                        <a href="{{ $apt->whatsapp_reminder_link }}" target="_blank" class="apt-action-btn whatsapp" title="Send WhatsApp Reminder">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <a href="javascript:void(0)" class="apt-action-btn" title="Reschedule" onclick="openReschedule({{ $apt->id }}, '{{ $apt->appointment_date->format('Y-m-d') }}', '{{ \Carbon\Carbon::parse($apt->appointment_time)->format('H:i') }}')">
                            <i class="fa-solid fa-calendar-days"></i>
                        </a>
                        <a href="{{ route('appointments.edit', $apt) }}" class="apt-action-btn" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('appointments.destroy', $apt) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this appointment?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="apt-action-btn danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:30px; color:var(--clr-muted);">No appointments found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $appointments->links() }}</div>

{{-- Reschedule Modal --}}
<div id="rescheduleModal" class="apt-modal-overlay">
    <div class="apt-modal-box">
        <h3><i class="fa-solid fa-calendar-days"></i> Reschedule Appointment</h3>
        <form id="rescheduleForm" method="POST">
            @csrf @method('PUT')
            <div class="form-grid">
                <div>
                    <label class="form-label-clinic">New Date</label>
                    <input type="date" name="appointment_date" id="reschedule_date" class="form-control-clinic" required>
                </div>
                <div>
                    <label class="form-label-clinic">New Time</label>
                    <input type="time" name="appointment_time" id="reschedule_time" class="form-control-clinic" required>
                </div>
            </div>
            <div style="margin-top:18px; display:flex; gap:10px;">
                <button type="submit" class="btn-clinic">Confirm Reschedule</button>
                <button type="button" class="btn-outline-clinic" onclick="closeReschedule()">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Details Modal --}}
<div id="apptDetailsModal" class="apt-modal-overlay">
    <div class="apt-modal-box" id="apptDetailsContent" style="width:460px;"></div>
</div>

<script>
function openReschedule(id, date, time) {
    document.getElementById('rescheduleForm').action = `/appointments/${id}/reschedule`;
    document.getElementById('reschedule_date').value = date;
    document.getElementById('reschedule_time').value = time;
    document.getElementById('rescheduleModal').style.display = 'flex';
}
function closeReschedule() {
    document.getElementById('rescheduleModal').style.display = 'none';
}

function showApptDetails(id) {
    fetch(`/appointments/${id}/details`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('apptDetailsContent').innerHTML = `
                <h3><i class="fa-solid fa-user"></i> ${data.patient_name}</h3>
                <p style="color:#64748b; font-size:0.85rem; margin:-10px 0 16px;">${data.patient_code} · ${data.gender} · ${data.phone}</p>
                <div class="form-grid" style="font-size:0.88rem;">
                    <div><strong>Doctor:</strong><br>${data.doctor_name}</div>
                    <div><strong>Status:</strong><br>${data.status}</div>
                    <div><strong>Date:</strong><br>${data.date}</div>
                    <div><strong>Time:</strong><br>${data.time}</div>
                    <div class="full-span" style="grid-column:1/-1;"><strong>Reason:</strong><br>${data.reason ?? '—'}</div>
                    <div class="full-span" style="grid-column:1/-1;"><strong>Address:</strong><br>${data.address ?? '—'}</div>
                    ${data.notes ? `<div style="grid-column:1/-1;"><strong>Notes:</strong><br>${data.notes}</div>` : ''}
                </div>
                <div style="margin-top:18px; display:flex; gap:10px;">
                    <a href="${data.whatsapp_link}" target="_blank" class="btn-clinic" style="background:#25D366;"><i class="fa-brands fa-whatsapp"></i> Send Reminder</a>
                    <button type="button" class="btn-outline-clinic" onclick="closeApptModal()">Close</button>
                </div>
            `;
            document.getElementById('apptDetailsModal').style.display = 'flex';
        });
}
function closeApptModal() {
    document.getElementById('apptDetailsModal').style.display = 'none';
}
</script>

@endsection