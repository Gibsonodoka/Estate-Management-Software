<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Estate;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::with('estate')->latest()->paginate(15);
        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        $estates = Estate::where('is_active', true)->get();
        return view('admin.properties.create', compact('estates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'estate_id' => 'required|exists:estates,id',
            'property_number' => 'required|string|max:50',
            'type' => 'required|in:residential,commercial',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'size' => 'nullable|numeric|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
        ]);

        Property::create($validated);

        return redirect()->route('admin.properties.index')->with('success', 'Property created successfully!');
    }

    public function show(Property $property)
    {
        $property->load(['estate', 'tenant']);
        return view('admin.properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $estates = Estate::where('is_active', true)->get();
        return view('admin.properties.edit', compact('property', 'estates'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'estate_id' => 'required|exists:estates,id',
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

        return redirect()->route('admin.properties.index')->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('admin.properties.index')->with('success', 'Property deleted successfully!');
    }
}
