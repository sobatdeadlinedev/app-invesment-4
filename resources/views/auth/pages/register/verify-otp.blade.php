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

        .verify-box {
            width: 100%;
            max-width: 440px;
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
        .top-brand {
            display: flex; flex-direction: column; align-items: center;
            gap: 12px; margin-bottom: 24px;
        }
        .top-brand img {
            width: 44px; height: 44px; object-fit: contain;
            filter: drop-shadow(0 0 14px rgba(245,200,66,0.35));
        }
        .top-brand-name {
            font-family: var(--mono); font-size: 11px; font-weight: 700;
            letter-spacing: 2.5px; text-transform: uppercase; color: var(--gold);
        }

        /* Steps */
        .steps {
            display: flex; align-items: center; justify-content: center; margin-bottom: 28px;
        }
        .step { display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .step-circle {
            width: 26px; height: 26px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--mono); font-size: 10px; font-weight: 700;
        }
        .step.done .step-circle {
            background: rgba(22,168,121,0.15); border: 2px solid var(--green); color: var(--green);
        }
        .step.active .step-circle {
            background: var(--gold); border: 2px solid var(--gold); color: #0B0E12;
            box-shadow: 0 0 0 4px var(--gold-dim);
        }
        .step.pending .step-circle {
            background: transparent; border: 2px solid rgba(255,255,255,0.08); color: var(--text-muted);
        }
        .step-lbl { font-size: 9px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
        .step.done .step-lbl { color: var(--green); }
        .step.active .step-lbl { color: var(--gold); }
        .step.pending .step-lbl { color: var(--text-muted); }
        .step-line {
            flex: 0 0 44px; height: 1px; margin: 0 8px 17px;
            background: rgba(255,255,255,0.08);
        }
        .step-line.done { background: var(--green); }

        /* Header */
        .form-head { text-align: center; margin-bottom: 24px; }
        .form-head h1 {
            font-size: 20px; font-weight: 700; color: var(--text-primary);
            letter-spacing: -0.2px; margin-bottom: 8px;
        }
        .form-head p { font-size: 13px; color: var(--text-secondary); line-height: 1.6; }
        .form-head strong { color: var(--gold); font-weight: 600; }

        /* Alerts */
        .alert {
            display: flex; gap: 10px; align-items: flex-start;
            border-radius: 8px; padding: 12px 14px; margin-bottom: 18px;
            font-size: 13px;
        }
        .alert-success {
            background: var(--green-dim); border: 1px solid rgba(22,168,121,0.2);
            color: #6ee7b7;
        }
        .alert-success i { color: var(--green); font-size: 16px; flex-shrink: 0; }
        .alert-danger {
            background: var(--red-dim); border: 1px solid rgba(240,79,89,0.25);
            color: #fca5a5;
        }
        .alert-danger i { color: var(--red); font-size: 16px; flex-shrink: 0; }

        /* OTP Digits */
        .otp-row {
            display: flex; gap: 8px; justify-content: center;
            margin-bottom: 24px;
        }

        .otp-box {
            width: 50px; height: 60px;
            background: var(--bg-input);
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 24px; font-weight: 700;
            font-family: var(--mono);
            color: var(--gold);
            text-align: center;
            outline: none; caret-color: var(--gold);
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s, background 0.2s;
        }

        .otp-box:focus {
            border-color: var(--gold);
            background: #1a1f28;
            box-shadow: 0 0 0 3px var(--gold-dim);
            transform: translateY(-2px);
        }

        .otp-box.filled {
            border-color: rgba(245,200,66,0.4);
            background: rgba(245,200,66,0.04);
        }

        .otp-box.is-invalid { border-color: var(--red) !important; }

        .otp-row.shake .otp-box { animation: shake 0.45s ease; }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            15%, 55% { transform: translateX(-5px); }
            35%, 75% { transform: translateX(5px); }
        }

        .otp-error {
            text-align: center; font-size: 12px;
            color: #fca5a5; margin-top: -14px; margin-bottom: 16px;
        }

        /* Submit */
        .btn-verify {
            width: 100%; height: 46px;
            background: var(--gold); border: none; border-radius: 8px;
            font-size: 14px; font-weight: 700; font-family: var(--sans);
            color: #0B0E12; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 20px rgba(245,200,66,0.25);
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
        }
        .btn-verify:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(245,200,66,0.35); }
        .btn-verify:active { transform: translateY(0); }
        .btn-verify:disabled { opacity: 0.35; cursor: not-allowed; transform: none; box-shadow: none; }
        .btn-verify.loading { pointer-events: none; opacity: 0.7; }
        .spinner {
            display: none; width: 15px; height: 15px;
            border: 2px solid rgba(0,0,0,0.25); border-top-color: #0B0E12;
            border-radius: 50%; animation: spin 0.7s linear infinite;
        }
        .btn-verify.loading .spinner { display: block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Resend */
        .resend-row {
            text-align: center; margin-top: 22px; padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 13px; color: var(--text-secondary);
        }
        .resend-link {
            color: var(--gold); text-decoration: none; font-weight: 600; margin-left: 4px;
        }
        .resend-link:hover { text-decoration: underline; }
        .resend-link.disabled { color: var(--text-muted); pointer-events: none; }
        #countdown { color: var(--gold); font-family: var(--mono); font-weight: 700; }

        /* Timer info */
        .timer-info {
            display: flex; gap: 10px; align-items: flex-start;
            margin-top: 18px; padding: 12px 14px;
            background: var(--gold-dim);
            border: 1px solid rgba(245,200,66,0.25);
            border-radius: 8px;
        }
        .timer-info-icon {
            flex-shrink: 0; width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(245,200,66,0.12); border-radius: 7px;
            color: var(--gold); font-size: 14px;
        }
        .timer-info-title { font-size: 12px; font-weight: 700; color: var(--gold); margin-bottom: 2px; }
        .timer-info-msg { font-size: 11px; color: var(--text-secondary); line-height: 1.4; }

        @media (max-width: 420px) {
            .verify-box { padding: 28px 20px; }
            .otp-box { width: 42px; height: 54px; font-size: 20px; border-radius: 9px; }
            .otp-row { gap: 6px; }
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
    <div class="bg-canvas">
        <div class="bg-grid"></div>
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>
    </div>

    <div class="page-wrap">
        <div class="verify-box">

            <div class="top-brand">
                <img src="{{ $appConfig['app_logo']['value'] }}" alt="Logo" />
                <div class="top-brand-name">{{ $appConfig['app_name']['value'] }}</div>
            </div>

            <!-- Steps -->
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
                <h1>Verify your email</h1>
                <p>
                    Enter the 6-digit OTP code sent to<br>
                    <strong>{{ session('register_email') }}</strong>
                </p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="ki-duotone ki-check-circle"><span class="path1"></span><span class="path2"></span></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle"><span class="path1"></span><span class="path2"></span></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-information">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
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

                <button type="submit" class="btn-verify" id="verifyBtn" disabled>
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

            <div class="timer-info">
                <div class="timer-info-icon">
                    <i class="ki-duotone ki-timer">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                </div>
                <div>
                    <div class="timer-info-title">OTP Valid for 10 Minutes</div>
                    <div class="timer-info-msg">Verify before it expires. Check your spam folder if you don't see the email.</div>
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