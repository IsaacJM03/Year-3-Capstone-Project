<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Claim;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get overall system statistics
     */
    public function statistics(Request $request)
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'donors' => User::where('role', 'donor')->count(),
                'receivers' => User::where('role', 'receiver')->count(),
                'admins' => User::where('role', 'admin')->count(),
            ],
            'donations' => [
                'total' => Donation::count(),
                'available' => Donation::where('status', 'available')->count(),
                'claimed' => Donation::where('status', 'claimed')->count(),
                'completed' => Donation::where('status', 'completed')->count(),
                'expired' => Donation::where('status', 'expired')->count(),
            ],
            'claims' => [
                'total' => Claim::count(),
                'pending' => Claim::where('status', 'pending')->count(),
                'approved' => Claim::where('status', 'approved')->count(),
                'rejected' => Claim::where('status', 'rejected')->count(),
                'completed' => Claim::where('status', 'completed')->count(),
            ],
            'campaigns' => [
                'total' => Campaign::count(),
                'active' => Campaign::where('status', 'active')->count(),
                'draft' => Campaign::where('status', 'draft')->count(),
                'completed' => Campaign::where('status', 'completed')->count(),
                'cancelled' => Campaign::where('status', 'cancelled')->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get donation statistics by food type
     */
    public function donationsByFoodType(Request $request)
    {
        $donations = Donation::select('food_type', DB::raw('count(*) as count'), DB::raw('sum(quantity) as total_quantity'))
            ->groupBy('food_type')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }

    /**
     * Get top donors
     */
    public function topDonors(Request $request)
    {
        $limit = $request->get('limit', 10);

        $topDonors = User::where('role', 'donor')
            ->withCount('donations')
            ->orderBy('donations_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'email']);

        return response()->json([
            'success' => true,
            'data' => $topDonors
        ]);
    }

    /**
     * Get top receivers
     */
    public function topReceivers(Request $request)
    {
        $limit = $request->get('limit', 10);

        $topReceivers = User::where('role', 'receiver')
            ->withCount(['claims' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('claims_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'email']);

        return response()->json([
            'success' => true,
            'data' => $topReceivers
        ]);
    }

    /**
     * Get donations over time
     */
    public function donationsOverTime(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        $format = match($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m',
        };

        $donations = Donation::select(
            DB::raw("DATE_FORMAT(created_at, '$format') as period"),
            DB::raw('count(*) as count')
        )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }

    /**
     * Get user-specific report (for donors/receivers)
     */
    public function userReport(Request $request)
    {
        $user = $request->user();
        
        $report = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ];

        if ($user->role === 'donor') {
            $report['donations'] = [
                'total' => $user->donations()->count(),
                'available' => $user->donations()->where('status', 'available')->count(),
                'claimed' => $user->donations()->where('status', 'claimed')->count(),
                'completed' => $user->donations()->where('status', 'completed')->count(),
                'expired' => $user->donations()->where('status', 'expired')->count(),
            ];
            $report['recent_donations'] = $user->donations()
                ->latest()
                ->limit(5)
                ->get(['id', 'title', 'status', 'created_at']);
        }

        if ($user->role === 'receiver') {
            $report['claims'] = [
                'total' => $user->claims()->count(),
                'pending' => $user->claims()->where('status', 'pending')->count(),
                'approved' => $user->claims()->where('status', 'approved')->count(),
                'rejected' => $user->claims()->where('status', 'rejected')->count(),
                'completed' => $user->claims()->where('status', 'completed')->count(),
            ];
            $report['campaigns'] = [
                'total' => $user->campaigns()->count(),
                'active' => $user->campaigns()->where('status', 'active')->count(),
            ];
            $report['recent_claims'] = $user->claims()
                ->with('donation:id,title')
                ->latest()
                ->limit(5)
                ->get(['id', 'donation_id', 'status', 'created_at']);
        }

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
}
