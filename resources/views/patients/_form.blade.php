<style>
    :root {
        --p-primary: #3FBFAD;
        --p-dark: #123C3A;
        --p-light: #f6f9ff;
        --p-border: #cbd5e1; /* Stronger, more visible border color */
        --p-text-muted: #334155;
        --p-purple: #7C5CFC;
        --p-orange: #FF8A5C;
    }
    .p-form-container { width: 100%; margin: 0; } /* Full Width */
    
    /* Card Styling */
    .p-card { 
        background: #fff; border-radius: 16px; padding: 0; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 28px; 
        border: 1px solid #e2e8f0; overflow: hidden; 
    }
    .p-card-header { 
        display: flex; align-items: center; gap: 14px; padding: 20px 28px; color: #fff; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .p-card-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; }
    .p-card-header .icon-box { 
        width: 42px; height: 42px; border-radius: 10px; background: rgba(255,255,255,0.25); 
        display: flex; align-items: center; justify-content: center; font-size: 18px; 
    }
    
    /* Colorful Headers */
    .header-teal { background: linear-gradient(135deg, #123C3A, #3FBFAD); }
    .header-purple { background: linear-gradient(135deg, #4B2ED8, #7C5CFC); }
    .header-orange { background: linear-gradient(135deg, #E4572E, #FF8A5C); }
    
    /* Body & Grid */
    .p-card-body { padding: 28px; }
    .p-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; }
    .p-form-group { display: flex; flex-direction: column; }
    
    /* Labels - Darker and Bolder */
    .p-form-group label { 
        font-size: 0.85rem; font-weight: 700; color: var(--p-text-muted); 
        margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; 
    }
    
    /* Inputs - Thicker borders, more padding, highly visible */
    .p-input { 
        width: 100%; padding: 14px 16px; border-radius: 10px; 
        border: 1.5px solid var(--p-border); background: #fff; 
        font-size: 1rem; color: #1e293b; transition: all 0.2s; 
    }
    .p-input:focus { 
        outline: none; border-color: var(--p-primary); border-width: 2px; 
        box-shadow: 0 0 0 4px rgba(63, 191, 173, 0.15); background: #fff; 
    }
    
    /* Readonly input styling (Age field) */
    .p-input[readonly] {
        background: #f8fafc; color: #64748b; cursor: not-allowed; border: 1.5px dashed #cbd5e1;
    }
</style>

@if($errors->any())
    <div class="alert-clinic" style="background: #FEF2F2; color: #B91C1C; margin-bottom: 20px; border: 1px solid #FCA5A5;">
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

<div class="p-form-container">
    
    <!-- Section 1: Patient Details (Teal) -->
    <div class="p-card">
        <div class="p-card-header header-teal">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <h3>Patient Details</h3>
        </div>
        <div class="p-card-body">
            <div class="p-form-grid">
                <div class="p-form-group">
                    <label>Patient Name *</label>
                    <input type="text" name="full_name" class="p-input" value="{{ old('full_name', $patient->full_name ?? '') }}" required>
                </div>
                <div class="p-form-group">
                    <label>Gender *</label>
                    <select name="gender" class="p-input" required>
                        <option value="">Select Gender</option>
                        <option value="Male" @selected(old('gender', $patient->gender ?? '') == 'Male')>Male</option>
                        <option value="Female" @selected(old('gender', $patient->gender ?? '') == 'Female')>Female</option>
                        <option value="Other" @selected(old('gender', $patient->gender ?? '') == 'Other')>Other</option>
                    </select>
                </div>
                <div class="p-form-group">
                    <label>DOB</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="p-input" value="{{ old('date_of_birth', isset($patient->date_of_birth) ? $patient->date_of_birth->format('Y-m-d') : '') }}">
                </div>
                <div class="p-form-group">
                    <label>Age (Auto-Calculated)</label>
                    <input type="text" id="age" name="age" class="p-input" placeholder="Auto-fills on DOB select" value="{{ old('age', $patient->age ?? '') }}" readonly>
                </div>
                <div class="p-form-group">
                    <label>Mobile Number *</label>
                    <input type="text" name="phone" class="p-input" value="{{ old('phone', $patient->phone ?? '') }}" required>
                </div>
                <div class="p-form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="p-input" value="{{ old('email', $patient->email ?? '') }}">
                </div>
                <div class="p-form-group">
                    <label>Aadhar Number</label>
                    <input type="text" name="aadhar" class="p-input" value="{{ old('aadhar', $patient->aadhar ?? '') }}">
                </div>
                <div class="p-form-group">
                    <label>Blood Group</label>
                    <input type="text" name="blood_group" class="p-input" value="{{ old('blood_group', $patient->blood_group ?? '') }}">
                </div>
                <div class="p-form-group" style="grid-column: span 2;">
                    <label>Address</label>
                    <textarea name="address" class="p-input" rows="2">{{ old('address', $patient->address ?? '') }}</textarea>
                </div>
                <div class="p-form-group">
                    <label>MLC</label>
                    <select name="mlc" class="p-input">
                        <option value="No" @selected(old('mlc', $patient->mlc ?? 'No') == 'No')>No</option>
                        <option value="Yes" @selected(old('mlc', $patient->mlc ?? '') == 'Yes')>Yes</option>
                    </select>
                </div>
                <!-- FIXED LABEL HERE -->
                <div class="p-form-group">
                    <label>Father Name</label>
                    <input type="text" name="fh_name" class="p-input" value="{{ old('fh_name', $patient->fh_name ?? '') }}">
                </div>
                <div class="p-form-group">
                    <label>Mother Name</label>
                    <input type="text" name="mother_name" class="p-input" value="{{ old('mother_name', $patient->mother_name ?? '') }}">
                </div>
                <div class="p-form-group">
                    <label>Marital Status</label>
                    <select name="marital_status" class="p-input">
                        <option value="">Select Status</option>
                        <option value="Single" @selected(old('marital_status', $patient->marital_status ?? '') == 'Single')>Single</option>
                        <option value="Married" @selected(old('marital_status', $patient->marital_status ?? '') == 'Married')>Married</option>
                        <option value="Widowed" @selected(old('marital_status', $patient->marital_status ?? '') == 'Widowed')>Widowed</option>
                    </select>
                </div>
                <div class="p-form-group">
                    <label>Status</label>
                    <select name="status" class="p-input" required>
                        <option value="Active" @selected(old('status', $patient->status ?? 'Active') == 'Active')>Active</option>
                        <option value="Completed" @selected(old('status', $patient->status ?? '') == 'Completed')>Completed</option>
                        <option value="Follow-up" @selected(old('status', $patient->status ?? '') == 'Follow-up')>Follow-up</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Patient Relative Details (Purple) -->
    <div class="p-card">
        <div class="p-card-header header-purple">
            <div class="icon-box"><i class="fa-solid fa-users"></i></div>
            <h3>Patient Relative Details</h3>
        </div>
        <div class="p-card-body">
            <div class="p-form-grid">
                <div class="p-form-group">
                    <label>Relative Name</label>
                    <input type="text" name="rel_name" class="p-input" value="{{ old('rel_name', $patient->rel_name ?? '') }}">
                </div>
                <div class="p-form-group">
                    <label>Relation</label>
                    <input type="text" name="rel_relation" class="p-input" placeholder="e.g. Brother, Son" value="{{ old('rel_relation', $patient->rel_relation ?? '') }}">
                </div>
                <div class="p-form-group">
                    <label>Contact No.</label>
                    <input type="text" name="rel_contact" class="p-input" value="{{ old('rel_contact', $patient->rel_contact ?? '') }}">
                </div>
                <div class="p-form-group" style="grid-column: span 2;">
                    <label>Address</label>
                    <textarea name="rel_address" class="p-input" rows="2">{{ old('rel_address', $patient->rel_address ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Medical Details (Orange) -->
    <div class="p-card">
        <div class="p-card-header header-orange">
            <div class="icon-box"><i class="fa-solid fa-notes-medical"></i></div>
            <h3>Medical Information</h3>
        </div>
        <div class="p-card-body">
            <div class="p-form-grid">
                <div class="p-form-group" style="grid-column: span 2;">
                    <label>Chief Complaint</label>
                    <textarea name="chief_complaint" class="p-input" rows="2">{{ old('chief_complaint', $patient->chief_complaint ?? '') }}</textarea>
                </div>
                <div class="p-form-group" style="grid-column: span 2;">
                    <label>Treatment Plan</label>
                    <textarea name="treatment_plan" class="p-input" rows="2">{{ old('treatment_plan', $patient->treatment_plan ?? '') }}</textarea>
                </div>
                <div class="p-form-group">
                    <label>Doctor Name</label>
                    <input type="text" name="doctor_name" class="p-input" value="{{ old('doctor_name', $patient->doctor_name ?? '') }}">
                </div>
            </div>
        </div>
    </div>

</div>

<!-- FIXED JAVASCRIPT FOR AGE CALCULATION -->
<script>
    function calculatePatientAge() {
        const dobInput = document.getElementById('date_of_birth');
        const ageInput = document.getElementById('age');
        
        if (!dobInput || !ageInput) return; 
        
        const dobValue = dobInput.value;
        if (!dobValue) {
            ageInput.value = '';
            return;
        }

        const dob = new Date(dobValue);
        if (isNaN(dob)) {
            ageInput.value = '';
            return;
        }
        
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        ageInput.value = age + ' Years';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dobField = document.getElementById('date_of_birth');
        if (dobField) {
            dobField.addEventListener('change', calculatePatientAge);
            dobField.addEventListener('blur', calculatePatientAge);
        }
    });
</script>