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
            max-width: 460px;
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
            padding: 10px 24px 4px;
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

        .hero-logo img { width: 100%; height: 100%; object-fit: contain; }

        .hero-copy h1 {
            font-size: 21px; font-weight: 800;
            line-height: 1.25; letter-spacing: -0.3px;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .hero-copy p {
            font-size: 12.5px; line-height: 1.6;
            color: var(--text-secondary);
            max-width: 320px;
            margin: 0 auto;
        }

        /* ── Card ── */
        .register-box {
            margin: 20px 16px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 26px 22px 24px;
            box-shadow: 0 20px 40px -24px rgba(20,30,25,0.18);
        }

        /* Stepper */
        .steps {
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 22px;
        }
        .step { display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .step-circle {
            width: 26px; height: 26px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--mono); font-size: 10px; font-weight: 700;
            transition: all 0.3s;
        }
        .step.active .step-circle {
            background: var(--green); color: #fff; border: 2px solid var(--green);
            box-shadow: 0 0 0 4px var(--green-dim);
        }
        .step.pending .step-circle {
            background: transparent; color: var(--text-muted);
            border: 2px solid var(--border);
        }
        .step-lbl { font-size: 9px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
        .step.active .step-lbl { color: var(--green-dark); }
        .step.pending .step-lbl { color: var(--text-muted); }
        .step-line {
            flex: 0 0 44px; height: 1px; margin: 0 8px 17px;
            background: var(--border);
        }

        /* Form header */
        .form-head { text-align: center; margin-bottom: 20px; }

        .form-head h2 {
            font-size: 19px; font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-head p { font-size: 12.5px; color: var(--text-secondary); line-height: 1.6; }

        /* Alerts */
        .alert-success {
            display: flex; gap: 10px; align-items: flex-start;
            background: var(--green-dim);
            border: 1px solid rgba(28,183,96,0.22);
            border-radius: 10px; padding: 12px 14px; margin-bottom: 18px;
            font-size: 13px; color: var(--green-dark);
        }
        .alert-success i { color: var(--green-dark); font-size: 16px; flex-shrink: 0; }

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
        .alert-error ul { margin: 0; padding-left: 16px; }
        .alert-error li.alert-error-msg { line-height: 1.6; }

        /* Fields */
        .field { margin-bottom: 16px; }

        .field-label {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 8px;
        }

        .field-label span {
            font-size: 12.5px; font-weight: 500;
            color: var(--text-secondary);
        }

        .input-wrap { position: relative; }

        .input-prefix {
            position: absolute; left: 0; top: 0; bottom: 0;
            width: 44px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted);
            font-size: 15px;
            transition: color 0.2s;
        }

        .input-wrap:focus-within .input-prefix { color: var(--green-dark); }

        .field-input {
            width: 100%;
            height: 50px;
            padding: 0 16px 0 44px;
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

        /* Notice */
        .otp-notice {
            display: flex; gap: 10px; align-items: flex-start;
            background: var(--green-dim);
            border: 1px solid rgba(28,183,96,0.22);
            border-radius: 12px; padding: 12px 14px; margin: 4px 0 20px;
        }
        .otp-notice-icon {
            flex-shrink: 0; width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(28,183,96,0.15); border-radius: 8px;
            color: var(--green-dark); font-size: 14px;
        }
        .otp-notice-title { font-size: 12px; font-weight: 700; color: var(--green-dark); margin-bottom: 2px; }
        .otp-notice-msg { font-size: 11px; color: var(--text-secondary); line-height: 1.4; }

        /* Submit button */
        .btn-submit {
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

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px -8px rgba(28,183,96,0.6);
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit.loading { pointer-events: none; opacity: 0.75; }

        .btn-submit .spinner {
            display: none;
            width: 15px; height: 15px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .btn-submit.loading .spinner { display: block; }
        .btn-submit.loading .btn-text { opacity: 0.8; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Sign in / back link row */
        .signin-row {
            text-align: center; margin-top: 20px;
            font-size: 13px; color: var(--text-secondary);
        }

        .signin-row a {
            color: var(--green-dark); text-decoration: none;
            font-weight: 700; margin-left: 4px;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .signin-row a:hover { text-decoration: underline; }

        /* Security note */
        .security-note {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            margin-top: 16px; margin-bottom: 4px;
            font-size: 11px; color: var(--text-muted);
        }
        .security-note i { color: var(--green); font-size: 13px; }

        @media (max-width: 420px) {
            .register-box { padding: 22px 18px 20px; }
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border-hover); border-radius: 3px; }
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
                <a href="{{ route('login') }}" class="back-btn" aria-label="Back">
                    <i class="ki-duotone ki-arrow-left"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </div>

            <div class="hero">
                <div class="hero-logo">
                    <img src="{{ $appConfig['app_logo']['value'] }}" alt="Logo" />
                </div>
                <div class="hero-copy">
                    <h1>Forgot Password?</h1>
                    <p>No worries, enter your email and we'll send you a code to reset it</p>
                </div>
            </div>

            <div class="register-box">

                <!-- Stepper -->
                <div class="steps">
                    <div class="step active">
                        <div class="step-circle">1</div>
                        <span class="step-lbl">Email</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step pending">
                        <div class="step-circle">2</div>
                        <span class="step-lbl">Verify</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step pending">
                        <div class="step-circle">3</div>
                        <span class="step-lbl">Reset</span>
                    </div>
                </div>

                <div class="form-head">
                    <h2>Enter Your Email</h2>
                    <p>We'll send a 6-digit OTP to verify it's you</p>
                </div>

                @if (session('success'))
                    <div class="alert-success">
                        <i class="ki-duotone ki-check-circle"><span class="path1"></span><span class="path2"></span></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert-error">
                        <i class="ki-duotone ki-shield-cross alert-error-icon">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i>
                        <div class="alert-error-msg">{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-error">
                        <i class="ki-duotone ki-shield-cross alert-error-icon">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i>
                        <div>
                            <div class="alert-error-title">Validation Error</div>
                            @foreach ($errors->all() as $error)
                                <div class="alert-error-msg">{{ $error }}</div>
                            @endforeach
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
                                <i class="ki-duotone ki-sms"><span class="path1"></span><span class="path2"></span></i>
                            </div>
                            <input type="email" name="email" placeholder="you@example.com"
                                autocomplete="off" value="{{ old('email') }}"
                                class="field-input @error('email') is-invalid @enderror" required />
                        </div>
                    </div>

                    <div class="otp-notice">
                        <div class="otp-notice-icon">
                            <i class="ki-duotone ki-information-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </div>
                        <div>
                            <div class="otp-notice-title">How it works</div>
                            <div class="otp-notice-msg">We'll send a 6-digit OTP to your email. Use it to reset your password securely.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <div class="spinner"></div>
                        <span class="btn-text">Send OTP Code</span>
                    </button>
                </form>

                <div class="signin-row">
                    Remembered your password?
                    <a href="{{ route('login') }}">
                        <i class="ki-duotone ki-arrow-left" style="font-size:12px;"><span class="path1"></span><span class="path2"></span></i>
                        Back to Login
                    </a>
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