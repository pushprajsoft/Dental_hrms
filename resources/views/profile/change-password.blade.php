@extends('layouts.app')

@section('title', 'Change Password')
@section('page-title', 'Change Password')
@section('page-subtitle', 'Update your login credentials')

@section('content')

    <div class="panel" style="max-width: 480px;">

        @if($errors->any())
            <div class="alert-clinic" style="background: var(--clr-warn-soft); color: var(--clr-warn);">
                <i class="fa-solid fa-triangle-exclamation"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <label class="form-label-clinic">Current Password</label>
            <input type="password" name="current_password" class="form-control-clinic"
                   required style="margin-bottom:18px;">

            <label class="form-label-clinic">New Password</label>
            <input type="password" name="new_password" class="form-control-clinic"
                   required style="margin-bottom:18px;">

            <label class="form-label-clinic">Confirm New Password</label>
            <input type="password" name="new_password_confirmation" class="form-control-clinic"
                   required style="margin-bottom:24px;">

            <button type="submit" class="btn-clinic">
                <i class="fa-solid fa-floppy-disk"></i> Update Password
            </button>
        </form>
    </div>

@endsection