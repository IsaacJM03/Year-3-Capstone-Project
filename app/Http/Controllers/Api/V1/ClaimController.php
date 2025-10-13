<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClaimController extends Controller
{
    /**
     * Display a listing of claims.
     */
    public function index(Request $request)
    {
        $query = Claim::with(['donation.donor', 'receiver']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $claims
        ]);
    }

    /**
     * Store a newly created claim.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donation_id' => 'required|exists:donations,id',
            'pickup_time' => 'nullable|date|after:now',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $donation = Donation::find($request->donation_id);

        if ($donation->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Donation is not available for claiming'
            ], 400);
        }

        // Check if user already has a pending claim for this donation
        $existingClaim = Claim::where('donation_id', $request->donation_id)
            ->where('receiver_id', $request->user()->id)
            ->where('status', 'pending')
            ->first();

        if ($existingClaim) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending claim for this donation'
            ], 400);
        }

        $claim = Claim::create([
            'donation_id' => $request->donation_id,
            'receiver_id' => $request->user()->id,
            'status' => 'pending',
            'pickup_time' => $request->pickup_time,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Claim submitted successfully',
            'data' => $claim->load(['donation.donor', 'receiver'])
        ], 201);
    }

    /**
     * Display the specified claim.
     */
    public function show(string $id)
    {
        $claim = Claim::with(['donation.donor', 'receiver'])->find($id);

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Claim not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $claim
        ]);
    }

    /**
     * Update the specified claim (approve/reject by donor, or update by receiver).
     */
    public function update(Request $request, string $id)
    {
        $claim = Claim::with('donation')->find($id);

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Claim not found'
            ], 404);
        }

        $user = $request->user();

        // Donor can approve/reject claims
        if ($claim->donation->donor_id === $user->id) {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:approved,rejected',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $claim->update(['status' => $request->status]);

            // Update donation status if approved
            if ($request->status === 'approved') {
                $claim->donation->update(['status' => 'claimed']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Claim ' . $request->status . ' successfully',
                'data' => $claim->load(['donation.donor', 'receiver'])
            ]);
        }

        // Receiver can update their own claim
        if ($claim->receiver_id === $user->id) {
            $validator = Validator::make($request->all(), [
                'status' => 'sometimes|in:completed',
                'pickup_time' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $claim->update($request->all());

            // Update donation status if completed
            if ($request->status === 'completed') {
                $claim->donation->update(['status' => 'completed']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Claim updated successfully',
                'data' => $claim->load(['donation.donor', 'receiver'])
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized to update this claim'
        ], 403);
    }

    /**
     * Remove the specified claim.
     */
    public function destroy(Request $request, string $id)
    {
        $claim = Claim::find($id);

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Claim not found'
            ], 404);
        }

        // Only receiver who created it or admin can delete
        if ($claim->receiver_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this claim'
            ], 403);
        }

        $claim->delete();

        return response()->json([
            'success' => true,
            'message' => 'Claim deleted successfully'
        ]);
    }

    /**
     * Get claims by authenticated receiver.
     */
    public function myClaims(Request $request)
    {
        $claims = Claim::where('receiver_id', $request->user()->id)
            ->with('donation.donor')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $claims
        ]);
    }
}
