<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Landlord;
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

        $query = Property::where('estate_id', $estate->id)
            ->with(['landlord']) // Eager load landlord relationship
            ->withCount('activeTenants');

        // Apply filters if any
        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('property_type')) {
            $query->where('property_type', request('property_type'));
        }

        if (request('landlord_id')) {
            $query->where('landlord_id', request('landlord_id'));
        }

        // Apply sorting
        if (request('sort') == 'oldest') {
            $query->oldest();
        } elseif (request('sort') == 'rent_asc') {
            $query->orderBy('rent_amount_per_unit', 'asc');
        } elseif (request('sort') == 'rent_desc') {
            $query->orderBy('rent_amount_per_unit', 'desc');
        } else {
            $query->latest(); // Default: newest first
        }

        $properties = $query->paginate(15);

        // Get landlords for the filter dropdown (only for this estate)
        $landlords = Landlord::where('estate_id', $estate->id)
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('estate-admin.properties.index', compact('properties', 'estate', 'landlords'));
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

        // Get landlords for this estate for the dropdown
        $landlords = Landlord::where('estate_id', $estate->id)
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('estate-admin.properties.create', compact('estate', 'landlords'));
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
            'landlord_id' => 'nullable|exists:landlords,id', // Now accepts landlord_id
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

        // Ensure the property belongs to the current estate
        $validated['estate_id'] = $estate->id;

        // Validate that landlord belongs to this estate if provided
        if (!empty($validated['landlord_id'])) {
            $landlord = Landlord::find($validated['landlord_id']);
            if (!$landlord || $landlord->estate_id != $estate->id) {
                return redirect()->back()
                    ->with('error', 'Selected landlord is not associated with this estate.')
                    ->withInput();
            }
        }

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

        // Get landlords for this estate for the dropdown
        $landlords = Landlord::where('estate_id', $estate->id)
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('estate-admin.properties.edit', compact('property', 'estate', 'landlords'));
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
            'landlord_id' => 'nullable|exists:landlords,id', // Now accepts landlord_id
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

        // Validate that landlord belongs to this estate if provided
        if (!empty($validated['landlord_id'])) {
            $landlord = Landlord::find($validated['landlord_id']);
            if (!$landlord || $landlord->estate_id != $estate->id) {
                return redirect()->back()
                    ->with('error', 'Selected landlord is not associated with this estate.')
                    ->withInput();
            }
        }

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
