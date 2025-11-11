<?php

// ============================================
// FILE: Modules/EstateManagement/app/Http/Controllers/EstateController.php
// ============================================

namespace Modules\EstateManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EstateController extends Controller
{
    /**
     * Get all estates
     */
    public function index(Request $request)
    {
        $query = Estate::with(['admin'])->withCount(['properties', 'users']);

        // Filter by subscription status
        if ($request->has('subscription_status')) {
            $query->where('subscription_status', $request->subscription_status);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('uci', 'like', "%{$search}%");
            });
        }

        $estates = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $estates
        ], 200);
    }

    /**
     * Create new estate
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'sometimes|string',
            'admin_id' => 'required|exists:users,id',
            'monthly_fee' => 'sometimes|numeric|min:0',
            'description' => 'sometimes|string',
            'amenities' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $estate = Estate::create([
            'name' => $request->name,
            'uci' => 'EST-' . strtoupper(Str::random(8)),
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country ?? 'Nigeria',
            'admin_id' => $request->admin_id,
            'subscription_status' => 'trial',
            'subscription_starts_at' => now(),
            'subscription_expires_at' => now()->addDays(30), // 30-day trial
            'monthly_fee' => $request->monthly_fee ?? 0,
            'is_active' => true,
            'description' => $request->description,
            'amenities' => $request->amenities,
        ]);

        // Update admin user's estate_id and role
        $admin = \App\Models\User::find($request->admin_id);
        $admin->update([
            'estate_id' => $estate->id,
            'role' => 'estate_admin'
        ]);
        $admin->assignRole('estate_admin');

        return response()->json([
            'success' => true,
            'message' => 'Estate created successfully',
            'data' => ['estate' => $estate->load('admin')]
        ], 201);
    }

    /**
     * Get single estate
     */
    public function show($id)
    {
        $estate = Estate::with(['admin', 'properties', 'users'])
            ->withCount(['properties', 'users'])
            ->find($id);

        if (!$estate) {
            return response()->json([
                'success' => false,
                'message' => 'Estate not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['estate' => $estate]
        ], 200);
    }

    /**
     * Update estate
     */
    public function update(Request $request, $id)
    {
        $estate = Estate::find($id);

        if (!$estate) {
            return response()->json([
                'success' => false,
                'message' => 'Estate not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'state' => 'sometimes|string',
            'monthly_fee' => 'sometimes|numeric|min:0',
            'subscription_status' => 'sometimes|in:active,expired,trial,cancelled',
            'is_active' => 'sometimes|boolean',
            'description' => 'sometimes|string',
            'amenities' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $estate->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Estate updated successfully',
            'data' => ['estate' => $estate]
        ], 200);
    }

    /**
     * Delete estate
     */
    public function destroy($id)
    {
        $estate = Estate::find($id);

        if (!$estate) {
            return response()->json([
                'success' => false,
                'message' => 'Estate not found'
            ], 404);
        }

        $estate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estate deleted successfully'
        ], 200);
    }

    /**
     * Renew estate subscription
     */
    public function renewSubscription(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'months' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $estate = Estate::find($id);

        if (!$estate) {
            return response()->json([
                'success' => false,
                'message' => 'Estate not found'
            ], 404);
        }

        $currentExpiry = $estate->subscription_expires_at ?? now();
        $newExpiry = $currentExpiry->addMonths($request->months);

        $estate->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => $newExpiry,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription renewed successfully',
            'data' => [
                'estate' => $estate,
                'new_expiry_date' => $newExpiry->format('Y-m-d')
            ]
        ], 200);
    }

    /**
     * Get vacant properties in estate (for general platform)
     */
    public function getVacantProperties($id)
    {
        $estate = Estate::find($id);

        if (!$estate) {
            return response()->json([
                'success' => false,
                'message' => 'Estate not found'
            ], 404);
        }

        $properties = $estate->properties()
            ->where('status', 'vacant')
            ->where('is_listed', true)
            ->with(['landlord'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'estate' => $estate->only(['id', 'name', 'uci', 'city', 'state']),
                'properties' => $properties
            ]
        ], 200);
    }
}
