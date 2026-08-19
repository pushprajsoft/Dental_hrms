@extends('layouts.app')

@section('title', 'Book Appointment')
@section('page-title', 'Book Appointment')
@section('page-subtitle', 'Schedule a new patient appointment')

@section('content')
<div class="panel">
    <form action="{{ route('appointments.store') }}" method="POST">
        @csrf
        @include('appointments._form')

        <div style="margin-top:26px; display:flex; gap:12px;">
            <button type="submit" class="btn-clinic"><i class="fa-solid fa-calendar-plus"></i> Book Appointment</button>
            <a href="{{ route('appointments.index') }}" class="btn-outline-clinic">Cancel</a>
        </div>
    </form>
</div>
@endsection