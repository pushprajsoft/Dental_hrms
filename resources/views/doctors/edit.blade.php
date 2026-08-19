@extends('layouts.app')

@section('title', 'Edit Doctor')
@section('page-title', 'Edit Doctor')
@section('page-subtitle', $doctor->doctor_code . ' · ' . $doctor->full_name)

@section('content')

    <div class="panel">
        <form action="{{ route('doctors.update', $doctor) }}" method="POST">
            @csrf
            @method('PUT')
            @include('doctors._form')

            <div style="margin-top:26px; display:flex; gap:12px;">
                <button type="submit" class="btn-clinic">
                    <i class="fa-solid fa-floppy-disk"></i> Update Doctor
                </button>
                <a href="{{ route('doctors.index') }}" class="btn-outline-clinic">Cancel</a>
            </div>
        </form>
    </div>

@endsection