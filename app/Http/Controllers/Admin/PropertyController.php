<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Estate;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties
     */
    public function index()
    {
        $properties = Property::with('estate')
            ->withCount('activeTenants')
            ->latest()
            ->paginate(15);

        return view('admin.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new property
     */
    public function create()
    {
        $estates = Estate::where('is_active', true)->get();
        return view('admin.properties.create', compact('estates'));
    }

    /**
     * Store a newly created property
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'estate_id' => 'required|exists:estates,id',
            'property_name' => 'required|string|max:255',
            'property_number' => 'required|string|max:50',
            'property_type' => 'required|string|in:apartment,duplex,bungalow,flat,penthouse,studio',
            'type' => 'required|in:residential,commercial',
            'units' => 'required|integer|min:1',
            'bedrooms' => 'nullable|integer|min:0',
            'bedrooms_per_unit' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'bathrooms_per_unit' => 'nullable|integer|min:0',
            'size' => 'nullable|numeric|min:0',
            'size_sqm' => 'nullable|numeric|min:0',
            'size_unit' => 'nullable|string|in:sqm,sqft,plot',
            'street' => 'nullable|string|max:255',
            'street_name' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:50',
            'rent_amount' => 'required|numeric|min:0',
            'rent_amount_per_unit' => 'nullable|numeric|min:0',
            'rent_period' => 'nullable|string|in:monthly,quarterly,bi-annually,annually',
            'status' => 'required|in:available,occupied,vacant,maintenance,reserved',
            'description' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'features' => 'nullable|array',
            'floor_number' => 'nullable|integer',
            'available_from' => 'nullable|date',
            'is_listed' => 'nullable|boolean',
        ]);

        Property::create($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property created successfully!');
    }

    /**
     * Display the specified property
     */
    public function show(Property $property)
    {
        $property->load(['estate', 'activeTenants']);
        return view('admin.properties.show', compact('property'));
    }

    /**
     * Show the form for editing the specified property
     */
    public function edit(Property $property)
    {
        $estates = Estate::where('is_active', true)->get();
        return view('admin.properties.edit', compact('property', 'estates'));
    }

    /**
     * Update the specified property
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'estate_id' => 'required|exists:estates,id',
            'property_name' => 'required|string|max:255',
            'property_number' => 'required|string|max:50',
            'property_type' => 'required|string|in:apartment,duplex,bungalow,flat,penthouse,studio',
            'type' => 'required|in:residential,commercial',
            'units' => 'required|integer|min:1',
            'bedrooms' => 'nullable|integer|min:0',
            'bedrooms_per_unit' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'bathrooms_per_unit' => 'nullable|integer|min:0',
            'size' => 'nullable|numeric|min:0',
            'size_sqm' => 'nullable|numeric|min:0',
            'size_unit' => 'nullable|string|in:sqm,sqft,plot',
            'street' => 'nullable|string|max:255',
            'street_name' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:50',
            'rent_amount' => 'required|numeric|min:0',
            'rent_amount_per_unit' => 'nullable|numeric|min:0',
            'rent_period' => 'nullable|string|in:monthly,quarterly,bi-annually,annually',
            'status' => 'required|in:available,occupied,vacant,maintenance,reserved',
            'description' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'features' => 'nullable|array',
            'floor_number' => 'nullable|integer',
            'available_from' => 'nullable|date',
            'is_listed' => 'nullable|boolean',
        ]);

        $property->update($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property updated successfully!');
    }

    /**
     * Remove the specified property
     */
    public function destroy(Property $property)
    {
        // Check if property has active tenants
        if ($property->activeTenants()->count() > 0) {
            return redirect()->route('admin.properties.index')
                ->with('error', 'Cannot delete property with active tenants.');
        }

        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property deleted successfully!');
    }
}
