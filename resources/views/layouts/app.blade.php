<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'StockSense AI')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-base:       #0c0c0f;
            --bg-sidebar:    #101013;
            --bg-card:       #141418;
            --bg-card-2:     #18181d;
            --bg-input:      #1c1c22;
            --bg-hover:      rgba(255,255,255,0.04);
            --border:        rgba(255,255,255,0.07);
            --border-light:  rgba(255,255,255,0.05);
            --accent:        #34d399;
            --accent-glow:   rgba(52,211,153,0.18);
            --accent-dim:    rgba(52,211,153,0.12);
            --red:           #f87171;
            --red-dim:       rgba(248,113,113,0.12);
            --yellow:        #fbbf24;
            --yellow-dim:    rgba(251,191,36,0.12);
            --blue:          #60a5fa;
            --blue-dim:      rgba(96,165,250,0.12);
            --indigo:        #818cf8;
            --text-white:    #f1f5f9;
            --text-muted:    #7c8596;
            --text-dim:      #3d4451;
            --sidebar-w:     240px;
            --header-h:      52px;
        }

        html, body { height: 100%; overflow: hidden; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-base);
            color: var(--text-white);
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: var(--header-h);
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 1rem 0 0;
            gap: 0.75rem;
            flex-shrink: 0;
            z-index: 100;
        }
        .topbar-brand {
            width: var(--sidebar-w);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0 1rem;
            border-right: 1px solid var(--border);
            height: 100%;
            text-decoration: none;
        }
        .brand-logo-icon {
            width: 30px; height: 30px;
            background: rgba(52,211,153,0.12);
            border: 1px solid rgba(52,211,153,0.3);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--accent);
        }
        .brand-name { font-size: 0.82rem; font-weight: 700; color: var(--text-white); line-height: 1.1; }
        .brand-sub  { font-size: 0.55rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: var(--accent); opacity: 0.7; }

        .topbar-search { position: relative; flex: 0 0 200px; }
        .topbar-search input {
            width: 100%; background: var(--bg-input); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text-white); font-size: 0.75rem;
            padding: 0.4rem 0.75rem 0.4rem 2rem; outline: none;
        }
        .topbar-search input:focus { border-color: rgba(52,211,153,0.4); }
        .topbar-search svg { position: absolute; left: 0.6rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); }

        .ticker-wrap { flex: 1; overflow: hidden; mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent); }
        .ticker-inner { display: flex; gap: 0.5rem; animation: ticker-scroll 25s linear infinite; width: max-content; }
        .ticker-inner:hover { animation-play-state: paused; }
        @keyframes ticker-scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .ticker-item {
            display: flex; align-items: center; gap: 0.4rem;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 6px; padding: 0.25rem 0.6rem; white-space: nowrap;
            text-decoration: none;
        }
        .ticker-code  { font-size: 0.7rem; font-weight: 700; font-family: 'JetBrains Mono', monospace; color: var(--text-white); }
        .ticker-price { font-size: 0.68rem; font-family: 'JetBrains Mono', monospace; color: var(--text-muted); }
        .ticker-pct   { font-size: 0.62rem; font-weight: 600; font-family: 'JetBrains Mono', monospace; }
        .ticker-pct.up   { color: var(--accent); }
        .ticker-pct.down { color: var(--red); }
        .ticker-pct.flat { color: var(--yellow); }
        .ticker-badge { font-size: 0.5rem; font-weight: 700; letter-spacing: 0.06em; border-radius: 4px; padding: 0.1rem 0.35rem; }
        .ticker-badge.bullish { background: var(--accent-dim); color: var(--accent); }
        .ticker-badge.bearish { background: var(--red-dim); color: var(--red); }
        .ticker-badge.neutral { background: var(--yellow-dim); color: var(--yellow); }

        .role-switch { display: flex; background: var(--bg-input); border: 1px solid var(--border); border-radius: 6px; overflow: hidden; }
        .role-btn { font-size: 0.65rem; font-weight: 600; padding: 0.3rem 0.65rem; border: none; background: transparent; color: var(--text-muted); }
        .role-btn.active { background: var(--accent); color: #0c0c0f; }

        .topbar-user { display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem; }
        .avatar {
            width: 30px; height: 30px; background: linear-gradient(135deg, var(--accent), #059669);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; color: #0c0c0f;
        }
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-size: 0.7rem; font-weight: 600; color: var(--text-white); font-family: 'JetBrains Mono', monospace; }
        .user-role { font-size: 0.58rem; color: var(--text-muted); }

        .layout-body { flex: 1; display: flex; overflow: hidden; }
        .sidebar {
            width: var(--sidebar-w); background: var(--bg-sidebar);
            border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0;
        }
        .nav-section-label {
            font-size: 0.58rem; font-weight: 700; letter-spacing: 0.12em;
            text-transform: uppercase; color: var(--text-dim); padding: 1rem 1rem 0.4rem;
        }
        .nav-item {
            display: flex; align-items: center; gap: 0.6rem; padding: 0.55rem 1rem;
            font-size: 0.78rem; font-weight: 500; color: var(--text-muted);
            text-decoration: none; transition: all 0.15s; position: relative;
        }
        .nav-item:hover { color: var(--text-white); background: var(--bg-hover); }
        .nav-item.active { color: var(--accent); background: rgba(52,211,153,0.08); }
        .nav-item.active::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0;
            width: 2.5px; background: var(--accent);
        }
        .sidebar-sep { height: 1px; background: var(--border); margin: 0.6rem 1rem; }
        .sidebar-footer { margin-top: auto; padding: 0.75rem 1rem; border-top: 1px solid var(--border); }
        .btn-logout {
            display: flex; align-items: center; gap: 0.5rem; width: 100%;
            padding: 0.45rem 0.75rem; background: rgba(248,113,113,0.08);
            border: 1px solid rgba(248,113,113,0.18); border-radius: 7px;
            color: var(--red); font-size: 0.75rem; font-weight: 600; cursor: pointer;
        }

        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .breadcrumb {
            display: flex; align-items: center; gap: 0.35rem;
            padding: 0.6rem 1.5rem 0; font-size: 0.68rem; color: var(--text-dim);
            font-family: 'JetBrains Mono', monospace;
        }
        .breadcrumb-item.current { color: var(--text-muted); }
        .content-scroll { flex: 1; overflow-y: auto; padding: 0.85rem 1.5rem 1.5rem; display: flex; flex-direction: column; gap: 1rem; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .page-title-row { display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.25rem; }
        .page-title { font-size: 1.35rem; font-weight: 800; color: var(--text-white); letter-spacing: -0.025em; }
        .page-desc  { font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; max-width: 650px; }

        .card-panel {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.1rem 1.25rem;
        }
        .card-panel-title {
            font-size: 0.85rem; font-weight: 700; color: var(--text-white);
            margin-bottom: 0.85rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;
        }

        .btn-accent {
            display: inline-flex; align-items: center; gap: 0.45rem;
            background: var(--accent); color: #0c0c0f; font-size: 0.75rem; font-weight: 700;
            padding: 0.45rem 0.9rem; border: none; border-radius: 8px; cursor: pointer;
            text-decoration: none;
        }
        .btn-outline {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: var(--bg-input); color: var(--text-white); font-size: 0.72rem; font-weight: 600;
            padding: 0.4rem 0.75rem; border: 1px solid var(--border); border-radius: 7px;
            cursor: pointer; text-decoration: none;
        }
        .btn-outline:hover { border-color: rgba(52,211,153,0.4); color: var(--accent); }
        .btn-danger {
            background: rgba(248,113,113,0.12); color: var(--red);
            border: 1px solid rgba(248,113,113,0.28); font-size: 0.68rem; font-weight: 600;
            padding: 0.3rem 0.6rem; border-radius: 6px; cursor: pointer;
        }

        .data-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
        .data-table th {
            text-align: left; font-size: 0.63rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.08em; color: var(--text-dim); padding: 0.55rem 0.75rem;
            border-bottom: 1px solid var(--border);
        }
        .data-table td {
            padding: 0.65rem 0.75rem; border-bottom: 1px solid var(--border-light);
            color: var(--text-white); vertical-align: middle;
        }
        .data-table tr:hover td { background: var(--bg-hover); }

        .sentiment-badge {
            font-size: 0.62rem; font-weight: 700; padding: 0.18rem 0.5rem;
            border-radius: 5px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block;
        }
        .sentiment-badge.positive { background: var(--accent-dim); color: var(--accent); }
        .sentiment-badge.negative { background: var(--red-dim); color: var(--red); }
        .sentiment-badge.neutral  { background: var(--yellow-dim); color: var(--yellow); }

        .alert-banner {
            padding: 0.7rem 1rem; border-radius: 9px; font-size: 0.78rem;
            background: var(--accent-dim); border: 1px solid rgba(52,211,153,0.3); color: var(--accent);
        }
        .alert-banner.error {
            background: var(--red-dim); border-color: rgba(248,113,113,0.3); color: var(--red);
        }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; }
        .field label { display: block; font-size: 0.68rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.3rem; }
        .field input, .field select {
            width: 100%; background: var(--bg-input); border: 1px solid var(--border);
            border-radius: 7px; color: var(--text-white); font-size: 0.76rem; padding: 0.45rem 0.7rem; outline: none;
        }
        .field input:focus, .field select:focus { border-color: var(--accent); }

        @media (max-width: 900px) {
            .sidebar { display: none; }
            .topbar-brand { width: auto; }
        }
    </style>
    @stack('head')
