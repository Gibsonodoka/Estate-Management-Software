<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties
     */
    public function index()
    {
        $estate = auth()->user()->estate;

        if (!$estate) {
            return redirect()->route('estate.dashboard')
                ->with('error', 'No estate associated with this account.');
        }

        $properties = Property::where('estate_id', $estate->id)
            ->withCount('activeTenants')
            ->latest()
            ->paginate(15);

        return view('estate-admin.properties.index', compact('properties', 'estate'));
    }

    /**
     * Show the form for creating a new property
     */
    public function create()
    {
        $estate = auth()->user()->estate;

        if (!$estate) {
            return redirect()->route('estate.dashboard')
                ->with('error', 'No estate associated with this account.');
        }

        return view('estate-admin.properties.create', compact('estate'));
    }

    /**
     * Store a newly created property
     */
    public function store(Request $request)
    {
        $estate = auth()->user()->estate;

        if (!$estate) {
            return redirect()->route('estate.dashboard')
                ->with('error', 'No estate associated with this account.');
        }

        $validated = $request->validate([
            'property_name' => 'required|string|max:255',
            'property_type' => 'required|string|in:apartment,duplex,bungalow,flat,penthouse,studio',
            'units' => 'required|integer|min:1',
            'bedrooms_per_unit' => 'nullable|integer|min:0',
            'bathrooms_per_unit' => 'nullable|integer|min:0',
            'size_sqm' => 'nullable|numeric|min:0',
            'size_unit' => 'nullable|string|in:sqm,sqft,plot',
            'street' => 'nullable|string|max:255',
            'street_name' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:50',
            'rent_amount_per_unit' => 'required|numeric|min:0',
            'rent_period' => 'required|string|in:monthly,quarterly,bi-annually,annually',
            'status' => 'required|in:available,occupied,vacant,maintenance,reserved',
            'description' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'features' => 'nullable|array',
            'floor_number' => 'nullable|integer',
            'available_from' => 'nullable|date',
            'is_listed' => 'nullable|boolean',
        ]);

        $validated['estate_id'] = $estate->id;
        $validated['landlord_id'] = auth()->id();

        Property::create($validated);

        return redirect()->route('estate.properties.index')
            ->with('success', 'Property created successfully!');
    }

    /**
     * Show the form for editing the specified property
     */
    public function edit(Property $property)
    {
        $estate = auth()->user()->estate;

        if (!$estate) {
            return redirect()->route('estate.dashboard')
                ->with('error', 'No estate associated with this account.');
        }

        // Ensure property belongs to user's estate
        if ($property->estate_id !== $estate->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('estate-admin.properties.edit', compact('property', 'estate'));
    }

    /**
     * Update the specified property
     */
    public function update(Request $request, Property $property)
    {
        $estate = auth()->user()->estate;

        if (!$estate) {
            return redirect()->route('estate.dashboard')
                ->with('error', 'No estate associated with this account.');
        }

        // Ensure property belongs to user's estate
        if ($property->estate_id !== $estate->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'property_name' => 'required|string|max:255',
            'property_type' => 'required|string|in:apartment,duplex,bungalow,flat,penthouse,studio',
            'units' => 'required|integer|min:1',
            'bedrooms_per_unit' => 'nullable|integer|min:0',
            'bathrooms_per_unit' => 'nullable|integer|min:0',
            'size_sqm' => 'nullable|numeric|min:0',
            'size_unit' => 'nullable|string|in:sqm,sqft,plot',
            'street' => 'nullable|string|max:255',
            'street_name' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:50',
            'rent_amount_per_unit' => 'required|numeric|min:0',
            'rent_period' => 'required|string|in:monthly,quarterly,bi-annually,annually',
            'status' => 'required|in:available,occupied,vacant,maintenance,reserved',
            'description' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'features' => 'nullable|array',
            'floor_number' => 'nullable|integer',
            'available_from' => 'nullable|date',
            'is_listed' => 'nullable|boolean',
        ]);

        $property->update($validated);

        return redirect()->route('estate.properties.index')
            ->with('success', 'Property updated successfully!');
    }

    /**
     * Remove the specified property
     */
    public function destroy(Property $property)
    {
        $estate = auth()->user()->estate;

        if (!$estate) {
            return redirect()->route('estate.dashboard')
                ->with('error', 'No estate associated with this account.');
        }

        // Ensure property belongs to user's estate
        if ($property->estate_id !== $estate->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if property has active tenants
        if ($property->activeTenants()->count() > 0) {
            return redirect()->route('estate.properties.index')
                ->with('error', 'Cannot delete property with active tenants.');
        }

        $property->delete();

        return redirect()->route('estate.properties.index')
            ->with('success', 'Property deleted successfully!');
    }
}
