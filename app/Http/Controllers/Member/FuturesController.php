<?php

namespace App\Http\Controllers\Member;

use App\Models\FuturesTrade;
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
            // ── Precious Metals ────────────────────────────────────────────────
            'XAUUSD'   => ['name' => 'Gold',               'symbol' => 'XAU/USD',   'icon' => 'bi bi-gem',               'color' => '#FFD700', 'cat' => 'metals', 'fx' => 'XAU',          'tv' => 'TVC:GOLD'],
            'XAGUSD'   => ['name' => 'Silver',             'symbol' => 'XAG/USD',   'icon' => 'bi bi-gem',               'color' => '#C0C0C0', 'cat' => 'metals', 'fx' => 'XAG',          'tv' => 'TVC:SILVER'],
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
        $recentTrades = FuturesTrade::where('user_id', $user->id)
            ->closed()
            ->latest('closed_at')
            ->limit(10)
            ->get();

        $currentPrice = $this->cachedPrice($coin);

        return view('member.pages.futures.index', compact(
            'coin', 'coinInfo', 'coins', 'currentPrice',
            'openTrade', 'recentTrades', 'user'
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
