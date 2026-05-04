<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.pages.login.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Email, username, atau nomor telepon harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        [$field, $value] = $this->resolveCredentialField($request->input('login'));

        $credentials = [
            $field     => $value,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole();
        }

        RateLimiter::hit($throttleKey, 60);

        throw ValidationException::withMessages([
            'login' => 'Email/username/nomor telepon atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Detect whether the input is an email, phone number, or username.
     */
    private function resolveCredentialField(string $input): array
    {
        $input = trim($input);

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return ['email', $input];
        }

        if (PhoneNormalizer::looksLikePhone($input)) {
            return ['phone', PhoneNormalizer::normalize($input)];
        }

        return ['username', $input];
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('login', '')) . '|' . $request->ip();
    }

    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard.index');
        }

        return redirect()->route('member.dashboard.index');
    }
}
