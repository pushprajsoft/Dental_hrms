@extends('layouts.app')

@section('title', 'Edit Appointment')
@section('page-title', 'Edit Appointment')
@section('page-subtitle', $appointment->appointment_code . ' — ' . $appointment->patient->full_name)

@section('content')
<div class="panel">
    <form action="{{ route('appointments.update', $appointment) }}" method="POST">
        @csrf
        @method('PUT')
        @include('appointments._form')

        <div style="margin-top:26px; display:flex; gap:12px;">
            <button type="submit" class="btn-clinic"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            <a href="{{ route('appointments.index') }}" class="btn-outline-clinic">Cancel</a>
        </div>
    </form>
</div>
@endsection