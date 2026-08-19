<div class="sidebar-group-heading">
    <i class="fa-solid fa-tooth"></i> OPD Management
</div>

<a href="{{ route('opd.index') }}" class="sidebar-link {{ request()->routeIs('opd.index') ? 'active' : '' }}">
    <i class="fa-solid fa-gauge"></i> OPD Dashboard
</a>

<a href="{{ route('opd.create') }}" class="sidebar-link {{ request()->routeIs('opd.create') ? 'active' : '' }}">
    <i class="fa-solid fa-user-plus"></i> Add OPD Visit
</a>

<a href="{{ route('opd.index') }}" class="sidebar-link {{ request()->routeIs('opd.show') || request()->routeIs('opd.edit') ? 'active' : '' }}">
    <i class="fa-solid fa-list"></i> All Visits
</a>

<div class="sidebar-group-heading">
    <i class="fa-solid fa-gear"></i> System Settings
</div>

<a href="{{ route('settings.print_layout') }}" class="sidebar-link {{ request()->routeIs('settings.print_layout') ? 'active' : '' }}">
    <i class="fa-solid fa-file-word"></i> Print Layout Setup
</a>


<div class="sidebar-divider"></div>

<a href="{{ route('dashboard') }}" class="sidebar-link">
    <i class="fa-solid fa-arrow-left"></i> Back to Main Dashboard
</a>