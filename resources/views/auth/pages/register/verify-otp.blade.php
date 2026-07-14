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

        .hero-copy p strong { color: var(--green-dark); font-weight: 700; }

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
        .step.done .step-circle {
            background: var(--green-dim); border: 2px solid var(--green); color: var(--green-dark);
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
        .step.done .step-lbl { color: var(--green-dark); }
        .step.active .step-lbl { color: var(--green-dark); }
        .step.pending .step-lbl { color: var(--text-muted); }
        .step-line {
            flex: 0 0 44px; height: 1px; margin: 0 8px 17px;
            background: var(--border);
        }
        .step-line.done { background: var(--green); }

        /* Form header */
        .form-head { text-align: center; margin-bottom: 20px; }

        .form-head h2 {
            font-size: 19px; font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-head p { font-size: 12.5px; color: var(--text-secondary); line-height: 1.6; }
        .form-head p strong { color: var(--green-dark); font-weight: 700; }

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

        /* OTP Digits */
        .otp-row {
            display: flex; gap: 8px; justify-content: center;
            margin-bottom: 20px;
        }

        .otp-box {
            width: 50px; height: 60px;
            background: var(--bg-input);
            border: 1px solid transparent;
            border-radius: 12px;
            font-size: 24px; font-weight: 700;
            font-family: var(--mono);
            color: var(--green-dark);
            text-align: center;
            outline: none; caret-color: var(--green);
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s, background 0.2s;
        }

        .otp-box:focus {
            border-color: var(--green);
            background: var(--bg-input-focus);
            box-shadow: 0 0 0 3px var(--green-dim);
            transform: translateY(-2px);
        }

        .otp-box.filled {
            border-color: rgba(28,183,96,0.35);
            background: var(--green-dim);
        }

        .otp-box.is-invalid { border-color: var(--red) !important; background: var(--red-dim) !important; }

        .otp-row.shake .otp-box { animation: shake 0.45s ease; }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            15%, 55% { transform: translateX(-5px); }
            35%, 75% { transform: translateX(5px); }
        }

        .otp-error {
            text-align: center; font-size: 11px;
            color: var(--red); margin-top: -12px; margin-bottom: 16px;
        }

        /* OTP notice */
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
        .btn-submit:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }

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

        /* Resend row */
        .resend-row {
            text-align: center; margin-top: 20px;
            font-size: 13px; color: var(--text-secondary);
        }
        .resend-link {
            color: var(--green-dark); text-decoration: none; font-weight: 700; margin-left: 4px;
        }
        .resend-link:hover { text-decoration: underline; }
        .resend-link.disabled { color: var(--text-muted); pointer-events: none; }
        #countdown { color: var(--green-dark); font-family: var(--mono); font-weight: 700; }

        /* Security note */
        .security-note {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            margin-top: 16px; margin-bottom: 4px;
            font-size: 11px; color: var(--text-muted);
        }
        .security-note i { color: var(--green); font-size: 13px; }

        @media (max-width: 420px) {
            .register-box { padding: 22px 18px 20px; }
            .otp-box { width: 42px; height: 54px; font-size: 20px; border-radius: 10px; }
            .otp-row { gap: 6px; }
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
                <a href="{{ url('/register') }}" class="back-btn" aria-label="Back">
                    <i class="ki-duotone ki-arrow-left"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </div>

            <div class="hero">
                <div class="hero-logo">
                    <img src="{{ $appConfig['app_logo']['value'] }}" alt="Logo" />
                </div>
                <div class="hero-copy">
                    <h1>Verify your email</h1>
                    <p>Enter the 6-digit OTP code sent to<br><strong>{{ session('register_email') }}</strong></p>
                </div>
            </div>

            <div class="register-box">

                <!-- Stepper -->
                <div class="steps">
                    <div class="step done">
                        <div class="step-circle">
                            <i class="ki-duotone ki-check" style="font-size:12px;">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </div>
                        <span class="step-lbl">Register</span>
                    </div>
                    <div class="step-line done"></div>
                    <div class="step active">
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
                    <h2>Enter Verification Code</h2>
                    <p>Check your inbox (and spam folder) for the code</p>
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
                            <div class="alert-error-title">Verification Error</div>
                            @foreach ($errors->all() as $error)
                                <div class="alert-error-msg">{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.verify-otp.post') }}" id="otpForm">
                    @csrf

                    <div class="otp-row" id="otpRow">
                        <input class="otp-box @error('otp') is-invalid @enderror" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                        <input class="otp-box @error('otp') is-invalid @enderror" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                        <input class="otp-box @error('otp') is-invalid @enderror" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                        <input class="otp-box @error('otp') is-invalid @enderror" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                        <input class="otp-box @error('otp') is-invalid @enderror" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                        <input class="otp-box @error('otp') is-invalid @enderror" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" />
                    </div>

                    <input type="hidden" name="otp" id="otpHidden" />

                    @error('otp')
                        <div class="otp-error">{{ $message }}</div>
                    @enderror

                    <div class="otp-notice">
                        <div class="otp-notice-icon">
                            <i class="ki-duotone ki-timer"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </div>
                        <div>
                            <div class="otp-notice-title">OTP Valid for 10 Minutes</div>
                            <div class="otp-notice-msg">Verify before it expires. Check your spam folder if you don't see the email.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="verifyBtn" disabled>
                        <div class="spinner"></div>
                        <span class="btn-text">Verify &amp; Complete Registration</span>
                    </button>

                    <div class="resend-row">
                        Didn't receive the code?
                        <a href="{{ route('register.resend-otp') }}" id="resendLink" class="resend-link disabled">
                            Resend in <span id="countdown">60</span>s
                        </a>
                    </div>
                </form>

                <div class="security-note">
                    <i class="ki-duotone ki-shield-tick"><span class="path1"></span><span class="path2"></span></i>
                    <span>Protected by 256-bit SSL encryption</span>
                </div>

            </div>

        </div>
    </div>

    <script>
        var hostUrl = "assets/";

        const digits    = document.querySelectorAll('.otp-box');
        const hidden    = document.getElementById('otpHidden');
        const verifyBtn = document.getElementById('verifyBtn');
        const otpRow    = document.getElementById('otpRow');

        function syncHidden() {
            const val = Array.from(digits).map(d => d.value).join('');
            hidden.value = val;
            verifyBtn.disabled = val.length < 6;
            digits.forEach(d => d.classList.toggle('filled', d.value !== ''));
        }

        digits.forEach((digit, idx) => {
            digit.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '').slice(-1);
                if (this.value && idx < digits.length - 1) digits[idx + 1].focus();
                syncHidden();
            });

            digit.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace') {
                    if (!this.value && idx > 0) {
                        digits[idx - 1].value = '';
                        digits[idx - 1].focus();
                    }
                    syncHidden();
                }
                if (e.key === 'ArrowLeft'  && idx > 0) digits[idx - 1].focus();
                if (e.key === 'ArrowRight' && idx < digits.length - 1) digits[idx + 1].focus();
            });

            digit.addEventListener('paste', function (e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                paste.split('').slice(0, 6).forEach((c, i) => { if (digits[i]) digits[i].value = c; });
                digits[Math.min(paste.length, 5)].focus();
                syncHidden();
            });
        });

        window.addEventListener('load', () => digits[0].focus());

        document.getElementById('otpForm').addEventListener('submit', function (e) {
            syncHidden();
            if (hidden.value.length !== 6) {
                e.preventDefault();
                otpRow.classList.add('shake');
                setTimeout(() => otpRow.classList.remove('shake'), 600);
                return;
            }
            verifyBtn.classList.add('loading');
            verifyBtn.querySelector('.btn-text').textContent = 'Verifying...';
        });

        // Countdown
        const resendLink = document.getElementById('resendLink');
        const countEl    = document.getElementById('countdown');
        let   left       = 60;

        const timer = setInterval(() => {
            left--;
            countEl.textContent = left;
            if (left <= 0) {
                clearInterval(timer);
                resendLink.classList.remove('disabled');
                resendLink.innerHTML = 'Resend OTP';
            }
        }, 1000);
    </script>

    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
</body>
</html>