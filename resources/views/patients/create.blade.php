@extends('layouts.app')

@section('title', 'Add Patient')
@section('page-title', 'Add New Patient')
@section('page-subtitle', 'Register a new patient record')

@section('content')

    <div class="panel">
        <form action="{{ route('patients.store') }}" method="POST">
            @csrf
            <input type="hidden" name="return_to" value="{{ request()->query('return_to') }}">
            @include('patients._form')

            <div style="margin-top:26px; display:flex; gap:12px;">
                <button type="submit" class="btn-clinic">
                    <i class="fa-solid fa-floppy-disk"></i> Save Patient
                </button>
                <a href="{{ request()->query('return_to') === 'opd' ? route('opd.create') : route('patients.index') }}" class="btn-outline-clinic">Cancel</a>
            </div>
        </form>
    </div>

@endsection