<!DOCTYPE html>
<html lang="en">
<head>
    <base href="{{ url('/') }}/" />
    <title>{{ $appConfig['app_name']['value'] }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ $appConfig['app_logo']['value'] }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #F5C842;
            --gold-dim: #C9A227;
            --gold-muted: rgba(245,200,66,0.12);
            --gold-border: rgba(245,200,66,0.25);
            --bg: #080B10;
            --bg-card: #0D1117;
            --bg-input: #111620;
            --bg-panel: #0F1419;
            --border: rgba(255,255,255,0.07);
            --border-hover: rgba(255,255,255,0.14);
            --text-primary: #F0F4F8;
            --text-secondary: #6B7A8D;
            --text-muted: #3D4A58;
            --red: #F04F59;
            --red-dim: rgba(240,79,89,0.12);
            --green: #16A879;
            --mono: 'JetBrains Mono', monospace;
            --sans: 'Space Grotesk', sans-serif;
        }

        html, body { height: 100%; }

        body {
            font-family: var(--sans);
            background: var(--bg);
            color: var(--text-primary);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Background canvas ── */
        .bg-canvas {
            position: fixed; inset: 0; z-index: 0; overflow: hidden;
        }

        .bg-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(245,200,66,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(245,200,66,0.025) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .bg-glow-1 {
            position: absolute; top: -20%; left: -10%;
            width: 60vw; height: 60vw; border-radius: 50%;
            background: radial-gradient(circle, rgba(245,200,66,0.06) 0%, transparent 65%);
            animation: drift1 18s ease-in-out infinite;
        }

        .bg-glow-2 {
            position: absolute; bottom: -20%; right: -10%;
            width: 50vw; height: 50vw; border-radius: 50%;
            background: radial-gradient(circle, rgba(22,168,121,0.05) 0%, transparent 65%);
            animation: drift2 22s ease-in-out infinite;
        }

        .bg-scanline {
            position: absolute; inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0,0,0,0.08) 2px,
                rgba(0,0,0,0.08) 4px
            );
            pointer-events: none;
        }

        @keyframes drift1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(4%, 6%) scale(1.08); }
        }
        @keyframes drift2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-5%, -4%) scale(1.06); }
        }

        /* ── Ticker bar ── */
        .ticker-bar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            height: 36px;
            background: rgba(8,11,16,0.95);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .ticker-label {
            flex-shrink: 0;
            padding: 0 16px;
            font-family: var(--mono);
            font-size: 9px; font-weight: 700;
            letter-spacing: 2px;
            color: var(--gold);
            border-right: 1px solid var(--border);
            height: 100%; display: flex; align-items: center;
            background: rgba(245,200,66,0.05);
        }

        .ticker-scroll {
            display: flex; gap: 0; overflow: hidden; flex: 1;
        }

        .ticker-track {
            display: flex; gap: 0;
            animation: ticker 28s linear infinite;
            white-space: nowrap;
        }

        .ticker-item {
            display: flex; align-items: center; gap: 8px;
            padding: 0 24px;
            font-family: var(--mono);
            font-size: 10px;
            border-right: 1px solid var(--border);
            height: 36px;
        }

        .ticker-sym { color: var(--text-secondary); font-weight: 500; }
        .ticker-price { color: var(--text-primary); font-weight: 700; }
        .ticker-chg.up { color: var(--green); }
        .ticker-chg.dn { color: var(--red); }

        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* ── Layout ── */
        .page-wrap {
            min-height: 100vh;
            padding-top: 36px;
            display: flex;
            position: relative; z-index: 1;
        }

        /* Left panel - visible only on desktop */
        .left-panel {
            display: none;
        }

        @media (min-width: 1024px) {
            .left-panel {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                width: 42%;
                max-width: 520px;
                padding: 32px 48px 40px;
                border-right: 1px solid var(--border);
                position: relative;
                overflow: hidden;
            }
        }

        .panel-brand { display: flex; flex-direction: column; gap: 20px; }

        .panel-logo img {
            width: 48px; height: 48px; object-fit: contain;
            filter: drop-shadow(0 0 12px rgba(245,200,66,0.4));
        }

        .panel-appname {
            font-family: var(--mono);
            font-size: 10px; font-weight: 700;
            letter-spacing: 3px; text-transform: uppercase;
            color: var(--gold);
        }

        .panel-headline {
            font-size: clamp(28px, 3vw, 40px);
            font-weight: 700; line-height: 1.15;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .panel-headline span { color: var(--gold); }

        .panel-sub {
            font-size: 14px; color: var(--text-secondary);
            line-height: 1.7; max-width: 340px;
        }

        .panel-stats {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 12px; margin-top: 36px;
        }

        .stat-card {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 10px; padding: 16px;
        }

        .stat-card-label {
            font-size: 10px; font-weight: 600;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--text-muted); margin-bottom: 6px;
        }

        .stat-card-value {
            font-family: var(--mono);
            font-size: 20px; font-weight: 700;
            color: var(--text-primary);
        }

        .stat-card-value.green { color: var(--green); }

        .panel-chart {
            margin-top: 32px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            position: relative; overflow: hidden;
        }

        .panel-chart-label {
            font-size: 10px; font-weight: 600;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--text-muted); margin-bottom: 12px;
        }

        .chart-svg { width: 100%; height: 80px; }

        .panel-footer {
            display: flex; align-items: center; gap: 8px;
            font-size: 11px; color: var(--text-muted);
        }

        .status-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--green);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.7); }
        }

        /* ── Right panel / Login form ── */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 20px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            animation: fadeUp 0.5s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Mobile logo (hidden on desktop) */
        .mobile-brand {
            display: flex; flex-direction: column; align-items: center;
            gap: 10px; margin-bottom: 36px;
        }

        @media (min-width: 1024px) {
            .mobile-brand { display: none; }
        }

        .mobile-brand img {
            width: 52px; height: 52px; object-fit: contain;
            filter: drop-shadow(0 0 14px rgba(245,200,66,0.4));
        }

        .mobile-brand-name {
            font-family: var(--mono);
            font-size: 9px; font-weight: 700;
            letter-spacing: 3px; text-transform: uppercase;
            color: var(--gold);
        }

        /* Form header */
        .form-head { margin-bottom: 32px; }

        .form-head h1 {
            font-size: 24px; font-weight: 700;
            color: var(--text-primary); letter-spacing: -0.3px;
            margin-bottom: 6px;
        }

        .form-head p {
            font-size: 13px; color: var(--text-secondary);
        }

        /* Divider */
        .divider {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 28px;
        }

        .divider-line {
            flex: 1; height: 1px; background: var(--border);
        }

        .divider-label {
            font-family: var(--mono);
            font-size: 9px; font-weight: 700; letter-spacing: 2px;
            color: var(--text-muted); text-transform: uppercase;
        }

        /* Error alert */
        .alert-error {
            display: flex; gap: 12px; align-items: flex-start;
            background: var(--red-dim);
            border: 1px solid rgba(240,79,89,0.3);
            border-left: 3px solid var(--red);
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
            animation: shake 0.45s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .alert-error-icon { color: var(--red); font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .alert-error-title { font-size: 12px; font-weight: 700; color: var(--red); margin-bottom: 3px; }
        .alert-error-msg { font-size: 12px; color: #fca5a5; line-height: 1.5; }

        /* Form fields */
        .field { margin-bottom: 18px; }

        .field-label {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 8px;
        }

        .field-label span {
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.8px; text-transform: uppercase;
            color: var(--text-secondary);
        }

        .field-label a {
            font-size: 11px; color: var(--gold);
            text-decoration: none; font-weight: 500;
            transition: color 0.2s;
        }
        .field-label a:hover { color: #fde68a; }

        .input-wrap {
            position: relative;
        }

        .input-prefix {
            position: absolute; left: 0; top: 0; bottom: 0;
            width: 46px;
            display: flex; align-items: center; justify-content: center;
            color: var(--gold);
            font-size: 16px;
            border-right: 1px solid var(--border);
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            height: 50px;
            padding: 0 46px 0 56px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: var(--sans);
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .field-input::placeholder { color: var(--text-muted); }

        .field-input:focus {
            border-color: var(--gold-border);
            background: #131820;
            box-shadow: 0 0 0 3px rgba(245,200,66,0.08);
        }

        .field-input.is-invalid {
            border-color: rgba(240,79,89,0.5);
        }

        .toggle-pw {
            position: absolute; right: 0; top: 0; bottom: 0;
            width: 46px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); font-size: 16px;
            transition: color 0.2s;
        }
        .toggle-pw:hover { color: var(--gold); }

        /* Remember row */
        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 24px;
        }

        .custom-check {
            width: 16px; height: 16px;
            border: 1px solid var(--border-hover);
            border-radius: 4px;
            background: var(--bg-input);
            cursor: pointer; position: relative;
            flex-shrink: 0; appearance: none;
            transition: all 0.2s;
        }

        .custom-check:checked {
            background: var(--gold);
            border-color: var(--gold);
        }

        .custom-check:checked::after {
            content: '';
            position: absolute; top: 2px; left: 5px;
            width: 4px; height: 8px;
            border: 2px solid #000;
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }

        .remember-label {
            font-size: 12px; color: var(--text-secondary);
            cursor: pointer;
        }

        /* Submit button */
        .btn-login {
            width: 100%; height: 52px;
            background: var(--gold);
            border: none; border-radius: 10px;
            font-size: 14px; font-weight: 700;
            font-family: var(--sans);
            color: #080B10;
            cursor: pointer; letter-spacing: 0.3px;
            position: relative; overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
            box-shadow: 0 4px 24px rgba(245,200,66,0.25);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }

        .btn-login::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
            pointer-events: none;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(245,200,66,0.35);
        }

        .btn-login:active { transform: translateY(0); }

        .btn-login.loading {
            pointer-events: none; opacity: 0.75;
        }

        .btn-login .spinner {
            display: none;
            width: 16px; height: 16px;
            border: 2px solid rgba(0,0,0,0.3);
            border-top-color: #080B10;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .btn-login.loading .spinner { display: block; }
        .btn-login.loading .btn-text { opacity: 0.6; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Sign up link */
        .signup-row {
            text-align: center; margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
            font-size: 13px; color: var(--text-secondary);
        }

        .signup-row a {
            color: var(--gold); text-decoration: none;
            font-weight: 600; margin-left: 4px;
            transition: color 0.2s;
        }
        .signup-row a:hover { color: #fde68a; }

        /* Security badge */
        .security-badge {
            display: flex; align-items: center; gap: 8px;
            margin-top: 20px;
            padding: 10px 14px;
            background: rgba(22,168,121,0.07);
            border: 1px solid rgba(22,168,121,0.15);
            border-radius: 8px;
        }

        .security-badge i { color: var(--green); font-size: 14px; }
        .security-badge span { font-size: 11px; color: var(--text-secondary); line-height: 1.4; }
        .security-badge strong { color: #34d399; }

        /* Responsive tweaks */
        @media (max-width: 480px) {
            .right-panel { padding: 20px 16px; }
            .form-head h1 { font-size: 20px; }
            .field-input { height: 48px; font-size: 14px; }
            .btn-login { height: 50px; }
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: #1e2533; border-radius: 3px; }
    </style>
    <script>
        if (window.top != window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>
</head>

<body id="kt_body" class="app-blank">
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <!-- Background -->
    <div class="bg-canvas">
        <div class="bg-grid"></div>
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>
        <div class="bg-scanline"></div>
    </div>

    <!-- Ticker bar -->
    <div class="ticker-bar">
        <div class="ticker-label">LIVE</div>
        <div class="ticker-scroll">
            <div class="ticker-track" id="tickerTrack">
                <div class="ticker-item"><span class="ticker-sym">BTC/USDT</span><span class="ticker-price">67,842.30</span><span class="ticker-chg up">+2.34%</span></div>
                <div class="ticker-item"><span class="ticker-sym">ETH/USDT</span><span class="ticker-price">3,521.10</span><span class="ticker-chg up">+1.87%</span></div>
                <div class="ticker-item"><span class="ticker-sym">BNB/USDT</span><span class="ticker-price">612.45</span><span class="ticker-chg dn">-0.52%</span></div>
                <div class="ticker-item"><span class="ticker-sym">SOL/USDT</span><span class="ticker-price">178.90</span><span class="ticker-chg up">+4.21%</span></div>
                <div class="ticker-item"><span class="ticker-sym">XRP/USDT</span><span class="ticker-price">0.6124</span><span class="ticker-chg dn">-1.03%</span></div>
                <div class="ticker-item"><span class="ticker-sym">ADA/USDT</span><span class="ticker-price">0.4832</span><span class="ticker-chg up">+0.78%</span></div>
                <div class="ticker-item"><span class="ticker-sym">DOGE/USDT</span><span class="ticker-price">0.1623</span><span class="ticker-chg up">+3.15%</span></div>
                <div class="ticker-item"><span class="ticker-sym">AVAX/USDT</span><span class="ticker-price">39.24</span><span class="ticker-chg dn">-0.84%</span></div>
                <!-- Duplicate for infinite loop -->
                <div class="ticker-item"><span class="ticker-sym">BTC/USDT</span><span class="ticker-price">67,842.30</span><span class="ticker-chg up">+2.34%</span></div>
                <div class="ticker-item"><span class="ticker-sym">ETH/USDT</span><span class="ticker-price">3,521.10</span><span class="ticker-chg up">+1.87%</span></div>
                <div class="ticker-item"><span class="ticker-sym">BNB/USDT</span><span class="ticker-price">612.45</span><span class="ticker-chg dn">-0.52%</span></div>
                <div class="ticker-item"><span class="ticker-sym">SOL/USDT</span><span class="ticker-price">178.90</span><span class="ticker-chg up">+4.21%</span></div>
                <div class="ticker-item"><span class="ticker-sym">XRP/USDT</span><span class="ticker-price">0.6124</span><span class="ticker-chg dn">-1.03%</span></div>
                <div class="ticker-item"><span class="ticker-sym">ADA/USDT</span><span class="ticker-price">0.4832</span><span class="ticker-chg up">+0.78%</span></div>
                <div class="ticker-item"><span class="ticker-sym">DOGE/USDT</span><span class="ticker-price">0.1623</span><span class="ticker-chg up">+3.15%</span></div>
                <div class="ticker-item"><span class="ticker-sym">AVAX/USDT</span><span class="ticker-price">39.24</span><span class="ticker-chg dn">-0.84%</span></div>
            </div>
        </div>
    </div>

    <!-- Page layout -->
    <div class="page-wrap">

        <!-- Left decorative panel (desktop only) -->
        <div class="left-panel">
            <div>
                <div class="panel-brand">
                    <div class="panel-logo">
                        <img src="{{ $appConfig['app_logo']['value'] }}" alt="Logo" />
                    </div>
                    <div class="panel-appname">{{ $appConfig['app_name']['value'] }}</div>
                </div>
                <div style="margin-top: 36px;">
                    <div class="panel-headline">
                        Trade Smarter.<br><span>Earn Bigger.</span>
                    </div>
                    <p class="panel-sub" style="margin-top: 16px;">
                        Professional-grade exchange platform with real-time markets, advanced order types, and institutional security.
                    </p>
                </div>
                <div class="panel-stats">
                    <div class="stat-card">
                        <div class="stat-card-label">24h Volume</div>
                        <div class="stat-card-value green">$2.4B</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">Active Users</div>
                        <div class="stat-card-value">184K</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">Listed Pairs</div>
                        <div class="stat-card-value">340+</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">Uptime</div>
                        <div class="stat-card-value green">99.98%</div>
                    </div>
                </div>
                <div class="panel-chart" style="margin-top: 24px;">
                    <div class="panel-chart-label">BTC / USDT — 7D</div>
                    <svg class="chart-svg" viewBox="0 0 400 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#F5C842" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#F5C842" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="M0 60 L40 52 L80 58 L120 42 L160 48 L200 32 L240 28 L280 18 L320 22 L360 12 L400 8 L400 80 L0 80 Z" fill="url(#chartGrad)"/>
                        <path d="M0 60 L40 52 L80 58 L120 42 L160 48 L200 32 L240 28 L280 18 L320 22 L360 12 L400 8" fill="none" stroke="#F5C842" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="panel-footer">
                <div class="status-dot"></div>
                <span>All systems operational</span>
            </div>
        </div>

        <!-- Right: login form -->
        <div class="right-panel">
            <div class="login-box">

                <!-- Mobile brand -->
                <div class="mobile-brand">
                    <img src="{{ $appConfig['app_logo']['value'] }}" alt="Logo" />
                    <div class="mobile-brand-name">{{ $appConfig['app_name']['value'] }}</div>
                </div>

                <div class="form-head">
                    <h1>Welcome back</h1>
                    <p>Sign in to your trading account</p>
                </div>

                <div class="divider">
                    <div class="divider-line"></div>
                    <div class="divider-label">Secure Login</div>
                    <div class="divider-line"></div>
                </div>

                @if ($errors->any())
                    <div class="alert-error">
                        <i class="ki-duotone ki-shield-cross alert-error-icon">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i>
                        <div>
                            <div class="alert-error-title">Authentication Failed</div>
                            @foreach ($errors->all() as $error)
                                <div class="alert-error-msg">{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="field">
                        <div class="field-label">
                            <span>Email / Username / Phone</span>
                        </div>
                        <div class="input-wrap">
                            <div class="input-prefix">
                                <i class="ki-duotone ki-profile-circle">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                </i>
                            </div>
                            <input
                                type="text"
                                name="login"
                                placeholder="you@example.com / 0812xxx / username"
                                autocomplete="username"
                                value="{{ old('login') }}"
                                class="field-input @error('login') is-invalid @enderror"
                                required
                            />
                        </div>
                    </div>

                    <div class="field">
                        <div class="field-label">
                            <span>Password</span>
                            <a href="{{ route('forget-password') }}">Forgot password?</a>
                        </div>
                        <div class="input-wrap">
                            <div class="input-prefix">
                                <i class="ki-duotone ki-lock">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </div>
                            <input
                                type="password"
                                name="password"
                                id="pwInput"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                class="field-input @error('password') is-invalid @enderror"
                                required
                            />
                            <button type="button" class="toggle-pw" onclick="togglePw()">
                                <i class="ki-duotone ki-eye" id="pwEye">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                </i>
                            </button>
                        </div>
                    </div>

                    <div class="remember-row">
                        <input type="checkbox" name="remember" id="rememberMe" class="custom-check" />
                        <label for="rememberMe" class="remember-label">Keep me signed in on this device</label>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <div class="spinner"></div>
                        <span class="btn-text">Sign In</span>
                        <i class="ki-duotone ki-arrow-right" style="font-size:16px;">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </button>
                </form>

                <div class="security-badge">
                    <i class="ki-duotone ki-shield-tick">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <span>Protected by <strong>256-bit SSL encryption</strong> and 2FA support</span>
                </div>

                <div class="signup-row">
                    Don't have an account?
                    <a href="{{ route('register') }}">Create account</a>
                </div>

            </div>
        </div>
    </div>

    <script>
        var hostUrl = "assets/";

        function togglePw() {
            const input = document.getElementById('pwInput');
            const icon  = document.getElementById('pwEye');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ki-eye', 'ki-eye-slash');
                icon.innerHTML = '<span class="path1"></span><span class="path2"></span>';
            } else {
                input.type = 'password';
                icon.classList.replace('ki-eye-slash', 'ki-eye');
                icon.innerHTML = '<span class="path1"></span><span class="path2"></span><span class="path3"></span>';
            }
        }

        document.querySelector('form').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.querySelector('.btn-text').textContent = 'Signing in...';
        });
    </script>

    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
</body>
</html>