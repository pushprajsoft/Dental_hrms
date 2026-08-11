@if($errors->any())
    <div class="alert-clinic" style="background: var(--clr-warn-soft); color: var(--clr-warn);">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <strong>Please fix the following:</strong>
            <ul style="margin:6px 0 0 18px; padding:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="form-grid">

    <div>
        <label class="form-label-clinic">Full Name *</label>
        <input type="text" name="full_name" class="form-control-clinic"
               value="{{ old('full_name', $doctor->full_name ?? '') }}" placeholder="e.g. Dr. Pushpraj" required>
    </div>

    <div>
        <label class="form-label-clinic">Specialization *</label>
        <input type="text" name="specialization" class="form-control-clinic"
               value="{{ old('specialization', $doctor->specialization ?? '') }}" placeholder="e.g. Orthodontist" required>
    </div>

    <div>
        <label class="form-label-clinic">Qualification</label>
        <input type="text" name="qualification" class="form-control-clinic" placeholder="e.g. BDS, MDS"
               value="{{ old('qualification', $doctor->qualification ?? '') }}">
    </div>

    <div>
        <label class="form-label-clinic">Experience (years)</label>
        <input type="number" name="experience_years" class="form-control-clinic" min="0" max="70"
               value="{{ old('experience_years', $doctor->experience_years ?? '') }}">
    </div>

    <div>
        <label class="form-label-clinic">Phone *</label>
        <input type="text" name="phone" class="form-control-clinic"
               value="{{ old('phone', $doctor->phone ?? '') }}" required>
    </div>

    <div>
        <label class="form-label-clinic">Email</label>
        <input type="email" name="email" class="form-control-clinic"
               value="{{ old('email', $doctor->email ?? '') }}">
    </div>

    <div>
        <label class="form-label-clinic">Joining Date</label>
        <input type="date" name="joining_date" class="form-control-clinic"
               value="{{ old('joining_date', isset($doctor->joining_date) ? $doctor->joining_date->format('Y-m-d') : '') }}">
    </div>

    <div>
        <label class="form-label-clinic">Status *</label>
        <select name="status" class="form-control-clinic" required>
            @foreach(['Active', 'On Leave', 'Inactive'] as $s)
                <option value="{{ $s }}" @selected(old('status', $doctor->status ?? 'Active') == $s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>

</div>