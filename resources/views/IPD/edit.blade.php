@extends('layouts.app')

@section('title', 'Edit IPD Admission')
@section('page-title', 'Edit / Discharge Patient')
@section('page-subtitle', 'Update admission details or discharge the patient')

@section('content')

<style>
    :root {
        --ipd-primary: #4154f1;
        --ipd-dark: #012970;
        --ipd-border: #cbd5e1;
        --ipd-text-muted: #334155;
        --ipd-purple: #7C5CFC;
        --ipd-orange: #FF8A5C;
        --ipd-teal: #3FBFAD;
    }
    .ipd-form-container { width: 100%; margin: 0; }
    .ipd-card { background: #fff; border-radius: 16px; padding: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 28px; border: 1px solid #e2e8f0; overflow: hidden; }
    .ipd-card-header { display: flex; align-items: center; gap: 14px; padding: 20px 28px; color: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .ipd-card-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; }
    .ipd-card-header .icon-box { width: 42px; height: 42px; border-radius: 10px; background: rgba(255,255,255,0.25); display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .header-blue { background: linear-gradient(135deg, #012970, #4154f1); }
    .header-teal { background: linear-gradient(135deg, #123C3A, #3FBFAD); }
    .header-purple { background: linear-gradient(135deg, #4B2ED8, #7C5CFC); }
    .header-orange { background: linear-gradient(135deg, #E4572E, #FF8A5C); }
    .header-grey { background: linear-gradient(135deg, #475569, #64748b); }
    .ipd-card-body { padding: 28px; }
    .ipd-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; }
    .ipd-form-group { display: flex; flex-direction: column; }
    .ipd-form-group label { font-size: 0.85rem; font-weight: 700; color: var(--ipd-text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .ipd-input { width: 100%; padding: 14px 16px; border-radius: 10px; border: 1.5px solid var(--ipd-border); background: #fff; font-size: 1rem; color: #1e293b; transition: all 0.2s; }
    .ipd-input:focus { outline: none; border-color: var(--ipd-primary); border-width: 2px; box-shadow: 0 0 0 4px rgba(65, 84, 241, 0.15); background: #fff; }
    .ipd-input[readonly] { background: #f8fafc; color: #64748b; cursor: not-allowed; border: 1.5px dashed #cbd5e1; }
    .btn-ipd-submit { background: var(--ipd-primary); color: #fff; padding: 14px 28px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem; }
    .btn-ipd-submit:hover { background: var(--ipd-dark); transform: translateY(-2px); }
    .btn-ipd-cancel { background: #fff; color: var(--ipd-text-muted); padding: 14px 28px; border-radius: 10px; border: 1.5px solid var(--ipd-border); font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem; }
    .attachment-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .attachment-table th { text-align: left; font-size: 0.85rem; color: var(--ipd-text-muted); padding: 12px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; }
    .attachment-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
</style>

<div class="ipd-form-container">
    
    @if($errors->any())
        <div class="alert-clinic" style="background: #FEF2F2; color: #B91C1C; margin-bottom: 20px; border: 1px solid #FCA5A5;">
            <i class="fa-solid fa-triangle-exclamation"></i> Please fix the errors below.
        </div>
    @endif

    <form action="{{ route('ipd.update', $ipdAdmission->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Section 1: Admission Details -->
        <div class="ipd-card">
            <div class="ipd-card-header header-blue">
                <div class="icon-box"><i class="fa-solid fa-hospital"></i></div>
                <h3>Admission Details</h3>
            </div>
            <div class="ipd-card-body">
                <div class="ipd-form-grid">
                    <div class="ipd-form-group">
                        <label>IPD Code</label>
                        <input type="text" class="ipd-input" value="{{ $ipdAdmission->ipd_code }}" readonly>
                    </div>
                    <div class="ipd-form-group">
                        <label>Admission Date & Time *</label>
                        <input type="datetime-local" name="admission_date" class="ipd-input" value="{{ old('admission_date', \Carbon\Carbon::parse($ipdAdmission->admission_date)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="ipd-form-group">
                        <label>Registered Type *</label>
                        <select name="registered_type" class="ipd-input" required>
                            <option value="Existing" @selected(old('registered_type', $ipdAdmission->registered_type) == 'Existing')>Existing Patient</option>
                            <option value="New" @selected(old('registered_type', $ipdAdmission->registered_type) == 'New')>New Patient</option>
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Scheme Type</label>
                        <select name="scheme_type" class="ipd-input">
                            <option value="">Select Type</option>
                            @foreach(['Cash', 'Insurance', 'CGHS'] as $s)
                                <option value="{{ $s }}" @selected(old('scheme_type', $ipdAdmission->scheme_type) == $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Scheme Name</label>
                        <select name="scheme_name" class="ipd-input">
                            <option value="">Select Scheme</option>
                            @foreach(['Ayushman Bharat', 'Star Health'] as $s)
                                <option value="{{ $s }}" @selected(old('scheme_name', $ipdAdmission->scheme_name) == $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Case Type</label>
                        <select name="case_type" class="ipd-input">
                            <option value="">Select Case Type</option>
                            @foreach(['General', 'Emergency', 'Medico-Legal'] as $c)
                                <option value="{{ $c }}" @selected(old('case_type', $ipdAdmission->case_type) == $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Bill Category</label>
                        <select name="bill_category" class="ipd-input">
                            <option value="">Select Bill Category</option>
                            @foreach(['General Ward', 'Private Ward', 'ICU'] as $b)
                                <option value="{{ $b }}" @selected(old('bill_category', $ipdAdmission->bill_category) == $b)>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Corporate</label>
                        <div style="display:flex; gap:15px; align-items:center; height: 100%; padding-top: 10px;">
                            <label style="display:flex; align-items:center; gap:6px; font-weight:500; text-transform: none; letter-spacing: 0;"><input type="radio" name="corporate" value="1" @checked(old('corporate', $ipdAdmission->corporate) == 1)> Yes</label>
                            <label style="display:flex; align-items:center; gap:6px; font-weight:500; text-transform: none; letter-spacing: 0;"><input type="radio" name="corporate" value="0" @checked(old('corporate', $ipdAdmission->corporate) == 0)> No</label>
                        </div>
                    </div>
                    <div class="ipd-form-group">
                        <label>ESIC No</label>
                        <input type="text" name="esic_no" class="ipd-input" value="{{ old('esic_no', $ipdAdmission->esic_no) }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>URN No</label>
                        <input type="text" name="urn_no" class="ipd-input" value="{{ old('urn_no', $ipdAdmission->urn_no) }}">
                    </div>
                    <div class="ipd-form-group" style="grid-column: span 2;">
                        <label>Admission Note</label>
                        <textarea name="admission_note" class="ipd-input" rows="2">{{ old('admission_note', $ipdAdmission->admission_note) }}</textarea>
                    </div>
                    <div class="ipd-form-group">
                        <label>Referral Doctor</label>
                        <input type="text" name="referral_doctor" class="ipd-input" value="{{ old('referral_doctor', $ipdAdmission->referral_doctor) }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Remark</label>
                        <input type="text" name="remark" class="ipd-input" value="{{ old('remark', $ipdAdmission->remark) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Patient Details -->
        <div class="ipd-card">
            <div class="ipd-card-header header-teal">
                <div class="icon-box"><i class="fa-solid fa-user"></i></div>
                <h3>Patient Details (Updates Main Patient Record)</h3>
            </div>
            <div class="ipd-card-body">
                <div class="ipd-form-grid">
                    <div class="ipd-form-group">
                        <label>Patient Name *</label>
                        <input type="text" name="p_name" class="ipd-input" value="{{ old('p_name', $ipdAdmission->patient->full_name ?? '') }}" required>
                    </div>
                    <div class="ipd-form-group">
                        <label>Gender *</label>
                        <select name="p_gender" class="ipd-input" required>
                            <option value="">Select Gender</option>
                            @foreach(['Male', 'Female', 'Other'] as $g)
                                <option value="{{ $g }}" @selected(old('p_gender', $ipdAdmission->patient->gender ?? '') == $g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>DOB</label>
                        <input type="date" id="p_dob" name="p_dob" class="ipd-input" value="{{ old('p_dob', isset($ipdAdmission->patient->date_of_birth) ? $ipdAdmission->patient->date_of_birth->format('Y-m-d') : '') }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Age (Auto-Calculated)</label>
                        <input type="text" id="p_age" name="p_age" class="ipd-input" value="{{ old('p_age', $ipdAdmission->patient->age ?? '') }}" readonly>
                    </div>
                    <div class="ipd-form-group">
                        <label>Mobile Number *</label>
                        <input type="text" name="p_mobile" class="ipd-input" value="{{ old('p_mobile', $ipdAdmission->patient->phone ?? '') }}" required>
                    </div>
                    <div class="ipd-form-group">
                        <label>Aadhar Number</label>
                        <input type="text" name="p_aadhar" class="ipd-input" value="{{ old('p_aadhar', $ipdAdmission->patient->aadhar ?? '') }}">
                    </div>
                    <div class="ipd-form-group" style="grid-column: span 2;">
                        <label>Address</label>
                        <textarea name="p_address" class="ipd-input" rows="2">{{ old('p_address', $ipdAdmission->patient->address ?? '') }}</textarea>
                    </div>
                    <div class="ipd-form-group">
                        <label>MLC *</label>
                        <select name="p_mlc" class="ipd-input">
                            @foreach(['No', 'Yes'] as $m)
                                <option value="{{ $m }}" @selected(old('p_mlc', $ipdAdmission->patient->mlc ?? 'No') == $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- FIXED LABEL HERE -->
                    <div class="ipd-form-group">
                        <label>Father Name</label>
                        <input type="text" name="p_fh_name" class="ipd-input" value="{{ old('p_fh_name', $ipdAdmission->patient->fh_name ?? '') }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Mother Name</label>
                        <input type="text" name="p_mother_name" class="ipd-input" value="{{ old('p_mother_name', $ipdAdmission->patient->mother_name ?? '') }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Marital Status</label>
                        <select name="p_marital_status" class="ipd-input">
                            <option value="">Select Status</option>
                            @foreach(['Single', 'Married', 'Widowed'] as $ms)
                                <option value="{{ $ms }}" @selected(old('p_marital_status', $ipdAdmission->patient->marital_status ?? '') == $ms)>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Patient Relative Details -->
        <div class="ipd-card">
            <div class="ipd-card-header header-purple">
                <div class="icon-box"><i class="fa-solid fa-users"></i></div>
                <h3>Patient Relative Details</h3>
            </div>
            <div class="ipd-card-body">
                <div class="ipd-form-grid">
                    <div class="ipd-form-group">
                        <label>Relative Name</label>
                        <input type="text" name="rel_name" class="ipd-input" value="{{ old('rel_name', $ipdAdmission->rel_name) }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Relation</label>
                        <input type="text" name="rel_relation" class="ipd-input" value="{{ old('rel_relation', $ipdAdmission->rel_relation) }}">
                    </div>
                    <div class="ipd-form-group">
                        <label>Contact No.</label>
                        <input type="text" name="rel_contact" class="ipd-input" value="{{ old('rel_contact', $ipdAdmission->rel_contact) }}">
                    </div>
                    <div class="ipd-form-group" style="grid-column: span 2;">
                        <label>Address</label>
                        <textarea name="rel_address" class="ipd-input" rows="2">{{ old('rel_address', $ipdAdmission->rel_address) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Consultant Details -->
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
                                <option value="{{ $d->id }}" @selected(old('doctor_id', $ipdAdmission->doctor_id) == $d->id)>{{ $d->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Attending Consultant</label>
                        <select name="attending_doctor_id" class="ipd-input">
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}" @selected(old('attending_doctor_id', $ipdAdmission->attending_doctor_id) == $d->id)>{{ $d->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Advance Paid (₹)</label>
                        <input type="number" name="advance_paid" class="ipd-input" step="0.01" value="{{ old('advance_paid', $ipdAdmission->advance_paid) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Discharge Details -->
        <div class="ipd-card">
            <div class="ipd-card-header header-grey">
                <div class="icon-box"><i class="fa-solid fa-door-open"></i></div>
                <h3>Discharge Details</h3>
            </div>
            <div class="ipd-card-body">
                <div class="ipd-form-grid">
                    <div class="ipd-form-group">
                        <label>Status *</label>
                        <select name="status" class="ipd-input" required>
                            <option value="Admitted" @selected(old('status', $ipdAdmission->status) == 'Admitted')>Admitted</option>
                            <option value="Discharged" @selected(old('status', $ipdAdmission->status) == 'Discharged')>Discharged</option>
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Discharge Date</label>
                        <input type="date" name="discharge_date" class="ipd-input" value="{{ old('discharge_date', $ipdAdmission->discharge_date ? \Carbon\Carbon::parse($ipdAdmission->discharge_date)->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Attachments -->
        <div class="ipd-card">
            <div class="ipd-card-header header-grey">
                <div class="icon-box"><i class="fa-solid fa-paperclip"></i></div>
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
                <i class="fa-solid fa-check"></i> Update Record
            </button>
        </div>

    </form>
</div>

<!-- FIXED JAVASCRIPT FOR AGE CALCULATION -->
<script>
    function calculateAge() {
        const dobInput = document.getElementById('p_dob');
        const ageInput = document.getElementById('p_age');
        
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
        const dobField = document.getElementById('p_dob');
        if (dobField) {
            dobField.addEventListener('change', calculateAge);
            dobField.addEventListener('blur', calculateAge);
        }
    });
</script>

@endsection