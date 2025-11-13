<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Estate;
use App\Models\Landlord;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Property::with(['estate', 'landlord', 'activeTenants']);

        // Apply filters if any
        if (request('estate')) {
            $query->where('estate_id', request('estate'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('type')) {
            $query->where('type', request('type'));
        }

        if (request('property_type')) {
            $query->where('property_type', request('property_type'));
        }

        if (request('landlord_id')) {
            $query->where('landlord_id', request('landlord_id'));
        }

        $properties = $query->latest()->paginate(15);
        $estates = Estate::where('is_active', true)->orderBy('name')->get();

        // Get landlords for the filter dropdown
        $landlords = Landlord::with('user')->get();

        return view('admin.properties.index', compact('properties', 'estates', 'landlords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $estates = Estate::where('is_active', true)->orderBy('name')->get();

        // Get landlords for the dropdown
        $landlords = Landlord::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.properties.create', compact('estates', 'landlords'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'estate_id' => 'required|exists:estates,id',
            'landlord_id' => 'nullable|exists:landlords,id', // Landlord is optional
            'property_name' => 'required|string|max:255',
            'property_type' => 'required|string',
            'units' => 'required|numeric|min:1',
            'bedrooms_per_unit' => 'nullable|numeric',
            'bathrooms_per_unit' => 'nullable|numeric',
            'size_sqm' => 'nullable|numeric',
            'size_unit' => 'nullable|string',
            'street' => 'nullable|string',
            'street_name' => 'nullable|string',
            'street_number' => 'nullable|string',
            'rent_amount_per_unit' => 'required|numeric',
            'rent_period' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'features' => 'nullable|array',
            'floor_number' => 'nullable|numeric',
            'available_from' => 'nullable|date',
            'is_listed' => 'nullable|boolean',
            // Legacy fields
            'old_property_number' => 'nullable|string',
            'old_bedrooms' => 'nullable|numeric',
            'old_bathrooms' => 'nullable|numeric',
            'size' => 'nullable|numeric',
            'old_rent_amount' => 'nullable|numeric',
        ]);

        $property = Property::create($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        $property->load(['estate', 'landlord', 'tenants.user', 'maintenanceRequests']);

        return view('admin.properties.show', compact('property'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        $estates = Estate::where('is_active', true)->orderBy('name')->get();

        // Get landlords for the dropdown
        $landlords = Landlord::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.properties.edit', compact('property', 'estates', 'landlords'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'estate_id' => 'required|exists:estates,id',
            'landlord_id' => 'nullable|exists:landlords,id', // Landlord is optional
            'property_name' => 'required|string|max:255',
            'property_type' => 'required|string',
            'units' => 'required|numeric|min:1',
            'bedrooms_per_unit' => 'nullable|numeric',
            'bathrooms_per_unit' => 'nullable|numeric',
            'size_sqm' => 'nullable|numeric',
            'size_unit' => 'nullable|string',
            'street' => 'nullable|string',
            'street_name' => 'nullable|string',
            'street_number' => 'nullable|string',
            'rent_amount_per_unit' => 'required|numeric',
            'rent_period' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'features' => 'nullable|array',
            'floor_number' => 'nullable|numeric',
            'available_from' => 'nullable|date',
            'is_listed' => 'nullable|boolean',
            // Legacy fields
            'old_property_number' => 'nullable|string',
            'old_bedrooms' => 'nullable|numeric',
            'old_bathrooms' => 'nullable|numeric',
            'size' => 'nullable|numeric',
            'old_rent_amount' => 'nullable|numeric',
        ]);

        $property->update($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        // Check if property has active tenants
        if ($property->tenants()->where('status', 'active')->count() > 0) {
            return redirect()->route('admin.properties.index')
                ->with('error', 'Cannot delete property with active tenants. Please move or delete tenants first.');
        }

        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property deleted successfully.');
    }
}
