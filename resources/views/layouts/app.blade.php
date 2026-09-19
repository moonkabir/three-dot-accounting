<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ThreeDot ERP') }} — {{ $title ?? 'Dashboard' }}</title>
    <meta name="description" content="ThreeDot Accounting ERP — Enterprise resource planning dashboard.">

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg-deep:       #080812;
            --bg-sidebar:    #0d0d1f;
            --bg-card:       rgba(255,255,255,0.04);
            --border:        rgba(255,255,255,0.07);
            --accent:        #6366f1;
            --accent-2:      #8b5cf6;
            --accent-glow:   rgba(99,102,241,0.3);
            --text-primary:  #f1f5f9;
            --text-muted:    #64748b;
            --text-sub:      #94a3b8;
            --success:       #10b981;
            --warning:       #f59e0b;
            --danger:        #ef4444;
            --info:          #3b82f6;
            --sidebar-w:     260px;
            --topbar-h:      64px;
        }
        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg-deep);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }

        /* ─── APP SHELL ─────────────────────────────────────── */
        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ─── SIDEBAR ─────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            overflow-y: auto;
            scrollbar-width: none;
        }
        .sidebar::-webkit-scrollbar { display: none; }

        .sidebar-logo {
            padding: 1.5rem 1.25rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 0 20px var(--accent-glow);
            flex-shrink: 0;
        }
        .sidebar-logo-text { font-size: 1rem; font-weight: 700; letter-spacing: -0.02em; }
        .sidebar-logo-text span { color: #818cf8; }
        .sidebar-logo-badge {
            font-size: 0.6rem; font-weight: 600; color: var(--accent-2);
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 4px; padding: 2px 5px;
            letter-spacing: 0.05em;
            margin-left: auto;
        }

        .sidebar-section {
            padding: 1rem 0.75rem 0.25rem;
        }
        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 0.5rem;
            margin-bottom: 0.35rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-sub);
            cursor: pointer;
            transition: background 0.15s, color 0.15s, transform 0.15s;
            position: relative;
            margin-bottom: 2px;
        }
        .nav-item:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-primary);
            transform: translateX(2px);
        }
        .nav-item.active {
            background: linear-gradient(90deg, rgba(99,102,241,0.18), rgba(99,102,241,0.06));
            color: #a5b4fc;
            border: 1px solid rgba(99,102,241,0.2);
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: linear-gradient(180deg, var(--accent), var(--accent-2));
            border-radius: 0 2px 2px 0;
        }
        .nav-icon { font-size: 1rem; width: 20px; text-align: center; flex-shrink: 0; }
        .nav-badge {
            margin-left: auto;
            font-size: 0.65rem; font-weight: 600;
            background: rgba(239,68,68,0.15);
            color: #f87171;
            border-radius: 8px;
            padding: 2px 7px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 0.75rem;
            border-top: 1px solid var(--border);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .sidebar-user:hover { background: rgba(255,255,255,0.05); }
        .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }
        .user-info { min-width: 0; }
        .user-name { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); truncate: true; }
        .user-role { font-size: 0.72rem; color: var(--text-muted); }
        .user-menu-btn { margin-left: auto; color: var(--text-muted); font-size: 0.8rem; }

        /* ─── MAIN AREA ─────────────────────────────────────── */
        .main-area {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ─── TOPBAR ─────────────────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            background: rgba(8,8,18,0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            gap: 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .topbar-breadcrumb {
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex; align-items: center; gap: 0.35rem;
        }

        .topbar-search {
            flex: 1;
            max-width: 360px;
            margin: 0 1rem;
            position: relative;
        }
        .topbar-search input {
            width: 100%;
            padding: 0.5rem 0.75rem 0.5rem 2.25rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 0.85rem;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }
        .topbar-search input::placeholder { color: var(--text-muted); }
        .topbar-search input:focus {
            border-color: rgba(99,102,241,0.5);
            background: rgba(99,102,241,0.05);
        }
        .search-icon {
            position: absolute; left: 0.75rem; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted); font-size: 0.85rem;
        }

        .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 0.75rem; }
        .topbar-btn {
            width: 38px; height: 38px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.15s, border-color 0.15s;
            position: relative;
            color: var(--text-sub);
        }
        .topbar-btn:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.15); }
        .notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid var(--bg-deep);
        }
        .topbar-user {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.35rem 0.75rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .topbar-user:hover { background: rgba(255,255,255,0.08); }
        .topbar-avatar {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700;
        }
        .topbar-username { font-size: 0.82rem; font-weight: 500; }

        /* ─── PAGE CONTENT ─────────────────────────────────── */
        .page-content {
            flex: 1;
            padding: 2rem;
            overflow-x: hidden;
        }

        /* ─── LOGOUT FORM ─────────────────────────────────── */
        .logout-form { display: inline; }
        .logout-btn {
            background: none; border: none; cursor: pointer;
            color: var(--text-sub); font-size: 0.875rem; font-family: 'Inter', sans-serif;
            font-weight: 500; padding: 0;
            transition: color 0.15s;
        }
        .logout-btn:hover { color: var(--danger); }
    </style>
</head>
<body>
<div class="app-shell">

    {{-- ── SIDEBAR ─────────────────────────────────────── --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">💠</div>
            <div class="sidebar-logo-text">Three<span>Dot</span></div>
            <span class="sidebar-logo-badge">ERP</span>
        </div>

        {{-- Core --}}
        <div class="sidebar-section">
            <div class="sidebar-section-label">Overview</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">🏠</span> Dashboard
            </a>
        </div>

        {{-- Finance --}}
        <div class="sidebar-section">
            <div class="sidebar-section-label">Finance</div>
            <a href="#" class="nav-item">
                <span class="nav-icon">📒</span> Accounting
            </a>
            <a href="#" class="nav-item">
                <span class="nav-icon">🧾</span> Invoices
                <span class="nav-badge">12</span>
            </a>
            <a href="#" class="nav-item">
                <span class="nav-icon">💳</span> Expenses
            </a>
            <a href="#" class="nav-item">
                <span class="nav-icon">🏦</span> Banking
            </a>
        </div>

        {{-- Operations --}}
        <div class="sidebar-section">
            <div class="sidebar-section-label">Operations</div>
            <a href="#" class="nav-item">
                <span class="nav-icon">🛒</span> Sales
            </a>
            <a href="#" class="nav-item">
                <span class="nav-icon">📦</span> Purchases
            </a>
            <a href="#" class="nav-item">
                <span class="nav-icon">🏭</span> Inventory
            </a>
        </div>

        {{-- HR --}}
        <div class="sidebar-section">
            <div class="sidebar-section-label">Human Resources</div>
            <a href="#" class="nav-item">
                <span class="nav-icon">👥</span> Employees
            </a>
            <a href="#" class="nav-item">
                <span class="nav-icon">💰</span> Payroll
            </a>
        </div>

        {{-- Reports --}}
        <div class="sidebar-section">
            <div class="sidebar-section-label">Insights</div>
            <a href="#" class="nav-item">
                <span class="nav-icon">📈</span> Reports
            </a>
            <a href="#" class="nav-item">
                <span class="nav-icon">⚙️</span> Settings
            </a>
        </div>

        {{-- User footer --}}
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <div class="sidebar-user" onclick="this.closest('form').submit()">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">Administrator</div>
                    </div>
                    <span class="user-menu-btn">↪</span>
                </div>
            </form>
        </div>
    </aside>

    {{-- ── MAIN AREA ─────────────────────────────────────── --}}
    <div class="main-area">
        {{-- Topbar --}}
        <header class="topbar">
            <div>
                <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
                <div class="topbar-breadcrumb">
                    <span>ThreeDot ERP</span>
                    <span>›</span>
                    <span style="color: #818cf8;">{{ $title ?? 'Dashboard' }}</span>
                </div>
            </div>

            <div class="topbar-search">
                <span class="search-icon">🔍</span>
                <input type="search" id="topbar-search-input" placeholder="Search modules, transactions…">
            </div>

            <div class="topbar-actions">
                <div class="topbar-btn" title="Notifications">
                    🔔
                    <span class="notif-dot"></span>
                </div>
                <div class="topbar-btn" title="Messages">✉️</div>
                <a href="{{ route('profile.edit') }}" class="topbar-user">
                    <div class="topbar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                    <span class="topbar-username">{{ Str::limit(Auth::user()->name, 12) }}</span>
                </a>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="page-content">
            {{ $slot }}
        </main>
    </div>

</div>
</body>
</html>
