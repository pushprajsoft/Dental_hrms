<div class="sidebar-group-heading">
    <i class="fa-solid fa-bed"></i> IPD Management
</div>

<a href="{{ route('ipd.dashboard') }}" class="sidebar-link {{ request()->routeIs('ipd.dashboard') ? 'active' : '' }}">
    <i class="fa-solid fa-gauge"></i> IPD Dashboard
</a>

<a href="{{ route('ipd.create') }}" class="sidebar-link {{ request()->routeIs('ipd.create') ? 'active' : '' }}">
    <i class="fa-solid fa-user-plus"></i> New IPD Admission
</a>

<a href="{{ route('ipd.dashboard') }}#admitted-list" class="sidebar-link">
    <i class="fa-solid fa-list-check"></i> Admitted Patients
</a>

<div class="sidebar-group-heading">
    <i class="fa-solid fa-gear"></i> IPD Settings
</div>

<!-- UPDATED BED MANAGEMENT LINK -->
<a href="{{ route('ipd.beds.index') }}" class="sidebar-link {{ request()->routeIs('ipd.beds.*') ? 'active' : '' }}">
    <i class="fa-solid fa-door-open"></i> Bed Management
</a>

<span class="sidebar-link-disabled" title="Coming soon">
    <i class="fa-solid fa-file-invoice-dollar"></i> Discharge Billing
</span>

<div class="sidebar-divider"></div>

<a href="{{ route('dashboard') }}" class="sidebar-link">
    <i class="fa-solid fa-arrow-left"></i> Back to Main Dashboard
</a>