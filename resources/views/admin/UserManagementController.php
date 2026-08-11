@extends('layouts.app')

@section('title', 'Create User')
@section('page-title', 'User Management')
@section('page-subtitle', 'Add a new admin or staff account to the system')

@section('content')

<style>
    /* Reusing the same styles from the edit page for consistency */
    .form-card {
        background: var(--clr-surface, #fff);
        border: 1px solid var(--clr-border, #e5e9f0);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        max-width: 800px;
        margin: 0 auto;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }
    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--clr-primary, #123C3A);
        margin-bottom: 8px;
    }
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid var(--clr-border, #e5e9f0);
        background: var(--clr-bg, #f8fafc);
        font-size: 0.95rem;
        color: var(--clr-primary);
        transition: all 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--clr-accent, #3FBFAD);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(63, 191, 173, 0.1);
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 20px;
        border-top: 1px solid var(--clr-border, #e5e9f0);
        padding-top: 20px;
    }
</style>

<div class="form-card">
    <div class="dash-panel-header" style="margin-bottom: 24px;">
        <h2><i class="fa-solid fa-user-plus"></i> Create New User</h2>
        <a href="{{ route('admin.users.index') }}" class="view-all-link"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" placeholder="e.g. Dr. John Doe" required>
                @error('name') <small style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</small> @enderror
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-input" value="{{ old('username') }}" placeholder="e.g. jdoe" required>
                @error('username') <small style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</small> @enderror
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="e.g. user@dental.com" required>
                @error('email') <small style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</small> @enderror
            </div>
            
            <div class="form-group">
                <label for="password">Temporary Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Min 8 characters" required>
                @error('password') <small style="color: #EF4444; font-size: 0.8rem;">{{ $message }}</small> @enderror
            </div>
            
            <!-- CONFIRM PASSWORD FIELD IS NOW CORRECTLY PLACED INSIDE THE GRID -->
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Re-type the password" required>
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="form-input" required>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Account Status</label>
                <select id="status" name="status" class="form-input" required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.users.index') }}" class="btn-clinic" style="background: transparent; color: var(--clr-muted); border: 1px solid var(--clr-border);">Cancel</a>
            <button type="submit" class="btn-clinic" style="background: var(--clr-accent, #3FBFAD);">
                <i class="fa-solid fa-check"></i> Create User
            </button>
        </div>
    </form>
</div>

@endsection