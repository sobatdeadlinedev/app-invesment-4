<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    private const OTP_TTL_MINUTES = 10;
    private const RESET_TOKEN_TTL_MINUTES = 15;

    public function showForgetPasswordForm()
    {
        return view('auth.pages.forget-password.index');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email'    => 'Format email tidak valid',
            'email.exists'   => 'Email tidak terdaftar',
        ]);

        $user = User::where('email', $request->email)->first();

        $this->dispatchOtp($request->email, $user->name);

        session()->put('reset_email', $request->email);
        session()->forget(['reset_token', 'reset_token_expires_at']);

        return redirect()->route('verify-otp')
            ->with('success', 'Kode OTP telah dikirim ke email Anda');
    }

    public function showVerifyOtpForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('forget-password')
                ->with('error', 'Silakan masukkan email terlebih dahulu');
        }

        return view('auth.pages.forget-password.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP harus diisi',
            'otp.size'     => 'Kode OTP harus 6 digit',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('forget-password')
                ->with('error', 'Sesi telah berakhir, silakan ulangi proses');
        }

        $otp = Otp::where('email', $email)
            ->where('purpose', Otp::PURPOSE_RESET_PASSWORD)
            ->where('otp', $request->otp)
            ->first();

        if (!$otp) {
            return back()->with('error', 'Kode OTP tidak valid');
        }

        if ($otp->is_used) {
            return back()->with('error', 'Kode OTP sudah pernah digunakan');
        }

        if ($otp->expired_at < Carbon::now()) {
            return back()->with('error', 'Kode OTP telah kadaluarsa');
        }

        $otp->update(['is_used' => true]);

        // Issue a short-lived reset token so reset-password page can't be
        // accessed just by knowing the email session value.
        session()->put('reset_token', Str::random(64));
        session()->put('reset_token_expires_at', now()->addMinutes(self::RESET_TOKEN_TTL_MINUTES)->timestamp);

        return redirect()->route('reset-password')
            ->with('success', 'Verifikasi berhasil, silakan reset password Anda');
    }

    public function resendOtp()
    {
        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('forget-password')
                ->with('error', 'Sesi telah berakhir, silakan masukkan email kembali');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('forget-password')
                ->with('error', 'User tidak ditemukan');
        }

        $this->dispatchOtp($email, $user->name);

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    public function showResetPasswordForm()
    {
        if (!$this->hasValidResetToken()) {
            return redirect()->route('forget-password')
                ->with('error', 'Silakan verifikasi email dan OTP terlebih dahulu');
        }

        return view('auth.pages.forget-password.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required'  => 'Password harus diisi',
            'password.min'       => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if (!$this->hasValidResetToken()) {
            return redirect()->route('forget-password')
                ->with('error', 'Sesi reset password telah berakhir, silakan ulangi proses');
        }

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('forget-password')
                ->with('error', 'User tidak ditemukan');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        session()->forget(['reset_email', 'reset_token', 'reset_token_expires_at']);

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset, silakan login dengan password baru');
    }

    private function hasValidResetToken(): bool
    {
        $token = session('reset_token');
        $expires = session('reset_token_expires_at');

        return $token && $expires && $expires > now()->timestamp && session('reset_email');
    }

    private function dispatchOtp(string $email, ?string $name): void
    {
        $otpCode = Otp::generate();

        Otp::updateOrCreate(
            ['email' => $email, 'purpose' => Otp::PURPOSE_RESET_PASSWORD],
            [
                'otp'        => $otpCode,
                'is_used'    => false,
                'expired_at' => Carbon::now()->addMinutes(self::OTP_TTL_MINUTES),
            ]
        );

        try {
            Mail::to($email)->send(new OtpMail($otpCode, $name ?? ''));
        } catch (\Throwable $e) {
            \Log::error('Failed to send reset password OTP email: ' . $e->getMessage());
        }
    }
}
