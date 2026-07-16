<?php

namespace App\Http\Controllers\Member;

use App\Models\FuturesTrade;
use App\Models\TradingSignal;
use App\Models\SignalParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class FuturesController extends Controller
{
    const PAYOUT_RATE = 85;

    private function supportedCoins(): array
    {
        return [
            // ── Crypto ─────────────────────────────────────────────────────────
            'BTCUSDT'  => ['name' => 'Bitcoin',            'symbol' => 'BTC/USDT',  'icon' => 'bi bi-currency-bitcoin',  'color' => '#F7931A', 'cat' => 'crypto', 'cg' => 'bitcoin',      'tv' => 'BINANCE:BTCUSDT'],
            'ETHUSDT'  => ['name' => 'Ethereum',           'symbol' => 'ETH/USDT',  'icon' => 'bi bi-diamond',           'color' => '#627EEA', 'cat' => 'crypto', 'cg' => 'ethereum',     'tv' => 'BINANCE:ETHUSDT'],
            'XRPUSDT'  => ['name' => 'Ripple',             'symbol' => 'XRP/USDT',  'icon' => 'bi bi-currency-exchange', 'color' => '#346AA9', 'cat' => 'crypto', 'cg' => 'ripple',       'tv' => 'BINANCE:XRPUSDT'],
            'DOGEUSDT' => ['name' => 'Dogecoin',           'symbol' => 'DOGE/USDT', 'icon' => 'bi bi-coin',              'color' => '#C2A633', 'cat' => 'crypto', 'cg' => 'dogecoin',     'tv' => 'BINANCE:DOGEUSDT'],
            'LTCUSDT'  => ['name' => 'Litecoin',           'symbol' => 'LTC/USDT',  'icon' => 'bi bi-lightning-charge',  'color' => '#345D9D', 'cat' => 'crypto', 'cg' => 'litecoin',     'tv' => 'BINANCE:LTCUSDT'],
            'LINKUSDT' => ['name' => 'Chainlink',          'symbol' => 'LINK/USDT', 'icon' => 'bi bi-link-45deg',        'color' => '#2A5ADA', 'cat' => 'crypto', 'cg' => 'chainlink',    'tv' => 'BINANCE:LINKUSDT'],
            'DOTUSDT'  => ['name' => 'Polkadot',           'symbol' => 'DOT/USDT',  'icon' => 'bi bi-circle-fill',       'color' => '#E6007A', 'cat' => 'crypto', 'cg' => 'polkadot',     'tv' => 'BINANCE:DOTUSDT'],
            'BCHUSDT'  => ['name' => 'Bitcoin Cash',       'symbol' => 'BCH/USDT',  'icon' => 'bi bi-cash-coin',         'color' => '#8DC351', 'cat' => 'crypto', 'cg' => 'bitcoin-cash', 'tv' => 'BINANCE:BCHUSDT'],
            'FILUSDT'  => ['name' => 'Filecoin',           'symbol' => 'FIL/USDT',  'icon' => 'bi bi-database',          'color' => '#0090FF', 'cat' => 'crypto', 'cg' => 'filecoin',     'tv' => 'BINANCE:FILUSDT'],
            'DASHUSDT' => ['name' => 'Dash',               'symbol' => 'DASH/USDT', 'icon' => 'bi bi-speedometer2',      'color' => '#008CE7', 'cat' => 'crypto', 'cg' => 'dash',         'tv' => 'BINANCE:DASHUSDT'],
            'ZECUSDT'  => ['name' => 'Zcash',              'symbol' => 'ZEC/USDT',  'icon' => 'bi bi-shield-lock',       'color' => '#ECB244', 'cat' => 'crypto', 'cg' => 'zcash',        'tv' => 'BINANCE:ZECUSDT'],
            // ── Forex ──────────────────────────────────────────────────────────
            'EURUSDT'  => ['name' => 'Euro',               'symbol' => 'EUR/USD',   'icon' => 'bi bi-currency-euro',     'color' => '#003399', 'cat' => 'forex',  'fx' => 'EUR',          'tv' => 'FX:EURUSD'],
            'GBPUSDT'  => ['name' => 'British Pound',      'symbol' => 'GBP/USD',   'icon' => 'bi bi-currency-pound',    'color' => '#012169', 'cat' => 'forex',  'fx' => 'GBP',          'tv' => 'FX:GBPUSD'],
            'AUDUSDT'  => ['name' => 'Australian Dollar',  'symbol' => 'AUD/USD',   'icon' => 'bi bi-currency-dollar',   'color' => '#00008B', 'cat' => 'forex',  'fx' => 'AUD',          'tv' => 'FX:AUDUSD'],
            'NZDUSDT'  => ['name' => 'New Zealand Dollar', 'symbol' => 'NZD/USD',   'icon' => 'bi bi-currency-dollar',   'color' => '#00247D', 'cat' => 'forex',  'fx' => 'NZD',          'tv' => 'FX:NZDUSD'],
            'BRLUSDT'  => ['name' => 'Brazilian Real',     'symbol' => 'BRL/USDT',  'icon' => 'bi bi-currency-dollar',   'color' => '#009739', 'cat' => 'forex',  'fx' => 'BRL',          'tv' => 'FX:BRLUSD'],
            'TRYUSDT'  => ['name' => 'Turkish Lira',       'symbol' => 'TRY/USDT',  'icon' => 'bi bi-currency-exchange', 'color' => '#E30A17', 'cat' => 'forex',  'fx' => 'TRY',          'tv' => 'FX:TRYUSD'],
            'HKDUSD'   => ['name' => 'Hong Kong Dollar',   'symbol' => 'HKD/USD',   'icon' => 'bi bi-currency-dollar',   'color' => '#DC2626', 'cat' => 'forex',  'fx' => 'HKD',          'tv' => 'FX:HKDUSD'],
            'INRUSD'   => ['name' => 'Indian Rupee',       'symbol' => 'INR/USD',   'icon' => 'bi bi-currency-rupee',    'color' => '#FF9933', 'cat' => 'forex',  'fx' => 'INR',          'tv' => 'FX:INRUSD'],
            'KRWUSD'   => ['name' => 'Korean Won',         'symbol' => 'KRW/USD',   'icon' => 'bi bi-currency-won',      'color' => '#0047A0', 'cat' => 'forex',  'fx' => 'KRW',          'tv' => 'FX:KRWUSD'],
            'SGDUSD'   => ['name' => 'Singapore Dollar',   'symbol' => 'SGD/USD',   'icon' => 'bi bi-currency-dollar',   'color' => '#ED2939', 'cat' => 'forex',  'fx' => 'SGD',          'tv' => 'FX:SGDUSD'],
            // ── Precious Metals ────────────────────────────────────────────────
            'XAUUSD'   => ['name' => 'Gold',               'symbol' => 'XAU/USD',   'icon' => 'bi bi-gem',               'color' => '#FFD700', 'cat' => 'metals', 'fx' => 'XAU',          'tv' => 'TVC:GOLD'],
            'XAGUSD'   => ['name' => 'Silver',             'symbol' => 'XAG/USD',   'icon' => 'bi bi-gem',               'color' => '#C0C0C0', 'cat' => 'metals', 'fx' => 'XAG',          'tv' => 'TVC:SILVER'],
            'XPTUSD'   => ['name' => 'Platinum',           'symbol' => 'XPT/USD',   'icon' => 'bi bi-gem',               'color' => '#E5E4E2', 'cat' => 'metals', 'fx' => 'XPT',          'tv' => 'TVC:XPTUSD'],
        ];
    }

    public function index(Request $request)
    {
        $coins = $this->supportedCoins();
        $coin  = strtoupper($request->query('coin', 'BTCUSDT'));
        if (!isset($coins[$coin])) $coin = 'BTCUSDT';

        $user         = auth()->user();
        $coinInfo     = $coins[$coin];
        $openTrade    = FuturesTrade::where('user_id', $user->id)->open()->first();

        $currentPrice = $this->cachedPrice($coin);

        // ========================================
        // Notifikasi Sinyal Expert — SEMUA COIN.
        // Badge "Invite Me" ini SENGAJA tidak di-scope ke
        // $coin yang lagi dibuka di chart. Kalau ada signal
        // aktif untuk coin apapun (BTC, ETH, pair USDT lain,
        // dst), tetap muncul di sini walau chart yang lagi
        // dibuka BTC. Link tujuan ikut coin milik signal itu
        // sendiri (lihat $latestSignal->coin di view), bukan
        // ikut $coin yang sedang aktif di chart.
        // ========================================
        $openSignalCount = TradingSignal::open()
            ->visible()
            ->accessibleBy($user->id)
            ->count();

        $latestSignal = TradingSignal::open()
            ->visible()
            ->accessibleBy($user->id)
            ->latest()
            ->first();

        // ========================================
        // TAB: Historical Orders — GABUNGAN
        // Futures trades (60s call/put) + Signal participations
        // (ikut sinyal expert), lintas semua coin, disatukan
        // dalam satu daftar riwayat terurut waktu terbaru.
        // ========================================
        $futuresHistory = FuturesTrade::where('user_id', $user->id)
            ->closed()
            ->get()
            ->map(function ($t) use ($coins) {
                $sym   = $coins[$t->coin] ?? null;
                $isWin = $t->result === 'win';
                return (object) [
                    'source'      => 'futures',
                    'sort_time'   => $t->closed_at,
                    'coin'        => $t->coin,
                    'coin_symbol' => $sym['symbol'] ?? $t->coin,
                    'coin_name'   => $sym['name'] ?? '',
                    'coin_color'  => $sym['color'] ?? '#1890ff',
                    'title'       => ($sym['symbol'] ?? $t->coin) . ' Futures',
                    'is_win'      => $isWin,
                    'is_pending'  => false,
                    'entry_price' => $t->entry_price,
                    'close_price' => $t->close_price,
                    'bet_amount'  => $t->amount,
                    'net_result'  => $t->profit_loss,
                    'rate'        => $t->amount > 0 ? abs($t->profit_loss) / $t->amount * 100 : null,
                    'opened_at'   => $t->opened_at,
                    'closed_at'   => $t->closed_at,
                    'direction'   => $t->direction,
                ];
            });

        $signalHistory = SignalParticipant::where('user_id', $user->id)
            ->with('signal')
            ->get()
            ->map(function ($p) use ($coins) {
                $signal      = $p->signal;
                $sym         = $coins[$signal->coin] ?? null;
                $isPending   = $signal->status !== 'settled' || $signal->result === null;
                $isWin       = $signal->result === 'win';
                $netResult   = ($p->profit_loss ?? 0) - ($p->fee_amount ?? 0);
                $adminChoice = strtolower($signal->admin_choice ?? '');
                return (object) [
                    'source'      => 'signal',
                    'sort_time'   => $p->joined_at,
                    'coin'        => $signal->coin,
                    'coin_symbol' => $sym['symbol'] ?? $signal->coin,
                    'coin_name'   => $sym['name'] ?? '',
                    'coin_color'  => $sym['color'] ?? '#1890ff',
                    'title'       => $signal->title,
                    'is_win'      => $isWin,
                    'is_pending'  => $isPending,
                    'entry_price' => $signal->entry_price,
                    'close_price' => $signal->target_price,
                    'bet_amount'  => $p->bet_amount,
                    'net_result'  => $netResult,
                    'rate'        => ($signal->rate_of_return ?? 0) > 0 ? $signal->rate_of_return : null,
                    'opened_at'   => $signal->opened_at,
                    'closed_at'   => $signal->closed_at,
                    'direction'   => $adminChoice,
                ];
            });

        $allHistory = $futuresHistory->concat($signalHistory)
            ->sortByDesc('sort_time')
            ->values();

        $page = (int) $request->query('history_page', 1);
        $perPage = 10;
        $recentTrades = new \Illuminate\Pagination\LengthAwarePaginator(
            $allHistory->forPage($page, $perPage)->values(),
            $allHistory->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'history_page', 'query' => $request->query()]
        );

        return view('member.pages.futures.index', compact(
            'coin', 'coinInfo', 'coins', 'currentPrice',
            'openTrade', 'recentTrades', 'user',
            'openSignalCount', 'latestSignal'
        ));
    }

    public function open(Request $request)
    {
        $request->validate([
            'coin'      => 'required|string',
            'direction' => 'required|in:call,put',
            'amount'    => 'required|numeric|min:1',
        ]);

        $user  = auth()->user();
        $coins = $this->supportedCoins();
        $coin  = strtoupper($request->coin);

        if (!isset($coins[$coin])) {
            return response()->json(['success' => false, 'message' => 'Coin tidak valid.']);
        }

        if (FuturesTrade::where('user_id', $user->id)->open()->exists()) {
            return response()->json(['success' => false, 'message' => 'Selesaikan trade yang berjalan dulu.']);
        }

        $amount = (float) $request->amount;

        if ($user->trade_balance < $amount) {
            return response()->json(['success' => false, 'message' => 'Trade balance tidak cukup.']);
        }

        $entryPrice = $this->freshPrice($coin);

        if (!$entryPrice) {
            return response()->json(['success' => false, 'message' => 'Gagal ambil harga. Coba lagi.']);
        }

        $user->decrement('trade_balance', $amount);

        $trade = FuturesTrade::create([
            'user_id'     => $user->id,
            'coin'        => $coin,
            'direction'   => $request->direction,
            'amount'      => $amount,
            'entry_price' => $entryPrice,
            'payout_rate' => self::PAYOUT_RATE,
            'status'      => 'open',
            'opened_at'   => now(),
        ]);

        return response()->json([
            'success' => true,
            'trade'   => [
                'id'          => $trade->id,
                'coin'        => $trade->coin,
                'direction'   => $trade->direction,
                'amount'      => (float) $trade->amount,
                'entry_price' => (float) $trade->entry_price,
                'payout_rate' => (float) $trade->payout_rate,
                'opened_at'   => $trade->opened_at->toISOString(),
                'closes_at'   => $trade->opened_at->addSeconds(60)->toISOString(),
            ],
        ]);
    }

    public function close(Request $request)
    {
        $request->validate(['trade_id' => 'required|integer']);

        $user  = auth()->user();
        $trade = FuturesTrade::where('id', $request->trade_id)
            ->where('user_id', $user->id)
            ->open()
            ->first();

        if (!$trade) {
            return response()->json(['success' => false, 'message' => 'Trade tidak ditemukan.']);
        }

        if (!$trade->isExpired()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Trade belum selesai.',
                'remaining' => $trade->secondsRemaining(),
            ]);
        }

        $closePrice = $this->freshPrice($trade->coin) ?? $this->cachedPrice($trade->coin) ?? (float) $trade->entry_price;
        $trade->settle($closePrice);
        $trade->refresh();

        return response()->json([
            'success' => true,
            'result'  => [
                'outcome'     => $trade->result,
                'entry_price' => (float) $trade->entry_price,
                'close_price' => (float) $trade->close_price,
                'amount'      => (float) $trade->amount,
                'profit_loss' => (float) $trade->profit_loss,
                'new_balance' => (float) $user->fresh()->trade_balance,
            ],
        ]);
    }

    public function price($coin)
    {
        $coin  = strtoupper($coin);
        $price = $this->cachedPrice($coin);

        return response()->json(['success' => true, 'coin' => $coin, 'price' => $price]);
    }

    public function cachedPrice(string $coin): ?float
    {
        $cached = Cache::get('futures_price_' . $coin);
        if ($cached !== null) return $cached;

        $price = $this->freshPrice($coin);
        if ($price !== null) {
            Cache::put('futures_price_' . $coin, $price, 10);
        }
        return $price;
    }

    public function freshPrice(string $coin): ?float
    {
        $coins = $this->supportedCoins();
        $info  = $coins[$coin] ?? null;
        if (!$info) return null;

        // Crypto via CoinGecko
        if (isset($info['cg'])) {
            try {
                $res = Http::timeout(5)->get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids'           => $info['cg'],
                    'vs_currencies' => 'usd',
                ]);
                if ($res->successful()) {
                    $price = $res->json()[$info['cg']]['usd'] ?? null;
                    return $price ? (float) $price : null;
                }
            } catch (\Exception $e) {
                Log::error("Futures crypto price failed ({$coin}): " . $e->getMessage());
            }
            return null;
        }

        // Forex & Metals via exchangerate-api (1 / rates[currency] = price in USD)
        if (isset($info['fx'])) {
            try {
                $res = Cache::remember('fx_rates_usd', 60, function () {
                    $r = Http::timeout(5)->get('https://api.exchangerate-api.com/v4/latest/USD');
                    return $r->successful() ? ($r->json()['rates'] ?? null) : null;
                });

                if ($res && isset($res[$info['fx']]) && (float) $res[$info['fx']] > 0) {
                    return round(1.0 / (float) $res[$info['fx']], 8);
                }
            } catch (\Exception $e) {
                Log::error("Futures forex price failed ({$coin}): " . $e->getMessage());
            }
        }

        return null;
    }
}