</head>
<body>

<div class="topbar">
    <a href="{{ route('dashboard') }}" class="topbar-brand">
        <div class="brand-logo-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
        </div>
        <div class="brand-text">
            <div class="brand-name">StockSense AI</div>
            <div class="brand-sub">Fintech Monitor</div>
        </div>
    </a>

    <div class="topbar-search">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <form action="{{ route('emiten.detail') }}" method="GET">
            <input type="text" name="ticker" value="{{ $searchTicker ?? '' }}" placeholder="Cari emiten (e.g. BBCA)..." autocomplete="off">
        </form>
    </div>

    <div class="ticker-wrap">
        <div class="ticker-inner">
            @for($lp = 0; $lp < 2; $lp++)
                @foreach($saham_ticker ?? [] as $item)
                    @php
                        $pct = $item['change_pct'] ?? 0;
                        $pctClass = $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat');
                        $pctSign  = $pct > 0 ? '+' : '';
                        $mood     = $item['mood'] ?? 'neutral';
                    @endphp
                    <a href="{{ route('emiten.detail', ['ticker' => $item['ticker_code']]) }}" class="ticker-item">
                        <span class="ticker-code">{{ $item['ticker_code'] }}</span>
                        @if($item['close_price'] !== null)
                            <span class="ticker-price">{{ number_format($item['close_price'], 0, ',', ',') }}</span>
                            <span class="ticker-pct {{ $pctClass }}">{{ $pctSign }}{{ $pct }}%</span>
                        @else
                            <span class="ticker-pct flat">—</span>
                        @endif
                        <span class="ticker-badge {{ $mood }}">{{ strtoupper($mood) }}</span>
                    </a>
                @endforeach
            @endfor
        </div>
    </div>

    <div class="role-switch">
        @auth
            <button class="role-btn {{ Auth::user()->role === 'admin' ? 'active' : '' }}">Admin</button>
            <button class="role-btn {{ Auth::user()->role === 'visitor' ? 'active' : '' }}">Visitor</button>
        @else
            <button class="role-btn">Admin</button>
            <button class="role-btn active">Visitor</button>
        @endauth
    </div>

    <div class="topbar-user">
        <div class="avatar">{{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'V' }}</div>
        <div class="user-info">
            <span class="user-name">{{ Auth::check() ? strtoupper(str_replace(' ', '_', Auth::user()->name)) : 'VISITOR_01' }}</span>
            <span class="user-role">{{ Auth::check() ? ucfirst(Auth::user()->role) . ' Access' : 'Guest Access' }}</span>
        </div>
    </div>
