<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    /**
     * Display a listing of campaigns
     */
    public function index(Request $request)
    {
        $query = Campaign::with('creator');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $campaigns = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Campaigns retrieved successfully',
            'data' => $campaigns
        ], 200);
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors()
            ], 422);
        }

        $campaign = Campaign::create([
            'title' => $request->title,
            'description' => $request->description,
            'creator_id' => $request->user()->id,
            'goal_amount' => $request->goal_amount,
            'deadline' => $request->deadline,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaign created successfully',
            'data' => $campaign
        ], 201);
    }

    /**
     * Display the specified campaign
     */
    public function show($id)
    {
        $campaign = Campaign::with('creator')->find($id);

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Campaign retrieved successfully',
            'data' => $campaign
        ], 200);
    }

    /**
     * Update the specified campaign
     */
    public function update(Request $request, $id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign not found',
                'data' => null
            ], 404);
        }

        // Only the creator can update
        if ($campaign->creator_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'goal_amount' => 'sometimes|required|numeric|min:0',
            'deadline' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:active,completed,expired',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors()
            ], 422);
        }

        $campaign->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Campaign updated successfully',
            'data' => $campaign
        ], 200);
    }

    /**
     * Remove the specified campaign
     */
    public function destroy(Request $request, $id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign not found',
                'data' => null
            ], 404);
        }

        // Only creator or admin can delete
        if ($campaign->creator_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campaign deleted successfully',
            'data' => null
        ], 200);
    }

    /**
     * Donate to a campaign
     */
    public function donate(Request $request, $id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign not found',
                'data' => null
            ], 404);
        }

        if ($campaign->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Campaign is not active',
                'data' => null
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors()
            ], 422);
        }

        $campaign->raised_amount += $request->amount;
        
        // Check if goal is reached
        if ($campaign->raised_amount >= $campaign->goal_amount) {
            $campaign->status = 'completed';
        }
        
        $campaign->save();

        return response()->json([
            'success' => true,
            'message' => 'Donation to campaign successful',
            'data' => $campaign
        ], 200);
    }
}
