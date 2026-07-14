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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green: #1CB760;
            --green-dark: #159A50;
            --green-dim: rgba(28,183,96,0.10);
            --bg: #F4F6F8;
            --bg-card: #FFFFFF;
            --bg-input: #F0F2F5;
            --bg-input-focus: #FFFFFF;
            --border: #E4E8ED;
            --border-hover: #CBD3DC;
            --text-primary: #17181A;
            --text-secondary: #6B7280;
            --text-muted: #9AA2AD;
            --red: #E5484D;
            --red-dim: rgba(229,72,77,0.08);
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

        .page-wrap {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .phone-shell {
            width: 100%;
            max-width: 440px;
            min-height: 100vh;
            background: var(--bg);
            position: relative;
            overflow: hidden;
        }

        /* ── Top bar ── */
        .top-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 20px 0;
        }

        .back-btn {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-primary);
            font-size: 18px;
            background: none; border: none; cursor: pointer;
            text-decoration: none;
        }

        /* ── Hero ── */
        .hero {
            padding: 10px 24px 8px;
            display: flex; flex-direction: column; align-items: center;
            text-align: center;
            gap: 14px;
        }

        .hero-logo {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            padding: 10px;
            box-shadow: 0 10px 24px -12px rgba(20,30,25,0.25);
        }

        .hero-logo img {
            width: 100%; height: 100%; object-fit: contain;
        }

        .hero-copy h1 {
            font-size: 21px; font-weight: 800;
            line-height: 1.25; letter-spacing: -0.3px;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .hero-copy p {
            font-size: 12.5px; line-height: 1.6;
            color: var(--text-secondary);
            max-width: 320px;
            margin: 0 auto;
        }

        /* ── Card ── */
        .login-box {
            margin: 22px 16px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px 22px 24px;
            box-shadow: 0 20px 40px -24px rgba(20,30,25,0.18);
        }

        .form-head { text-align: center; margin-bottom: 20px; }

        .form-head h2 {
            font-size: 19px; font-weight: 700;
            color: var(--text-primary);
        }

        /* Tabs (mail / phone) — decorative, matches original single "login" field */
        .method-tabs {
            display: flex;
            background: var(--bg-input);
            border-radius: 999px;
            padding: 4px;
            margin-bottom: 22px;
        }

        .method-tab {
            flex: 1;
            text-align: center;
            padding: 9px 0;
            font-size: 13px; font-weight: 600;
            color: var(--text-secondary);
            border-radius: 999px;
            cursor: default;
        }

        .method-tab.active {
            background: var(--green);
            color: #fff;
            box-shadow: 0 6px 14px -6px rgba(28,183,96,0.55);
        }

        /* Error alert */
        .alert-error {
            display: flex; gap: 10px; align-items: flex-start;
            background: var(--red-dim);
            border: 1px solid rgba(229,72,77,0.2);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 18px;
        }

        .alert-error-icon { color: var(--red); font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-error-title { font-size: 12px; font-weight: 700; color: var(--red); margin-bottom: 2px; }
        .alert-error-msg { font-size: 12px; color: #b93338; line-height: 1.5; }

        /* Form fields */
        .field { margin-bottom: 16px; }

        .field-label {
            display: block;
            font-size: 12.5px; font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .input-wrap { position: relative; }

        .field-input {
            width: 100%;
            height: 50px;
            padding: 0 44px 0 16px;
            background: var(--bg-input);
            border: 1px solid transparent;
            border-radius: 12px;
            font-size: 14px;
            font-family: var(--sans);
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .field-input::placeholder { color: var(--text-muted); }

        .field-input:focus {
            border-color: var(--green);
            background: var(--bg-input-focus);
            box-shadow: 0 0 0 3px var(--green-dim);
        }

        .field-input.is-invalid { border-color: var(--red); background: var(--red-dim); }

        .toggle-pw {
            position: absolute; right: 4px; top: 0; bottom: 0;
            width: 42px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); font-size: 15px;
        }
        .toggle-pw:hover { color: var(--green-dark); }

        /* Remember + forgot row */
        .remember-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px;
        }

        .remember-left { display: flex; align-items: center; gap: 8px; }

        .custom-check {
            width: 17px; height: 17px;
            border: 1.5px solid var(--border-hover);
            border-radius: 5px;
            background: var(--bg-input);
            cursor: pointer; position: relative;
            flex-shrink: 0; appearance: none;
        }

        .custom-check:checked {
            background: var(--green);
            border-color: var(--green);
        }

        .custom-check:checked::after {
            content: '';
            position: absolute; top: 2px; left: 5px;
            width: 4px; height: 8px;
            border: 2px solid #fff;
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }

        .remember-label {
            font-size: 12.5px; color: var(--text-secondary);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 12.5px; color: var(--text-secondary);
            text-decoration: none;
        }
        .forgot-link:hover { color: var(--green-dark); text-decoration: underline; }

        /* Submit button */
        .btn-login {
            width: 100%; height: 52px;
            background: var(--green);
            border: none; border-radius: 999px;
            font-size: 15px; font-weight: 700;
            font-family: var(--sans);
            color: #fff;
            cursor: pointer;
            box-shadow: 0 10px 24px -8px rgba(28,183,96,0.55);
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px -8px rgba(28,183,96,0.6);
        }
        .btn-login:active { transform: translateY(0); }

        .btn-login.loading { pointer-events: none; opacity: 0.75; }

        .btn-login .spinner {
            display: none;
            width: 15px; height: 15px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .btn-login.loading .spinner { display: block; }
        .btn-login.loading .btn-text { opacity: 0.8; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Sign up link */
        .signup-row {
            text-align: center; margin-top: 20px;
            font-size: 13px; color: var(--text-secondary);
        }

        .signup-row a {
            color: var(--green-dark); text-decoration: none;
            font-weight: 700; margin-left: 4px;
        }
        .signup-row a:hover { text-decoration: underline; }

        /* Security note */
        .security-note {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            margin-top: 16px; margin-bottom: 4px;
            font-size: 11px; color: var(--text-muted);
        }

        .security-note i { color: var(--green); font-size: 13px; }

        @media (max-width: 420px) {
            .login-box { padding: 24px 18px 20px; }
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

    <div class="page-wrap">
        <div class="phone-shell">

            <div class="top-bar">
                <a href="{{ url('/') }}" class="back-btn" aria-label="Back">
                    <i class="ki-duotone ki-arrow-left"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </div>

            <div class="hero">
                <div class="hero-logo">
                    <img src="{{ $appConfig['app_logo']['value'] }}" alt="Logo" />
                </div>
                <div class="hero-copy">
                    <h1>{{ $appConfig['app_name']['value'] }}</h1>
                    <p>Providing secure and reliable digital asset trading and account management services for members worldwide.</p>
                </div>
            </div>

            <div class="login-box">

                <div class="form-head">
                    <h2>Log In</h2>
                </div>

                <div class="method-tabs">
                    <div class="method-tab active">Account</div>
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
                        <label class="field-label" for="loginInput">Email</label>
                        <div class="input-wrap">
                            <input
                                type="text"
                                name="login"
                                id="loginInput"
                                placeholder="you@example.com"
                                autocomplete="username"
                                value="{{ old('login') }}"
                                class="field-input @error('login') is-invalid @enderror"
                                required
                            />
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label" for="pwInput">Password</label>
                        <div class="input-wrap">
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
                        <div class="remember-left">
                            <input type="checkbox" name="remember" id="rememberMe" class="custom-check" />
                            <label for="rememberMe" class="remember-label">Remember my password</label>
                        </div>
                        <a href="{{ route('forget-password') }}" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <div class="spinner"></div>
                        <span class="btn-text">Submit</span>
                    </button>
                </form>

                <div class="signup-row">
                    Don't have an account?
                    <a href="{{ route('register') }}">Sign up now</a>
                </div>

                <div class="security-note">
                    <i class="ki-duotone ki-shield-tick"><span class="path1"></span><span class="path2"></span></i>
                    <span>Protected by 256-bit SSL encryption</span>
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