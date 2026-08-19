@extends('layouts.app')

@section('title', 'Edit Bed')
@section('page-title', 'Edit Bed Details')
@section('page-subtitle', 'Update bed information and status')

@section('content')

<style>
    :root {
        --ipd-primary: #4154f1;
        --ipd-dark: #012970;
        --ipd-border: #cbd5e1;
        --ipd-text-muted: #334155;
    }
    .bed-form-container { max-width: 800px; margin: 0 auto; }
    .bed-card { background: #fff; border-radius: 16px; padding: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; }
    .bed-card-header { background: linear-gradient(135deg, #012970, #4154f1); color: #fff; padding: 20px 28px; display: flex; align-items: center; gap: 12px; }
    .bed-card-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; }
    .bed-card-body { padding: 28px; }
    .bed-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .bed-form-group { display: flex; flex-direction: column; }
    .bed-form-group label { font-size: 0.85rem; font-weight: 700; color: var(--ipd-text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .bed-input { width: 100%; padding: 14px 16px; border-radius: 10px; border: 1.5px solid var(--ipd-border); background: #fff; font-size: 1rem; transition: all 0.2s; }
    .bed-input:focus { outline: none; border-color: var(--ipd-primary); border-width: 2px; box-shadow: 0 0 0 4px rgba(65, 84, 241, 0.15); }
    .btn-bed-submit { background: var(--ipd-primary); color: #fff; padding: 14px 28px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem; }
    .btn-bed-submit:hover { background: var(--ipd-dark); }
    .btn-bed-cancel { background: #fff; color: var(--ipd-text-muted); padding: 14px 28px; border-radius: 10px; border: 1.5px solid var(--ipd-border); font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem; }
</style>

<div class="bed-form-container">
    
    @if($errors->any())
        <div class="alert-clinic" style="background: #FEF2F2; color: #B91C1C; margin-bottom: 20px; border: 1px solid #FCA5A5;">
            <i class="fa-solid fa-triangle-exclamation"></i> Please fix the errors below.
        </div>
    @endif

    <div class="bed-card">
        <div class="bed-card-header">
            <i class="fa-solid fa-pen-to-square"></i>
            <h3>Edit Bed: {{ $bed->bed_number }}</h3>
        </div>
        <div class="bed-card-body">
            <form action="{{ route('ipd.beds.update', $bed->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bed-form-grid">
                    <div class="bed-form-group">
                        <label>Bed Number *</label>
                        <input type="text" name="bed_number" class="bed-input" value="{{ old('bed_number', $bed->bed_number) }}" required>
                    </div>
                    <div class="bed-form-group">
                        <label>Room Number</label>
                        <input type="text" name="room_number" class="bed-input" value="{{ old('room_number', $bed->room_number) }}">
                    </div>
                    <div class="bed-form-group">
                        <label>Bed Type *</label>
                        <select name="bed_type" class="bed-input" required>
                            <option value="General" @selected(old('bed_type', $bed->bed_type) == 'General')>General</option>
                            <option value="Private" @selected(old('bed_type', $bed->bed_type) == 'Private')>Private</option>
                            <option value="ICU" @selected(old('bed_type', $bed->bed_type) == 'ICU')>ICU</option>
                            <option value="Ward" @selected(old('bed_type', $bed->bed_type) == 'Ward')>Ward</option>
                        </select>
                    </div>
                    <div class="bed-form-group">
                        <label>Charge Per Day (₹) *</label>
                        <input type="number" name="charge_per_day" class="bed-input" step="0.01" value="{{ old('charge_per_day', $bed->charge_per_day) }}" required>
                    </div>
                    <div class="bed-form-group" style="grid-column: span 2;">
                        <label>Status *</label>
                        <select name="status" class="bed-input" required>
                            <option value="Available" @selected(old('status', $bed->status) == 'Available')>Available</option>
                            <option value="Occupied" @selected(old('status', $bed->status) == 'Occupied')>Occupied</option>
                            <option value="Maintenance" @selected(old('status', $bed->status) == 'Maintenance')>Maintenance</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 16px; justify-content: flex-end; margin-top: 28px;">
                    <a href="{{ route('ipd.beds.index') }}" class="btn-bed-cancel">
                        <i class="fa-solid fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-bed-submit">
                        <i class="fa-solid fa-check"></i> Update Bed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection