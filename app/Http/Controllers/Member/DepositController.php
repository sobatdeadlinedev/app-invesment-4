<?php

namespace App\Http\Controllers\Member;

use App\Models\Config;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotificationMail;

class DepositController extends Controller
{
    /**
     * Display the deposit form / active deposit info
     */
    public function index()
    {
        // Auto-expire deposit yang sudah lewat 1 jam
        $this->autoExpirePendingDeposits();

        // Cek apakah ada deposit pending yang masih aktif (belum expired)
        $pendingDeposit = Transaction::forUser(auth()->id())
            ->deposit()
            ->pending()
            ->latest()
            ->first();

        $walletTrc20Raw = Config::get('app_wallet_trc20', ['name' => 'TRON Network (TRC20)', 'addresses' => []]);
        $walletBep20Raw = Config::get('app_wallet_bep20', ['name' => 'Binance Smart Chain (BEP20)', 'addresses' => []]);

        // Backward-compat: key lama 'address' (string)
        if (!isset($walletTrc20Raw['addresses']) && isset($walletTrc20Raw['address'])) {
            $walletTrc20Raw['addresses'] = array_filter([$walletTrc20Raw['address']]);
        }
        if (!isset($walletBep20Raw['addresses']) && isset($walletBep20Raw['address'])) {
            $walletBep20Raw['addresses'] = array_filter([$walletBep20Raw['address']]);
        }

        $trc20Addresses = array_values(array_filter($walletTrc20Raw['addresses'] ?? []));
        $bep20Addresses = array_values(array_filter($walletBep20Raw['addresses'] ?? []));

        // ── GUARD: kalau admin belum konfigurasi wallet address sama sekali ──
        if (empty($trc20Addresses) && empty($bep20Addresses)) {
            Log::warning('Deposit wallet belum dikonfigurasi: addresses kosong', [
                'trc20_raw' => $walletTrc20Raw,
                'bep20_raw' => $walletBep20Raw,
            ]);

            return redirect()->route('member.profile.index')
                ->with('error', 'Wallet deposit belum dikonfigurasi admin. Hubungi admin.');
        }

        $sessionKey = 'deposit_wallet_' . auth()->id();

        if ($pendingDeposit) {
            // Ada pending → bersihkan session
            session()->forget($sessionKey);

            $pendingAddress = $pendingDeposit->wallet_address;

            // Tentukan jaringan dari payment_method yang tersimpan di transaksi
            $isBep20 = str_contains(strtolower($pendingDeposit->payment_method ?? ''), 'bep20')
                    || str_contains(strtolower($pendingDeposit->payment_method ?? ''), 'bsc');

            // Jika wallet_address kosong (deposit lama), ambil random dari config sesuai jaringan
            if (empty($pendingAddress)) {
                if ($isBep20) {
                    $pendingAddress = !empty($bep20Addresses) ? $bep20Addresses[array_rand($bep20Addresses)] : '';
                } else {
                    $pendingAddress = !empty($trc20Addresses) ? $trc20Addresses[array_rand($trc20Addresses)] : '';
                }

                // Simpan ke DB agar tidak berubah lagi saat refresh
                if (!empty($pendingAddress)) {
                    $pendingDeposit->update(['wallet_address' => $pendingAddress]);
                }
            }

            $walletTrc20 = [
                'name'    => $walletTrc20Raw['name'],
                'address' => !$isBep20 ? $pendingAddress : (!empty($trc20Addresses) ? $trc20Addresses[0] : ''),
            ];
            $walletBep20 = [
                'name'    => $walletBep20Raw['name'],
                'address' => $isBep20 ? $pendingAddress : (!empty($bep20Addresses) ? $bep20Addresses[0] : ''),
            ];
        } else {
            // Tidak ada pending → cek session dulu, kalau belum ada baru random
            $cached = session($sessionKey);

            // ── GUARD: session lama mungkin nyimpen address kosong (sebelum config diisi) ──
            $cachedIsValid = $cached
                && !empty($cached['trc20']['address'] ?? null)
                && !empty($cached['bep20']['address'] ?? null);

            if ($cachedIsValid) {
                $walletTrc20 = $cached['trc20'];
                $walletBep20 = $cached['bep20'];
            } else {
                $walletTrc20 = [
                    'name'    => $walletTrc20Raw['name'],
                    'address' => !empty($trc20Addresses) ? $trc20Addresses[array_rand($trc20Addresses)] : '',
                ];
                $walletBep20 = [
                    'name'    => $walletBep20Raw['name'],
                    'address' => !empty($bep20Addresses) ? $bep20Addresses[array_rand($bep20Addresses)] : '',
                ];

                session([$sessionKey => [
                    'trc20' => $walletTrc20,
                    'bep20' => $walletBep20,
                ]]);
            }
        }

        $user            = auth()->user();
        $exchangeBalance = $user->exchange_balance;
        $tradeBalance    = $user->trade_balance;
        $userBalance     = $user->exchange_balance + $user->trade_balance;

        return view('member.pages.deposit.index', compact(
            'walletTrc20',
            'walletBep20',
            'exchangeBalance',
            'tradeBalance',
            'userBalance',
            'pendingDeposit'
        ));
    }

