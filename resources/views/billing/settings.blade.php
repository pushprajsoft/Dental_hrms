@extends('layouts.app')

@section('title', 'Invoice Settings')
@section('page-title', 'Print Layout & GST Settings')
@section('page-subtitle', 'Configure clinic invoice header, footer, and tax details')

@section('content')
<style>
    .form-card { background: #fff; border: 1px solid #e5e9f0; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); max-width: 800px; margin: 0 auto; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 8px; color: #123C3A; }
    .form-input { width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #e5e9f0; background: #f8fafc; font-size: 0.95rem; }
    .toggle-switch { display: flex; align-items: center; gap: 10px; margin-top: 20px; }
</style>

<div class="form-card">
    <form action="{{ route('billing.settings.update') }}" method="POST">
        @csrf
        
        <h2 style="margin-top:0; border-bottom:1px solid #e5e9f0; padding-bottom:10px;"><i class="fa-solid fa-hospital" style="color:#3FBFAD;"></i> Clinic Details (Header)</h2>
        <div class="form-grid">
            <div class="form-group">
                <label>Clinic Name</label>
                <input type="text" name="clinic_name" class="form-input" value="{{ old('clinic_name', $settings->clinic_name) }}" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="clinic_phone" class="form-input" value="{{ old('clinic_phone', $settings->clinic_phone) }}">
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Address</label>
                <textarea name="clinic_address" class="form-input" rows="2">{{ old('clinic_address', $settings->clinic_address) }}</textarea>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="clinic_email" class="form-input" value="{{ old('clinic_email', $settings->clinic_email) }}">
            </div>
            <div class="form-group">
                <label>Clinic State (Crucial for GST Type)</label>
                <select name="clinic_state" class="form-input" required>
                    @php 
                        $states = [
                            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattis