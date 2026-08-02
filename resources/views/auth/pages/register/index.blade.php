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

        .form-head p { font-size: 12.5px; color: var(--text-secondary); }

        /* Referral banner */
        .referral-banner {
            display: flex; gap: 10px; align-items: flex-start;
            background: var(--green-dim);
            border: 1px solid rgba(28,183,96,0.25);
            border-radius: 10px; padding: 12px 14px;
            margin-bottom: 18px;
        }
        .referral-banner i { color: var(--green-dark); font-size: 15px; flex-shrink: 0; margin-top: 1px; }
        .referral-banner-title { font-size: 12px; font-weight: 700; color: var(--green-dark); margin-bottom: 2px; }
        .referral-banner-msg { font-size: 12px; color: var(--text-secondary); line-height: 1.4; }
        .referral-banner-msg strong { color: var(--green-dark); }

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
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 8px;
        }

        .field-label span {
            font-size: 12.5px; font-weight: 500;
            color: var(--text-secondary);
        }

        .field-label .label-optional {
            font-size: 11px; color: var(--text-muted);
            font-weight: 400; margin-left: 4px;
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

        .field-input.no-right { padding-right: 16px; }

        .field-input::placeholder { color: var(--text-muted); }

        .field-input:focus {
            border-color: var(--green);
            background: var(--bg-input-focus);
            box-shadow: 0 0 0 3px var(--green-dim);
        }

        .field-input.is-invalid { border-color: var(--red); background: var(--red-dim); }

        .field-input[readonly] {
            opacity: 0.75; cursor: not-allowed;
            background: var(--green-dim); border-color: rgba(28,183,96,0.25);
        }

        .toggle-pw {
            position: absolute; right: 4px; top: 0; bottom: 0;
            width: 42px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); font-size: 15px;
        }
        .toggle-pw:hover { color: var(--green-dark); }

        .field-hint { font-size: 11px; color: var(--text-muted); margin-top: 6px; }
        .field-error { font-size: 11px; color: var(--red); margin-top: 6px; }

        /* Two-col grid */
        @media (min-width: 480px) {
            .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
            .grid-2 .field { margin-bottom: 0; }
        }

        /* OTP notice */
        .otp-notice {
            display: flex; gap: 10px; align-items: flex-start;
            background: var(--green-dim);
            border: 1px solid rgba(28,183,96,0.22);
            border-radius: 12px; padding: 12px 14px; margin: 18px 0 20px;
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

        /* Sign in link */
        .signin-row {
            text-align: center; margin-top: 20px;
            font-size: 13px; color: var(--text-secondary);
        }

        .signin-row a {
            color: var(--green-dark); text-decoration: none;
            font-weight: 700; margin-left: 4px;
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
                    <p>Sign up to start trading securely on {{ $appConfig['app_name']['value'] }}</p>
                </div>
            </div>

            <div class="register-box">

                <!-- Stepper -->
                <div class="steps">
                    <div class="step active">
                        <div class="step-circle">1</div>
                        <span class="step-lbl">Register</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step pending">
                        <div class="step-circle">2</div>
                        <span class="step-lbl">Verify</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step pending">
                        <div class="step-circle">3</div>
                        <span class="step-lbl">Done</span>
                    </div>
                </div>

                <div class="form-head">
                    <h2>Create your account</h2>
                </div>

                @if ($errors->any())
                    <div class="alert-error">
                        <i class="ki-duotone ki-shield-cross alert-error-icon">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i>
                        <div>
                            <div class="alert-error-title">Registration Error</div>
                            @foreach ($errors->all() as $error)
                                <div class="alert-error-msg">{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf

                    <!-- Full Name -->
                    <div class="field">
                        <div class="field-label">
                            <span>Full Name</span>
                        </div>
                        <div class="input-wrap">
                            <input type="text" name="name" placeholder="Your full name"
                                autocomplete="name" value="{{ old('name') }}"
                                class="field-input no-right @error('name') is-invalid @enderror" required />
                        </div>
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Email & Phone -->
                    <div class="grid-2">
                        <div class="field">
                            <div class="field-label">
                                <span>Email Address</span>
                            </div>
                            <div class="input-wrap">
                                <input type="email" name="email" placeholder="you@example.com"
                                    autocomplete="email" value="{{ old('email') }}"
                                    class="field-input no-right @error('email') is-invalid @enderror" required />
                            </div>
                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <div class="field-label">
                                <span>Phone Number</span>
                            </div>
                            <div class="input-wrap">
                                <input type="text" name="phone" placeholder="08xxxxxxxxxx"
                                    autocomplete="tel" value="{{ old('phone') }}"
                                    class="field-input no-right @error('phone') is-invalid @enderror" required />
                            </div>
                            <div class="field-hint">Will be formatted to 62xxx</div>
                            @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Password & Confirm -->
                    <div class="grid-2">
                        <div class="field">
                            <div class="field-label">
                                <span>Password</span>
                            </div>
                            <div class="input-wrap">
                                <input type="password" name="password" id="pw1" placeholder="Min. 8 characters"
                                    autocomplete="new-password"
                                    class="field-input @error('password') is-invalid @enderror" required />
                                <button type="button" class="toggle-pw" onclick="togglePw('pw1','eye1')" aria-label="Toggle password">
                                    <i class="ki-duotone ki-eye" id="eye1">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                    </i>
                                </button>
                            </div>
                            @error('password')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <div class="field-label">
                                <span>Confirm Password</span>
                            </div>
                            <div class="input-wrap">
                                <input type="password" name="password_confirmation" id="pw2" placeholder="Repeat password"
                                    autocomplete="new-password" class="field-input" required />
                                <button type="button" class="toggle-pw" onclick="togglePw('pw2','eye2')" aria-label="Toggle password">
                                    <i class="ki-duotone ki-eye" id="eye2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                    </i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Referral Code (optional) -->
                    <div class="field">
                        <div class="field-label">
                            <span>Referral Code <span class="label-optional">(optional)</span></span>
                        </div>
                        <div class="input-wrap">
                            <input type="text" name="referral_code" placeholder="Enter referral code if you have one"
                                autocomplete="off"
                                value="{{ old('referral_code', $referralCode ?? '') }}"
                                class="field-input no-right @error('referral_code') is-invalid @enderror"
                                {{ $referralCode ? 'readonly' : '' }} />
                        </div>
                        <div class="field-hint">Leave blank if you weren't referred by anyone</div>
                        @error('referral_code')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <!-- OTP Notice -->
                    <div class="otp-notice">
                        <div class="otp-notice-icon">
                            <i class="ki-duotone ki-sms"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <div>
                            <div class="otp-notice-title">Email Verification Required</div>
                            <div class="otp-notice-msg">A 6-digit OTP code will be sent to your email after submitting this form.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="registerBtn">
                        <div class="spinner"></div>
                        <span class="btn-text">Continue to Verification</span>
                    </button>
                </form>

                <div class="signin-row">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in</a>
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

        function togglePw(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
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
            const btn = document.getElementById('registerBtn');
            btn.classList.add('loading');
            btn.querySelector('.btn-text').textContent = 'Sending OTP...';
        });
    </script>

    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
</body>
</html>