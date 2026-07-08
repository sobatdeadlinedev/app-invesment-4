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
            padding: 40px 20px;
            position: relative; z-index: 1;
        }

        .register-box {
            width: 100%;
            max-width: 460px;
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
            gap: 12px; margin-bottom: 24px;
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

        /* Stepper */
        .steps {
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 24px;
        }
        .step { display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .step-circle {
            width: 26px; height: 26px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--mono); font-size: 10px; font-weight: 700;
            transition: all 0.3s;
        }
        .step.active .step-circle {
            background: var(--gold); color: #0B0E12; border: 2px solid var(--gold);
            box-shadow: 0 0 0 4px var(--gold-dim);
        }
        .step.pending .step-circle {
            background: transparent; color: var(--text-muted);
            border: 2px solid rgba(255,255,255,0.08);
        }
        .step-lbl { font-size: 9px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
        .step.active .step-lbl { color: var(--gold); }
        .step.pending .step-lbl { color: var(--text-muted); }
        .step-line {
            flex: 0 0 44px; height: 1px; margin: 0 8px 17px;
            background: rgba(255,255,255,0.08);
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

        /* Referral banner */
        .referral-banner {
            display: flex; gap: 10px; align-items: flex-start;
            background: rgba(22,168,121,0.08);
            border: 1px solid rgba(22,168,121,0.2);
            border-radius: 8px; padding: 12px 14px;
            margin-bottom: 18px;
        }
        .referral-banner i { color: var(--green); font-size: 15px; flex-shrink: 0; margin-top: 1px; }
        .referral-banner-title { font-size: 12px; font-weight: 700; color: #34d399; margin-bottom: 2px; }
        .referral-banner-msg { font-size: 12px; color: var(--text-secondary); line-height: 1.4; }
        .referral-banner-msg strong { color: #6ee7b7; }

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

        .field-label .label-optional {
            font-size: 11px; color: var(--text-muted);
            font-weight: 400; margin-left: 4px;
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

        .field-input.no-right { padding-right: 16px; }

        .field-input::placeholder { color: var(--text-muted); }

        .field-input:focus {
            border-color: var(--gold);
            background: #1a1f28;
            box-shadow: 0 0 0 3px var(--gold-dim);
        }

        .field-input.is-invalid { border-color: rgba(240,79,89,0.5); }

        .field-input[readonly] {
            opacity: 0.7; cursor: not-allowed;
            background: rgba(245,200,66,0.04); border-color: rgba(245,200,66,0.25);
        }

        .toggle-pw {
            position: absolute; right: 0; top: 0; bottom: 0;
            width: 44px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); font-size: 15px;
        }
        .toggle-pw:hover { color: var(--gold); }

        .field-hint { font-size: 11px; color: var(--text-muted); margin-top: 5px; }
        .field-error { font-size: 11px; color: #fca5a5; margin-top: 5px; }

        /* Two-col grid */
        @media (min-width: 480px) {
            .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
            .grid-2 .field { margin-bottom: 0; }
        }

        /* OTP notice */
        .otp-notice {
            display: flex; gap: 10px; align-items: flex-start;
            background: var(--gold-dim);
            border: 1px solid rgba(245,200,66,0.25);
            border-radius: 8px; padding: 12px 14px; margin: 18px 0 20px;
        }
        .otp-notice-icon {
            flex-shrink: 0; width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(245,200,66,0.12); border-radius: 7px;
            color: var(--gold); font-size: 14px;
        }
        .otp-notice-title { font-size: 12px; font-weight: 700; color: var(--gold); margin-bottom: 2px; }
        .otp-notice-msg { font-size: 11px; color: var(--text-secondary); line-height: 1.4; }

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

        /* Sign in link */
        .signin-row {
            text-align: center; margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 13px; color: var(--text-secondary);
        }

        .signin-row a {
            color: var(--gold); text-decoration: none;
            font-weight: 600; margin-left: 4px;
        }
        .signin-row a:hover { text-decoration: underline; }

        /* Security note */
        .security-note {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            margin-top: 18px;
            font-size: 11px; color: var(--text-muted);
        }
        .security-note i { color: var(--green); font-size: 13px; }

        @media (max-width: 420px) {
            .register-box { padding: 28px 22px; }
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
        <div class="register-box">

            <div class="brand">
                <img src="{{ $appConfig['app_logo']['value'] }}" alt="Logo" />
                <div class="brand-name">{{ $appConfig['app_name']['value'] }}</div>
            </div>

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
                <h1>Create your account</h1>
                <p>Sign up to start trading on {{ $appConfig['app_name']['value'] }}</p>
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

            @if ($referralCode && $referrer)
                <div class="referral-banner">
                    <i class="ki-duotone ki-shield-tick">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <div>
                        <div class="referral-banner-title">Referral Code Applied</div>
                        <div class="referral-banner-msg">You were referred by <strong>{{ $referrer->name }}</strong></div>
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
                        <div class="input-prefix">
                            <i class="ki-duotone ki-user">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </div>
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
                            <div class="input-prefix">
                                <i class="ki-duotone ki-sms">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </div>
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
                            <div class="input-prefix">
                                <i class="ki-duotone ki-phone">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </div>
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
                            <div class="input-prefix">
                                <i class="ki-duotone ki-lock">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </div>
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
                            <div class="input-prefix">
                                <i class="ki-duotone ki-lock">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </div>
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
                        <div class="input-prefix">
                            <i class="ki-duotone ki-gift">
                                <span class="path1"></span><span class="path2"></span>
                                <span class="path3"></span><span class="path4"></span>
                            </i>
                        </div>
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

            <div class="security-note">
                <i class="ki-duotone ki-shield-tick"><span class="path1"></span><span class="path2"></span></i>
                <span>Protected by 256-bit SSL encryption</span>
            </div>

            <div class="signin-row">
                Already have an account?
                <a href="{{ route('login') }}">Sign in</a>
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