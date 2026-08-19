<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') · DentaCare HRMS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        .top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 32px 0;
            border-bottom: 1px solid var(--clr-border, #e5e9f0);
            background: var(--clr-bg, #f6f9ff);
        }

        .module-tabs { display: flex; gap: 6px; }
        .module-tab {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 18px;
            font-family: 'Outfit', sans-serif; font-weight: 600; font-size: 0.92rem;
            color: var(--clr-muted, #64748b);
            border-radius: 10px 10px 0 0;
            border: 1px solid transparent; border-bottom: none;
            text-decoration: none; transition: all .15s ease; position: relative;
        }
        .module-tab:hover { color: var(--clr-primary, #123C3A); background: rgba(63, 191, 173, 0.08); }
        .module-tab.active {
            color: var(--clr-primary, #123C3A);
            background: var(--clr-surface, #fff);
            border-color: var(--clr-border, #e5e9f0);
            box-shadow: 0 -2px 0 var(--clr-accent, #3FBFAD) inset;
        }
        .module-tab.active i { color: var(--clr-accent, #3FBFAD); }
        .module-tab-badge {
            position: absolute; top: 2px; right: 2px;
            background: #EF4444; color: #fff; font-size: 0.65rem; font-weight: 700;
            min-width: 18px; height: 18px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; padding: 0 4px; line-height: 1;
        }

        .top-row-meta { display: flex; align-items: center; gap: 18px; padding-bottom: 10px; }

        .sidebar-group-heading {
            display: flex; align-items: center; gap: 8px;
            margin: 18px 0 6px; padding: 0 4px;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
            color: var(--clr-accent, #3FBFAD);
        }

        .sidebar-divider { height: 1px; background: var(--clr-border, #e5e9f0); margin: 16px 4px; }

        /* ================================================
           SCROLL BEHAVIOR & SIDEBAR TOGGLE
           ================================================ */
        .app-shell {
            display: flex;
            height: 100vh;
            overflow: hidden;
            position: relative; /* Needed for absolute positioning of toggle button */
        }
        
        .sidebar {
            flex-shrink: 0;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out; /* Smooth slide animation */
            width: 260px; /* Default width */
        }
        
        .main-area {
            flex: 1;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease-in-out;
        }

        #stickyHeader {
            position: sticky; top: 0; z-index: 50;
            background: var(--clr-bg, #f6f9ff);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease;
        }
        #stickyHeader.header-hidden { transform: translateY(-100%); }

        /* Floating Toggle Button */
        .sidebar-toggle-btn {
            position: absolute;
            top: 30px;
            left: 250px; /* Sits on the right edge of the sidebar */
            z-index: 1000;
            background: var(--clr-accent, #3FBFAD);
            color: #fff;
            border: 3px solid var(--clr-bg, #f6f9ff);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            transition: all 0.3s ease-in-out;
        }
        .sidebar-toggle-btn:hover {
            background: var(--clr-primary, #123C3A);
            transform: scale(1.1);
        }
        .sidebar-toggle-btn i {
            transition: transform 0.3s ease;
        }

        /* When sidebar is hidden */
        .app-shell.sidebar-collapsed .sidebar {
            transform: translateX(-100%); /* Slides sidebar completely out */
            margin-right: -260px; /* Prevents empty space gap */
        }
        .app-shell.sidebar-collapsed .sidebar-toggle-btn {
            left: 10px; /* Moves button to the far left edge */
        }
        .app-shell.sidebar-collapsed .sidebar-toggle-btn i {
            transform: rotate(180deg); /* Flips arrow to point right */
        }
    </style>
</head>
<body>

<div class="app-shell" id="appShell">

    <!-- Floating Toggle Button -->
    <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <aside class="sidebar" style="display:flex; flex-direction:column; justify-content:space-between; height:100vh;">
        <div class="brand">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
                <path d="M12 2C9 2 7 4 6.5 6.5C6 9 6.7 10.5 6.9 13C7.1 15.5 7.5 20 9 20.5C10.3 20.9 10.7 17.5 11 15.5C11.2 14.2 11.4 13.5 12 13.5C12.6 13.5 12.8 14.2 13 15.5C13.3 17.5 13.7 20.9 15 20.5C16.5 20 16.9 15.5 17.1 13C17.3 10.5 18 9 17.5 6.5C17 4 15 2 12 2Z" fill="#3FBFAD"/>
            </svg>
            <div>
                <div class="brand-title">Shubh</div>
                <div class="brand-sub">HMS</div>
            </div>
        </div>

        <nav>
            @if(request()->routeIs('opd.*'))
                @include('layouts.partials.sidebar-opd')
            @elseif(request()->routeIs('ipd.*'))
                @include('layouts.partials.sidebar-ipd')
            @elseif(request()->routeIs('pathology.*'))
                @include('layouts.partials.sidebar-pathology')
            @else
                @include('layouts.partials.sidebar-main')
            @endif
        </nav>

        <div class="sidebar-footer">
            <a href="{{ \App\Models\WhatsappSetting::current()->supportLink() }}"
               target="_blank" class="btn-clinic" style="background:#25D366; width:100%; justify-content:center;">
                <i class="fa-brands fa-whatsapp"></i> Quick Support
            </a>
        </div>
    </aside>

    <div class="main-area">

        <div id="stickyHeader">
            <div class="top-row">
                @php
                    $todayApptCount = \App\Models\Appointment::whereDate('appointment_date', now()->toDateString())->count();
                @endphp

                <div class="module-tabs">
                    <a href="{{ route('dashboard') }}" class="module-tab {{ (request()->routeIs('opd.*') || request()->routeIs('appointments.*') || request()->routeIs('ipd.*')) ? '' : 'active' }}">
                        <i class="fa-solid fa-gauge"></i> Main Dashboard
                    </a>
                    <a href="{{ route('opd.index') }}" class="module-tab {{ request()->routeIs('opd.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-hospital-user"></i> OPD
                    </a>
                    <!-- NEW IPD TAB -->
                    <a href="{{ route('ipd.dashboard') }}" class="module-tab {{ request()->routeIs('ipd.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-bed"></i> IPD
                    </a>

                      <!-- NEW PATHOLOGY TAB -->
                    <a href="{{ route('pathology.dashboard') }}" class="module-tab {{ request()->routeIs('pathology.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-flask-vial"></i> Pathology
                    </a>

                    <a href="{{ route('appointments.index') }}" class="module-tab {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-check"></i> Appointments
                        @if($todayApptCount > 0)
                            <span class="module-tab-badge">{{ $todayApptCount }}</span>
                        @endif
                    </a>
                </div>

                <div class="top-row-meta">
                    <div style="text-align:right;">
                        <div id="live-clock" style="font-family:'Outfit', sans-serif; font-weight:600; font-size:1.05rem; color: var(--clr-primary);"></div>
                        <div id="live-date" style="font-size:0.78rem; color: var(--clr-muted);"></div>
                    </div>

                    <div class="profile-dropdown">
                        <button type="button" class="profile-trigger" onclick="toggleProfileMenu()">
                            <span class="avatar-chip" style="background: var(--clr-accent); margin-right:0;">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <span class="profile-name">{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down" style="font-size:0.7rem; color: var(--clr-muted);"></i>
                        </button>

                        <div class="profile-menu" id="profileMenu">
                            <div class="profile-menu-header">
                                <div class="profile-menu-name">{{ auth()->user()->name }}</div>
                                <div class="profile-menu-email">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="profile-menu-item">
                                <i class="fa-solid fa-key"></i> Change Password
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="profile-menu-item profile-menu-logout">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="topbar">
                <div>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <div class="topbar-sub">@yield('page-subtitle', 'Manage your clinic patient records')</div>
                </div>
            </div>

            <svg class="smile-wave" viewBox="0 0 1440 20" preserveAspectRatio="none">
                <path d="M0,0 C360,20 1080,20 1440,0 L1440,0 L0,0 Z" fill="#3FBFAD" fill-opacity="0.35"></path>
            </svg>
        </div>

        <div class="content-wrap">

            @if(session('success'))
                <div class="alert-clinic">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

@yield('scripts')

<script>
    function toggleProfileMenu() {
        document.getElementById('profileMenu').classList.toggle('open');
    }

    document.addEventListener('click', function (event) {
        const dropdown = document.querySelector('.profile-dropdown');
        const menu = document.getElementById('profileMenu');
        if (dropdown && menu && !dropdown.contains(event.target)) {
            menu.classList.remove('open');
        }
    });

    function updateClock() {
        const now = new Date();
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('live-clock').textContent = now.toLocaleTimeString('en-US', timeOptions);
        document.getElementById('live-date').textContent = now.toLocaleDateString('en-US', dateOptions);
    }

    updateClock();
    setInterval(updateClock, 1000);

    // Fixed sidebar + auto-hide header on scroll
    (function () {
        const mainArea = document.querySelector('.main-area');
        const stickyHeader = document.getElementById('stickyHeader');
        let lastScroll = 0;
        let ticking = false;
        const THRESHOLD = 10;

        function onScroll() {
            const current = mainArea.scrollTop;
            const diff = current - lastScroll;

            if (Math.abs(diff) > THRESHOLD) {
                if (diff > 0 && current > 60) {
                    stickyHeader.classList.add('header-hidden');
                } else {
                    stickyHeader.classList.remove('header-hidden');
                }
                lastScroll = current;
            }
            ticking = false;
        }

        mainArea.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(onScroll);
                ticking = true;
            }
        }, { passive: true });
    })();

    // SIDEBAR TOGGLE LOGIC
    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    const appShell = document.getElementById('appShell');

    if (sidebarToggleBtn && appShell) {
        sidebarToggleBtn.addEventListener('click', function() {
            appShell.classList.toggle('sidebar-collapsed');
            
            // Optional: Save state to local storage so it remembers if user likes it hidden
            if (appShell.classList.contains('sidebar-collapsed')) {
                localStorage.setItem('sidebarState', 'collapsed');
            } else {
                localStorage.setItem('sidebarState', 'expanded');
            }
        });

        // Check local storage on page load
        if (localStorage.getItem('sidebarState') === 'collapsed') {
            appShell.classList.add('sidebar-collapsed');
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>