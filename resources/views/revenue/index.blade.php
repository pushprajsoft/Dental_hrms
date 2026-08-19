@extends('layouts.app')

@section('title', 'Revenue Analysis')
@section('page-title', 'Revenue & Transactions')
@section('page-subtitle', 'Monitor clinic financial position, filter data, and manage records')

@section('content')

<style>
    .revenue-toolbar {
        background: var(--clr-surface, #fff);
        border: 1px solid var(--clr-border, #e5e9f0);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .filter-form { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
    .filter-input { display: flex; flex-direction: column; }
    .filter-input label {
        font-size: 0.75rem; font-weight: 600; color: var(--clr-muted, #64748b);
        margin-bottom: 4px; text-transform: uppercase;
    }
    .filter-input input {
        padding: 10px 14px; border-radius: 10px;
        border: 1px solid var(--clr-border, #e5e9f0);
        background: var(--clr-bg, #f8fafc); font-size: 0.9rem; color: var(--clr-primary);
    }
    .btn-export {
        background: #22C55E; color: #fff; padding: 12px 20px; border-radius: 10px;
        text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;
        transition: background 0.2s;
    }
    .btn-export:hover { background: #15803D; }

    .summary-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px; margin-bottom: 24px;
    }
    .summary-card {
        background: var(--clr-surface, #fff); border: 1px solid var(--clr-border, #e5e9f0);
        border-radius: 16px; padding: 24px; display: flex; justify-content: space-between;
        align-items: center; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .summary-icon {
        width: 50px; height: 50px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fff;
    }
    .summary-text h3 { font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 700; margin: 0; color: var(--clr-primary); }
    .summary-text p { margin: 0; font-size: 0.85rem; color: var(--clr-muted); text-transform: uppercase; letter-spacing: 0.5px; }

    .chart-toggle-btns { display: flex; gap: 8px; background: #f1f5f9; padding: 4px; border-radius: 10px; }
    .toggle-btn {
        padding: 6px 14px; border-radius: 8px; border: none; background: transparent;
        cursor: pointer; font-weight: 600; font-size: 0.85rem; color: #64748b; transition: all 0.2s;
    }
    .toggle-btn.active { background: #3FBFAD; color: #fff; }

    .dash-panel { margin-bottom: 0; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
        text-align: left; font-size: 0.75rem; text-transform: uppercase;
        letter-spacing: .05em; color: var(--clr-muted, #64748b); padding: 16px;
        border-bottom: 2px solid var(--clr-border, #e5e9f0); background: var(--clr-bg, #f8fafc);
    }
    .data-table td { padding: 14px 16px; border-bottom: 1px solid var(--clr-border, #e5e9f0); font-size: 0.9rem; color: var(--clr-primary); }

    .chart-scroll-container {
        position: relative; height: 380px; width: 100%;
        overflow-x: auto; overflow-y: hidden;
        scrollbar-width: thin; scrollbar-color: #3FBFAD #f1f5f9;
    }
    .chart-scroll-container::-webkit-scrollbar { height: 10px; }
    .chart-scroll-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; margin: 0 20px; }
    .chart-scroll-container::-webkit-scrollbar-thumb { background: #3FBFAD; border-radius: 10px; }
    .chart-inner {
        position: relative; height: 350px; min-width: 100%;
        width: {{ max(100, count($chartLabels) * 80) }}px; padding: 0 20px; box-sizing: border-box;
    }

    /* Action Icons (Edit/Delete) */
    .action-icon-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 8px; margin-right: 4px;
        border: 1px solid var(--clr-border, #e5e9f0); color: var(--clr-muted, #64748b);
        text-decoration: none; cursor: pointer; background: #fff; transition: all 0.2s;
    }
    .action-icon-btn:hover { background: var(--clr-accent, #3FBFAD); color: #fff; border-color: var(--clr-accent); }
    .action-icon-btn.danger:hover { background: #EF4444; color: #fff; border-color: #EF4444; }
</style>

@if(session('success'))
    <div class="alert-clinic" style="background: #D1FAE5; color: #065F46; padding: 12px 20px; border-radius: 10px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<!-- Toolbar (Filter & Export) -->
<div class="revenue-toolbar">
    <form action="{{ route('revenue.index') }}" method="GET" class="filter-form">
        <div class="filter-input">
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ $fromDate }}">
        </div>
        <div class="filter-input">
            <label>To Date</label>
            <input type="date" name="to_date" value="{{ $toDate }}">
        </div>
        <button type="submit" class="btn-clinic" style="margin-top: 22px;">
            <i class="fa-solid fa-filter"></i> Apply Filter
        </button>
        <a href="{{ route('revenue.index') }}" class="btn-clinic" style="margin-top: 22px; background: transparent; color: var(--clr-muted); border: 1px solid var(--clr-border);">
            <i class="fa-solid fa-rotate-left"></i> Reset
        </a>
    </form>

    <div style="display: flex; gap: 12px;">
        <a href="{{ route('revenue.export', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" class="btn-export">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-text">
            <h3>₹{{ number_format($totalRevenue, 2) }}</h3>
            <p>Total Revenue</p>
        </div>
        <div class="summary-icon" style="background: linear-gradient(135deg, #22C55E, #15803D); box-shadow: 0 6px 12px rgba(34,197,94,0.3);">
            <i class="fa-solid fa-indian-rupee-sign"></i>
        </div>
    </div>
    <div class="summary-card">
        <div class="summary-text">
            <h3>{{ $totalVisits }}</h3>
            <p>Total Visits</p>
        </div>
        <div class="summary-icon" style="background: linear-gradient(135deg, #3FBFAD, #17847A); box-shadow: 0 6px 12px rgba(63,191,173,0.3);">
            <i class="fa-solid fa-hospital-user"></i>
        </div>
    </div>
    <div class="summary-card">
        <div class="summary-text">
            <h3>₹{{ number_format($avgRevenue, 2) }}</h3>
            <p>Avg Revenue / Visit</p>
        </div>
        <div class="summary-icon" style="background: linear-gradient(135deg, #7C5CFC, #4B2ED8); box-shadow: 0 6px 12px rgba(124,92,252,0.3);">
            <i class="fa-solid fa-chart-line"></i>
        </div>
    </div>
</div>

<!-- Dynamic Chart Panel -->
<div class="dash-panel" style="margin-bottom: 24px;">
    <div class="dash-panel-header">
        <h2><i class="fa-solid fa-chart-pie"></i> Revenue Visualization</h2>
        <div class="chart-toggle-btns">
            <button id="btnLine" class="toggle-btn active" onclick="toggleChart('line')"><i class="fa-solid fa-chart-line"></i> Line</button>
            <button id="btnBar" class="toggle-btn" onclick="toggleChart('bar')"><i class="fa-solid fa-chart-column"></i> Bar</button>
        </div>
    </div>
    <div class="chart-scroll-container">
        <div class="chart-inner">
            <canvas id="dynamicChart"></canvas>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="dash-panel" style="padding: 0; overflow: hidden;">
    <div class="dash-panel-header" style="padding: 24px;">
        <h2><i class="fa-solid fa-table"></i> Transaction Details</h2>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Visit Code</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th style="text-align: right;">Total</th>
                    <th style="text-align: right;">Paid</th>
                    <th>Status</th>
                    <th style="text-align: center;">Actions</th> <!-- ADDED ACTIONS COLUMN -->
                </tr>
            </thead>
            <tbody>
                @forelse($visits as $visit)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($visit->visit_date)->format('d M Y') }}</td>
                        <td><span style="font-family: monospace; background: #f1f5f9; padding: 4px 8px; border-radius: 6px;">{{ $visit->visit_code }}</span></td>
                        <td>{{ $visit->patient->full_name ?? 'N/A' }}</td>
                        <td>{{ $visit->doctor->full_name ?? 'N/A' }}</td>
                        <td style="text-align: right;">₹{{ number_format($visit->total_amount, 2) }}</td>
                        <td style="text-align: right; font-weight: 600; color: #15803D;">₹{{ number_format($visit->amount_paid, 2) }}</td>
                        <td>
                            <span class="role-badge {{ $visit->status == 'Paid' ? 'status-active' : 'role-super' }}" style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem;">
                                {{ $visit->status }}
                            </span>
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            <!-- EDIT BUTTON -->
                            <a href="{{ route('opd.edit', $visit->id) }}" class="action-icon-btn" title="Edit Transaction">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <!-- DELETE BUTTON -->
                            <form action="{{ route('opd.destroy', $visit->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-icon-btn danger" title="Delete Transaction">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--clr-muted);">
                            No revenue data found. <a href="{{ route('opd.create') }}" style="color: var(--clr-accent); font-weight: 600;">Add a new OPD Visit</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function loadChartScript(url, callback) {
        var script = document.createElement('script');
        script.src = url;
        script.onload = callback;
        script.onerror = function() {
            console.warn('Failed to load Chart.js from: ' + url);
            if (url.includes('jsdelivr')) {
                loadChartScript('https://unpkg.com/chart.js@4.4.4/dist/chart.umd.min.js', callback);
            } else if (url.includes('unpkg')) {
                loadChartScript('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js', callback);
            }
        };
        document.head.appendChild(script);
    }

    let revenueChart;
    let currentType = 'line';
    const chartLabels = {!! json_encode($chartLabels ?? []) !!};
    const chartData = {!! json_encode($chartData ?? []) !!};

    function renderChart(type) {
        const canvas = document.getElementById('dynamicChart');
        if (!canvas || typeof Chart === 'undefined') return;
        const ctx = canvas.getContext('2d');
        if (revenueChart) revenueChart.destroy();

        const gradient = ctx.createLinearGradient(0, 0, 0, 350);
        if (type === 'line') {
            gradient.addColorStop(0, 'rgba(34, 197, 94, 0.5)');
            gradient.addColorStop(1, 'rgba(34, 197, 94, 0.0)');
        } else {
            gradient.addColorStop(0, 'rgba(63, 191, 173, 0.8)');
            gradient.addColorStop(1, 'rgba(63, 191, 173, 0.2)');
        }

        revenueChart = new Chart(ctx, {
            type: type,
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: chartData,
                    backgroundColor: gradient,
                    borderColor: type === 'line' ? '#22C55E' : '#3FBFAD',
                    borderWidth: 3, fill: type === 'line', tension: 0.4,
                    pointBackgroundColor: '#fff', pointBorderColor: type === 'line' ? '#22C55E' : '#3FBFAD',
                    pointRadius: 5, pointHoverRadius: 7, borderRadius: type === 'bar' ? 8 : 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false, callbacks: { label: (c) => '₹ ' + Number(c.raw).toFixed(2) } }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (v) => '₹' + v }, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false }, ticks: { autoSkip: false, maxRotation: 0, minRotation: 0 } }
                }
            }
        });
    }

    window.toggleChart = function(type) {
        currentType = type;
        renderChart(type);
        document.getElementById('btnLine').classList.toggle('active', type === 'line');
        document.getElementById('btnBar').classList.toggle('active', type === 'bar');
    };

    function initRevenueChart() {
        if (typeof Chart === 'undefined') return;
        renderChart(currentType);
    }

    window.addEventListener('load', function() {
        if (typeof Chart === 'undefined') {
            loadChartScript('https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js', initRevenueChart);
        } else {
            initRevenueChart();
        }
    });
</script>
@endsection