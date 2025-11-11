<?php

// ============================================
// FILE: Modules/GeneralListing/app/Http/Controllers/PropertyListingController.php
// ============================================

namespace Modules\GeneralListing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PropertyListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyListingController extends Controller
{
    public function index(Request $request)
    {
        $query = PropertyListing::with(['property.estate', 'agent'])
            ->where('status', 'available')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('state')) {
            $query->where('state', $request->state);
        }

        if ($request->has('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->has('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->has('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $listings = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $listings
        ], 200);
    }

    public function featured()
    {
        $listings = PropertyListing::with(['property.estate', 'agent'])
            ->where('status', 'available')
            ->where('is_featured', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['listings' => $listings]
        ], 200);
    }

    public function search(Request $request)
    {
        $query = PropertyListing::with(['property.estate', 'agent'])
            ->where('status', 'available')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('state')) {
            $query->where('state', $request->state);
        }

        if ($request->has('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->has('bedrooms_min')) {
            $query->where('bedrooms', '>=', $request->bedrooms_min);
        }

        if ($request->has('bathrooms_min')) {
            $query->where('bathrooms', '>=', $request->bathrooms_min);
        }

        if ($request->has('price_min') && $request->has('price_max')) {
            $query->whereBetween('price', [$request->price_min, $request->price_max]);
        }

        if ($request->has('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->has('features')) {
            $features = is_array($request->features) ? $request->features : explode(',', $request->features);
            foreach ($features as $feature) {
                $query->whereJsonContains('features', trim($feature));
            }
        }

        $listings = $query->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $listings
        ], 200);
    }

    public function show($id)
    {
        $listing = PropertyListing::with(['property.estate', 'agent.agentProfile'])
            ->find($id);

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }

        $listing->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => ['listing' => $listing]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'sometimes|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'property_type' => 'required|in:apartment,duplex,bungalow,flat,penthouse,studio,land,commercial',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'listing_type' => 'required|in:rent,sale',
            'features' => 'sometimes|array',
            'images' => 'sometimes|array',
            'video_url' => 'sometimes|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if ($user->role !== 'agent' && $user->role !== 'site_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only agents can create listings'
            ], 403);
        }

        $listing = PropertyListing::create([
            'property_id' => $request->property_id,
            'agent_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country ?? 'Nigeria',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'property_type' => $request->property_type,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'price' => $request->price,
            'listing_type' => $request->listing_type,
            'status' => 'available',
            'features' => $request->features,
            'images' => $request->images,
            'video_url' => $request->video_url,
            'published_at' => now(),
        ]);

        if ($user->agentProfile) {
            $user->agentProfile->increment('properties_listed');
        }

        return response()->json([
            'success' => true,
            'message' => 'Listing created successfully',
            'data' => ['listing' => $listing]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $listing = PropertyListing::find($id);

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }

        if ($listing->agent_id !== $request->user()->id && $request->user()->role !== 'site_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this listing'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:available,rented,sold,inactive',
            'features' => 'sometimes|array',
            'images' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $listing->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Listing updated successfully',
            'data' => ['listing' => $listing]
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $listing = PropertyListing::find($id);

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }

        if ($listing->agent_id !== $request->user()->id && $request->user()->role !== 'site_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this listing'
            ], 403);
        }

        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Listing deleted successfully'
        ], 200);
    }

    public function toggleFeatured(Request $request, $id)
    {
        $listing = PropertyListing::find($id);

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }

        if ($request->user()->role !== 'site_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can feature listings'
            ], 403);
        }

        $listing->update([
            'is_featured' => !$listing->is_featured
        ]);

        return response()->json([
            'success' => true,
            'message' => $listing->is_featured ? 'Listing featured' : 'Listing unfeatured',
            'data' => ['listing' => $listing]
        ], 200);
    }

    public function myListings(Request $request)
    {
        $user = $request->user();

        $query = PropertyListing::where('agent_id', $user->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $listings = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $listings
        ], 200);
    }
}
