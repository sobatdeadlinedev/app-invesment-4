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

        .login-box {
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
        .form-head { text-align: center; margin-bottom: 28px; }

        .form-head h1 {
            font-size: 20px; font-weight: 700;
            color: var(--text-primary); letter-spacing: -0.2px;
            margin-bottom: 6px;
        }

        .form-head p {
            font-size: 13px; color: var(--text-secondary);
        }

        /* Error alert */
        .alert-error {
            display: flex; gap: 10px; align-items: flex-start;
            background: var(--red-dim);
            border: 1px solid rgba(240,79,89,0.25);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }

        .alert-error-icon { color: var(--red); font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-error-title { font-size: 12px; font-weight: 700; color: var(--red); margin-bottom: 2px; }
        .alert-error-msg { font-size: 12px; color: #fca5a5; line-height: 1.5; }

        /* Form fields */
        .field { margin-bottom: 16px; }

        .field-label {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 7px;
        }

        .field-label span {
            font-size: 12px; font-weight: 500;
            color: var(--text-secondary);
        }

        .field-label a {
            font-size: 12px; color: var(--gold);
            text-decoration: none; font-weight: 500;
        }
        .field-label a:hover { text-decoration: underline; }

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
            padding: 0 44px 0 42px;
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

        .toggle-pw {
            position: absolute; right: 0; top: 0; bottom: 0;
            width: 44px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); font-size: 15px;
        }
        .toggle-pw:hover { color: var(--gold); }

        /* Remember row */
        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 22px;
        }

        .custom-check {
            width: 16px; height: 16px;
            border: 1px solid var(--border-hover);
            border-radius: 4px;
            background: var(--bg-input);
            cursor: pointer; position: relative;
            flex-shrink: 0; appearance: none;
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

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(245,200,66,0.35);
        }
        .btn-login:active { transform: translateY(0); }

        .btn-login.loading {
            pointer-events: none; opacity: 0.7;
        }

        .btn-login .spinner {
            display: none;
            width: 15px; height: 15px;
            border: 2px solid rgba(0,0,0,0.3);
            border-top-color: #0B0E12;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .btn-login.loading .spinner { display: block; }
        .btn-login.loading .btn-text { opacity: 0.6; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Sign up link */
        .signup-row {
            text-align: center; margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 13px; color: var(--text-secondary);
        }

        .signup-row a {
            color: var(--gold); text-decoration: none;
            font-weight: 600; margin-left: 4px;
        }
        .signup-row a:hover { text-decoration: underline; }

        /* Security note */
        .security-note {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            margin-top: 18px;
            font-size: 11px; color: var(--text-muted);
        }

        .security-note i { color: var(--green); font-size: 13px; }

        @media (max-width: 420px) {
            .login-box { padding: 28px 22px; }
        }
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
        <div class="login-box">

            <div class="brand">
                <img src="{{ $appConfig['app_logo']['value'] }}" alt="Logo" />
                <div class="brand-name">{{ $appConfig['app_name']['value'] }}</div>
            </div>

            <div class="form-head">
                <h1>Welcome back</h1>
                <p>Sign in to your trading account</p>
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
                </button>
            </form>

            <div class="security-note">
                <i class="ki-duotone ki-shield-tick"><span class="path1"></span><span class="path2"></span></i>
                <span>Protected by 256-bit SSL encryption</span>
            </div>

            <div class="signup-row">
                Don't have an account?
                <a href="{{ route('register') }}">Create account</a>
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