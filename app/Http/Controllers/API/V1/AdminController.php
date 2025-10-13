<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationClaim;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get summary statistics
     */
    public function summary(Request $request)
    {
        $totalDonations = Donation::count();
        $activeDonors = User::where('role', 'donor')->where('verified', true)->count();
        $activeReceivers = User::where('role', 'receiver')->where('verified', true)->count();
        $deliveredDonations = Donation::where('status', 'delivered')->count();
        
        // Calculate total food saved (sum of quantities)
        $foodSaved = Donation::where('status', 'delivered')->sum('quantity');

        $summary = [
            'total_donations' => $totalDonations,
            'delivered_donations' => $deliveredDonations,
            'food_saved_units' => $foodSaved,
            'active_donors' => $activeDonors,
            'active_receivers' => $activeReceivers,
            'pending_claims' => DonationClaim::where('claim_status', 'pending')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Summary retrieved successfully',
            'data' => $summary
        ], 200);
    }

    /**
     * Get donations per category
     */
    public function donationsPerCategory(Request $request)
    {
        $donationsPerCategory = Donation::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Donations per category retrieved successfully',
            'data' => $donationsPerCategory
        ], 200);
    }
}
