@if($errors->any())
    <div class="alert-clinic" style="background:#FEE2E2; color:#B91C1C;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-grid">

    <div class="full-span">
        <label class="form-label-clinic">Patient *</label>
        <select name="patient_id" class="form-control-clinic" required>
            <option value="">Select patient</option>
            @foreach($patients as $p)
                <option value="{{ $p->id }}" @selected(old('patient_id', $preselectedPatientId ?? ($appointment->patient_id ?? '')) == $p->id)>
                    {{ $p->patient_code }} — {{ $p->full_name }} ({{ $p->phone }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label-clinic">Doctor</label>
        <select name="doctor_id" class="form-control-clinic">
            <option value="">Not assigned</option>
            @foreach($doctors as $d)
                <option value="{{ $d->id }}" @selected(old('doctor_id', $appointment->doctor_id ?? '') == $d->id)>
                    {{ $d->full_name }} ({{ $d->specialization }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label-clinic">Duration *</label>
        <select name="duration_minutes" class="form-control-clinic" required>
            @foreach([15, 30, 45, 60] as $min)
                <option value="{{ $min }}" @selected(old('duration_minutes', $appointment->duration_minutes ?? 30) == $min)>{{ $min }} minutes</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label-clinic">Appointment Date *</label>
        <input type="date" name="appointment_date" class="form-control-clinic"
               value="{{ old('appointment_date', isset($appointment->appointment_date) ? $appointment->appointment_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>

    <div>
        <label class="form-label-clinic">Appointment Time *</label>
        <input type="time" name="appointment_time" class="form-control-clinic"
               value="{{ old('appointment_time', $appointment->appointment_time ?? '') }}" required>
    </div>

    <div class="full-span">
        <label class="form-label-clinic">Reason for Visit</label>
        <input type="text" name="reason" class="form-control-clinic" placeholder="e.g. Root canal follow-up"
               value="{{ old('reason', $appointment->reason ?? '') }}">
    </div>

    <div>
        <label class="form-label-clinic">Status *</label>
        <select name="status" class="form-control-clinic" required>
            @foreach(['Scheduled', 'Confirmed', 'Completed', 'Cancelled', 'No-Show'] as $s)
                <option value="{{ $s }}" @selected(old('status', $appointment->status ?? 'Scheduled') == $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>

    <div class="full-span">
        <label class="form-label-clinic">Notes</label>
        <textarea name="notes" class="form-control-clinic" rows="3">{{ old('notes', $appointment->notes ?? '') }}</textarea>
    </div>

</div>