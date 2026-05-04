<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\ReferralUsage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReferralController extends Controller
{
    public function index()
    {
        $users = User::role('member')
            ->withCount('referrals')
            ->latest()
            ->get();

        // Untuk setiap user, hitung total referrals multi-level
        $users->each(function ($user) {
            $user->total_multi_level_referrals = $this->countMultiLevelReferrals($user->id);
            $user->multi_level_referrals = $this->getMultiLevelReferrals($user->id);
        });

        return view('admin.pages.team.index', compact('users'));
    }

    /**
     * Hitung total referrals dari semua level
     */
    private function countMultiLevelReferrals($userId, $counted = [])
    {
        if (in_array($userId, $counted)) {
            return 0;
        }

        $counted[] = $userId;
        $count = 0;

        // Level 1 referrals
        $directReferrals = ReferralUsage::where('referrer_id', $userId)->get();
        $count += $directReferrals->count();

        // Recursive untuk level berikutnya
        foreach ($directReferrals as $referral) {
            $count += $this->countMultiLevelReferrals($referral->referred_id, $counted);
        }

        return $count;
    }

    /**
     * Dapatkan semua referrals dengan level hierarchy
     */
    private function getMultiLevelReferrals($userId, $level = 1, $processed = [])
    {
        if (in_array($userId, $processed)) {
            return collect([]);
        }

        $processed[] = $userId;
        $result = collect([]);

        // Ambil direct referrals
        $directReferrals = ReferralUsage::where('referrer_id', $userId)
            ->with('referred')
            ->get();

        foreach ($directReferrals as $referral) {
            // Tambahkan info level
            $referralData = $referral->toArray();
            $referralData['level'] = $level;
            $referralData['referrer_name'] = User::find($userId)->name;
            $result->push($referralData);

            // Recursive untuk level berikutnya
            $subReferrals = $this->getMultiLevelReferrals(
                $referral->referred_id,
                $level + 1,
                $processed
            );

            $result = $result->merge($subReferrals);
        }

        return $result;
    }
}
