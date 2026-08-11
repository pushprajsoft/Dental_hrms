@extends('layouts.app')

@section('title', 'Add Doctor')
@section('page-title', 'Add New Doctor')
@section('page-subtitle', 'Register a new doctor record')

@section('content')

    <div class="panel">
        <form action="{{ route('doctors.store') }}" method="POST">
            @csrf
            @include('doctors._form')

            <div style="margin-top:26px; display:flex; gap:12px;">
                <button type="submit" class="btn-clinic">
                    <i class="fa-solid fa-floppy-disk"></i> Save Doctor
                </button>
                <a href="{{ route('doctors.index') }}" class="btn-outline-clinic">Cancel</a>
            </div>
        </form>
    </div>

@endsection