<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Admin Controllers
use App\Http\Controllers\Admin\TradingSignalController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Member\BalanceTransferController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Member\TeamController as MemberTeamController;
use App\Http\Controllers\Admin\ConfigController as AdminConfigController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Admin\BalanceController as AdminBalanceController;
use App\Http\Controllers\Admin\DepositController as AdminDepositController;
use App\Http\Controllers\Member\InvestController as MemberInvestController;
use App\Http\Controllers\Member\MarketController as MemberMarketController;
use App\Http\Controllers\Member\SignalController as MemberSignalController;
use App\Http\Controllers\Member\FuturesController as MemberFuturesController;

// Member Controllers
use App\Http\Controllers\Member\WalletController as MemberWalletController;
use App\Http\Controllers\Admin\ReferralController as AdminReferralController;
use App\Http\Controllers\Member\DepositController as MemberDepositController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Member\WithdrawController as MemberWithdrawController;
use App\Http\Controllers\Admin\CommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;
use App\Http\Controllers\Member\VerificationController as MemberVerificationController;

// Root Route - Auto redirect based on auth status
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->hasRole('admin')
            ? redirect()->route('admin.dashboard.index')
            : redirect()->route('member.dashboard.index');
    }
    return redirect()->route('login');
});

// routes/web.php
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');

// Guest Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.post');

    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
 
// Register - Step 2: OTP Verification
Route::get('/register/verify-otp', [RegisterController::class, 'showVerifyOtpForm'])->name('register.verify-otp');
Route::post('/register/verify-otp', [RegisterController::class, 'verifyOtp'])->name('register.verify-otp.post');
 
// Register - Resend OTP
Route::get('/register/resend-otp', [RegisterController::class, 'resendOtp'])->name('register.resend-otp');

    // Forgot password
    Route::get('/forget-password', [ForgetPasswordController::class, 'showForgetPasswordForm'])->name('forget-password');
    Route::post('/forget-password', [ForgetPasswordController::class, 'sendOtp'])->name('forget-password.send-otp');
    Route::get('/verify-otp', [ForgetPasswordController::class, 'showVerifyOtpForm'])->name('verify-otp');
    Route::post('/verify-otp', [ForgetPasswordController::class, 'verifyOtp'])->name('verify-otp.post');
    Route::get('/forget-password/resend-otp', [ForgetPasswordController::class, 'resendOtp'])->name('forget-password.resend-otp');
    Route::get('/reset-password', [ForgetPasswordController::class, 'showResetPasswordForm'])->name('reset-password');
    Route::post('/reset-password', [ForgetPasswordController::class, 'resetPassword'])->name('reset-password.post');
});

// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
    });

    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [AdminWalletController::class, 'index'])->name('index');
    });

    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [AdminTeamController::class, 'index'])->name('index');
    });

    Route::prefix('refferal')->name('refferal.')->group(function () {
        Route::get('/', [AdminReferralController::class, 'index'])->name('index');
    });

    Route::prefix('commission')->name('commission.')->group(function () {
        Route::get('/', [AdminCommissionController::class, 'index'])->name('index');
    });

    Route::prefix('deposit')->name('deposit.')->group(function () {
        Route::get('/', [AdminDepositController::class, 'index'])->name('index');
        Route::get('/{deposit}', [AdminDepositController::class, 'show'])->name('show');
        Route::post('/{deposit}/approve', [AdminDepositController::class, 'approve'])->name('approve');
        Route::post('/{deposit}/reject', [AdminDepositController::class, 'reject'])->name('reject');

        // NEW: Manual adjustment (add balance)
        Route::post('/adjustment', [AdminDepositController::class, 'adjustment'])->name('adjustment');
    });

    Route::prefix('withdrawal')->name('withdrawal.')->group(function () {
        Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
        Route::get('/{withdrawal}', [AdminWithdrawalController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/approve', [AdminWithdrawalController::class, 'approve'])->name('approve');
        Route::post('/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])->name('reject');

        // NEW: Manual deduction (reduce balance)
        Route::post('/deduction', [AdminWithdrawalController::class, 'deduction'])->name('deduction');
    });

    Route::prefix('balance')->name('balance.')->group(function () {
        Route::get('/', [AdminBalanceController::class, 'index'])->name('index');
    });

    Route::prefix('config')->name('config.')->group(function () {
        Route::get('/', [AdminConfigController::class, 'index'])->name('index');
        Route::post('/update', [AdminConfigController::class, 'update'])->name('update');
    });

    Route::prefix('verification')->name('verification.')->group(function () {
        Route::get('/', [AdminVerificationController::class, 'index'])->name('index');
        Route::get('/{verification}', [AdminVerificationController::class, 'show'])->name('show');
        Route::post('/{verification}/verify', [AdminVerificationController::class, 'verify'])->name('verify');
        Route::post('/{verification}/reject', [AdminVerificationController::class, 'reject'])->name('reject');
    });

    // Trading Signals Management
    Route::prefix('signals')->name('signals.')->group(function () {
        Route::get('/', [TradingSignalController::class, 'index'])->name('index');
        Route::get('/create', [TradingSignalController::class, 'create'])->name('create');
        Route::post('/', [TradingSignalController::class, 'store'])->name('store');
        Route::get('/{id}', [TradingSignalController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TradingSignalController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TradingSignalController::class, 'update'])->name('update');
        Route::post('/{id}/close', [TradingSignalController::class, 'close'])->name('close');
        Route::post('/{id}/settle', [TradingSignalController::class, 'settle'])->name('settle');
        Route::delete('/{id}', [TradingSignalController::class, 'destroy'])->name('destroy');
    });
});

// Member Routes
Route::prefix('member')->name('member.')->middleware(['auth', 'role:member'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [MemberDashboardController::class, 'index'])->name('index');
        Route::post('/prices', [MemberDashboardController::class, 'getPrices'])->name('prices');
        Route::post('/chart-data', [MemberDashboardController::class, 'getChartData'])->name('chart-data');
    });

    Route::prefix('market')->name('market.')->group(function () {
        Route::get('/', [MemberMarketController::class, 'index'])->name('index');
        Route::post('/prices', [MemberMarketController::class, 'getPrices'])->name('prices');
    });


    // Futures Trading (CALL/PUT 1-menit)
    Route::prefix('futures')->name('futures.')->group(function () {
        Route::get('/', [MemberFuturesController::class, 'index'])->name('index');
        Route::post('/open', [MemberFuturesController::class, 'open'])->name('open');
        Route::post('/close', [MemberFuturesController::class, 'close'])->name('close');
        Route::get('/price/{coin}', [MemberFuturesController::class, 'price'])->name('price');
    });

    // Trading Signals (di invest)
    Route::prefix('invest')->name('invest.')->group(function () {
        Route::get('/coin', [MemberInvestController::class, 'coinSignals'])->name('coin'); // All-in-one page
    });

    // Signal actions
    Route::post('/signals/{id}/join', [MemberSignalController::class, 'join'])->name('signals.join');

    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [MemberTeamController::class, 'index'])->name('index');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [MemberProfileController::class, 'index'])->name('index');
    });

    Route::prefix('deposit')->name('deposit.')->group(function () {
        Route::get('/', [MemberDepositController::class, 'index'])->name('index');
        Route::post('/store', [MemberDepositController::class, 'store'])->name('store');
        Route::get('/history', [MemberDepositController::class, 'history'])->name('history');
    });

    Route::prefix('withdraw')->name('withdraw.')->group(function () {
        Route::get('/', [MemberWithdrawController::class, 'index'])->name('index');
        Route::post('/store', [MemberWithdrawController::class, 'store'])->name('store');
        Route::get('/history', [MemberWithdrawController::class, 'history'])->name('history');
        Route::delete('/cancel/{reference}', [MemberWithdrawController::class, 'cancel'])->name('cancel');
    });

    // Verification Routes
    Route::prefix('verification')->name('verification.')->group(function () {
        Route::get('/', [MemberVerificationController::class, 'index'])->name('index');
        Route::post('/store', [MemberVerificationController::class, 'store'])->name('store');
    });

    // Wallet Routes
       Route::prefix('wallet')->name('wallet.')->group(function () {
    Route::post('/', [MemberWalletController::class, 'store'])->name('store');
    Route::put('/{id}', [MemberWalletController::class, 'update'])->name('update');
    Route::delete('/{id}', [MemberWalletController::class, 'destroy'])->name('destroy');
});

    // Balance Transfer
    Route::prefix('balance')->name('balance.')->group(function () {
        Route::get('/transfer', [BalanceTransferController::class, 'index'])->name('transfer');
        Route::post('/transfer/to-trade', [BalanceTransferController::class, 'exchangeToTrade'])->name('transfer.to-trade');
        Route::post('/transfer/to-exchange', [BalanceTransferController::class, 'tradeToExchange'])->name('transfer.to-exchange');
    });
});
