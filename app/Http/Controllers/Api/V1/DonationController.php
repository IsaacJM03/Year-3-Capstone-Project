<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    /**
     * Display a listing of donations.
     */
    public function index(Request $request)
    {
        $query = Donation::with('donor');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by food type
        if ($request->has('food_type')) {
            $query->where('food_type', 'like', '%' . $request->food_type . '%');
        }

        // Location-based filtering
        if ($request->has('latitude') && $request->has('longitude')) {
            $radius = $request->get('radius', 10);
            $query->nearby($request->latitude, $request->longitude, $radius);
        }

        $donations = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }

    /**
     * Store a newly created donation.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'food_type' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'expiry_date' => 'nullable|date|after:now',
            'pickup_address' => 'required|string',
            'pickup_latitude' => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'image_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $donation = Donation::create([
            'donor_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'food_type' => $request->food_type,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'expiry_date' => $request->expiry_date,
            'pickup_address' => $request->pickup_address,
            'pickup_latitude' => $request->pickup_latitude,
            'pickup_longitude' => $request->pickup_longitude,
            'status' => 'available',
            'image_url' => $request->image_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Donation created successfully',
            'data' => $donation->load('donor')
        ], 201);
    }

    /**
     * Display the specified donation.
     */
    public function show(string $id)
    {
        $donation = Donation::with(['donor', 'claims.receiver'])->find($id);

        if (!$donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $donation
        ]);
    }

    /**
     * Update the specified donation.
     */
    public function update(Request $request, string $id)
    {
        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found'
            ], 404);
        }

        // Only donor who created it can update
        if ($donation->donor_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this donation'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'food_type' => 'sometimes|string',
            'quantity' => 'sometimes|numeric|min:0',
            'unit' => 'sometimes|string',
            'expiry_date' => 'nullable|date',
            'pickup_address' => 'sometimes|string',
            'pickup_latitude' => 'sometimes|numeric|between:-90,90',
            'pickup_longitude' => 'sometimes|numeric|between:-180,180',
            'status' => 'sometimes|in:available,claimed,completed,expired',
            'image_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $donation->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Donation updated successfully',
            'data' => $donation->load('donor')
        ]);
    }

    /**
     * Remove the specified donation.
     */
    public function destroy(Request $request, string $id)
    {
        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found'
            ], 404);
        }

        // Only donor who created it or admin can delete
        if ($donation->donor_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this donation'
            ], 403);
        }

        $donation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Donation deleted successfully'
        ]);
    }

    /**
     * Get donations by authenticated donor.
     */
    public function myDonations(Request $request)
    {
        $donations = Donation::where('donor_id', $request->user()->id)
            ->with('claims.receiver')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }
}
