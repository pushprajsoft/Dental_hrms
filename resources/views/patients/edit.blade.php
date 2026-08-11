@extends('layouts.app')

@section('title', 'Edit Patient')
@section('page-title', 'Edit Patient')
@section('page-subtitle', $patient->patient_code . ' · ' . $patient->full_name)

@section('content')

    <div class="panel">
        <form action="{{ route('patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')
            @include('patients._form')

            <div style="margin-top:26px; display:flex; gap:12px;">
                <button type="submit" class="btn-clinic">
                    <i class="fa-solid fa-floppy-disk"></i> Update Patient
                </button>
                <a href="{{ route('patients.index') }}" class="btn-outline-clinic">Cancel</a>
            </div>
        </form>
    </div>

@endsection