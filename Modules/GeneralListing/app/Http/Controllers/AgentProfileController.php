<?php

// ============================================
// FILE: Modules/GeneralListing/app/Http/Controllers/AgentProfileController.php
// ============================================

namespace Modules\GeneralListing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AgentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgentProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = AgentProfile::with('user');

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('agency_name', 'like', "%{$search}%");
        }

        $agents = $query->orderBy('average_rating', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $agents
        ], 200);
    }

    public function verified()
    {
        $agents = AgentProfile::with('user')
            ->where('is_verified', true)
            ->orderBy('average_rating', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['agents' => $agents]
        ], 200);
    }

    public function show($id)
    {
        $agent = AgentProfile::with(['user', 'listings'])->find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['agent' => $agent]
        ], 200);
    }

    public function getMyProfile(Request $request)
    {
        $user = $request->user();
        $profile = $user->agentProfile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Agent profile not found. Please create one.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['profile' => $profile->load('listings')]
        ], 200);
    }

    public function createProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency_name' => 'sometimes|string|max:255',
            'license_number' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string',
            'office_address' => 'sometimes|string',
            'office_phone' => 'sometimes|string|max:20',
            'service_areas' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if ($user->agentProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Agent profile already exists'
            ], 400);
        }

        $user->update(['role' => 'agent']);

        if (class_exists('\Spatie\Permission\Models\Role')) {
            if (!$user->hasRole('agent')) {
                $user->assignRole('agent');
            }
        }

        $profile = AgentProfile::create([
            'user_id' => $user->id,
            'agency_name' => $request->agency_name,
            'license_number' => $request->license_number,
            'bio' => $request->bio,
            'office_address' => $request->office_address,
            'office_phone' => $request->office_phone,
            'service_areas' => $request->service_areas,
            'verification_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agent profile created successfully',
            'data' => ['profile' => $profile]
        ], 201);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $profile = $user->agentProfile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Agent profile not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'agency_name' => 'sometimes|string|max:255',
            'license_number' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string',
            'office_address' => 'sometimes|string',
            'office_phone' => 'sometimes|string|max:20',
            'service_areas' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $profile->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Agent profile updated successfully',
            'data' => ['profile' => $profile]
        ], 200);
    }

    public function requestVerification(Request $request)
    {
        $user = $request->user();
        $profile = $user->agentProfile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Agent profile not found'
            ], 404);
        }

        if ($profile->verification_status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Agent already verified'
            ], 400);
        }

        $profile->update([
            'verification_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verification request submitted'
        ], 200);
    }

    public function pendingVerification()
    {
        $agents = AgentProfile::with('user')
            ->where('verification_status', 'pending')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['agents' => $agents]
        ], 200);
    }

    public function verifyAgent(Request $request, $id)
    {
        $profile = AgentProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Agent profile not found'
            ], 404);
        }

        $profile->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_status' => 'approved',
            'verification_notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agent verified successfully',
            'data' => ['profile' => $profile]
        ], 200);
    }

    public function rejectAgent(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $profile = AgentProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Agent profile not found'
            ], 404);
        }

        $profile->update([
            'verification_status' => 'rejected',
            'verification_notes' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agent verification rejected',
            'data' => ['profile' => $profile]
        ], 200);
    }

    public function getAgentListings($id)
    {
        $agent = AgentProfile::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent profile not found'
            ], 404);
        }

        $listings = $agent->listings()
            ->where('status', 'available')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'agent' => $agent->load('user'),
                'listings' => $listings
            ]
        ], 200);
    }
}
