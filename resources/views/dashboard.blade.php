<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="StockSense AI - Dashboard Analisis Sentimen Saham Indonesia">
    <title>Dashboard - StockSense AI</title>
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

        /* ===========================
           HEADER (TOP BAR)
        =========================== */
        .topbar {
            height: var(--header-h);
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 1rem 0 0;
            gap: 0.75rem;
            flex-shrink: 0;
            position: relative;
            z-index: 100;
        }

        /* Logo area in topbar */
        .topbar-brand {
            width: var(--sidebar-w);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0 1rem;
            border-right: 1px solid var(--border);
            height: 100%;
        }

        .brand-logo-icon {
            width: 30px;
            height: 30px;
            background: rgba(52,211,153,0.12);
            border: 1px solid rgba(52,211,153,0.3);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-logo-icon svg { color: var(--accent); }

        .brand-text { flex: 1; min-width: 0; }

        .brand-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-white);
            letter-spacing: -0.01em;
            line-height: 1.1;
        }

        .brand-sub {
            font-size: 0.55rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent);
            opacity: 0.7;
        }

        /* Search */
        .topbar-search {
            position: relative;
            flex: 0 0 200px;
        }

        .topbar-search input {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-white);
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            padding: 0.4rem 0.75rem 0.4rem 2rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .topbar-search input::placeholder { color: var(--text-muted); }
        .topbar-search input:focus { border-color: rgba(52,211,153,0.4); }

        .topbar-search svg {
            position: absolute;
            left: 0.6rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        /* Ticker Scroll */
        .ticker-wrap {
            flex: 1;
            overflow: hidden;
            mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
        }

        .ticker-inner {
            display: flex;
            gap: 0.5rem;
            animation: ticker-scroll 25s linear infinite;
            width: max-content;
        }

        .ticker-inner:hover { animation-play-state: paused; }

        @keyframes ticker-scroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .ticker-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 0.25rem 0.6rem;
            white-space: nowrap;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .ticker-item:hover { border-color: rgba(255,255,255,0.2); }

        .ticker-code {
            font-size: 0.7rem;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-white);
        }

        .ticker-price {
            font-size: 0.68rem;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-muted);
        }

        .ticker-pct {
            font-size: 0.62rem;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
        }

        .ticker-pct.up   { color: var(--accent); }
        .ticker-pct.down { color: var(--red); }
        .ticker-pct.flat { color: var(--yellow); }

        .ticker-badge {
            font-size: 0.5rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            border-radius: 4px;
            padding: 0.1rem 0.35rem;
        }

        .ticker-badge.bullish  { background: var(--accent-dim); color: var(--accent); }
        .ticker-badge.bearish  { background: var(--red-dim);    color: var(--red); }
        .ticker-badge.neutral  { background: var(--yellow-dim); color: var(--yellow); }

        /* Role Switcher */
        .role-switch {
            display: flex;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
        }

        .role-btn {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.3rem 0.65rem;
            border: none;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.03em;
        }

        .role-btn.active {
            background: var(--accent);
            color: #0c0c0f;
        }

        /* Notification */
        .topbar-notif {
            position: relative;
            width: 32px;
            height: 32px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color 0.2s;
            flex-shrink: 0;
        }

        .topbar-notif:hover { border-color: rgba(255,255,255,0.2); }

        .notif-dot {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 6px;
            height: 6px;
            background: var(--red);
            border-radius: 50%;
            border: 1.5px solid var(--bg-sidebar);
        }

        /* User Avatar */
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .topbar-user:hover { background: var(--bg-hover); }

        .avatar {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, var(--accent), #059669);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: #0c0c0f;
            flex-shrink: 0;
        }

        .user-info { display: flex; flex-direction: column; }

        .user-name {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-white);
            line-height: 1;
            font-family: 'JetBrains Mono', monospace;
        }

        .user-role {
            font-size: 0.58rem;
            color: var(--text-muted);
            line-height: 1.3;
        }

        /* ===========================
           LAYOUT BODY
        =========================== */
        .layout-body {
            flex: 1;
            display: flex;
            overflow: hidden;
        }

        /* ===========================
           SIDEBAR
        =========================== */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

        .nav-section-label {
            font-size: 0.58rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-dim);
            padding: 1rem 1rem 0.4rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.5rem 1rem;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.15s;
            border-radius: 0;
            position: relative;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .nav-item:hover {
            background: var(--bg-hover);
            color: var(--text-white);
        }

        .nav-item.active {
            background: rgba(52,211,153,0.1);
            color: var(--accent);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--accent);
            border-radius: 0 2px 2px 0;
        }

        .nav-item svg { flex-shrink: 0; opacity: 0.7; }
        .nav-item.active svg { opacity: 1; }

        .nav-item span.label { flex: 1; }

        .nav-badge {
            font-size: 0.52rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            background: var(--bg-input);
            border: 1px solid var(--border);
            color: var(--text-dim);
            border-radius: 4px;
            padding: 0.1rem 0.35rem;
            font-family: 'JetBrains Mono', monospace;
        }

        .nav-item.active .nav-badge {
            background: rgba(52,211,153,0.15);
            border-color: rgba(52,211,153,0.25);
            color: var(--accent);
        }

        .sidebar-sep {
            height: 1px;
            background: var(--border);
            margin: 0.5rem 1rem;
        }

        /* Sidebar logout */
        .sidebar-footer {
            margin-top: auto;
            padding: 0.75rem;
            border-top: 1px solid var(--border);
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.5rem 0.75rem;
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            border-color: var(--red);
            color: var(--red);
            background: var(--red-dim);
        }

        /* ===========================
           MAIN CONTENT
        =========================== */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Breadcrumb */
        .breadcrumb {
            padding: 0.5rem 1.25rem;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-shrink: 0;
        }

        .breadcrumb-item {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-dim);
            font-family: 'JetBrains Mono', monospace;
        }

        .breadcrumb-sep { color: var(--text-dim); font-size: 0.6rem; }

        .breadcrumb-item.current { color: var(--text-muted); }

        .breadcrumb-fr {
            font-size: 0.55rem;
            padding: 0.1rem 0.35rem;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-dim);
        }

        /* Content scroll area */
        .content-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
        }

        .content-scroll::-webkit-scrollbar { width: 5px; }
        .content-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        /* Page Header */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .page-header-left { flex: 1; }

        .page-title-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.35rem;
            flex-wrap: wrap;
        }

        .page-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-white);
            letter-spacing: -0.03em;
            line-height: 1.2;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
        }

        .status-badge.active {
            background: rgba(52,211,153,0.12);
            border: 1px solid rgba(52,211,153,0.3);
            color: var(--accent);
        }

        .status-badge .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            animation: pulse-anim 1.5s ease-in-out infinite;
        }

        @keyframes pulse-anim {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.85); }
        }

        .page-desc {
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.5;
            max-width: 520px;
        }

        .page-header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .schedule-info {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            color: var(--text-muted);
            background: var(--bg-card);
            border: 1px solid var(--border);
            padding: 0.45rem 0.75rem;
            border-radius: 8px;
        }

        .btn-force-run {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            background: var(--text-white);
            color: var(--bg-base);
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-force-run:hover {
            background: #fff;
            box-shadow: 0 4px 16px rgba(241,245,249,0.15);
            transform: translateY(-1px);
        }

        /* Stream Endpoints Bar */
        .endpoints-bar {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            font-size: 0.68rem;
        }

        .endpoint-item {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--text-muted);
        }

        .endpoint-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .endpoint-dot.green { background: var(--accent); box-shadow: 0 0 6px var(--accent); }
        .endpoint-dot.yellow { background: var(--yellow); }
        .endpoint-dot.blue  { background: var(--blue); }

        .endpoint-name { font-weight: 600; color: var(--text-white); }
        .endpoint-pct  { font-family: 'JetBrains Mono', monospace; color: var(--accent); }

        .endpoint-sep {
            width: 1px;
            height: 14px;
            background: var(--border);
        }

        .endpoint-ssl {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: var(--accent);
            background: rgba(52,211,153,0.08);
            border: 1px solid rgba(52,211,153,0.2);
            padding: 0.2rem 0.5rem;
            border-radius: 5px;
        }

        /* ===========================
           METRIC CARDS
        =========================== */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .metric-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.9rem 1rem;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .metric-card:hover { border-color: rgba(255,255,255,0.14); }

        .metric-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.6rem;
        }

        .metric-label {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .metric-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .metric-icon.green  { background: var(--accent-dim); color: var(--accent); }
        .metric-icon.red    { background: var(--red-dim);    color: var(--red); }
        .metric-icon.yellow { background: var(--yellow-dim); color: var(--yellow); }
        .metric-icon.blue   { background: var(--blue-dim);   color: var(--blue); }
        .metric-icon.indigo { background: rgba(129,140,248,0.12); color: var(--indigo); }

        .metric-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-white);
            letter-spacing: -0.03em;
            line-height: 1;
            margin-bottom: 0.2rem;
        }

        .metric-value.healthy { color: var(--accent); }
        .metric-value.sm      { font-size: 1.1rem; }

        .metric-sub {
            font-size: 0.65rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .metric-sub .positive { color: var(--accent); font-weight: 600; }
        .metric-sub .negative { color: var(--red); font-weight: 600; }

        .metric-bar {
            height: 3px;
            background: var(--border);
            border-radius: 2px;
            overflow: hidden;
        }

        .metric-bar-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.6s ease;
        }

        .metric-bar-fill.green  { background: var(--accent); }
        .metric-bar-fill.red    { background: var(--red); }
        .metric-bar-fill.yellow { background: var(--yellow); }
        .metric-bar-fill.blue   { background: var(--blue); }

        .metric-next {
            font-size: 0.62rem;
            color: var(--accent);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* ===========================
           BOTTOM SECTION
        =========================== */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 0.75rem;
        }

        /* Sub-Process Card */
        .subproc-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .subproc-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card-2);
        }

        .subproc-tag {
            font-size: 0.55rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            background: var(--blue-dim);
            color: var(--blue);
            border: 1px solid rgba(96,165,250,0.2);
            border-radius: 5px;
            padding: 0.15rem 0.45rem;
        }

        .daemon-id {
            font-size: 0.62rem;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-dim);
        }

        .subproc-body { padding: 1rem; }

        .subproc-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-white);
            letter-spacing: -0.02em;
            margin-bottom: 0.3rem;
        }

        .subproc-desc {
            font-size: 0.73rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 0.9rem;
        }

        .pipeline-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
            margin-bottom: 0.9rem;
        }

        .pipeline-item {
            background: var(--bg-card-2);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 0.75rem;
        }

        .pipeline-item-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.4rem;
        }

        .pipeline-item-icon {
            width: 26px;
            height: 26px;
            background: var(--accent-dim);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pipeline-item-icon svg { color: var(--accent); }

        .pipeline-item-name {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-white);
        }

        .pipeline-item-src {
            font-size: 0.65rem;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
            line-height: 1.4;
        }

        .pipeline-status {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.62rem;
            font-weight: 600;
        }

        .pipeline-status.ok     { color: var(--accent); }
        .pipeline-status.error  { color: var(--red); }

        /* Logs table */
        .log-section {
            border-top: 1px solid var(--border);
            padding: 0.75rem 1rem;
        }

        .log-toolbar {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }

        .log-tabs {
            display: flex;
            gap: 0.25rem;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 0.2rem;
        }

        .log-tab {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 5px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.15s;
        }

        .log-tab.active {
            background: var(--bg-card);
            color: var(--text-white);
        }

        .log-tab .count {
            font-size: 0.58rem;
            font-weight: 700;
            margin-left: 0.25rem;
            background: var(--border);
            border-radius: 3px;
            padding: 0.05rem 0.3rem;
        }

        .log-search {
            flex: 1;
            position: relative;
            min-width: 160px;
        }

        .log-search input {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 7px;
            color: var(--text-white);
            font-family: 'Inter', sans-serif;
            font-size: 0.7rem;
            padding: 0.35rem 0.7rem 0.35rem 1.8rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .log-search input::placeholder { color: var(--text-dim); }
        .log-search input:focus { border-color: rgba(52,211,153,0.4); }
        .log-search svg { position: absolute; left: 0.55rem; top: 50%; transform: translateY(-50%); color: var(--text-dim); }

        .btn-icon {
            width: 28px;
            height: 28px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-icon:hover { border-color: rgba(255,255,255,0.2); color: var(--text-white); }

        .btn-export {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--text-muted);
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 0.3rem 0.65rem;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-export:hover { color: var(--text-white); border-color: rgba(255,255,255,0.2); }

        /* Log table */
        .log-table { width: 100%; border-collapse: collapse; }

        .log-table th {
            font-size: 0.58rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-dim);
            text-align: left;
            padding: 0.4rem 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .log-table td {
            font-size: 0.7rem;
            color: var(--text-muted);
            padding: 0.45rem 0.5rem;
            border-bottom: 1px solid var(--border-light);
            font-family: 'JetBrains Mono', monospace;
        }

        .log-table tr:last-child td { border-bottom: none; }

        .log-table tr:hover td { background: var(--bg-hover); }

        .log-status-ok {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.6rem;
            font-weight: 700;
            color: var(--accent);
            background: var(--accent-dim);
            border-radius: 4px;
            padding: 0.1rem 0.4rem;
        }

        .log-status-fail {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.6rem;
            font-weight: 700;
            color: var(--red);
            background: var(--red-dim);
            border-radius: 4px;
            padding: 0.1rem 0.4rem;
        }

        /* ===========================
           JADWAL SIDEBAR CARD
        =========================== */
        .jadwal-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .jadwal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card-2);
        }

        .jadwal-title {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .auto-sync-badge {
            font-size: 0.55rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            background: var(--accent-dim);
            color: var(--accent);
            border: 1px solid rgba(52,211,153,0.25);
            border-radius: 5px;
            padding: 0.1rem 0.4rem;
        }

        .jadwal-body { padding: 0.75rem; display: flex; flex-direction: column; gap: 0.5rem; }

        .jadwal-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: var(--bg-card-2);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 0.65rem 0.75rem;
        }

        .jadwal-status {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .jadwal-status.done    { background: var(--accent-dim); color: var(--accent); }
        .jadwal-status.pending { background: var(--bg-input); border: 2px solid var(--border); color: var(--text-dim); }
        .jadwal-status.running {
            background: var(--yellow-dim);
            color: var(--yellow);
            animation: spin-slow 2s linear infinite;
        }

        @keyframes spin-slow { to { transform: rotate(360deg); } }

        .jadwal-info { flex: 1; }

        .jadwal-batch {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-white);
            letter-spacing: 0.03em;
        }

        .jadwal-time {
            font-size: 0.62rem;
            font-family: 'JetBrains Mono', monospace;
            color: var(--accent);
        }

        .jadwal-time.pending { color: var(--text-dim); }

        .jadwal-cron {
            font-size: 0.6rem;
            color: var(--text-dim);
            font-family: 'JetBrains Mono', monospace;
            padding: 0.4rem 0.75rem;
            text-align: center;
        }

        /* News stream items */
        .news-item {
            padding: 0.6rem 0;
            border-bottom: 1px solid var(--border-light);
            cursor: pointer;
            transition: background 0.15s;
        }

        .news-item:last-child { border-bottom: none; }
        .news-item:hover { background: var(--bg-hover); border-radius: 6px; padding-left: 0.4rem; }

        .news-item-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.2rem;
        }

        .sentiment-badge {
            font-size: 0.55rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border-radius: 4px;
            padding: 0.1rem 0.4rem;
        }

        .sentiment-badge.positive { background: var(--accent-dim); color: var(--accent); }
        .sentiment-badge.negative { background: var(--red-dim);    color: var(--red); }
        .sentiment-badge.neutral  { background: var(--yellow-dim); color: var(--yellow); }

        .news-time {
            font-size: 0.62rem;
            color: var(--text-dim);
            font-family: 'JetBrains Mono', monospace;
        }

        .news-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-white);
            line-height: 1.35;
            margin-bottom: 0.15rem;
        }

        .news-snippet {
            font-size: 0.67rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        /* Empty state */
        .empty-state {
            padding: 2rem;
            text-align: center;
            color: var(--text-dim);
            font-size: 0.78rem;
        }
    </style>
</head>
<body>

{{-- ===================== TOP BAR ===================== --}}
<div class="topbar">
    {{-- Brand --}}
    <div class="topbar-brand">
        <div class="brand-logo-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                <polyline points="16 7 22 7 22 13"></polyline>
            </svg>
        </div>
        <div class="brand-text">
            <div class="brand-name">StockSense AI</div>
            <div class="brand-sub">Fintech Monitor</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="topbar-search">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <form action="{{ route('dashboard') }}" method="GET">
            <input type="text" name="ticker" id="topbar-ticker-search"
                   value="{{ $searchTicker ?? '' }}"
                   placeholder="Search ticker (e.g. BBCA)..."
                   autocomplete="off">
        </form>
    </div>

    {{-- Live Ticker (FR-06) --}}
    <div class="ticker-wrap">
        <div class="ticker-inner" id="tickerInner">
            @for($loopPass = 0; $loopPass < 2; $loopPass++)
                @foreach($saham_ticker as $item)
                    @php
                        $pct = $item['change_pct'] ?? 0;
                        $pctClass = $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat');
                        $pctSign  = $pct > 0 ? '+' : '';
                        $mood     = $item['mood'] ?? 'neutral';
                    @endphp
                    <div class="ticker-item">
                        <span class="ticker-code">{{ $item['ticker_code'] }}</span>
                        @if($item['close_price'] !== null)
                            <span class="ticker-price">{{ number_format($item['close_price'], 0, ',', ',') }}</span>
                            <span class="ticker-pct {{ $pctClass }}">{{ $pctSign }}{{ $pct }}%</span>
                        @else
                            <span class="ticker-pct flat">—</span>
                        @endif
                        <span class="ticker-badge {{ $mood }}">{{ strtoupper($mood) }}</span>
                    </div>
                @endforeach
            @endfor
        </div>
    </div>

    {{-- Role Switcher (FR-02) --}}
    <div class="role-switch">
        @auth
            <button class="role-btn {{ Auth::user()->role === 'admin' ? 'active' : '' }}">Admin</button>
            <button class="role-btn {{ Auth::user()->role === 'visitor' ? 'active' : '' }}">Visitor</button>
        @else
            <button class="role-btn">Admin</button>
            <button class="role-btn active">Visitor</button>
        @endauth
    </div>

    {{-- Notification --}}
    <div class="topbar-notif">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <div class="notif-dot"></div>
    </div>

    {{-- User --}}
    <div class="topbar-user">
        <div class="avatar">
            {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'V' }}
        </div>
        <div class="user-info">
            <span class="user-name">
                {{ Auth::check() ? strtoupper(str_replace(' ', '_', Auth::user()->name)) : 'VISITOR_01' }}
            </span>
            <span class="user-role">
                {{ Auth::check() ? ucfirst(Auth::user()->role) . ' Access' : 'Guest Access' }}
            </span>
        </div>
    </div>
