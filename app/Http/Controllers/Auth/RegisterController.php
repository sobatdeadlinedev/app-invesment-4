<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegisterOtpMail;
use App\Models\Otp;
use App\Models\ReferralUsage;
use App\Models\User;
use App\Support\PhoneNormalizer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    private const OTP_TTL_MINUTES = 10;

    /**
     * Show registration form
     */
    public function showRegistrationForm(Request $request)
    {
        $referralCode = $request->query('ref');

        $referrer = null;
        if ($referralCode) {
            $referrer = User::where('refferal_code', $referralCode)->first();
        }

        return view('auth.pages.register.index', compact('referralCode', 'referrer'));
    }

    /**
     * Handle registration - validate, store pending data in session, send OTP
     */
    public function register(Request $request)
    {
        $request->merge([
            'phone' => PhoneNormalizer::normalize((string) $request->input('phone', '')),
        ]);

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'phone'         => 'required|string|max:20|unique:users,phone|regex:/^[0-9]+$/',
            'password'      => ['required', 'confirmed', Password::min(8)],
            'referral_code' => 'nullable|string|exists:users,refferal_code',
        ], [
            'name.required'        => 'Nama lengkap harus diisi',
            'email.required'       => 'Email harus diisi',
            'email.email'          => 'Format email tidak valid',
            'email.unique'         => 'Email sudah terdaftar',
            'phone.required'       => 'Nomor telepon harus diisi',
            'phone.unique'         => 'Nomor telepon ' . $request->input('phone') . ' sudah terdaftar. Silakan gunakan nomor lain atau login jika Anda sudah memiliki akun.',
            'phone.regex'          => 'Nomor telepon hanya boleh berisi angka',
            'password.required'    => 'Password harus diisi',
            'password.confirmed'   => 'Konfirmasi password tidak cocok',
            'password.min'         => 'Password minimal 8 karakter',
            'referral_code.exists' => 'Kode referral tidak valid',
        ]);

        // Store registration data in session (pending verification).
        // Password is encrypted with APP_KEY so it never sits as plain text.
        session()->put('register_pending', [
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'password'      => Crypt::encryptString($request->password),
            'referral_code' => $request->referral_code,
        ]);

        $this->dispatchOtp($request->email, $request->name);

        session()->put('register_email', $request->email);

        return redirect()->route('register.verify-otp')
            ->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan verifikasi untuk menyelesaikan pendaftaran.');
    }

    /**
     * Show OTP verification form for register
     */
    public function showVerifyOtpForm()
    {
        if (!session('register_email') || !session('register_pending')) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran tidak ditemukan. Silakan daftar ulang.');
        }

        return view('auth.pages.register.verify-otp');
    }

    /**
     * Verify OTP and complete registration
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP harus diisi',
            'otp.size'     => 'Kode OTP harus 6 digit',
        ]);

        $email = session('register_email');
        $pendingData = session('register_pending');

        if (!$email || !$pendingData) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran telah berakhir. Silakan daftar ulang.');
        }

        $otp = Otp::where('email', $email)
            ->where('purpose', Otp::PURPOSE_REGISTER)
            ->where('otp', $request->otp)
            ->first();

        if (!$otp) {
            return back()->with('error', 'Kode OTP tidak valid');
        }

        if ($otp->is_used) {
            return back()->with('error', 'Kode OTP sudah pernah digunakan');
        }

        if ($otp->expired_at < Carbon::now()) {
            return back()->with('error', 'Kode OTP telah kadaluarsa. Silakan daftar ulang.');
        }

        $otp->update(['is_used' => true]);

        try {
            $plainPassword = Crypt::decryptString($pendingData['password']);
        } catch (\Throwable $e) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran tidak valid. Silakan daftar ulang.');
        }

        $user = User::create([
            'name'     => $pendingData['name'],
            'email'    => $pendingData['email'],
            'phone'    => $pendingData['phone'],
            'password' => Hash::make($plainPassword),
        ]);

        $user->assignRole('member');

        if (!empty($pendingData['referral_code'])) {
            $referrer = User::where('refferal_code', $pendingData['referral_code'])->first();
            if ($referrer) {
                ReferralUsage::create([
                    'referrer_id'   => $referrer->id,
                    'referred_id'   => $user->id,
                    'referral_code' => $pendingData['referral_code'],
                    'used_at'       => now(),
                ]);
            }
        }

        session()->forget(['register_email', 'register_pending']);

        Auth::login($user);

        return redirect()->route('member.dashboard.index')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '!');
    }

    /**
     * Resend OTP for registration
     */
    public function resendOtp()
    {
        $email = session('register_email');
        $pendingData = session('register_pending');

        if (!$email || !$pendingData) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran tidak ditemukan. Silakan daftar ulang.');
        }

        $this->dispatchOtp($email, $pendingData['name']);

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    private function dispatchOtp(string $email, string $name): void
    {
        $otpCode = Otp::generate();

        Otp::updateOrCreate(
            ['email' => $email, 'purpose' => Otp::PURPOSE_REGISTER],
            [
                'otp'        => $otpCode,
                'is_used'    => false,
                'expired_at' => Carbon::now()->addMinutes(self::OTP_TTL_MINUTES),
            ]
        );

        try {
            Mail::to($email)->send(new RegisterOtpMail($otpCode, $name));
        } catch (\Throwable $e) {
            // Email pengiriman gagal — biarkan controller-level handler memutuskan
            // tindakan lanjutan; di sini kita hanya log via Laravel default.
            \Log::error('Failed to send register OTP email: ' . $e->getMessage());
        }
    }
}