</div>

<div class="layout-body">
    <aside class="sidebar">
        <div class="nav-section-label">Navigation & Modules</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            <span>Dashboard Overview</span>
        </a>
        <a href="{{ route('emiten.detail') }}" class="nav-item {{ request()->routeIs('emiten.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"></path><path d="M4 6h2"></path><path d="M4 10h2"></path><path d="M4 14h2"></path><path d="M4 18h2"></path></svg>
            <span>Emiten & Berita Terkait</span>
        </a>

        @if(Auth::check() && Auth::user()->role === 'admin')
            <div class="sidebar-sep"></div>
            <div class="nav-section-label">Administrator</div>
            <a href="{{ route('master.emiten') }}" class="nav-item {{ request()->routeIs('master.*') ? 'active' : '' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Master Data Emiten</span>
            </a>
            <a href="{{ route('api.logs') }}" class="nav-item {{ request()->routeIs('api.logs*') ? 'active' : '' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                <span>Log API & Cron Sync</span>
            </a>
        @endif

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Logout ({{ Auth::user()->name }})</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="breadcrumb">
            <span>StockSense_AI</span><span>›</span>
            <span class="breadcrumb-item current">@yield('breadcrumb')</span>
        </div>
        <div class="content-scroll">
            @if(session('success'))
                <div class="alert-banner">{{ session('success') }}</div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert-banner error">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>

