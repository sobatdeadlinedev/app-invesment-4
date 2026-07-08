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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #F5C842;
            --gold-dim: rgba(245,200,66,0.12);
            --bg: #0B0E12;
            --bg-card: #12161C;
            --bg-input: #171C24;
            --border: rgba(255,255,255,0.08);
            --border-hover: rgba(255,255,255,0.16);
            --text-primary: #F0F4F8;
            --text-secondary: #7A8699;
            --text-muted: #4A5568;
            --red: #F04F59;
            --red-dim: rgba(240,79,89,0.1);
            --green: #16A879;
            --green-dim: rgba(22,168,121,0.1);
            --mono: 'JetBrains Mono', monospace;
            --sans: 'Inter', sans-serif;
        }

        html, body { height: 100%; }

        body {
            font-family: var(--sans);
            background: var(--bg);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }

        /* ── Background accents ── */
        .bg-canvas { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }

        .bg-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(245,200,66,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(245,200,66,0.02) 1px, transparent 1px);
            background-size: 44px 44px;
        }

        .bg-glow-1 {
            position: absolute; top: -25%; left: -10%;
            width: 55vw; height: 55vw; border-radius: 50%;
            background: radial-gradient(circle, rgba(245,200,66,0.08) 0%, transparent 65%);
            animation: drift1 20s ease-in-out infinite;
        }

        .bg-glow-2 {
            position: absolute; bottom: -25%; right: -10%;
            width: 50vw; height: 50vw; border-radius: 50%;
            background: radial-gradient(circle, rgba(22,168,121,0.06) 0%, transparent 65%);
            animation: drift2 24s ease-in-out infinite;
        }

        @keyframes drift1 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(4%,5%) scale(1.06); }
        }
        @keyframes drift2 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-4%,-4%) scale(1.05); }
        }

        .page-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 20px;
            position: relative; z-index: 1;
        }

        .form-box {
            width: 100%;
            max-width: 400px;
            background: linear-gradient(180deg, #14181f 0%, var(--bg-card) 100%);
            border: 1px solid var(--border);
            border-top: 2px solid var(--gold);
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 24px 60px -20px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.02);
            animation: fadeUp 0.5s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Brand */
        .brand {
            display: flex; flex-direction: column; align-items: center;
            gap: 12px; margin-bottom: 28px;
        }

        .brand img {
            width: 44px; height: 44px; object-fit: contain;
            filter: drop-shadow(0 0 14px rgba(245,200,66,0.35));
        }

        .brand-name {
            font-family: var(--mono);
            font-size: 11px; font-weight: 700;
            letter-spacing: 2.5px; text-transform: uppercase;
            color: var(--gold);
        }

        /* Form header */
        .form-head { text-align: center; margin-bottom: 24px; }

        .form-head h1 {
            font-size: 20px; font-weight: 700;
            color: var(--text-primary); letter-spacing: -0.2px;
            margin-bottom: 6px;
        }

        .form-head p {
            font-size: 13px; color: var(--text-secondary);
        }

        /* Alert */
        .alert {
            display: flex; gap: 10px; align-items: flex-start;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: var(--green-dim);
            border: 1px solid rgba(22,168,121,0.2);
        }
        .alert-success i { color: var(--green); font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-success .alert-msg { font-size: 12px; color: #6ee7b7; line-height: 1.5; }

        .alert-error {
            background: var(--red-dim);
            border: 1px solid rgba(240,79,89,0.25);
        }
        .alert-error i { color: var(--red); font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-error-title { font-size: 12px; font-weight: 700; color: var(--red); margin-bottom: 2px; }
        .alert-error .alert-msg { font-size: 12px; color: #fca5a5; line-height: 1.5; }
        .alert-error ul { margin: 0; padding-left: 16px; }
        .alert-error li { font-size: 12px; color: #fca5a5; line-height: 1.6; }

        /* Fields */
        .field { margin-bottom: 20px; }

        .field-label {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 7px;
        }

        .field-label span {
            font-size: 12px; font-weight: 500;
            color: var(--text-secondary);
        }

        .input-wrap { position: relative; }

        .input-prefix {
            position: absolute; left: 0; top: 0; bottom: 0;
            width: 42px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted);
            font-size: 15px;
            transition: color 0.2s;
        }

        .input-wrap:focus-within .input-prefix { color: var(--gold); }

        .field-input {
            width: 100%;
            height: 46px;
            padding: 0 16px 0 42px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: var(--sans);
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .field-input::placeholder { color: var(--text-muted); }

        .field-input:focus {
            border-color: var(--gold);
            background: #1a1f28;
            box-shadow: 0 0 0 3px var(--gold-dim);
        }

        .field-input.is-invalid { border-color: rgba(240,79,89,0.5); }

        /* Submit button */
        .btn-submit {
            width: 100%; height: 46px;
            background: var(--gold);
            border: none; border-radius: 8px;
            font-size: 14px; font-weight: 700;
            font-family: var(--sans);
            color: #0B0E12;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(245,200,66,0.25);
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(245,200,66,0.35);
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit.loading { pointer-events: none; opacity: 0.7; }

        .btn-submit .spinner {
            display: none;
            width: 15px; height: 15px;
            border: 2px solid rgba(0,0,0,0.3);
            border-top-color: #0B0E12;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .btn-submit.loading .spinner { display: block; }
        .btn-submit.loading .btn-text { opacity: 0.6; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Info badge */
        .info-badge {
            display: flex; align-items: flex-start; gap: 10px;
            margin-top: 18px;
            padding: 12px 14px;
            background: var(--gold-dim);
            border: 1px solid rgba(245,200,66,0.25);
            border-radius: 8px;
        }

        .info-badge-icon {
            flex-shrink: 0;
            width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(245,200,66,0.12);
            border-radius: 7px;
            color: var(--gold); font-size: 14px;
        }

        .info-badge-text h5 {
            font-size: 12px; font-weight: 700;
            color: var(--gold); margin-bottom: 2px;
        }

        .info-badge-text p {
            font-size: 11px; color: var(--text-secondary); line-height: 1.4; margin: 0;
        }

        /* Back to login */
        .back-row {
            text-align: center; margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 13px; color: var(--text-secondary);
        }

        .back-row a {
            color: var(--gold); text-decoration: none;
            font-weight: 600; margin-left: 4px;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .back-row a:hover { text-decoration: underline; }

        @media (max-width: 420px) {
            .form-box { padding: 28px 22px; }
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

    <div class="bg-canvas">
        <div class="bg-grid"></div>
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>
    </div>

    <div class="page-wrap">
        <div class="form-box">

            <div class="brand">
                <img src="{{ $appConfig['app_logo']['value'] }}" alt="Logo" />
                <div class="brand-name">{{ $appConfig['app_name']['value'] }}</div>
            </div>

            <div class="form-head">
                <h1>Forgot Password?</h1>
                <p>Enter your email to receive a one-time verification code</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="ki-duotone ki-check-circle">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <div class="alert-msg">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    <i class="ki-duotone ki-cross-circle">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <div>
                        <div class="alert-error-title">Error</div>
                        <div class="alert-msg">{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="ki-duotone ki-shield-cross">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                    <div>
                        <div class="alert-error-title">Validation Error</div>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('forget-password.send-otp') }}">
                @csrf

                <div class="field">
                    <div class="field-label">
                        <span>Email Address</span>
                    </div>
                    <div class="input-wrap">
                        <div class="input-prefix">
                            <i class="ki-duotone ki-sms">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            placeholder="you@example.com"
                            autocomplete="off"
                            value="{{ old('email') }}"
                            class="field-input @error('email') is-invalid @enderror"
                            required
                        />
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <div class="spinner"></div>
                    <span class="btn-text">Send OTP Code</span>
                </button>
            </form>

            <div class="info-badge">
                <div class="info-badge-icon">
                    <i class="ki-duotone ki-information-5">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                </div>
                <div class="info-badge-text">
                    <h5>How it works</h5>
                    <p>We'll send a 6-digit OTP to your email. Use it to reset your password securely.</p>
                </div>
            </div>

            <div class="back-row">
                <a href="{{ route('login') }}">
                    <i class="ki-duotone ki-arrow-left" style="font-size:14px;">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    Back to Login
                </a>
            </div>

        </div>
    </div>

    <script>
        var hostUrl = "assets/";

        document.querySelector('form').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.querySelector('.btn-text').textContent = 'Sending...';
        });
    </script>

    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
</body>
</html>