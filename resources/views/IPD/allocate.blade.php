@extends('layouts.app')

@section('title', 'Bed Allotment & Transfer')
@section('page-title', 'Bed Allotment Details')
@section('page-subtitle', 'Assign a new bed or transfer patient to a different room')

@section('content')

<style>
    :root {
        --ipd-primary: #4154f1;
        --ipd-dark: #012970;
        --ipd-border: #cbd5e1;
        --ipd-text-muted: #334155;
        --ipd-purple: #7C5CFC;
        --ipd-orange: #FF8A5C;
    }
    .ipd-form-container { max-width: 800px; margin: 0 auto; }
    .ipd-card { background: #fff; border-radius: 16px; padding: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 24px; }
    .ipd-card-header { display: flex; align-items: center; gap: 14px; padding: 20px 28px; color: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .ipd-card-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; }
    .ipd-card-header .icon-box { width: 42px; height: 42px; border-radius: 10px; background: rgba(255,255,255,0.25); display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .header-blue { background: linear-gradient(135deg, #012970, #4154f1); }
    .header-purple { background: linear-gradient(135deg, #4B2ED8, #7C5CFC); }
    .header-orange { background: linear-gradient(135deg, #E4572E, #FF8A5C); }
    .ipd-card-body { padding: 28px; }
    .ipd-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .ipd-form-group { display: flex; flex-direction: column; }
    .ipd-form-group label { font-size: 0.85rem; font-weight: 700; color: var(--ipd-text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .ipd-input { width: 100%; padding: 14px 16px; border-radius: 10px; border: 1.5px solid var(--ipd-border); background: #fff; font-size: 1rem; color: #1e293b; transition: all 0.2s; }
    .ipd-input:focus { outline: none; border-color: var(--ipd-primary); border-width: 2px; box-shadow: 0 0 0 4px rgba(65, 84, 241, 0.15); }
    
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .info-box { background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px solid #e2e8f0; }
    .info-box strong { display: block; color: var(--ipd-dark); font-size: 0.9rem; margin-bottom: 4px; }
    .info-box span { font-size: 1.1rem; font-weight: 700; color: var(--ipd-text-muted); }
    
    .btn-ipd-submit { background: var(--ipd-purple); color: #fff; padding: 14px 28px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem; }
    .btn-ipd-submit:hover { background: #4B2ED8; transform: translateY(-2px); }
    .btn-ipd-cancel { background: #fff; color: var(--ipd-text-muted); padding: 14px 28px; border-radius: 10px; border: 1.5px solid var(--ipd-border); font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem; }
</style>

<div class="ipd-form-container">
    
    <div class="alert-clinic" style="background: #EFF6FF; color: #1D4ED8; margin-bottom: 20px; border: 1px solid #BFDBFE;">
        <i class="fa-solid fa-info-circle"></i> Select a Bed Type to filter available beds. Transferring will automatically free up the current bed.
    </div>

    <!-- Patient Info -->
    <div class="ipd-card">
        <div class="ipd-card-header header-blue">
            <div class="icon-box"><i class="fa-solid fa-user"></i></div>
            <h3>Patient Details</h3>
        </div>
        <div class="ipd-card-body">
            <div class="info-grid">
                <div class="info-box">
                    <strong>IPD Code</strong>
                    <span>{{ $ipdAdmission->ipd_code }}</span>
                </div>
                <div class="info-box">
                    <strong>Patient Name</strong>
                    <span>{{ $ipdAdmission->p_name ?? $ipdAdmission->patient->full_name ?? 'N/A' }}</span>
                </div>
                <div class="info-box">
                    <strong>Doctor</strong>
                    <span>{{ $ipdAdmission->doctor->full_name ?? 'N/A' }}</span>
                </div>
                <div class="info-box">
                    <strong>Status</strong>
                    <span>{{ $ipdAdmission->status }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($ipdAdmission->bed_id)
    <!-- Current Bed Info (Shows only if patient already has a bed) -->
    <div class="ipd-card">
        <div class="ipd-card-header header-orange">
            <div class="icon-box"><i class="fa-solid fa-bed"></i></div>
            <h3>Current Bed Details</h3>
        </div>
        <div class="ipd-card-body">
            <div class="info-grid">
                <div class="info-box">
                    <strong>Current Bed Number</strong>
                    <span>{{ $ipdAdmission->bed->bed_number ?? 'N/A' }}</span>
                </div>
                <div class="info-box">
                    <strong>Room Number</strong>
                    <span>{{ $ipdAdmission->bed->room_number ?? 'N/A' }}</span>
                </div>
                <div class="info-box">
                    <strong>Current Bed Type</strong>
                    <span style="color: var(--ipd-orange);">{{ $ipdAdmission->bed->bed_type ?? 'N/A' }}</span>
                </div>
                <div class="info-box">
                    <strong>Allotted On</strong>
                    <span>{{ \Carbon\Carbon::parse($ipdAdmission->allotment_date)->format('d M Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Bed Allotment / Transfer Form -->
    <div class="ipd-card">
        <div class="ipd-card-header header-purple">
            <div class="icon-box"><i class="fa-solid fa-exchange-alt"></i></div>
            <h3>{{ $ipdAdmission->bed_id ? 'Transfer to New Bed' : 'Bed Allotment' }}</h3>
        </div>
        <div class="ipd-card-body">
            <form action="{{ route('ipd.allocate.update', $ipdAdmission->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="ipd-form-grid">
                    <div class="ipd-form-group">
                        <label>Filter by Bed Type *</label>
                        <select id="bed_type_filter" class="ipd-input" onchange="filterBeds()">
                            <option value="All">All Types</option>
                            <option value="General">General</option>
                            <option value="Private">Private</option>
                            <option value="ICU">ICU</option>
                            <option value="Ward">Ward</option>
                        </select>
                    </div>
                    <div class="ipd-form-group">
                        <label>Select {{ $ipdAdmission->bed_id ? 'New Bed' : 'Bed' }} *</label>
                        <select name="bed_id" id="bed_select" class="ipd-input" required>
                            <option value="">-- Choose Available Bed --</option>
                            @foreach($beds as $bed)
                                <option value="{{ $bed->id }}" data-bed-type="{{ $bed->bed_type }}" @selected($ipdAdmission->bed_id == $bed->id)>
                                    {{ $bed->bed_number }} (Room: {{ $bed->room_number ?? 'N/A' }}) - {{ $bed->bed_type }} - ₹{{ $bed->charge_per_day }}/day
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ipd-form-group" style="grid-column: span 2;">
                        <label>{{ $ipdAdmission->bed_id ? 'Transfer' : 'Allotment' }} Date & Time *</label>
                        <input type="datetime-local" name="allotment_date" class="ipd-input" value="{{ old('allotment_date', $ipdAdmission->allotment_date ? \Carbon\Carbon::parse($ipdAdmission->allotment_date)->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>

                <div style="display: flex; gap: 16px; justify-content: flex-end; margin-top: 28px;">
                    <a href="{{ route('ipd.dashboard') }}" class="btn-ipd-cancel">
                        <i class="fa-solid fa-times"></i> Close
                    </a>
                    <button type="submit" class="btn-ipd-submit">
                        <i class="fa-solid fa-exchange-alt"></i> {{ $ipdAdmission->bed_id ? 'Transfer Bed' : 'Allocate Bed' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function filterBeds() {
        const typeFilter = document.getElementById('bed_type_filter').value;
        const bedSelect = document.getElementById('bed_select');
        const options = bedSelect.getElementsByTagName('option');

        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            // Skip the first "-- Choose --" option
            if (i === 0) continue;

            const bedType = option.getAttribute('data-bed-type');
            
            if (typeFilter === 'All' || bedType === typeFilter) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        }
        // Reset selection if the current selected bed is hidden by the filter
        const selectedBedType = bedSelect.options[bedSelect.selectedIndex].getAttribute('data-bed-type');
        if (selectedBedType && selectedBedType !== typeFilter && typeFilter !== 'All') {
            bedSelect.value = "";
        }
    }
</script>

@endsection