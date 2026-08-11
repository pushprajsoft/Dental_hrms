<div class="sidebar-group-heading">
    <i class="fa-solid fa-grid-2"></i> Main Menu
</div>

<a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <i class="fa-solid fa-gauge ic-gear"></i> Dashboard
</a>

<a href="{{ route('patients.index') }}" class="sidebar-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
    <i class="fa-solid fa-users ic-users"></i> Patients
</a>

<a href="{{ route('doctors.index') }}" class="sidebar-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
    <i class="fa-solid fa-user-doctor ic-doctors"></i> Doctors
</a>


<div class="sidebar-group-heading">
    <i class="fa-solid fa-gear"></i> System Settings
</div>

<!-- NEW BACKUP LINK -->
<a href="{{ route('backup.index') }}" class="sidebar-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
    <i class="fa-solid fa-database ic-bill"></i> Backup & Restore
</a>

<a href="{{ route('whatsapp.settings') }}" class="sidebar-link {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}">
    <i class="fa-brands fa-whatsapp ic-gear"></i> WhatsApp Settings
</a>

@if(auth()->check() && auth()->user()->role === 'super_admin')
    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user-shield ic-gear"></i> User Management
    </a>
@endif