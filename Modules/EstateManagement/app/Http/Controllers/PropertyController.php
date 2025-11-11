<?php
// ============================================
// FILE: Modules/EstateManagement/app/Http/Controllers/PropertyController.php
// ============================================

namespace Modules\EstateManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Estate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    /**
     * Get all properties in an estate
     */
    public function index(Request $request, $estateId)
    {
        $estate = Estate::find($estateId);

        if (!$estate) {
            return response()->json([
                'success' => false,
                'message' => 'Estate not found'
            ], 404);
        }

        $query = Property::where('estate_id', $estateId)
            ->with(['landlord', 'currentTenant.user']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by landlord
        if ($request->has('landlord_id')) {
            $query->where('landlord_id', $request->landlord_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('property_number', 'like', "%{$search}%")
                  ->orWhere('street', 'like', "%{$search}%");
            });
        }

        $properties = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $properties
        ], 200);
    }

    /**
     * Create new property
     */
    public function store(Request $request, $estateId)
    {
        $estate = Estate::find($estateId);

        if (!$estate) {
            return response()->json([
                'success' => false,
                'message' => 'Estate not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'landlord_id' => 'required|exists:users,id',
            'property_number' => 'required|string',
            'street' => 'sometimes|string',
            'property_type' => 'required|in:apartment,duplex,bungalow,flat,penthouse,studio',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'rent_period' => 'sometimes|in:monthly,quarterly,bi-annually,annually',
            'description' => 'sometimes|string',
            'size_sqm' => 'sometimes|numeric|min:0',
            'features' => 'sometimes|array',
            'floor_number' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate property number in estate
        $exists = Property::where('estate_id', $estateId)
            ->where('property_number', $request->property_number)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Property number already exists in this estate'
            ], 422);
        }

        $property = Property::create([
            'estate_id' => $estateId,
            'landlord_id' => $request->landlord_id,
            'property_number' => $request->property_number,
            'street' => $request->street,
            'property_type' => $request->property_type,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'rent_amount' => $request->rent_amount,
            'rent_period' => $request->rent_period ?? 'annually',
            'status' => 'vacant',
            'description' => $request->description,
            'size_sqm' => $request->size_sqm,
            'features' => $request->features,
            'floor_number' => $request->floor_number,
            'available_from' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property created successfully',
            'data' => ['property' => $property->load('landlord')]
        ], 201);
    }

    /**
     * Get single property
     */
    public function show($estateId, $id)
    {
        $property = Property::where('estate_id', $estateId)
            ->with(['estate', 'landlord', 'currentTenant.user', 'maintenanceRequests'])
            ->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['property' => $property]
        ], 200);
    }

    /**
     * Update property
     */
    public function update(Request $request, $estateId, $id)
    {
        $property = Property::where('estate_id', $estateId)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'property_number' => 'sometimes|string',
            'street' => 'sometimes|string',
            'property_type' => 'sometimes|in:apartment,duplex,bungalow,flat,penthouse,studio',
            'bedrooms' => 'sometimes|integer|min:0',
            'bathrooms' => 'sometimes|integer|min:0',
            'rent_amount' => 'sometimes|numeric|min:0',
            'rent_period' => 'sometimes|in:monthly,quarterly,bi-annually,annually',
            'status' => 'sometimes|in:occupied,vacant,maintenance,reserved',
            'is_listed' => 'sometimes|boolean',
            'description' => 'sometimes|string',
            'size_sqm' => 'sometimes|numeric|min:0',
            'features' => 'sometimes|array',
            'floor_number' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $property->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Property updated successfully',
            'data' => ['property' => $property]
        ], 200);
    }

    /**
     * Delete property
     */
    public function destroy($estateId, $id)
    {
        $property = Property::where('estate_id', $estateId)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // Check if property has active tenants
        if ($property->tenants()->where('status', 'active')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete property with active tenants'
            ], 400);
        }

        $property->delete();

        return response()->json([
            'success' => true,
            'message' => 'Property deleted successfully'
        ], 200);
    }

    /**
     * Toggle property listing status
     */
    public function toggleListing(Request $request, $estateId, $id)
    {
        $property = Property::where('estate_id', $estateId)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $property->update([
            'is_listed' => !$property->is_listed
        ]);

        return response()->json([
            'success' => true,
            'message' => $property->is_listed ? 'Property listed on platform' : 'Property removed from listing',
            'data' => ['property' => $property]
        ], 200);
    }
}
