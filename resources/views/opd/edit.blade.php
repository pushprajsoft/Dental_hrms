@extends('layouts.app')

@section('title', 'Edit OPD Visit')
@section('page-title', 'Edit OPD Visit')
@section('page-subtitle', $visit->visit_code . ' · ' . ($visit->patient->full_name ?? ''))

@section('content')

    <div class="panel">
        <form action="{{ route('opd.update', $visit) }}" method="POST">
            @csrf
            @method('PUT')
            @include('opd._form')

            <div style="margin-top:26px; display:flex; gap:12px;">
                <button type="submit" class="btn-clinic">
                    <i class="fa-solid fa-floppy-disk"></i> Update Visit
                </button>
                <a href="{{ route('opd.index') }}" class="btn-outline-clinic">Cancel</a>
            </div>
        </form>
    </div>

@endsection