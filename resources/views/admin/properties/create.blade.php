@extends('layouts.app')

@section('title', 'Create Property')
@section('header', 'Add New Property')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Property</h1>
                <p class="text-sm text-gray-600 mt-1">Create a new property in the system</p>
            </div>
            <a href="{{ route('admin.properties.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                Back to Properties
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm">
        <form action="{{ route('admin.properties.store') }}" method="POST" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Estate Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estate <span class="text-red-500">*</span></label>
                    <select name="estate_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('estate_id') border-red-500 @enderror">
                        <option value="">Select Estate</option>
                        @foreach($estates as $estate)
                            <option value="{{ $estate->id }}" {{ old('estate_id') == $estate->id ? 'selected' : '' }}>
                                {{ $estate->name }} - {{ $estate->city }}
                            </option>
                        @endforeach
                    </select>
                    @error('estate_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Property Name and Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="property_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Property Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="property_name" id="property_name" value="{{ old('property_name') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('property_name') border-red-500 @enderror"
                            placeholder="e.g., Sunset Apartments, Palm Heights" required>
                        @error('property_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="old_property_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Property Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="old_property_number" id="old_property_number" value="{{ old('old_property_number') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('old_property_number') border-red-500 @enderror"
                            placeholder="e.g., A12, Block 3-Unit 5" required>
                        @error('old_property_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Property Types -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Property Category <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror" required>
                            <option value="">Select Category</option>
                            <option value="residential" {{ old('type', 'residential') == 'residential' ? 'selected' : '' }}>Residential</option>
                            <option value="commercial" {{ old('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="property_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Property Type <span class="text-red-500">*</span>
                        </label>
                        <select name="property_type" id="property_type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('property_type') border-red-500 @enderror" required>
                            <option value="">Select Type</option>
                            <option value="apartment" {{ old('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="duplex" {{ old('property_type') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                            <option value="bungalow" {{ old('property_type') == 'bungalow' ? 'selected' : '' }}>Bungalow</option>
                            <option value="flat" {{ old('property_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                            <option value="penthouse" {{ old('property_type') == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                            <option value="studio" {{ old('property_type') == 'studio' ? 'selected' : '' }}>Studio</option>
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
                    <input type="number" name="units" id="units" value="{{ old('units', 1) }}" min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('units') border-red-500 @enderror" required>
                    @error('units')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bedrooms and Bathrooms -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label for="old_bedrooms" class="block text-sm font-medium text-gray-700 mb-2">
                            Bedrooms (Legacy)
                        </label>
                        <input type="number" name="old_bedrooms" id="old_bedrooms" value="{{ old('old_bedrooms') }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('old_bedrooms') border-red-500 @enderror">
                        @error('old_bedrooms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bedrooms_per_unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Bedrooms per Unit
                        </label>
                        <input type="number" name="bedrooms_per_unit" id="bedrooms_per_unit" value="{{ old('bedrooms_per_unit') }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bedrooms_per_unit') border-red-500 @enderror">
                        @error('bedrooms_per_unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="old_bathrooms" class="block text-sm font-medium text-gray-700 mb-2">
                            Bathrooms (Legacy)
                        </label>
                        <input type="number" name="old_bathrooms" id="old_bathrooms" value="{{ old('old_bathrooms') }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('old_bathrooms') border-red-500 @enderror">
                        @error('old_bathrooms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bathrooms_per_unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Bathrooms per Unit
                        </label>
                        <input type="number" name="bathrooms_per_unit" id="bathrooms_per_unit" value="{{ old('bathrooms_per_unit') }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bathrooms_per_unit') border-red-500 @enderror">
                        @error('bathrooms_per_unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Size -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-2">
                            Size (Legacy)
                        </label>
                        <input type="number" name="size" id="size" value="{{ old('size') }}" min="0" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('size') border-red-500 @enderror">
                        @error('size')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="size_sqm" class="block text-sm font-medium text-gray-700 mb-2">
                            Size (sqm/sqft)
                        </label>
                        <input type="number" name="size_sqm" id="size_sqm" value="{{ old('size_sqm') }}" min="0" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('size_sqm') border-red-500 @enderror">
                        @error('size_sqm')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="size_unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Size Unit
                        </label>
                        <select name="size_unit" id="size_unit"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('size_unit') border-red-500 @enderror">
                            <option value="sqm" {{ old('size_unit', 'sqm') == 'sqm' ? 'selected' : '' }}>Square Meters (sqm)</option>
                            <option value="sqft" {{ old('size_unit') == 'sqft' ? 'selected' : '' }}>Square Feet (sqft)</option>
                            <option value="plot" {{ old('size_unit') == 'plot' ? 'selected' : '' }}>Plot</option>
                        </select>
                        @error('size_unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="street" class="block text-sm font-medium text-gray-700 mb-2">
                            Street
                        </label>
                        <input type="text" name="street" id="street" value="{{ old('street') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('street') border-red-500 @enderror">
                        @error('street')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="street_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Street Name
                        </label>
                        <input type="text" name="street_name" id="street_name" value="{{ old('street_name') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('street_name') border-red-500 @enderror">
                        @error('street_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="street_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Street Number
                        </label>
                        <input type="text" name="street_number" id="street_number" value="{{ old('street_number') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('street_number') border-red-500 @enderror">
                        @error('street_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Rent Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="old_rent_amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Rent Amount (₦) (Legacy) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="old_rent_amount" id="old_rent_amount" value="{{ old('old_rent_amount') }}" min="0" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('old_rent_amount') border-red-500 @enderror" required>
                        @error('old_rent_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rent_amount_per_unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Rent per Unit (₦)
                        </label>
                        <input type="number" name="rent_amount_per_unit" id="rent_amount_per_unit" value="{{ old('rent_amount_per_unit') }}" min="0" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent_amount_per_unit') border-red-500 @enderror">
                        @error('rent_amount_per_unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rent_period" class="block text-sm font-medium text-gray-700 mb-2">
                            Rent Period <span class="text-red-500">*</span>
                        </label>
                        <select name="rent_period" id="rent_period"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent_period') border-red-500 @enderror" required>
                            <option value="">Select Rent Period</option>
                            <option value="monthly" {{ old('rent_period', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ old('rent_period') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="bi-annually" {{ old('rent_period') == 'bi-annually' ? 'selected' : '' }}>Bi-annually</option>
                            <option value="annually" {{ old('rent_period') == 'annually' ? 'selected' : '' }}>Annually</option>
                        </select>
                        @error('rent_period')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror" required>
                            <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            <option value="vacant" {{ old('status') == 'vacant' ? 'selected' : '' }}>Vacant</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="floor_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Floor Number
                        </label>
                        <input type="number" name="floor_number" id="floor_number" value="{{ old('floor_number') }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('floor_number') border-red-500 @enderror">
                        @error('floor_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="available_from" class="block text-sm font-medium text-gray-700 mb-2">
                            Available From
                        </label>
                        <input type="date" name="available_from" id="available_from" value="{{ old('available_from') }}"
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
                            <div class="flex items-center">
                                <input type="checkbox" name="utilities_included[]" value="water" id="utility_water"
                                    {{ (is_array(old('utilities_included')) && in_array('water', old('utilities_included'))) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="utility_water" class="ml-2 text-sm text-gray-700">Water</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="utilities_included[]" value="electricity" id="utility_electricity"
                                    {{ (is_array(old('utilities_included')) && in_array('electricity', old('utilities_included'))) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="utility_electricity" class="ml-2 text-sm text-gray-700">Electricity</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="utilities_included[]" value="internet" id="utility_internet"
                                    {{ (is_array(old('utilities_included')) && in_array('internet', old('utilities_included'))) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="utility_internet" class="ml-2 text-sm text-gray-700">Internet</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="utilities_included[]" value="security" id="utility_security"
                                    {{ (is_array(old('utilities_included')) && in_array('security', old('utilities_included'))) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="utility_security" class="ml-2 text-sm text-gray-700">Security</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="features[]" value="parking" id="feature_parking"
                                    {{ (is_array(old('features')) && in_array('parking', old('features'))) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="feature_parking" class="ml-2 text-sm text-gray-700">Parking</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="features[]" value="swimming_pool" id="feature_swimming_pool"
                                    {{ (is_array(old('features')) && in_array('swimming_pool', old('features'))) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="feature_swimming_pool" class="ml-2 text-sm text-gray-700">Swimming Pool</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="features[]" value="gym" id="feature_gym"
                                    {{ (is_array(old('features')) && in_array('gym', old('features'))) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="feature_gym" class="ml-2 text-sm text-gray-700">Gym</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="features[]" value="garden" id="feature_garden"
                                    {{ (is_array(old('features')) && in_array('garden', old('features'))) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="feature_garden" class="ml-2 text-sm text-gray-700">Garden</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_listed" name="is_listed" type="checkbox" value="1" {{ old('is_listed') ? 'checked' : '' }}
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
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.properties.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create Property
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
