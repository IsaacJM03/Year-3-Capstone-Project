<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    /**
     * Store a newly created organization
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'contact_person' => 'required|string|max:255',
            'verification_doc_url' => 'nullable|string|url',
            'type' => 'required|in:receiver,donor_company',
            'csr_info' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'registration_number' => 'nullable|string',
            'website' => 'nullable|url',
        ]);

        $organization = Organization::create([
            'name' => $request->name,
            'address' => $request->address,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'contact_person' => $request->contact_person,
            'verification_doc_url' => $request->verification_doc_url,
            'type' => $request->type,
            'csr_info' => $request->csr_info,
            'verified' => false, // Default to false, admin needs to verify
        ]);

        return response()->json([
            'message' => 'Organization created successfully',
            'organization' => $organization,
        ], 201);
    }

    /**
     * Display the specified organization
     */
    public function show($id)
    {
        $organization = Organization::with(['users', 'pickups'])->findOrFail($id);

        return response()->json([
            'organization' => $organization,
        ]);
    }

    /**
     * Verify an organization (Admin only)
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'verified' => 'required|boolean',
            'verification_notes' => 'nullable|string',
        ]);

        $organization = Organization::findOrFail($id);
        
        $organization->update([
            'verified' => $request->verified,
            'verification_notes' => $request->verification_notes,
            'verified_at' => $request->verified ? now() : null,
            'verified_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Organization verification status updated',
            'organization' => $organization,
        ]);
    }
}
