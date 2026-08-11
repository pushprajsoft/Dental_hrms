@extends('layouts.app')

@section('title', 'Admin Management')
@section('page-title', 'Admin & User Management')
@section('page-subtitle', 'Manage Super Admins and Admin accounts')

@section('content')

<style>
    .role-badge {
        display:inline-flex; align-items:center; gap:6px;
        padding: 4px 10px; border-radius: 20px;
        font-size: 0.78rem; font-weight: 600;
    }
    .role-super { background:#FEF3C7; color:#92400E; }
    .role-admin { background:#DBEAFE; color:#1E40AF; }
    .status-active { background:#D1FAE5; color:#065F46; }
    .status-inactive { background:#FEE2E2; color:#B91C1C; }
    .users-table { width:100%; border-collapse:collapse; }
    .users-table th {
        text-align:left; font-size:0.78rem; text-transform:uppercase;
        letter-spacing:.04em; color: var(--clr-muted, #64748b);
        padding: 10px 14px; border-bottom: 1px solid var(--clr-border, #e5e9f0);
    }
    .users-table td {
        padding: 12px 14px; border-bottom: 1px solid var(--clr-border, #e5e9f0);
        font-size: 0.9rem;
    }
    .action-icon-btn {
        display:inline-flex; align-items:center; justify-content:center;
        width:32px; height:32px; border-radius:8px; margin-right:4px;
        border:1px solid var(--clr-border, #e5e9f0); color: var(--clr-muted, #64748b);
        text-decoration:none; cursor:pointer; background:#fff;
    }
    .action-icon-btn:hover { background: var(--clr-bg, #f6f9ff); }
    .action-icon-btn.danger:hover { background:#FEE2E2; color:#B91C1C; border-color:#FCA5A5; }
</style>

@if(session('success'))
    <div class="alert-clinic"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert-clinic" style="background:#FEE2E2; color:#B91C1C;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
    <a href="{{ route('admin.users.create') }}" class="btn-clinic">
        <i class="fa-solid fa-user-plus"></i> Add New User
    </a>
</div>

<div class="profile-card" style="padding:0; overflow-x:auto;">
    <table class="users-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username ?? '—' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="role-badge {{ $user->isSuperAdmin() ? 'role-super' : 'role-admin' }}">
                            <i class="fa-solid {{ $user->isSuperAdmin() ? 'fa-crown' : 'fa-user-shield' }}"></i>
                            {{ $user->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                        </span>
                    </td>
                    <td>
                        <span class="role-badge {{ $user->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                            {{ $user->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="action-icon-btn" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Remove {{ $user->name }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-icon-btn danger" title="Remove">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:24px; color:var(--clr-muted);">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection