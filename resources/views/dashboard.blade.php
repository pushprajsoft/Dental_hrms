@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Main Dashboard')
@section('page-subtitle', 'Welcome back! Here is what is happening at your clinic today.')

@section('content')

    <!-- DASHBOARD STYLES -->
    <style>
        .dash-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .dash-grid-layout {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1100px) {
            .dash-grid-layout { grid-template-columns: 1fr; }
        }

        .dash-stat-card {
            background: var(--clr-surface, #fff);
            border: 1px solid var(--clr-border, #e5e9f0);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .dash-stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 5px;
            background: var(--card-color);
        }
        .dash-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px var(--card-shadow);
        }
        .dash-stat-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            background: var(--card-color);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #fff;
            box-shadow: 0 6px 12px var(--card-shadow);
            flex-shrink: 0;
        }
        .dash-stat-info h3 {
            margin: 0; font-family: 'Outfit', sans-serif;
            font-size: 32px; font-weight: 700;
            color: var(--clr-primary, #123C3A); line-height: 1.2;
        }
        .dash-stat-info p {
            margin: 0; color: var(--clr-muted, #64748b);
            font-size: 14px; font-weight: 500;
        }

        .dash-panel {
            background: var(--clr-surface, #fff);
            border: 1px solid var(--clr-border, #e5e9f0);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            margin-bottom: 24px;
        }
        .dash-panel-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--clr-border, #e5e9f0);
            padding-bottom: 15px;
        }
        .dash-panel-header h2 {
            margin: 0; font-family: 'Outfit', sans-serif;
            font-size: 18px; font-weight: 600;
            color: var(--clr-primary, #123C3A);
            display: flex; align-items: center; gap: 10px;
        }
        .dash-panel-header h2 i { color: var(--clr-accent, #3FBFAD); }

        .quick-actions-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;
        }
        .qa-btn {
            display: flex; flex-direction: column; align-items: center;
            text-decoration: none; padding: 18px 10px;
            border-radius: 12px; border: 1px solid var(--clr-border, #e5e9f0);
            background: var(--clr-bg, #f8fafc); transition: all 0.3s ease;
        }
        .qa-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.06);
            border-color: transparent;
        }
        .qa-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: var(--qa-color);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px; margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .qa-btn:hover .qa-icon { transform: scale(1.1); }
        .qa-label { font-size: 13px; font-weight: 600; color: var(--clr-primary, #123C3A); }

        .patient-list-item {
            display: flex; align-items: center; gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid var(--clr-border, #e5e9f0);
        }
        .patient-list-item:last-child { border-bottom: none; }
        .patient-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: var(--clr-accent, #3FBFAD); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 16px; font-family: 'Outfit', sans-serif;
        }
        .patient-details { flex: 1; }
        .patient-details strong { display: block; font-size: 14px; color: var(--clr-primary, #123C3A); }
        .patient-details span { font-size: 12px; color: var(--clr-muted, #64748b); }
        .view-all-link {
            font-size: 13px; font-weight: 600;
            color: var(--clr-accent, #3FBFAD); text-decoration: none;
        }
        .view-all-link:hover { text-decoration: underline; }

        .today-apt-row {
            padding: 14px 10px !important; border-radius: 10px; transition: background 0.2s;
        }
        .today-apt-row:hover { background: var(--clr-bg, #f6f9ff) !important; }
        
        .revenue-stat {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: #15803D; /* Dark Green for money */
            font-size: 1.1rem;
        }
    </style>

    <!-- STATS CARDS -->
    <div class="dash-stats-grid">
        <div class="dash-stat-card" style="--card-color: #3FBFAD; --card-shadow: rgba(63,191,173,0.2);">
            <div class="dash-stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="dash-stat-info">
                <h3>{{ $stats['totalPatients'] }}</h3>
                <p>Total Patients</p>
            </div>
        </div>

        <div class="dash-stat-card" style="--card-color: #7C5CFC; --card-shadow: rgba(124,92,252,0.2);">
            <div class="dash-stat-icon"><i class="fa-solid fa-user-doctor"></i></div>
            <div class="dash-stat-info">
                <h3>{{ $stats['totalDoctors'] }}</h3>
                <p>Total Doctors</p>
            </div>
        </div>

        <div class="dash-stat-card" style="--card-color: #FF8A5C; --card-shadow: rgba(228,87,46,0.2);">
            <div class="dash-stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="dash-stat-info">
                <h3>{{ $stats['todaysAppointments'] }}</h3>
                <p>Today's Appointments</p>
            </div>
        </div>
    </div>

    <!-- MAIN DASHBOARD GRID -->
    <div class="dash-grid-layout">

        <!-- LEFT COLUMN -->
        <div>
            <!-- Today's Schedule -->
            <div class="dash-panel">
                <div class="dash-panel-header">
                    <h2><i class="fa-solid fa-calendar-day"></i> Today's Schedule</h2>
                    <a href="{{ route('appointments.create') }}" class="view-all-link"><i class="fa-solid fa-plus"></i> New Appointment</a>
                </div>
                @include('appointments._today_widget')
            </div>

            <!-- Recent Patients Dynamic List -->
            <div class="dash-panel">
                <div class="dash-panel-header">
                    <h2><i class="fa-solid fa-user-clock"></i> Recent Patients</h2>
                    <a href="{{ route('patients.index') }}" class="view-all-link">View All</a>
                </div>
                <div>
                    @forelse($recentPatients as $patient)
                        <div class="patient-list-item">
                            <div class="patient-avatar">
                                {{ strtoupper(substr($patient->full_name, 0, 1)) }}
                            </div>
                            <div class="patient-details">
                                <strong>{{ $patient->full_name }}</strong>
                                <span>{{ $patient->patient_code }} · {{ $patient->gender }}</span>
                            </div>
                            <a href="{{ route('patients.show', $patient->id) }}" class="btn-clinic" style="padding: 6px 12px; font-size: 12px;">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 20px; color: var(--clr-muted);">
                            <i class="fa-solid fa-folder-open" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            No patients registered yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div>
            <!-- Quick Actions -->
            <div class="dash-panel">
                <div class="dash-panel-header">
                    <h2><i class="fa-solid fa-bolt"></i> Quick Actions</h2>
                </div>
                <div class="quick-actions-grid">
                    <a href="{{ route('patients.create') }}" class="qa-btn" style="--qa-color: #3FBFAD;">
                        <div class="qa-icon"><i class="fa-solid fa-user-plus"></i></div>
                        <div class="qa-label">Add Patient</div>
                    </a>
                    <a href="{{ route('doctors.create') }}" class="qa-btn" style="--qa-color: #7C5CFC;">
                        <div class="qa-icon"><i class="fa-solid fa-user-doctor"></i></div>
                        <div class="qa-label">Add Doctor</div>
                    </a>
                    <a href="{{ route('appointments.create') }}" class="qa-btn" style="--qa-color: #FF8A5C;">
                        <div class="qa-icon"><i class="fa-solid fa-calendar-plus"></i></div>
                        <div class="qa-label">Book Appt</div>
                    </a>
                    <a href="{{ route('opd.index') }}" class="qa-btn" style="--qa-color: #22C55E;">
                        <div class="qa-icon"><i class="fa-solid fa-hospital-user"></i></div>
                        <div class="qa-label">OPD Visit</div>
                    </a>
                </div>
            </div>

            <!-- Revenue (Bar Chart) - Renamed from "Last 7 Days" -->
            <div class="dash-panel">
                <div class="dash-panel-header">
                    <h2><i class="fa-solid fa-coins"></i> Revenue</h2>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <span class="revenue-stat">₹{{ number_format(array_sum($dailyRevData), 2) }}</span>
                        <a href="{{ route('revenue.index') }}" class="view-all-link">Full Report <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div style="position: relative; height: 220px;">
                    <canvas id="dailyRevenueChart"></canvas>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
<!-- Robust Chart.js Loader (Tries multiple sources) -->
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

    function initDashboardCharts() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js could not be loaded from any source.');
            return;
        }

        // 1. Daily Revenue (Bar Chart)
        const dailyCtx = document.getElementById('dailyRevenueChart');
        if (dailyCtx) {
            const ctx2 = dailyCtx.getContext('2d');
            const gradient2 = ctx2.createLinearGradient(0, 0, 0, 220);
            gradient2.addColorStop(0, 'rgba(63, 191, 173, 0.8)');
            gradient2.addColorStop(1, 'rgba(63, 191, 173, 0.2)');

            new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dailyRevLabels) !!},
                    datasets: [{
                        label: 'Daily Collection',
                        data: {!! json_encode($dailyRevData) !!},
                        backgroundColor: gradient2,
                        borderColor: '#3FBFAD',
                        borderWidth: 2,
                        borderRadius: 6,
                        maxBarThickness: 35
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: { callbacks: { label: (c) => '₹ ' + Number(c.raw).toFixed(2) } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { color: '#64748b', callback: (v) => '₹' + v }, grid: { color: '#f1f5f9' } },
                        x: { ticks: { color: '#64748b' }, grid: { display: false } }
                    }
                }
            });
        }
    }

    // Start loading process
    window.addEventListener('load', function() {
        if (typeof Chart === 'undefined') {
            loadChartScript('https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js', initDashboardCharts);
        } else {
            initDashboardCharts();
        }
    });
</script>
@endsection