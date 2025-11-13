@extends('layouts.estate-app')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Property</h1>
            <p class="text-sm text-gray-600 mt-1">Update {{ $property->property_name }}</p>
        </div>
        <a href="{{ route('estate.properties.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
            Back to Properties
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm">
    <form action="{{ route('estate.properties.update', $property) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Landlord Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Property Owner / Landlord</label>
                <div class="flex space-x-3">
                    <select name="landlord_id" id="landlord_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('landlord_id') border-red-500 @enderror">
                        <option value="">Select Landlord</option>
                        @foreach($landlords as $landlord)
                            <option value="{{ $landlord->id }}" {{ old('landlord_id', $property->landlord_id) == $landlord->id ? 'selected' : '' }}>
                                @if($landlord->is_company)
                                    {{ $landlord->company_name }} (Company)
                                @else
                                    {{ $landlord->contact_person ?? ($landlord->user ? $landlord->user->name : 'Unknown') }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('estate.landlords.create') }}" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Landlord
                    </a>
                </div>
                @error('landlord_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-500">Select the landlord who owns this property</p>
            </div>

            <!-- Property Name and Type -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="property_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Property Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="property_name" id="property_name" value="{{ old('property_name', $property->property_name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('property_name') border-red-500 @enderror"
                        placeholder="e.g., Sunset Apartments, Palm Heights" required>
                    @error('property_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="property_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Property Type <span class="text-red-500">*</span>
                    </label>
                    <select name="property_type" id="property_type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('property_type') border-red-500 @enderror" required>
                        <option value="">Select Property Type</option>
                        <option value="apartment" {{ old('property_type', $property->property_type) == 'apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="duplex" {{ old('property_type', $property->property_type) == 'duplex' ? 'selected' : '' }}>Duplex</option>
                        <option value="bungalow" {{ old('property_type', $property->property_type) == 'bungalow' ? 'selected' : '' }}>Bungalow</option>
                        <option value="flat" {{ old('property_type', $property->property_type) == 'flat' ? 'selected' : '' }}>Flat</option>
                        <option value="penthouse" {{ old('property_type', $property->property_type) == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                        <option value="studio" {{ old('property_type', $property->property_type) == 'studio' ? 'selected' : '' }}>Studio</option>
                    </select>
                    @error('property_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Units -->
            <div>
                <label for="units" class="block text-sm font-medium text-gray-700 mb-2">
                    Number of Units <span class="text-red-500">*</span>
                </label>
                <input type="number" name="units" id="units" value="{{ old('units', $property->units) }}" min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('units') border-red-500 @enderror" required>
                @error('units')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bedrooms and Bathrooms -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="bedrooms_per_unit" class="block text-sm font-medium text-gray-700 mb-2">Bedrooms per Unit</label>
                    <input type="number" name="bedrooms_per_unit" id="bedrooms_per_unit" value="{{ old('bedrooms_per_unit', $property->bedrooms_per_unit) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bedrooms_per_unit') border-red-500 @enderror">
                    @error('bedrooms_per_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bathrooms_per_unit" class="block text-sm font-medium text-gray-700 mb-2">Bathrooms per Unit</label>
                    <input type="number" name="bathrooms_per_unit" id="bathrooms_per_unit" value="{{ old('bathrooms_per_unit', $property->bathrooms_per_unit) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bathrooms_per_unit') border-red-500 @enderror">
                    @error('bathrooms_per_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Size -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="size_sqm" class="block text-sm font-medium text-gray-700 mb-2">Property Size</label>
                    <input type="number" name="size_sqm" id="size_sqm" value="{{ old('size_sqm', $property->size_sqm) }}" min="0" step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('size_sqm') border-red-500 @enderror">
                    @error('size_sqm')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="size_unit" class="block text-sm font-medium text-gray-700 mb-2">Size Unit</label>
                    <select name="size_unit" id="size_unit"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('size_unit') border-red-500 @enderror">
                        <option value="sqm" {{ old('size_unit', $property->size_unit) == 'sqm' ? 'selected' : '' }}>Square Meters (sqm)</option>
                        <option value="sqft" {{ old('size_unit', $property->size_unit) == 'sqft' ? 'selected' : '' }}>Square Feet (sqft)</option>
                        <option value="plot" {{ old('size_unit', $property->size_unit) == 'plot' ? 'selected' : '' }}>Plot</option>
                    </select>
                    @error('size_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="street" class="block text-sm font-medium text-gray-700 mb-2">Street</label>
                    <input type="text" name="street" id="street" value="{{ old('street', $property->street) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('street') border-red-500 @enderror">
                    @error('street')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="street_name" class="block text-sm font-medium text-gray-700 mb-2">Street Name</label>
                    <input type="text" name="street_name" id="street_name" value="{{ old('street_name', $property->street_name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('street_name') border-red-500 @enderror">
                    @error('street_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="street_number" class="block text-sm font-medium text-gray-700 mb-2">Street Number</label>
                    <input type="text" name="street_number" id="street_number" value="{{ old('street_number', $property->street_number) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('street_number') border-red-500 @enderror">
                    @error('street_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Estate Information -->
            <div class="p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <strong>Estate:</strong> {{ $estate->name }} (automatically assigned)
                </p>
            </div>

            <!-- Rent Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="rent_amount_per_unit" class="block text-sm font-medium text-gray-700 mb-2">
                        Rent Amount per Unit (₦) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="rent_amount_per_unit" id="rent_amount_per_unit" value="{{ old('rent_amount_per_unit', $property->rent_amount_per_unit) }}" min="0" step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent_amount_per_unit') border-red-500 @enderror" required>
                    @error('rent_amount_per_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rent_period" class="block text-sm font-medium text-gray-700 mb-2">Rent Period <span class="text-red-500">*</span></label>
                    <select name="rent_period" id="rent_period"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent_period') border-red-500 @enderror" required>
                        <option value="">Select Rent Period</option>
                        <option value="monthly" {{ old('rent_period', $property->rent_period) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('rent_period', $property->rent_period) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="bi-annually" {{ old('rent_period', $property->rent_period) == 'bi-annually' ? 'selected' : '' }}>Bi-annually</option>
                        <option value="annually" {{ old('rent_period', $property->rent_period) == 'annually' ? 'selected' : '' }}>Annually</option>
                    </select>
                    @error('rent_period')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Current Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror" required>
                        <option value="available" {{ old('status', $property->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ old('status', $property->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="vacant" {{ old('status', $property->status) == 'vacant' ? 'selected' : '' }}>Vacant</option>
                        <option value="maintenance" {{ old('status', $property->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        <option value="reserved" {{ old('status', $property->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="floor_number" class="block text-sm font-medium text-gray-700 mb-2">Floor Number</label>
                    <input type="number" name="floor_number" id="floor_number" value="{{ old('floor_number', $property->floor_number) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('floor_number') border-red-500 @enderror">
                    @error('floor_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="available_from" class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                    <input type="date" name="available_from" id="available_from" value="{{ old('available_from', $property->available_from ? $property->available_from->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('available_from') border-red-500 @enderror">
                    @error('available_from')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Utilities & Features -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Utilities Included</label>
                    <div class="space-y-2">
                        @php
                            $utilities = old('utilities_included', $property->utilities_included ?? []);
                            if (!is_array($utilities) && is_string($utilities)) {
                                $utilities = json_decode($utilities, true) ?? [];
                            }
                        @endphp
                        <div class="flex items-center">
                            <input type="checkbox" name="utilities_included[]" value="water" id="utility_water"
                                {{ in_array('water', $utilities) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="utility_water" class="ml-2 text-sm text-gray-700">Water</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="utilities_included[]" value="electricity" id="utility_electricity"
                                {{ in_array('electricity', $utilities) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="utility_electricity" class="ml-2 text-sm text-gray-700">Electricity</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="utilities_included[]" value="internet" id="utility_internet"
                                {{ in_array('internet', $utilities) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="utility_internet" class="ml-2 text-sm text-gray-700">Internet</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="utilities_included[]" value="security" id="utility_security"
                                {{ in_array('security', $utilities) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="utility_security" class="ml-2 text-sm text-gray-700">Security</label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                    <div class="space-y-2">
                        @php
                            $features = old('features', $property->features ?? []);
                            if (!is_array($features) && is_string($features)) {
                                $features = json_decode($features, true) ?? [];
                            }
                        @endphp
                        <div class="flex items-center">
                            <input type="checkbox" name="features[]" value="parking" id="feature_parking"
                                {{ in_array('parking', $features) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="feature_parking" class="ml-2 text-sm text-gray-700">Parking</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="features[]" value="swimming_pool" id="feature_swimming_pool"
                                {{ in_array('swimming_pool', $features) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="feature_swimming_pool" class="ml-2 text-sm text-gray-700">Swimming Pool</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="features[]" value="gym" id="feature_gym"
                                {{ in_array('gym', $features) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="feature_gym" class="ml-2 text-sm text-gray-700">Gym</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="features[]" value="garden" id="feature_garden"
                                {{ in_array('garden', $features) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="feature_garden" class="ml-2 text-sm text-gray-700">Garden</label>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="is_listed" name="is_listed" type="checkbox" value="1" {{ old('is_listed', $property->is_listed) ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_listed" class="font-medium text-gray-700">List Property</label>
                        <p class="text-gray-500">Show this property in public listings</p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $property->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex items-center justify-end space-x-4">
            <a href="{{ route('estate.properties.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Update Property
            </button>
        </div>
    </form>
</div>
@endsection
