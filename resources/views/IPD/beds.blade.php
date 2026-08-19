@extends('layouts.app')

@section('title', 'Bed Management')
@section('page-title', 'Bed Management Dashboard')
@section('page-subtitle', 'Manage hospital beds, rooms, and availability')

@section('content')

<style>
    :root {
        --ipd-primary: #4154f1;
        --ipd-dark: #012970;
        --ipd-light: #f6f9ff;
        --ipd-border: #e2e8f0;
    }
    
    /* Filter Panel */
    .ipd-filter-panel {
        background: #fff; border-radius: 14px; padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;
        border: 1px solid #f1f5f9;
    }
    .ipd-filter-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px; align-items: end;
    }
    .ipd-input {
        width: 100%; padding: 10px 12px; border-radius: 8px;
        border: 1px solid var(--ipd-border); background: #fff;
        font-size: 0.9rem; color: #1e293b;
    }
    .ipd-input:focus { border-color: var(--ipd-primary); outline: none; box-shadow: 0 0 0 3px rgba(65, 84, 241, 0.1); }
    
    .bed-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .bed-stat-card {
        background: #fff; border-radius: 12px; padding: 20px;
        display: flex; align-items: center; gap: 15px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border-left: 5px solid var(--ipd-primary);
    }
    .bed-icon-box {
        width: 50px; height: 50px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: #fff;
    }
    
    .bed-panel {
        background: #fff; border-radius: 14px; padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;
    }
    .bed-panel-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;
    }
    .bed-panel-header h2 { margin: 0; font-size: 1.1rem; color: var(--ipd-dark); font-weight: 700; }
    
    .bed-table { width: 100%; border-collapse: collapse; }
    .bed-table th { text-align: left; font-size: 0.75rem; color: #64748b; padding: 12px; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; }
    .bed-table td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #444; }
    
    .btn-bed {
        background: var(--ipd-primary); color: #fff; padding: 10px 20px;
        border-radius: 8px; border: none; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
    }
    .btn-bed:hover { background: var(--ipd-dark); }
    
    .badge-available { background: #D1FAE5; color: #065F46; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-occupied { background: #FEE2E2; color: #B91C1C; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-maintenance { background: #FEF3C7; color: #B45309; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
</style>

<!-- Removed duplicate session('success') block -->

<!-- Bed Stats Cards -->
<div class="bed-stats-grid">
    <div class="bed-stat-card">
        <div class="bed-icon-box" style="background: var(--ipd-primary);">
            <i class="fa-solid fa-bed"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--ipd-dark);">{{ $stats['total'] }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Total Beds</div>
        </div>
    </div>
    
    <div class="bed-stat-card" style="border-left-color: #22C55E;">
        <div class="bed-icon-box" style="background: #22C55E;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--ipd-dark);">{{ $stats['available'] }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Available</div>
        </div>
    </div>

    <div class="bed-stat-card" style="border-left-color: #EF4444;">
        <div class="bed-icon-box" style="background: #EF4444;">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--ipd-dark);">{{ $stats['occupied'] }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Occupied</div>
        </div>
    </div>

    <div class="bed-stat-card" style="border-left-color: #F59E0B;">
        <div class="bed-icon-box" style="background: #F59E0B;">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--ipd-dark);">{{ $stats['maintenance'] }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Maintenance</div>
        </div>
    </div>
</div>

<!-- Filter Panel -->
<div class="ipd-filter-panel">
    <form action="{{ route('ipd.beds.index') }}" method="GET">
        <div class="ipd-filter-grid">
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#64748b;">Search Bed/Room</label>
                <input type="text" name="search" class="ipd-input" placeholder="e.g. B-101" value="{{ $search }}">
            </div>
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#64748b;">Bed Type</label>
                <select name="bed_type" class="ipd-input">
                    <option value="">All Types</option>
                    @foreach(['General', 'Private', 'ICU', 'Ward'] as $t)
                        <option value="{{ $t }}" @selected($bedType == $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#64748b;">Status</label>
                <select name="status" class="ipd-input">
                    <option value="">All Status</option>
                    @foreach(['Available', 'Occupied', 'Maintenance'] as $s)
                        <option value="{{ $s }}" @selected($status == $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn-bed" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-filter"></i> Apply Filter
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Bed List -->
<div class="bed-panel">
    <div class="bed-panel-header">
        <h2><i class="fa-solid fa-door-open" style="color: var(--ipd-primary);"></i> Bed Master List</h2>
        <a href="{{ route('ipd.beds.create') }}" class="btn-bed">
            <i class="fa-solid fa-plus"></i> Add Bed Master
        </a>
    </div>
    <div style="overflow-x: auto;">
    <table class="bed-table">
        <thead>
            <tr>
                <th>Sr. No.</th> <!-- ADDED SERIAL NO -->
                <th>Bed Number</th>
                <th>Room Number</th>
                <th>Bed Type</th>
                <th>Charge / Day</th>
                <th>Status</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($beds as $bed)
            <tr>
                <td>{{ $loop->iteration }}</td> <!-- AUTO INCREMENT SERIAL NO -->
                <td><strong>{{ $bed->bed_number }}</strong></td>
                <td>{{ $bed->room_number ?? '-' }}</td>
                <td>{{ $bed->bed_type }}</td>
                <td>₹{{ number_format($bed->charge_per_day, 2) }}</td>
                <td>
                    @if($bed->status == 'Available')
                        <span class="badge-available">{{ $bed->status }}</span>
                    @elseif($bed->status == 'Occupied')
                        <span class="badge-occupied">{{ $bed->status }}</span>
                    @else
                        <span class="badge-maintenance">{{ $bed->status }}</span>
                    @endif
                </td>
                <td style="text-align: center; white-space: nowrap;">
                    <a href="{{ route('ipd.beds.edit', $bed->id) }}" class="btn-bed" style="padding: 6px 12px; font-size: 0.8rem; background: #012970;">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form action="{{ route('ipd.beds.destroy', $bed->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this bed?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-bed" style="padding: 6px 12px; font-size: 0.8rem; background: #EF4444;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;"> <!-- Update colspan to 7 -->
                    <i class="fa-solid fa-bed" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                    No beds found matching your filters.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@endsection