    /**
     * Process deposit request — no file upload, generate timer 1 jam
     */
    public function store(Request $request)
    {
        // Auto-expire dulu sebelum cek pending
        $this->autoExpirePendingDeposits();

        // Guard: tolak kalau masih ada pending yang aktif
        $hasPending = Transaction::forUser(auth()->id())
            ->deposit()
            ->pending()
            ->exists();

        if ($hasPending) {
            return redirect()
                ->route('member.deposit.index')
                ->with('error', 'Anda masih memiliki deposit yang sedang diproses. Tunggu hingga selesai atau batalkan terlebih dahulu.');
        }

        $request->validate([
            'amount'         => 'required|numeric|min:100',
            'wallet_type'    => 'required|in:trc20,bep20',
            'wallet_address' => 'required|string',
        ], [
            'amount.required'      => __('app.amount_required'),
            'amount.min'           => __('app.minimum_deposit_alert'),
            'wallet_type.required' => __('app.wallet_type_required'),
            'wallet_type.in'       => __('app.wallet_type_invalid'),
            'wallet_address.required' => 'Alamat wallet kosong/tidak valid. Silakan refresh halaman deposit dan coba lagi.',
        ]);

        try {
            DB::beginTransaction();

            $reference       = Transaction::generateReference('DEP');
            $depositAmount   = $request->amount;
            $walletTypeLabel = $request->wallet_type === 'trc20' ? 'TRC20 (TRON)' : 'BEP20 (BSC)';
            $expiredAt       = now()->addHour(); // Timer 1 jam

            $transaction = Transaction::create([
                'user_id'        => auth()->id(),
                'reference'      => $reference,
                'amount'         => $depositAmount,
                'total_amount'   => $depositAmount,
                'type'           => 'deposit',
                'balance_type'   => 'exchange',
                'wallet_id'      => null,
                'withdrawal_fee' => null,
                'source_user_id' => null,
                'status'         => 'pending',
                'payment_method' => $walletTypeLabel,
                'wallet_address' => $request->wallet_address,
                'payment_proof'  => null,
                'approved_by'    => null,
                'expired_at'     => $expiredAt,
            ]);

            // Kirim notifikasi email ke admin
            $this->sendAdminNotification($transaction);

            DB::commit();

            // Bersihkan session wallet
            session()->forget('deposit_wallet_' . auth()->id());

            return redirect()
                ->route('member.deposit.index')
                ->with('success', 'Deposit berhasil dikonfirmasi! Silakan transfer ke alamat yang ditampilkan. Referensi: ' . $reference);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Deposit failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat deposit. Silakan coba lagi.');
        }
    }

    /**
     * Member batalkan deposit sendiri (hanya jika masih pending)
     */
    public function cancel($id)
    {
        $deposit = Transaction::forUser(auth()->id())
            ->deposit()
            ->pending()
            ->findOrFail($id);

        $deposit->update([
            'status' => 'cancelled',
        ]);

        // Bersihkan session supaya bisa deposit baru
        session()->forget('deposit_wallet_' . auth()->id());

        return redirect()
            ->route('member.deposit.index')
            ->with('success', 'Deposit telah dibatalkan. Anda dapat membuat deposit baru.');
    }

    /**
     * Auto-expire deposit yang sudah lewat waktu expired_at
     * Dipanggil di awal index() dan store()
     */
    private function autoExpirePendingDeposits()
    {
        Transaction::forUser(auth()->id())
            ->deposit()
            ->pending()
            ->where('expired_at', '<', now())
            ->whereNotNull('expired_at')
            ->update(['status' => 'expired']);
    }

    /**
     * Send email notification to admin
     */
    private function sendAdminNotification($transaction)
    {
        try {
            $adminEmail = Config::get('app_email')['value'] ?? null;

            if (!$adminEmail) {
                Log::warning('Admin email not configured');
                return;
            }

            $user = $transaction->user;

            $data = [
                'reference'      => $transaction->reference,
                'amount'         => $transaction->amount,
                'payment_method' => $transaction->payment_method,
                'wallet_address' => $transaction->wallet_address,
                'expired_at'     => $transaction->expired_at
                    ? $transaction->expired_at->format('d M Y H:i')
                    : '-',
                'created_at'     => $transaction->created_at->format('d M Y H:i'),
            ];

            Mail::to($adminEmail)->send(new AdminNotificationMail(
                'deposit',
                $data,
                $user->name,
                $user->email
            ));
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification email: ' . $e->getMessage());
        }
    }

    /**
     * Show deposit history
     */
    public function history()
    {
        // Auto-expire saat buka riwayat juga
        $this->autoExpirePendingDeposits();

        $transactions = Transaction::forUser(auth()->id())
            ->deposit()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $pendingCount = Transaction::forUser(auth()->id())
            ->deposit()
            ->pending()
            ->count();

        $completedCount = Transaction::forUser(auth()->id())
            ->deposit()
            ->whereIn('status', ['approved', 'completed'])
            ->count();

        return view('member.pages.deposit.history', compact('transactions', 'pendingCount', 'completedCount'));
    }

    public function getPendingCount()
    {
        // Auto-expire dulu
        $this->autoExpirePendingDeposits();

        $count = Transaction::forUser(auth()->id())
            ->deposit()
            ->pending()
            ->count();

        return response()->json([
            'success' => true,
            'count'   => $count,
        ]);
    }
}