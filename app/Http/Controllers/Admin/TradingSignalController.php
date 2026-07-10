<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradingSignal;
use App\Models\SignalParticipant;
use App\Models\SignalAllowedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TradingSignalController extends Controller
{
    public function index()
    {
        $signals = TradingSignal::with(['creator', 'allowedUsers'])
            ->withCount('participants')
            ->latest()
            ->paginate(15);

        return view('admin.pages.signals.index', compact('signals'));
    }

    public function create()
    {
        $coins = TradingSignal::getAvailableCoins();

        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.pages.signals.create', compact('coins', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'coin' => 'required|string|in:' . implode(',', array_keys(TradingSignal::getAvailableCoins())),
            'description' => 'nullable|string',
            'entry_price' => 'required|numeric|min:0',
            'target_price' => 'required|numeric|min:0',
            'bet_type' => 'required|in:percentage,fixed',
            'bet_value' => 'required|numeric|min:0.01',
            'is_public' => 'nullable|boolean',
            'allowed_user_ids' => 'required_if:is_public,false|array',
            'allowed_user_ids.*' => 'exists:users,id',
            'scheduled_at' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $isPublic = $request->has('is_public') && $request->is_public;

            $scheduledAt = $request->filled('scheduled_at')
                ? \Carbon\Carbon::parse($request->scheduled_at)
                : now();

            $signal = TradingSignal::create([
                'title' => $request->title,
                'coin' => strtoupper($request->coin),
                'description' => $request->description,
                'entry_price' => $request->entry_price,
                'target_price' => $request->target_price,
                'bet_type' => $request->bet_type,
                'bet_value' => $request->bet_value,
                'is_public' => $isPublic,
                'status' => 'open',
                'created_by' => auth()->id(),
                'scheduled_at' => $scheduledAt,
            ]);

            if (!$isPublic && !empty($request->allowed_user_ids)) {
                SignalAllowedUser::syncAllowedUsers($signal->id, $request->allowed_user_ids);
            }

            DB::commit();

            Log::info('Signal created', [
                'signal_id' => $signal->id,
                'title' => $signal->title,
                'entry_price' => $signal->entry_price,
                'target_price' => $signal->target_price,
                'scheduled_at' => $signal->scheduled_at,
            ]);

            return redirect()
                ->route('admin.signals.index')
                ->with('success', 'Trading signal created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create signal failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create signal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $signal = TradingSignal::with(['creator', 'participants.user', 'allowedUsers.user'])
            ->findOrFail($id);

        $totalParticipants = $signal->participants->count();
        $totalBetAmount = $signal->participants->sum('bet_amount');

        return view('admin.pages.signals.show', compact('signal', 'totalParticipants', 'totalBetAmount'));
    }

    public function edit($id)
    {
        $signal = TradingSignal::with('allowedUsers')->findOrFail($id);

        if ($signal->status !== 'open') {
            return redirect()->route('admin.signals.index')->with('error', 'Cannot edit signal that is already closed or settled.');
        }

        $coins = TradingSignal::getAvailableCoins();

        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.pages.signals.edit', compact('signal', 'coins', 'users'));
    }

    public function update(Request $request, $id)
    {
        $signal = TradingSignal::findOrFail($id);

        if ($signal->status !== 'open') {
            return redirect()->route('admin.signals.index')->with('error', 'Cannot update signal.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'coin' => 'required|string|in:' . implode(',', array_keys(TradingSignal::getAvailableCoins())),
            'description' => 'nullable|string',
            'entry_price' => 'required|numeric|min:0',
            'target_price' => 'required|numeric|min:0',
            'bet_type' => 'required|in:percentage,fixed',
            'bet_value' => 'required|numeric|min:0.01',
            'is_public' => 'nullable|boolean',
            'allowed_user_ids' => 'required_if:is_public,false|array',
            'allowed_user_ids.*' => 'exists:users,id',
            'scheduled_at' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $isPublic = $request->has('is_public') && $request->is_public;

            $signal->update([
                'title' => $request->title,
                'coin' => strtoupper($request->coin),
                'description' => $request->description,
                'entry_price' => $request->entry_price,
                'target_price' => $request->target_price,
                'bet_type' => $request->bet_type,
                'bet_value' => $request->bet_value,
                'is_public' => $isPublic,
                'scheduled_at' => $request->filled('scheduled_at')
                    ? \Carbon\Carbon::parse($request->scheduled_at)
                    : $signal->scheduled_at,
            ]);

            if (!$isPublic && !empty($request->allowed_user_ids)) {
                SignalAllowedUser::syncAllowedUsers($signal->id, $request->allowed_user_ids);
            } else if ($isPublic) {
                SignalAllowedUser::where('signal_id', $signal->id)->delete();
            }

            DB::commit();

            return redirect()->route('admin.signals.show', $signal->id)->with('success', 'Signal updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update signal failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update signal.');
        }
    }

    /**
     * Close signal - Admin memilih CALL atau PUT
     * System akan bandingkan dengan kondisi harga sebenarnya
     */
    public function close(Request $request, $id)
    {
        Log::info('=== CLOSE SIGNAL START ===', [
            'signal_id' => $id,
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        try {
            $signal = TradingSignal::findOrFail($id);

            if ($signal->status !== 'open') {
                Log::warning('Signal status invalid', [
                    'signal_id' => $signal->id,
                    'status' => $signal->status,
                ]);

                return redirect()
                    ->route('admin.signals.index')
                    ->with('error', 'Signal is not open. Current status: ' . $signal->status);
            }

            $validated = $request->validate([
                'admin_choice' => 'required|in:call,put',
                'rate_of_return' => 'required|numeric|min:0|max:100',
            ], [
                'admin_choice.required' => 'Admin must choose CALL or PUT',
                'admin_choice.in' => 'Choice must be CALL or PUT',
                'rate_of_return.required' => 'Win rate must be filled',
                'rate_of_return.min' => 'Win rate minimum 0%',
                'rate_of_return.max' => 'Win rate maximum 100%',
            ]);

            DB::beginTransaction();

            // Tentukan kondisi harga sebenarnya
            $actualResult = ($signal->entry_price < $signal->target_price) ? 'call' : 'put';

            // Admin choice
            $adminChoice = $request->admin_choice;

            // Apakah admin benar?
            $isAdminCorrect = ($adminChoice === $actualResult);

            // Result untuk database (win = user menang, loss = user kalah)
            $result = $isAdminCorrect ? 'win' : 'loss';

            // Close signal dengan result, rate of return, dan admin choice
            $signal->closeSignal($result, $request->rate_of_return, $adminChoice);

            DB::commit();

            Log::info('=== CLOSE SIGNAL SUCCESS ===', [
                'signal_id' => $signal->id,
                'entry_price' => $signal->entry_price,
                'target_price' => $signal->target_price,
                'actual_result' => strtoupper($actualResult),
                'admin_choice' => strtoupper($adminChoice),
                'is_admin_correct' => $isAdminCorrect,
                'user_result' => $result === 'win' ? 'USERS WIN' : 'USERS LOSE',
                'rate_of_return' => $request->rate_of_return,
            ]);

            $priceDirection = $actualResult === 'call' ? 'UP (CALL)' : 'DOWN (PUT)';
            $adminChoiceText = strtoupper($adminChoice);
            $userOutcome = $isAdminCorrect ? 'USERS WIN' : 'USERS LOSE';

            return redirect()
                ->route('admin.signals.show', $signal->id)
                ->with('success', "Signal closed! Price went {$priceDirection}. Admin chose {$adminChoiceText}. Result: {$userOutcome} with {$request->rate_of_return}% rate.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'signal_id' => $id,
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== CLOSE SIGNAL FAILED ===', [
                'signal_id' => $id,
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to close signal: ' . $e->getMessage());
        }
    }

    /**
     * Settle signal
     * - result = 'win' → Users menang (get profit)
     * - result = 'loss' → Users kalah (lose bet amount)
     */
    public function settle($id)
    {
        Log::info('=== SETTLE SIGNAL START ===', ['signal_id' => $id]);

        $signal = TradingSignal::with('participants.user')->findOrFail($id);

        if ($signal->status !== 'closed') {
            Log::warning('Signal not closed yet', [
                'signal_id' => $signal->id,
                'status' => $signal->status,
            ]);

            return redirect()
                ->route('admin.signals.show', $signal->id)
                ->with('error', 'Signal must be closed before settling. Current status: ' . $signal->status);
        }

        try {
            DB::beginTransaction();

            $settledCount = 0;
            $totalRewards = 0;
            $totalLosses = 0;
            $usersWin = ($signal->result === 'win');

            foreach ($signal->participants as $participant) {
                Log::info('=== PARTICIPANT BEFORE SETTLEMENT ===', [
                    'participant_id' => $participant->id,
                    'user_id' => $participant->user_id,
                    'joined_at' => $participant->joined_at?->format('Y-m-d H:i:s'),
                    'bet_amount' => $participant->bet_amount,
                ]);

                if ($participant->isSettled()) {
                    Log::info('Participant already settled, skipping');
                    continue;
                }

                $user = $participant->user;
                $betAmount = $participant->bet_amount;

                if ($usersWin) {
                    $user->unlockBalance($betAmount);
                    $profit = $betAmount * ($signal->rate_of_return / 100);
                    $user->addTradeBalance($profit);
                    $profitLoss = $profit;
                    $totalRewards += $profit;

                    Log::info('Participant WON', [
                        'participant_id' => $participant->id,
                        'bet_amount' => $betAmount,
                        'profit' => $profit,
                    ]);
                } else {
                    $user->removeLockedBalance($betAmount);
                    $profitLoss = -$betAmount;
                    $totalLosses += $betAmount;

                    Log::info('Participant LOST', [
                        'participant_id' => $participant->id,
                        'bet_amount' => $betAmount,
                    ]);
                }

                $user->addAchievedVolume($betAmount);

                // ✅ GUNAKAN SAVE() INDIVIDUAL - BUKAN UPDATE()
                $participant->profit_loss = $profitLoss;
                $participant->fee_amount = 0;
                $participant->status = 'settled';
                $participant->settled_at = now();
                $participant->save();

                // ❌ HAPUS REFRESH - INI PENYEBAB JOINED_AT BERUBAH
                // $participant->refresh();

                Log::info('=== PARTICIPANT AFTER SETTLEMENT ===', [
                    'participant_id' => $participant->id,
                    'joined_at' => $participant->joined_at?->format('Y-m-d H:i:s'),
                    'settled_at' => $participant->settled_at?->format('Y-m-d H:i:s'),
                    'status' => $participant->status,
                ]);

                $settledCount++;
            }

            $signal->markAsSettled();

            DB::commit();

            Log::info('=== SETTLE SIGNAL SUCCESS ===', [
                'signal_id' => $signal->id,
                'total_settled' => $settledCount,
                'users_outcome' => $usersWin ? 'ALL WIN' : 'ALL LOSE',
            ]);

            if ($usersWin) {
                return redirect()
                    ->route('admin.signals.show', $signal->id)
                    ->with('success', "Signal settled! All {$settledCount} participants WON. Total rewards: " . number_format($totalRewards, 2) . " USDT.");
            } else {
                return redirect()
                    ->route('admin.signals.show', $signal->id)
                    ->with('success', "Signal settled! All {$settledCount} participants LOST. Total losses: " . number_format($totalLosses, 2) . " USDT.");
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== SETTLE SIGNAL FAILED ===', [
                'signal_id' => $signal->id,
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('admin.signals.show', $signal->id)
                ->with('error', 'Failed to settle signal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $signal = TradingSignal::withCount('participants')->findOrFail($id);

        if ($signal->participants_count > 0) {
            return redirect()
                ->route('admin.signals.index')
                ->with('error', 'Cannot delete signal with participants.');
        }

        $signal->delete();

        return redirect()
            ->route('admin.signals.index')
            ->with('success', 'Signal deleted successfully.');
    }
}