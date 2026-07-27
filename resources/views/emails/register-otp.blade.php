<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email Pendaftaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0A0A0A;
            color: #E5E5E5;
            padding: 40px 20px;
        }
        .email-wrapper {
            max-width: 520px;
            margin: 0 auto;
        }
        .email-card {
            background: linear-gradient(145deg, #1A1A1A, #0F0F0F);
            border: 1px solid rgba(192,192,192,0.12);
            border-radius: 20px;
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
            padding: 32px 40px;
            text-align: center;
        }
        .email-header h1 {
            font-size: 22px;
            font-weight: 800;
            color: #0A0A0A;
            letter-spacing: -0.5px;
        }
        .email-header p {
            font-size: 13px;
            color: rgba(10,10,10,0.7);
            margin-top: 4px;
        }
        .email-body {
            padding: 40px;
        }
        .greeting {
            font-size: 16px;
            color: #C0C0C0;
            margin-bottom: 16px;
        }
        .greeting strong {
            color: #FFD700;
        }
        .desc {
            font-size: 14px;
            color: #9A9A9A;
            line-height: 1.7;
            margin-bottom: 32px;
        }
        .otp-label {
            font-size: 12px;
            font-weight: 700;
            color: #9A9A9A;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
        }
        .otp-box {
            background: rgba(255, 215, 0, 0.06);
            border: 2px solid rgba(255, 215, 0, 0.3);
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            margin-bottom: 24px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 800;
            color: #FFD700;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
        }
        .expiry-notice {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 28px;
        }
        .expiry-icon {
            font-size: 18px;
            flex-shrink: 0;
        }
        .expiry-text {
            font-size: 13px;
            color: #FCA5A5;
            line-height: 1.5;
        }
        .divider {
            border: none;
            border-top: 1px solid rgba(192,192,192,0.08);
            margin: 28px 0;
        }
        .security-note {
            font-size: 12px;
            color: #9A9A9A;
            line-height: 1.6;
        }
        .security-note strong {
            color: #C0C0C0;
        }
        .email-footer {
            padding: 24px 40px;
            border-top: 1px solid rgba(192,192,192,0.08);
            text-align: center;
            font-size: 12px;
            color: #9A9A9A;
        }
        .email-footer strong {
            color: #FFD700;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <!-- Header -->
            <div class="email-header">
                <h1>PRESTIGE TRADERS</h1>
                <p>Verifikasi Email Pendaftaran</p>
            </div>

            <!-- Body -->
            <div class="email-body">
                <div class="greeting">
                    Halo, <strong>{{ $userName ?? 'User' }}</strong> 👋
                </div>

                <p class="desc">
                    Terima kasih telah mendaftar di <strong style="color: #FFD700;">PRESTIGE TRADERS</strong>.
                    Gunakan kode OTP berikut untuk memverifikasi email Anda dan menyelesaikan proses pendaftaran.
                </p>

                <div class="otp-label">Kode Verifikasi OTP</div>

                <div class="otp-box">
                    <div class="otp-code">{{ $otpCode }}</div>
                </div>

                <div class="expiry-notice">
                    <span class="expiry-icon">⏱️</span>
                    <div class="expiry-text">
                        Kode OTP ini <strong>hanya berlaku 10 menit</strong> sejak email ini dikirim.
                        Jangan bagikan kode ini kepada siapapun.
                    </div>
                </div>

                <hr class="divider">

                <div class="security-note">
                    <strong>Catatan Keamanan:</strong> Jika Anda tidak melakukan pendaftaran di PRESTIGE TRADERS,
                    abaikan email ini. Akun tidak akan dibuat jika kode OTP tidak diverifikasi.
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                &copy; {{ date('Y') }} <strong>PRESTIGE TRADERS</strong>. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>