</div>

{{-- ===================== BODY ===================== --}}
<div class="layout-body">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar">

        <div class="nav-section-label">Navigation & Modules</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ !request()->is('dashboard/log*') && !request()->is('dashboard/emiten*') && !request()->is('dashboard/master*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span class="label">Dashboard Overview</span>
        </a>

        <a href="{{ route('emiten.detail') }}" class="nav-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"></path>
                <path d="M4 6h2"></path><path d="M4 10h2"></path><path d="M4 14h2"></path><path d="M4 18h2"></path>
            </svg>
            <span class="label">Emiten & Berita Terkait</span>
        </a>

        @if(Auth::check() && Auth::user()->role === 'admin')
            <div class="sidebar-sep"></div>
            <div class="nav-section-label">Administrator</div>

            <a href="{{ route('master.emiten') }}" class="nav-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <span class="label">Master Data Emiten</span>
            </a>

            <a href="{{ route('api.logs') }}" class="nav-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
                <span class="label">Log API & Cron Sync</span>
            </a>
        @endif

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Logout ({{ Auth::user()->name }})
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="main-content">

        {{-- Breadcrumb --}}
        <div class="breadcrumb">
            <span class="breadcrumb-item">Admin_Sys</span>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-item">Dashboard_Overview</span>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-item current">AI Impact Radar</span>
        </div>

        {{-- Scrollable content --}}
        <div class="content-scroll">

            {{-- Page Header --}}
            <div class="page-header">
                <div class="page-header-left">
                    <div class="page-title-row">
                        <h1 class="page-title">AI Impact Radar</h1>
                        <span class="status-badge active">
                            <span class="pulse-dot"></span>
                            Live Analysis
                        </span>
                    </div>
                    <p class="page-desc">
                        Pemantauan real-time sentimen berita pasar terhadap emiten saham Indonesia.
                        Data diperbarui otomatis melalui pipeline AI setiap batch harian.
                    </p>
                </div>
                <div class="page-header-right">
                    <div class="schedule-info">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Jadwal: 08:00, 12:00, 18:00 WIB
                    </div>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <button class="btn-force-run" id="btnForceRun">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                            </svg>
                            Tarik Data Manual Sekarang
                        </button>
                    @endif
                </div>
            </div>

            {{-- Stream Endpoints --}}
            <div class="endpoints-bar">
                <div class="endpoint-item">
                    <span class="endpoint-dot green"></span>
                    <span>RSS/Scraper Engine <span style="color:var(--text-dim)"></span></span>
                    <span class="endpoint-pct">99.0%</span>
                </div>
                <div class="endpoint-sep"></div>
                <div class="endpoint-item">
                    <span class="endpoint-dot green"></span>
                    <span>Yahoo/IDX Financial Feed <span style="color:var(--text-dim)"></span></span>
                    <span class="endpoint-pct">102%</span>
                </div>
                <div class="endpoint-sep"></div>
                <div class="endpoint-item">
                    <span class="endpoint-dot blue"></span>
                    <span>Queue Redis</span>
                    <span style="color:var(--text-muted); font-family: 'JetBrains Mono', monospace; font-size:0.65rem;">0 lag</span>
                </div>
                <div class="endpoint-sep"></div>
                <div class="endpoint-item">
                    <span style="color:var(--text-dim); font-size:0.65rem;">Sync Target:</span>
                    <span style="color:var(--text-white); font-size:0.65rem; font-weight:600; font-family:'JetBrains Mono',monospace;">POSTGRES_DB_PROD</span>
                </div>
                <div class="endpoint-ssl">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    SSL ENCRYPTED
                </div>
            </div>

            {{-- FR-09: Metric Cards --}}
            <div class="metrics-grid">

                {{-- Status Eksekusi --}}
                @php
                    $jobTotal = max(1, $jobsToday ?? 0);
                    $jobPct   = ($jobsToday ?? 0) > 0 ? (int) round((($jobsOkToday ?? 0) / $jobTotal) * 100) : 100;
                @endphp
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Status Eksekusi Hari Ini</span>
                        <div class="metric-icon green">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="metric-value healthy sm">{{ $jobPct >= 80 ? 'Normal / Sehat' : 'Perlu Cek Log' }}</div>
                    <div class="metric-sub">
                        <span class="positive">{{ $jobsOkToday ?? 0 }}/{{ $jobsToday ?? 0 }}</span> Job Berhasil ({{ $jobPct }}%)
                    </div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill green" style="width:{{ $jobPct }}%"></div>
                    </div>
                </div>

                {{-- Total Berita --}}
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Total Berita Ditarik <span style="color:var(--text-dim)"></span></span>
                        <div class="metric-icon blue">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="metric-value">{{ $totalBeritaAll ?? 0 }}</div>
                    <div class="metric-sub">
                        <span class="positive">+{{ $totalBeritaHariIni ?? 0 }}</span> artikel hari ini
                    </div>
                    <div class="metric-bar">
                        @php $pctBerita = min(100, ($totalBeritaAll ?? 0) > 0 ? 75 : 0); @endphp
                        <div class="metric-bar-fill blue" style="width:{{ $pctBerita }}%"></div>
                    </div>
                </div>

                {{-- Sentimen Positif --}}
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Sentimen Positif</span>
                        <div class="metric-icon green">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                                <polyline points="16 7 22 7 22 13"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="metric-value">{{ $pctPositif ?? 0 }}<span style="font-size:1rem;font-weight:500;color:var(--text-muted)">%</span></div>
                    <div class="metric-sub">dari total sentimen teranalisis</div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill green" style="width:{{ $pctPositif ?? 0 }}%"></div>
                    </div>
                </div>

                {{-- Sentimen Negatif --}}
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Sentimen Negatif</span>
                        <div class="metric-icon red">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 17 13.5 8.5 8.5 13.5 2 7"></polyline>
                                <polyline points="16 17 22 17 22 11"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="metric-value" style="color:var(--red)">{{ $pctNegatif ?? 0 }}<span style="font-size:1rem;font-weight:500;color:var(--text-muted)">%</span></div>
                    <div class="metric-sub">dari total sentimen teranalisis</div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill red" style="width:{{ $pctNegatif ?? 0 }}%"></div>
                    </div>
                </div>

                {{-- Last Sync --}}
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Terakhir Sync Sukses</span>
                        <div class="metric-icon indigo">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="metric-value sm" id="lastSyncTime">
                        {{ ($lastSuccess && $lastSuccess->finished_at) ? $lastSuccess->finished_at->format('H:i:s') . ' WIB' : 'Belum Sync' }}
                    </div>
                    <div class="metric-sub" id="lastSyncDate">
                        {{ ($lastSuccess && $lastSuccess->finished_at) ? $lastSuccess->finished_at->format('d M Y') : now()->format('d M Y') }}
                    </div>
                    <div class="metric-next">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span id="nextSchedule">Menghitung...</span>
                    </div>
                </div>
            </div>

            {{-- Bottom Grid --}}
            <div class="bottom-grid">

                {{-- Sub-Process Card --}}
                <div class="subproc-card">
                    <div class="subproc-header">
                        <span class="subproc-tag">Sub-Process Architecture</span>
                        <span class="daemon-id">DAEMON ID: CRON_JOB_SYS_01</span>
                    </div>
                    <div class="subproc-body">
                        <div class="subproc-title">Live News Stream & Analisis Sentimen AI</div>
                        <div class="subproc-desc">
                            Menampilkan berita terkini yang sudah diindeks dan dianalisis oleh AI pipeline.
                            Klik berita untuk melihat detail dampak pada emiten terkait.
                        </div>

                        {{-- Pipeline Sources --}}
                        <div class="pipeline-grid">
                            <div class="pipeline-item">
                                <div class="pipeline-item-header">
                                    <div class="pipeline-item-icon">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"></path>
                                        </svg>
                                    </div>
                                    <span class="pipeline-item-name">News Parser</span>
                                </div>
                                <div class="pipeline-item-src">Bisnis.com, Kontan, CNBC, Investor.id</div>
                                <div class="pipeline-status ok">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Status: OK · Latency 240ms
                                </div>
                            </div>
                            <div class="pipeline-item">
                                <div class="pipeline-item-header">
                                    <div class="pipeline-item-icon">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                                        </svg>
                                    </div>
                                    <span class="pipeline-item-name">Master Data</span>
                                </div>
                                <div class="pipeline-item-src">Yahoo Finance / IDX API Connector</div>
                                <div class="pipeline-status ok">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Status: OK · OHLC Sync Valid
                                </div>
                            </div>
                        </div>

                        {{-- FR-11: News Stream --}}
                        <div class="log-section" style="padding: 0; border: none;">
                            <div class="log-toolbar">
                                <div class="log-tabs">
                                    <button class="log-tab active" data-filter="all">Semua <span class="count">{{ $daftar_berita->count() }}</span></button>
                                    <button class="log-tab" data-filter="positive">Positif</button>
                                    <button class="log-tab" data-filter="negative">Negatif</button>
                                    <button class="log-tab" data-filter="neutral">Netral</button>
                                </div>
                                <div class="log-search">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    <input type="text" id="newsTitleSearch" placeholder="Cari judul berita...">
                                </div>
                                <a href="{{ route('dashboard') }}" class="btn-icon" title="Refresh" style="text-decoration:none">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                                </a>
                            </div>

                            {{-- News List --}}
                            @forelse($daftar_berita as $berita)
                                @php
                                    $sentimen = $berita->sentiments->first();
                                    $label    = $sentimen ? $sentimen->sentiment_label : 'neutral';
                                    $conf     = $sentimen ? round($sentimen->confidence_score * 100) : null;
                                    $tickers  = $berita->sentiments->map(fn($s) => $s->stock?->ticker_code)->filter()->unique();
                                    $labelMap = ['positive' => 'Positif', 'negative' => 'Negatif', 'neutral' => 'Netral'];
                                @endphp
                                <div class="news-item" data-sentiment="{{ $label }}" data-title="{{ strtolower($berita->title) }}">
                                    <div class="news-item-header">
                                        <span class="sentiment-badge {{ $label }}">
                                            {{ $labelMap[$label] ?? 'Netral' }}{{ $conf ? " ({$conf}%)" : '' }}
                                        </span>
                                        @foreach($tickers as $tk)
                                            <a href="{{ route('dashboard', ['ticker' => $tk]) }}"
                                               style="font-family:'JetBrains Mono',monospace; font-size:0.62rem; font-weight:700; color:var(--indigo-light); background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.28); padding:0.1rem 0.4rem; border-radius:4px; text-decoration:none;">
                                                {{ $tk }}
                                            </a>
                                        @endforeach
                                        @if($berita->source_name)
                                            <span style="font-size:0.65rem; color:var(--text-muted);">· {{ $berita->source_name }}</span>
                                        @endif
                                        <span class="news-time">{{ $berita->published_at ? $berita->published_at->diffForHumans() : $berita->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="news-title">
                                        <a href="{{ $berita->source_url }}" target="_blank" rel="noopener noreferrer" style="color:inherit; text-decoration:none;">
                                            {{ $berita->title }}
                                        </a>
                                    </div>
                                    <div class="news-snippet">{{ Str::limit($berita->content, 110) }}</div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 0.5rem; color:var(--text-dim)"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"></path></svg>
                                    Belum ada berita di database.<br>
                                    <span style="font-size:0.65rem">Klik "Tarik Data Manual Sekarang" atau jalankan cron job.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Jadwal Rotasi --}}
                <div class="jadwal-card">
                    <div class="jadwal-header">
                        <span class="jadwal-title">Jadwal Rotasi Harian</span>
                        <span class="auto-sync-badge">Auto Sync</span>
                    </div>
                    <div class="jadwal-body">
                        <div class="jadwal-item">
                            <div class="jadwal-status done">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div class="jadwal-info">
                                <div class="jadwal-batch">BATCH #1 (PAGI)</div>
                                <div class="jadwal-time">08:00:23 WIB</div>
                            </div>
                        </div>
                        <div class="jadwal-item">
                            <div class="jadwal-status done">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div class="jadwal-info">
                                <div class="jadwal-batch">BATCH #2 (SIANG)</div>
                                <div class="jadwal-time">12:00:15 WIB</div>
                            </div>
                        </div>
                        <div class="jadwal-item">
                            <div class="jadwal-status pending">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <div class="jadwal-info">
                                <div class="jadwal-batch">BATCH #3 (SORE)</div>
                                <div class="jadwal-time pending">18:00:00 WIB</div>
                            </div>
                        </div>

                        <div class="jadwal-cron">Cron Spec: <strong>0 8,12,18 * * *</strong></div>

                        {{-- Mini log table (FR-04) --}}
                        <div style="margin-top:0.5rem;">
                            <div style="font-size:0.62rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-dim); margin-bottom:0.4rem;">Log Eksekusi Terakhir</div>
                            <table class="log-table" style="font-size:0.65rem">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Target</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentLogs ?? [] as $log)
                                        <tr>
                                            <td>{{ ($log->finished_at ?? $log->created_at)->format('H:i:s') }}</td>
                                            <td>{{ $log->source_type === 'price' ? 'OHLC_IDX' : 'NEWS_RSS' }}</td>
                                            <td>+{{ $log->records_fetched }}</td>
                                            <td>
                                                @if(in_array($log->status, ['success', 'partial']))
                                                    <span class="log-status-ok">✓ {{ strtoupper($log->status) }}</span>
                                                @else
                                                    <span class="log-status-fail">✗ {{ strtoupper($log->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="text-align:center; color:var(--text-dim);">Belum ada log eksekusi</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>{{-- /bottom-grid --}}
        </div>{{-- /content-scroll --}}
    </main>
</div>

<script>
    // =============================================
    // Live Clock for "Next Schedule"
    // =============================================
    function updateNextSchedule() {
        const now = new Date();
        const schedules = [8, 12, 18]; // jam WIB (asumsi WIB = UTC+7)
        const wib = new Date(now.getTime() + (7 * 60 * 60 * 1000));
        const wibHour = wib.getUTCHours();
        const wibMin  = wib.getUTCMinutes();

        let nextHour = null;
        for (const h of schedules) {
            if (wibHour < h || (wibHour === h && wibMin === 0)) {
                nextHour = h;
                break;
            }
        }

        if (nextHour === null) nextHour = schedules[0] + 24;

        const diffHours = nextHour - wibHour - (wibMin > 0 ? 1 : 0);
        const diffMins  = wibMin > 0 ? 60 - wibMin : 0;

        const el = document.getElementById('nextSchedule');
        if (el) el.textContent = `Next schedule in ${String(Math.max(0,diffHours)).padStart(2,'0')}h ${String(diffMins).padStart(2,'0')}m`;
    }
    updateNextSchedule();
    setInterval(updateNextSchedule, 60000);

    // =============================================
    // Force Run button -> POST /dashboard/sync
    // =============================================
    const btnForceRun = document.getElementById('btnForceRun');
    if (btnForceRun) {
        btnForceRun.addEventListener('click', async function () {
            this.disabled = true;
            this.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 0.7s linear infinite"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg> Sedang Menarik Data...`;
            try {
                await fetch('{{ route('dashboard.sync') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                window.location.reload();
            } catch (e) {
                this.disabled = false;
                this.innerHTML = `Gagal, coba lagi`;
            }
        });
    }

    // =============================================
    // Filter tab sentimen + pencarian judul berita
    // =============================================
    let activeSentiment = 'all';
    const searchInput = document.getElementById('newsTitleSearch');

    function applyNewsFilter() {
        const q = (searchInput ? searchInput.value : '').trim().toLowerCase();
        document.querySelectorAll('.news-item').forEach(item => {
            const matchSentiment = activeSentiment === 'all' || item.dataset.sentiment === activeSentiment;
            const matchTitle = !q || (item.dataset.title || '').includes(q);
            item.style.display = (matchSentiment && matchTitle) ? '' : 'none';
        });
    }

    document.querySelectorAll('.log-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.log-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            activeSentiment = this.dataset.filter || 'all';
            applyNewsFilter();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyNewsFilter);
    }
</script>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

</body>
</html>