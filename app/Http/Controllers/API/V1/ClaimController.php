<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\DonationClaim;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClaimController extends Controller
{
    /**
     * Display a listing of the user's claims
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'receiver') {
            $claims = DonationClaim::with('donation.donor')
                ->where('receiver_id', $user->id)
                ->latest()
                ->paginate(15);
        } elseif ($user->role === 'donor') {
            $claims = DonationClaim::with('donation', 'receiver')
                ->whereHas('donation', function ($query) use ($user) {
                    $query->where('donor_id', $user->id);
                })
                ->latest()
                ->paginate(15);
        } else {
            // Admin can see all claims
            $claims = DonationClaim::with('donation.donor', 'receiver')
                ->latest()
                ->paginate(15);
        }

        return response()->json([
            'success' => true,
            'message' => 'Claims retrieved successfully',
            'data' => $claims
        ], 200);
    }

    /**
     * Store a newly created claim (receiver requests to claim donation)
     */
    public function store(Request $request)
    {
        // Only receivers can claim donations
        if ($request->user()->role !== 'receiver') {
            return response()->json([
                'success' => false,
                'message' => 'Only receivers can claim donations',
                'data' => null
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'donation_id' => 'required|exists:donations,id',
            'pickup_time' => 'nullable|date|after:now',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors()
            ], 422);
        }

        $donation = Donation::find($request->donation_id);

        if ($donation->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Donation is not available for claiming',
                'data' => null
            ], 400);
        }

        $claim = DonationClaim::create([
            'donation_id' => $request->donation_id,
            'receiver_id' => $request->user()->id,
            'pickup_time' => $request->pickup_time,
            'notes' => $request->notes,
        ]);

        // Update donation status
        $donation->update(['status' => 'claimed']);

        return response()->json([
            'success' => true,
            'message' => 'Claim created successfully',
            'data' => $claim->load('donation', 'receiver')
        ], 201);
    }

    /**
     * Approve a claim (donor or admin)
     */
    public function approve(Request $request, $id)
    {
        $claim = DonationClaim::with('donation')->find($id);

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Claim not found',
                'data' => null
            ], 404);
        }

        $user = $request->user();
        
        // Only donor who owns the donation or admin can approve
        if ($claim->donation->donor_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $claim->update(['claim_status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Claim approved successfully',
            'data' => $claim
        ], 200);
    }

    /**
     * Mark claim as delivered (donor or admin)
     */
    public function deliver(Request $request, $id)
    {
        $claim = DonationClaim::with('donation')->find($id);

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Claim not found',
                'data' => null
            ], 404);
        }

        $user = $request->user();
        
        // Only donor who owns the donation or admin can mark as delivered
        if ($claim->donation->donor_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $claim->update(['claim_status' => 'delivered']);
        $claim->donation->update(['status' => 'delivered']);

        return response()->json([
            'success' => true,
            'message' => 'Claim marked as delivered successfully',
            'data' => $claim
        ], 200);
    }
}
