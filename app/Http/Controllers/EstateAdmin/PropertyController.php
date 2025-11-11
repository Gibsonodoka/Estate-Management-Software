<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        $estate = auth()->user()->estate;
        $properties = Property::where('estate_id', $estate->id)
            ->with(['tenant'])
            ->latest()
            ->paginate(15);

        return view('estate-admin.properties.index', compact('properties', 'estate'));
    }

    public function create()
    {
        $estate = auth()->user()->estate;
        return view('estate-admin.properties.create', compact('estate'));
    }

    public function store(Request $request)
    {
        $estate = auth()->user()->estate;

        $validated = $request->validate([
            'property_number' => 'required|string|max:50',
            'type' => 'required|in:residential,commercial',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'size' => 'nullable|numeric|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
        ]);

        $validated['estate_id'] = $estate->id;

        Property::create($validated);

        return redirect()->route('estate.properties.index')->with('success', 'Property created successfully!');
    }

    public function edit(Property $property)
    {
        // Ensure property belongs to user's estate
        if ($property->estate_id !== auth()->user()->estate_id) {
            abort(403);
        }

        $estate = auth()->user()->estate;
        return view('estate-admin.properties.edit', compact('property', 'estate'));
    }

    public function update(Request $request, Property $property)
    {
        // Ensure property belongs to user's estate
        if ($property->estate_id !== auth()->user()->estate_id) {
            abort(403);
        }

        $validated = $request->validate([
            'property_number' => 'required|string|max:50',
            'type' => 'required|in:residential,commercial',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'size' => 'nullable|numeric|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
        ]);

        $property->update($validated);

        return redirect()->route('estate.properties.index')->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property)
    {
        // Ensure property belongs to user's estate
        if ($property->estate_id !== auth()->user()->estate_id) {
            abort(403);
        }

        $property->delete();
        return redirect()->route('estate.properties.index')->with('success', 'Property deleted successfully!');
    }
}
