<div class="sidebar-group-heading">
    <i class="fa-solid fa-flask-vial"></i> Pathology Lab
</div>

<a href="{{ route('pathology.dashboard') }}" class="sidebar-link {{ request()->routeIs('pathology.dashboard') ? 'active' : '' }}">
    <i class="fa-solid fa-gauge"></i> Lab Dashboard
</a>

<a href="{{ route('pathology.dashboard') }}#new-test-form" class="sidebar-link">
    <i class="fa-solid fa-plus-circle"></i> New Test Request
</a>

<a href="{{ route('pathology.dashboard') }}#pending-tests" class="sidebar-link">
    <i class="fa-solid fa-hourglass-half"></i> Pending Tests
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