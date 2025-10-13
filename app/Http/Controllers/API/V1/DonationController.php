<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    /**
     * Display a listing of available donations
     */
    public function index(Request $request)
    {
        $query = Donation::with('donor')->where('status', 'available');

        // Apply filters
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $donations = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Donations retrieved successfully',
            'data' => $donations
        ], 200);
    }

    /**
     * Store a newly created donation
     */
    public function store(Request $request)
    {
        // Only donors can create donations
        if ($request->user()->role !== 'donor') {
            return response()->json([
                'success' => false,
                'message' => 'Only donors can create donations',
                'data' => null
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'expiry_date' => 'required|date|after:today',
            'pickup_location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'image_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors()
            ], 422);
        }

        $donation = Donation::create([
            'donor_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'expiry_date' => $request->expiry_date,
            'pickup_location' => $request->pickup_location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'image_url' => $request->image_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Donation created successfully',
            'data' => $donation
        ], 201);
    }

    /**
     * Display the specified donation
     */
    public function show($id)
    {
        $donation = Donation::with('donor', 'claims.receiver')->find($id);

        if (!$donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Donation retrieved successfully',
            'data' => $donation
        ], 200);
    }

    /**
     * Update the specified donation
     */
    public function update(Request $request, $id)
    {
        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found',
                'data' => null
            ], 404);
        }

        // Only the donor who created it can update
        if ($donation->donor_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|required|integer|min:1',
            'unit' => 'sometimes|required|string|max:50',
            'expiry_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:available,claimed,delivered,expired',
            'pickup_location' => 'sometimes|required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'image_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors()
            ], 422);
        }

        $donation->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Donation updated successfully',
            'data' => $donation
        ], 200);
    }

    /**
     * Remove the specified donation
     */
    public function destroy(Request $request, $id)
    {
        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found',
                'data' => null
            ], 404);
        }

        // Only donor or admin can delete
        if ($donation->donor_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $donation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Donation deleted successfully',
            'data' => null
        ], 200);
    }

    /**
     * Get nearby donations based on location
     */
    public function nearby(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors()
            ], 422);
        }

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 5; // Default 5km radius

        // Using Haversine formula to calculate distance
        $donations = Donation::selectRaw("
                *,
                (6371 * acos(cos(radians(?))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians(?))
                + sin(radians(?))
                * sin(radians(latitude)))) AS distance
            ", [$lat, $lng, $lat])
            ->where('status', 'available')
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->with('donor')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Nearby donations retrieved successfully',
            'data' => $donations
        ], 200);
    }
}
