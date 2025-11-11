<?php

// ============================================
// FILE: Modules/EstateManagement/app/Http/Controllers/TenantController.php
// ============================================

namespace Modules\EstateManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    /**
     * Get all tenants in an estate
     */
    public function index(Request $request, $estateId)
    {
        $query = Tenant::whereHas('property', function($q) use ($estateId) {
            $q->where('estate_id', $estateId);
        })->with(['user', 'property', 'landlord']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->has('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by landlord
        if ($request->has('landlord_id')) {
            $query->where('landlord_id', $request->landlord_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $tenants = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $tenants
        ], 200);
    }

    /**
     * Create new tenant
     */
    public function store(Request $request, $estateId)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'property_id' => 'required|exists:properties,id',
            'landlord_id' => 'required|exists:users,id',
            'move_in_date' => 'required|date',
            'lease_start_date' => 'required|date',
            'lease_end_date' => 'required|date|after:lease_start_date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'sometimes|numeric|min:0',
            'notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify property belongs to estate
        $property = Property::where('id', $request->property_id)
            ->where('estate_id', $estateId)
            ->first();

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found in this estate'
            ], 404);
        }

        // Check if property is vacant
        if ($property->status !== 'vacant') {
            return response()->json([
                'success' => false,
                'message' => 'Property is not vacant'
            ], 400);
        }

        // Check if user is already a tenant in another property
        $existingTenant = Tenant::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->exists();

        if ($existingTenant) {
            return response()->json([
                'success' => false,
                'message' => 'User is already an active tenant in another property'
            ], 400);
        }

        // Create tenant record
        $tenant = Tenant::create([
            'user_id' => $request->user_id,
            'property_id' => $request->property_id,
            'landlord_id' => $request->landlord_id,
            'move_in_date' => $request->move_in_date,
            'lease_start_date' => $request->lease_start_date,
            'lease_end_date' => $request->lease_end_date,
            'rent_amount' => $request->rent_amount,
            'deposit_amount' => $request->deposit_amount ?? 0,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        // Update property status to occupied
        $property->update([
            'status' => 'occupied',
            'is_listed' => false,
        ]);

        // Update user role and estate_id
        $user = User::find($request->user_id);
        $user->update([
            'role' => 'tenant',
            'estate_id' => $estateId,
        ]);
        $user->assignRole('tenant');

        return response()->json([
            'success' => true,
            'message' => 'Tenant created successfully',
            'data' => ['tenant' => $tenant->load(['user', 'property', 'landlord'])]
        ], 201);
    }

    /**
     * Get single tenant
     */
    public function show($estateId, $id)
    {
        $tenant = Tenant::whereHas('property', function($q) use ($estateId) {
            $q->where('estate_id', $estateId);
        })->with(['user', 'property', 'landlord'])->find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['tenant' => $tenant]
        ], 200);
    }

    /**
     * Update tenant
     */
    public function update(Request $request, $estateId, $id)
    {
        $tenant = Tenant::whereHas('property', function($q) use ($estateId) {
            $q->where('estate_id', $estateId);
        })->find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'lease_end_date' => 'sometimes|date|after:lease_start_date',
            'rent_amount' => 'sometimes|numeric|min:0',
            'deposit_amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:active,notice_given,moved_out,evicted',
            'notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $tenant->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tenant updated successfully',
            'data' => ['tenant' => $tenant]
        ], 200);
    }

    /**
     * Delete tenant
     */
    public function destroy($estateId, $id)
    {
        $tenant = Tenant::whereHas('property', function($q) use ($estateId) {
            $q->where('estate_id', $estateId);
        })->find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        $tenant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tenant deleted successfully'
        ], 200);
    }

    /**
     * Tenant gives notice to leave
     */
    public function giveNotice(Request $request, $estateId, $id)
    {
        $validator = Validator::make($request->all(), [
            'notice_date' => 'required|date',
            'notice_period_days' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $tenant = Tenant::whereHas('property', function($q) use ($estateId) {
            $q->where('estate_id', $estateId);
        })->find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        $tenant->update([
            'status' => 'notice_given',
            'notice_date' => $request->notice_date,
            'notice_period_days' => $request->notice_period_days ?? 30,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notice recorded successfully',
            'data' => ['tenant' => $tenant]
        ], 200);
    }

    /**
     * Process tenant move out
     */
    public function moveOut(Request $request, $estateId, $id)
    {
        $validator = Validator::make($request->all(), [
            'move_out_date' => 'required|date',
            'list_property' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $tenant = Tenant::whereHas('property', function($q) use ($estateId) {
            $q->where('estate_id', $estateId);
        })->with('property')->find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        // Update tenant status
        $tenant->update([
            'status' => 'moved_out',
            'move_out_date' => $request->move_out_date,
        ]);

        // Update property status
        $listProperty = $request->list_property ?? true;
        $tenant->property->update([
            'status' => 'vacant',
            'is_listed' => $listProperty,
            'available_from' => $request->move_out_date,
        ]);

        // Update user's estate_id to null
        $tenant->user->update([
            'estate_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tenant move-out processed successfully',
            'data' => [
                'tenant' => $tenant,
                'property_listed' => $listProperty
            ]
        ], 200);
    }

    /**
     * Get tenant's payment history
     */
    public function paymentHistory($estateId, $id)
    {
        $tenant = Tenant::whereHas('property', function($q) use ($estateId) {
            $q->where('estate_id', $estateId);
        })->find($id);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        $payments = \App\Models\PaymentRecord::where('user_id', $tenant->user_id)
            ->where('property_id', $tenant->property_id)
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['payments' => $payments]
        ], 200);
    }
}
