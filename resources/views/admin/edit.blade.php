@extends('layouts.app')

@section('title', 'Admin Management')
@section('page-title', 'Admin & User Management')
@section('page-subtitle', 'Manage Super Admins and Admin accounts')

@section('content')

<style>
    /* Modern Table & UI Styling */
    .users-table { width: 100%; border-collapse: collapse; }
    .users-table th {
        text-align: left; 
        font-size: 0.75rem; 
        text-transform: uppercase;
        letter-spacing: .05em; 
        color: var(--clr-muted, #64748b);
        padding: 16px; 
        border-bottom: 2px solid var(--clr-border, #e5e9f0);
        background: var(--clr-bg, #f8fafc);
    }
    .users-table td {
        padding: 18px 16px; 
        border-bottom: 1px solid var(--clr-border, #e5e9f0);
        font-size: 0.92rem;
        color: var(--clr-primary);
        vertical-align: middle;
    }
    .users-table tr:last-child td { border-bottom: none; }
    .users-table tbody tr {
        transition: background 0.2s ease;
    }
    .users-table tbody tr:hover {
        background: rgba(63, 191, 173, 0.04); /* Subtle teal hover */
    }

    /* User Cell (Avatar + Info) */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .user-avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--clr-accent), var(--clr-primary));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-family: 'Outfit', sans-serif;
        font-size: 1rem;
    }

    /* Badges */
    .role-badge, .status-badge {
        display: inline-flex; 
        align-items: center; 
        gap: 6px;
        padding: 6px 12px; 
        border-radius: 20px;
        font-size: 0.75rem; 
        font-weight: 600;
    }
    .role-super { background: #FEF3C7; color: #92400E; }
    .role-admin { background: #DBEAFE; color: #1E40AF; }
    .status-active { background: #D1FAE5; color: #065F46; }
    .status-inactive { background: #FEE2E2; color: #B91C1C; }

    /* Action Icons */
    .action-icon-btn {
        display: inline-flex; 
        align-items: center; 
        justify-content: center;
        width: 36px; 
        height: 36px; 
        border-radius: 10px; 
        margin-right: 6px;
        border: 1px solid var(--clr-border, #e5e9f0); 
        color: var(--clr-muted, #64748b);
        text-decoration: none; 
        transition: all 0.2s ease;
        background: #fff;
    }
    .action-icon-btn:hover { 
        background: var(--clr-accent, #3FBFAD); 
        color: #fff; 
        border-color: var(--clr-accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(63, 191, 173, 0.2);
    }
    .action-icon-btn.danger:hover { 
        background: #EF4444; 
        color: #fff; 
        border-color: #EF4444;
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.2);
    }
</style>

<div class="dash-panel" style="padding: 0; overflow: hidden;">
    
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 24px; border-bottom: 1px solid var(--clr-border);">
        <div>
            <h2 style="margin: 0; font-family: 'Outfit'; font-size: 1.2rem; color: var(--clr-primary);">
                <i class="fa-solid fa-users-gear" style="color: var(--clr-accent);"></i> System Users
            </h2>
            <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: var(--clr-muted);">View, edit, and manage clinic staff accounts</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-clinic">
            <i class="fa-solid fa-user-plus"></i> Add New User
        </a>
    </div>

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table class="users-table">
            <thead>
                <tr>
                    <th>User Details</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    <div style="font-size: 0.75rem; color: var(--clr-muted);">ID: #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span style="font-family: monospace; background: #f1f5f9; padding: 4px 8px; border-radius: 6px;">{{ $user->username }}</span></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'super_admin')
                                <span class="role-badge role-super"><i class="fa-solid fa-crown"></i> Super Admin</span>
                            @else
                                <span class="role-badge role-admin"><i class="fa-solid fa-user-shield"></i> Admin</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="status-badge status-active"><i class="fa-solid fa-circle-check"></i> Active</span>
                            @else
                                <span class="status-badge status-inactive"><i class="fa-solid fa-circle-xmark"></i> Inactive</span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="action-icon-btn" title="Edit User">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-icon-btn danger" title="Delete User" style="border: none;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--clr-muted);">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection