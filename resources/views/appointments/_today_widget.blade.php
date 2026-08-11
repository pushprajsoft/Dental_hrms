@php
    $todaysAppointments = \App\Models\Appointment::with(['patient', 'doctor'])
        ->whereDate('appointment_date', now()->toDateString())
        ->orderBy('appointment_time')
        ->get();
@endphp

<style>
    .today-apt-row { display:flex; align-items:center; gap:14px; padding:12px 6px; border-bottom:1px solid var(--clr-border,#e5e9f0); cursor:pointer; }
    .today-apt-row:hover { background:var(--clr-bg,#f6f9ff); border-radius:8px; }
    .today-apt-time { font-family:'Outfit',sans-serif; font-weight:700; color:var(--clr-primary,#123C3A); font-size:0.85rem; width:80px; }
    .today-apt-patient { flex:1; }
    .today-apt-patient strong { display:block; font-size:0.9rem; }
    .today-apt-patient span { font-size:0.78rem; color:var(--clr-muted,#64748b); }
</style>

<div>
@forelse($todaysAppointments as $apt)
    <div class="today-apt-row" onclick="showApptDetails({{ $apt->id }})">
        <div class="today-apt-time">{{ $apt->formatted_time }}</div>
        <div class="today-apt-patient">
            <strong>{{ $apt->patient->full_name }}</strong>
            <span>{{ $apt->doctor->full_name ?? 'No doctor assigned' }}</span>
        </div>
        <span class="apt-status-badge {{ $apt->status_color }}"
              style="display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:20px; font-size:0.72rem; font-weight:600;">
            {{ $apt->status }}
        </span>
    </div>
@empty
    <div style="text-align:center; padding:30px; color:#94a3b8;">
        <i class="fa-solid fa-calendar-xmark" style="font-size:1.6rem; display:block; margin-bottom:8px;"></i>
        No appointments scheduled for today.
    </div>
@endforelse
</div>

<div id="apptDetailsModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:2000; align-items:center; justify-content:center;">
    <div id="apptDetailsContent" style="background:#fff; border-radius:16px; padding:26px 28px; width:460px; max-width:92vw;"></div>
</div>

<script>
function showApptDetails(id) {
    fetch(`/appointments/${id}/details`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('apptDetailsContent').innerHTML = `
                <h3 style="font-family:'Outfit',sans-serif; color:#123C3A; margin:0 0 4px;"><i class="fa-solid fa-user"></i> ${data.patient_name}</h3>
                <p style="color:#64748b; font-size:0.85rem; margin:0 0 16px;">${data.patient_code} · ${data.gender} · ${data.phone}</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:0.88rem;">
                    <div><strong>Doctor:</strong><br>${data.doctor_name}</div>
                    <div><strong>Status:</strong><br>${data.status}</div>
                    <div><strong>Date:</strong><br>${data.date}</div>
                    <div><strong>Time:</strong><br>${data.time}</div>
                    <div style="grid-column:1/-1;"><strong>Reason:</strong><br>${data.reason ?? '—'}</div>
                </div>
                <div style="margin-top:18px; display:flex; gap:10px;">
                    <a href="${data.whatsapp_link}" target="_blank" style="background:#25D366; color:#fff; padding:9px 16px; border-radius:8px; text-decoration:none; font-size:0.85rem;"><i class="fa-brands fa-whatsapp"></i> Send Reminder</a>
                    <button type="button" onclick="document.getElementById('apptDetailsModal').style.display='none'" style="padding:9px 16px; border-radius:8px; border:1px solid #e5e9f0; background:#fff; cursor:pointer;">Close</button>
                </div>
            `;
            document.getElementById('apptDetailsModal').style.display = 'flex';
        });
}
</script>