@extends('layouts.app')

@section('title', 'Doctor Details')
@section('page-title', 'Doctor Details')
@section('page-subtitle', $doctor->doctor_code)

@section('content')

    <div class="panel">
        <div class="panel-header">
            <h2>
                <span class="avatar-chip">{{ strtoupper(substr($doctor->full_name, 0, 1)) }}</span>
                {{ $doctor->full_name }}
            </h2>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('doctors.edit', $doctor) }}" class="btn-outline-clinic">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
                <a href="{{ route('doctors.index') }}" class="btn-outline-clinic">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="form-grid">
            <div><span class="form-label-clinic">Doctor Code</span>{{ $doctor->doctor_code }}</div>
            <div><span class="form-label-clinic">Specialization</span>{{ $doctor->specialization }}</div>
            <div><span class="form-label-clinic">Qualification</span>{{ $doctor->qualification ?: '—' }}</div>
            <div><span class="form-label-clinic">Experience</span>{{ $doctor->experience_years ? $doctor->experience_years . ' years' : '—' }}</div>
            <div><span class="form-label-clinic">Phone</span>{{ $doctor->phone }}</div>
            <div><span class="form-label-clinic">Email</span>{{ $doctor->email ?: '—' }}</div>
            <div><span class="form-label-clinic">Joining Date</span>{{ $doctor->joining_date ? $doctor->joining_date->format('d M Y') : '—' }}</div>
            <div><span class="form-label-clinic">Status</span>{{ $doctor->status }}</div>
        </div>
    </div>

@endsection