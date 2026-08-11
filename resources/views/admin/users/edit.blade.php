@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'User Management')
@section('page-subtitle', 'Edit user details, roles, and reset passwords')

@section('content')

<style>
    /* Reusing the same styles from the create page for consistency */
    .form-card {
        background: var(--clr-surface, #fff);
        border: 1px solid var(--clr-border, #e5e9f0);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 24px;
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
    .danger-zone {
        border: 1px dashed #FCA5A5;
        background: #FEF2F2;
        border-radius: 16px;
        padding: 24px;
    }
    .danger-zone h3 {
        color: #B91C1C;
        font-family: 'Outfit', sans-serif;
        margin: 0 0 10px 0;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div style="display: flex; gap: 24px; align-items: flex-start; flex-wrap: wrap;">
    
    {{-- Main Edit Form --}}
    <div class="form-card" style="flex: 2; min-width: 300px;">
        <div class="dash-panel-header" style="margin-bottom: 24px;">
            <h2><i class="fa-solid fa-user-pen"></i> Edit User: {{ $user->name }}</h2>
            <a href="{{ route('admin.users.index') }}" class="view-all-link"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" value="{{ old('username', $user->username) }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-input" required>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Account Status</label>
                    <select id="status" name="status" class="form-input" required>
                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn-clinic" style="background: transparent; color: var(--clr-muted); border: 1px solid var(--clr-border);">Cancel</a>
                <button type="submit" class="btn-clinic" style="background: var(--clr-accent, #3FBFAD);">
                    <i class="fa-solid fa-check"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Sidebar: Reset Password --}}
    <div style="flex: 1; min-width: 300px;">
        <div class="form-card danger-zone">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> Reset Password</h3>
            <p style="font-size: 0.88rem; color: #7F1D1D; margin-bottom: 20px;">
                Generate a new temporary password for this user. They will be required to change it upon next login.
            </p>

            <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="new_password">New Temporary Password</label>
                    <input type="password" id="new_password" name="password" class="form-input" placeholder="Enter new password" required minlength="8">
                </div>

                <!-- CONFIRMATION FIELD -->
                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Re-type the password" required>
                </div>

                <button type="submit" class="btn-clinic" style="background: #EF4444; width: 100%; justify-content: center;">
                    <i class="fa-solid fa-key"></i> Reset Password
                </button>
            </form>
        </div>
    </div>

</div>

@endsection