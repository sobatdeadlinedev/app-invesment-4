<?php

namespace App\Http\Controllers\Member;

use App\Models\User;
use App\Models\Transaction;
use App\Models\ReferralUsage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    public function index()
    {
        $user = User::current();

        // Get multi-level team members
        $teamMembers = $this->getMultiLevelReferrals($user->id);

        // Hitung statistik per level
        $levelStats = $teamMembers->groupBy('level')->map(function ($items) {
            return [
                'count' => $items->count(),
                'members' => $items
            ];
        });

        $totalTeam = $teamMembers->count();
        $directTeam = $teamMembers->where('level', 1)->count();

        // Total commission revenue earned from referrals
        $totalRevenue = Transaction::forUser($user->id)
            ->commission()
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        // Generate referral link
        $referralLink = route('register', ['ref' => $user->refferal_code]);

        return view('member.pages.team.index', compact(
            'user',
            'teamMembers',
            'totalTeam',
            'directTeam',
            'levelStats',
            'referralLink',
            'totalRevenue'
        ));
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
            ->with(['referred' => function ($query) {
                $query->select('id', 'name', 'username', 'phone', 'email', 'created_at');
            }])
            ->get();

        foreach ($directReferrals as $referral) {
            if (!$referral->referred) continue;

            // Tambahkan info level dan referrer
            $referralData = [
                'id' => $referral->referred->id,
                'name' => $referral->referred->name,
                'username' => $referral->referred->username,
                'phone' => $referral->referred->phone,
                'email' => $referral->referred->email,
                'created_at' => $referral->referred->created_at,
                'joined_at' => $referral->used_at,
                'level' => $level,
                'referrer_id' => $userId,
                'referrer_name' => User::find($userId)->name,
                'referral_code' => $referral->referral_code
            ];

            $result->push((object) $referralData);

            // Recursive untuk level berikutnya
            $subReferrals = $this->getMultiLevelReferrals(
                $referral->referred->id,
                $level + 1,
                $processed
            );

            $result = $result->merge($subReferrals);
        }

        return $result;
    }
}
