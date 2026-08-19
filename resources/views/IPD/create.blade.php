@extends('layouts.app')

@section('title', 'New IPD Admission')
@section('page-title', 'IPD Patient Registration')
@section('page-subtitle', 'Admit a new patient to the In-Patient Department')

@section('content')

<style>
    :root {
        --ipd-primary: #4154f1;
        --ipd-dark: #012970;
        --ipd-border: #cbd5e1; /* Stronger, more visible border color */
        --ipd-text-muted: #334155;
        --ipd-teal: #3FBFAD;
        --ipd-purple: #7C5CFC;
        --ipd-orange: #FF8A5C;
    }
    .ipd-form-container { width: 100%; margin: 0; } /* Full Width */
    
    /* Card Styling */
    .ipd-card { 
        background: #fff; border-radius: 16px; padding: 0; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 28px; 
        border: 1px solid #e2e8f0; overflow: hidden; 
    }
    .ipd-card-header { 
        display: flex; align-items: center; gap: 14px; padding: 20px 28px; color: #fff; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .ipd-card-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; }
    .ipd-card-header .icon-box { 
        width: 42px; height: 42px; border-radius: 10px; background: rgba(255,255,255,0.25); 
        display: flex; align-items: center; justify-content: center; font-size: 18px; 
    }
    
    /* Colorful Headers */
    .header-blue { background: linear-gradient(135deg, #012970, #4154f1); }
    .header-teal { background: linear-gradient(135deg, #123C3A, #3FBFAD); }
    .header-purple { background: linear-gradient(135deg, #4B2ED8, #7C5CFC); }
    .header-orange { background: linear-gradient(135deg, #E4572E, #FF8A5C); }
    
    /* Body & Grid */
    .ipd-card-body { padding: 28px; }
    .ipd-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; }
    .ipd-form-group { display: flex; flex-direction: column; }
    
    /* Labels - Darker and Bolder */
    .ipd-form-group label { 
        font-size: 0.85rem; font-weight: 700; color: var(--ipd-text-muted); 
        margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; 
    }
    
    /* Inputs - Thicker borders, more padding, highly visible */
    .ipd-input { 
        width: 100%; padding: 14px 16px; border-radius: 10px; 
        border: 1.5px solid var(--ipd-border); background: #fff; 
        font-size: 1rem; color: #1e293b; transition: all 0.2s; 
    }
    .ipd-input:focus { 
        outline: none; border-color: var(--ipd-primary); border-width: 2px; 
        box-shadow: 0 0 0 4px rgba(65, 84, 241, 0.15); background: #fff; 
    }
    
    .btn-ipd-submit { 
        background: var(--ipd-primary); color: #fff; padding: 14px 28px; border-radius: 10px; 
        border: none; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; 
        gap: 8px; font-size: 1rem; transition: all 0.2s;
    }
    .btn-ipd-submit:hover { background: var(--ipd-dark); transform: translateY(-2px); }
    
    .btn-ipd-cancel { 
        background: #fff; color: var(--ipd-text-muted); padding: 14px 28px; border-radius: 10px; 
        border: 1.5px solid var(--ipd-border); font-weight: 600; cursor: pointer; text-decoration: none; 
        display: inline-flex; align-items: center; gap: 8px; font-size: 1rem;
    }
    
    .attachment-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .attachment-table th { text-align: left; font-size: 0.85rem; color: var(--ipd-text-muted); padding: 12px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; }
    .attachment-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
</style>

<div class="ipd-form-container">
    
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

    <form action="{{ route('ipd.store') }}" method="POST">
        @csrf

        <!-- Section 1: Admission Details (Blue) -->
        <div class="ipd-card">
            <div class="ipd-card-header header-blue">
                <div class="icon-box"><i class="fa-solid fa-hospital"></i></div>
                <h3>Admission Details</h3>
            </div>
            <div class="ipd-card-body">
                <div class="ipd-form-grid">
                    <div class="ipd-form-group">
                        <label>Admission Date & Time *</label>
                        <input type="datetime-local" name="admission_date" class="ipd-input" value="{{ old('admission_date', date('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="ipd-form-group">
                        <label>Registered Type *</label>
                        <select name="registered_type" id="registered_type" class="ipd-input" required onchange="togglePatientFields(this.value)">
                            <option value="Existing" @selected(old('registered_type') == 'Existing' || !old('registered_type'))>Existing Patient</option>
                            <option value="New" @selected(old('registered_type') == 'New')">New Patient</option>
                        </select>
                    </div>
                    
                    <!-- Existing Patient Dropdown -->
                    <div class="ipd-form-group" id="existingPatientDiv">
                        <label>Select Existing Patient *</label>
                        <select name="patient_id" class="ipd-input">
                            <option value="">-- Select Patient --</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" @selected(old('patient_id') == $p->id)>{{ $p->full_name }} ({{ $p->patient_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ipd-form-group">
                        <label>Scheme Type</label>
                        <select name="scheme_type" class="ipd-input">
                            <option value="">Select Type</option>
                            <option>Cash</option>
                            <option>Insurance</option>
                            <option>CGHS</option>
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Scheme Name</label>
                        <select name="scheme_name" class="ipd-input">
                            <option value="">Select Scheme</option>
                            <option>Ayushman Bharat</option>
                            <option>Star Health</option>
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Case Type</label>
                        <select name="case_type" class="ipd-input">
                            <option value="">Select Case Type</option>
                            <option>General</option>
                            <option>Emergency</option>
                            <option>Medico-Legal</option>
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Bill Category</label>
                        <select name="bill_category" class="ipd-input">
                            <option value="">Select Bill Category</option>
                            <option>General Ward</option>
                            <option>Private Ward</option>
                            <option>ICU</option>
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Corporate</label>
                        <div style="display:flex; gap:15px; align-items:center; height: 100%; padding-top: 10px;">
                            <label style="display:flex; align-items:center; gap:6px; font-weight:500; text-transform: none; letter-spacing: 0;"><input type="radio" name="corporate" value="1"> Yes</label>
                            <label style="display:flex; align-items:center; gap:6px; font-weight:500; text-transform: none; letter-spacing: 0;"><input type="radio" name="corporate" value="0" checked> No</label>
                        </div>
                    </div>
                    <div class="ipd-form-group">
                        <label>ESIC No</label>
                        <input type="text" name="esic_no" class="ipd-input" placeholder="Enter ESIC Number">
                    </div>
                    <div class="ipd-form-group">
                        <label>URN No</label>
                        <input type="text" name="urn_no" class="ipd-input" placeholder="Enter URN Number">
                    </div>
                    <div class="ipd-form-group" style="grid-column: span 2;">
                        <label>Admission Note</label>
                        <textarea name="admission_note" class="ipd-input" rows="2" placeholder="Enter admission note..."></textarea>
                    </div>
                    <div class="ipd-form-group">
                        <label>Referral Doctor</label>
                        <input type="text" name="referral_doctor" class="ipd-input" placeholder="Enter Referral Doctor">
                    </div>
                    <div class="ipd-form-group">
                        <label>Remark</label>
                        <input type="text" name="remark" class="ipd-input" placeholder="Enter Remark">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: New Patient Details (Teal) - Hidden by default -->
        <div class="ipd-card" id="newPatientCard" style="display: none;">
            <div class="ipd-card-header header-teal">
                <div class="icon-box"><i class="fa-solid fa-user"></i></div>
                <h3>New Patient Details</h3>
            </div>
            <div class="ipd-card-body">
                <div class="ipd-form-grid">
                    <div class="ipd-form-group">
                        <label>Patient Name *</label>
                        <input type="text" name="p_name" class="ipd-input" placeholder="Patient Name" value="{{ old('p_name') }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Gender *</label>
                        <select name="p_gender" class="ipd-input">
                            <option value="">Select Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>DOB</label>
                        <input type="date" id="ipd_dob" name="p_dob" class="ipd-input" onchange="calculateIpdAge(this)" value="{{ old('p_dob') }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Age (Auto-Calculated)</label>
                        <input type="text" id="ipd_age" name="p_age" class="ipd-input" placeholder="Auto-fills on DOB select" value="{{ old('p_age') }}" readonly style="background: #f8fafc; color: #64748b; cursor: not-allowed; border: 1.5px dashed #cbd5e1;">
                    </div>
                    <div class="ipd-form-group">
                        <label>Mobile Number *</label>
                        <input type="text" name="p_mobile" class="ipd-input" placeholder="Enter mobile number" value="{{ old('p_mobile') }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Aadhar Number</label>
                        <input type="text" name="p_aadhar" class="ipd-input" placeholder="Enter Aadhaar Number" value="{{ old('p_aadhar') }}">
                    </div>
                    <div class="ipd-form-group" style="grid-column: span 2;">
                        <label>Address</label>
                        <textarea name="p_address" class="ipd-input" rows="2" placeholder="Enter Address">{{ old('p_address') }}</textarea>
                    </div>
                    <div class="ipd-form-group">
                        <label>MLC *</label>
                        <select name="p_mlc" class="ipd-input">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>F/H Name</label>
                        <input type="text" name="p_fh_name" class="ipd-input" placeholder="Father/Husband Name" value="{{ old('p_fh_name') }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Mother Name</label>
                        <input type="text" name="p_mother_name" class="ipd-input" placeholder="Enter Mother Name" value="{{ old('p_mother_name') }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Marital Status</label>
                        <select name="p_marital_status" class="ipd-input">
                            <option value="">Select Status</option>
                            <option>Single</option>
                            <option>Married</option>
                            <option>Widowed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Patient Relative Details (Purple) -->
        <div class="ipd-card">
            <div class="ipd-card-header header-purple">
                <div class="icon-box"><i class="fa-solid fa-users"></i></div>
                <h3>Patient Relative Details</h3>
            </div>
            <div class="ipd-card-body">
                <div class="ipd-form-grid">
                    <div class="ipd-form-group">
                        <label>Relative Name</label>
                        <input type="text" name="rel_name" class="ipd-input" placeholder="Enter Name">
                    </div>
                    <div class="ipd-form-group">
                        <label>Relation</label>
                        <input type="text" name="rel_relation" class="ipd-input" placeholder="e.g. Brother, Son">
                    </div>
                    <div class="ipd-form-group">
                        <label>Contact No.</label>
                        <input type="text" name="rel_contact" class="ipd-input" placeholder="Enter Contact No.">
                    </div>
                    <div class="ipd-form-group" style="grid-column: span 2;">
                        <label>Address</label>
                        <textarea name="rel_address" class="ipd-input" rows="2" placeholder="Enter Address"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Consultant Details (Orange) -->
        <div class="ipd-card">
            <div class="ipd-card-header header-orange">
                <div class="icon-box"><i class="fa-solid fa-user-doctor"></i></div>
                <h3>Consultant Details</h3>
            </div>
            <div class="ipd-card-body">
                <div class="ipd-form-grid">
                    <div class="ipd-form-group">
                        <label>Incharge Consultant *</label>
                        <select name="doctor_id" class="ipd-input" required>
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}" @selected(old('doctor_id') == $d->id)>{{ $d->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Attending Consultant</label>
                        <select name="attending_doctor_id" class="ipd-input">
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}" @selected(old('attending_doctor_id') == $d->id)>{{ $d->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Advance Paid (₹)</label>
                        <input type="number" name="advance_paid" class="ipd-input" placeholder="0.00" step="0.01">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Attachments -->
        <div class="ipd-card">
            <div class="ipd-card-header" style="background: #f1f5f9; color: #334155;">
                <div class="icon-box" style="background: #e2e8f0; color: #475569;"><i class="fa-solid fa-paperclip"></i></div>
                <h3>Attachment List</h3>
            </div>
            <div class="ipd-card-body">
                <table class="attachment-table">
                    <thead>
                        <tr>
                            <th>Attachment</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="file" class="ipd-input" style="padding: 8px;"></td>
                            <td><input type="text" class="ipd-input" placeholder="Enter remarks"></td>
                            <td><button type="button" class="btn-ipd-cancel" style="padding: 8px 14px;"><i class="fa-solid fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 16px; justify-content: flex-end; margin-top: 10px;">
            <a href="{{ route('ipd.dashboard') }}" class="btn-ipd-cancel">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-ipd-submit">
                <i class="fa-solid fa-check"></i> Admit Patient
            </button>
        </div>

    </form>
</div>

<script>
    // Toggle Existing vs New Patient Fields
    function togglePatientFields(type) {
        if (type === 'New') {
            document.getElementById('existingPatientDiv').style.display = 'none';
            document.getElementById('newPatientCard').style.display = 'block';
        } else {
            document.getElementById('existingPatientDiv').style.display = 'flex';
            document.getElementById('newPatientCard').style.display = 'none';
        }
    }

    // Auto-Calculate Age for IPD New Patient
    function calculateIpdAge(dobInput) {
        const dob = new Date(dobInput.value);
        if (isNaN(dob)) {
            document.getElementById('ipd_age').value = '';
            return;
        }
        
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        
        document.getElementById('ipd_age').value = age + ' Years';
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
        togglePatientFields(document.getElementById('registered_type').value);
    });
</script>

@endsection