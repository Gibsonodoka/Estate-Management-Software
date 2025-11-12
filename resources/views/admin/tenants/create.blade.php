@extends('layouts.app')

@section('title', 'Add New Tenant')
@section('header', 'Add New Tenant')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Add New Tenant</h2>
            <p class="text-gray-600 mt-1">Create a new tenant record</p>
        </div>
        <a href="{{ route('admin.tenants.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
            Back to Tenants
        </a>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm">
        <form action="{{ route('admin.tenants.store') }}" method="POST" class="p-6">
            @csrf

            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900 pb-2 border-b border-gray-200">Tenant Information</h3>

                <!-- Property Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Property <span class="text-red-500">*</span></label>
                        <select name="property_id" id="property_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('property_id') border-red-500 @enderror">
                            <option value="">Select Property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                    {{ $property->property_name }} ({{ $property->estate->name ?? 'No Estate' }})
                                </option>
                            @endforeach
                        </select>
                        @error('property_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Existing User</label>
                        <select name="user_id" id="user_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('user_id') border-red-500 @enderror">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">If you select an existing user, first/last name and email fields below will be ignored.</p>
                    </div>
                </div>

                <!-- Personal Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-start mt-8">
                        <div class="flex items-center h-5">
                            <input id="create_account" name="create_account" type="checkbox" value="1" {{ old('create_account') ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="create_account" class="font-medium text-gray-700">Create User Account</label>
                            <p class="text-gray-500">Create a user account for this tenant to access the portal</p>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 pb-2 border-b border-gray-200 mt-8">Lease Details (Optional)</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lease Start Date</label>
                        <input type="date" name="lease_start_date" id="lease_start_date" value="{{ old('lease_start_date') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('lease_start_date') border-red-500 @enderror">
                        @error('lease_start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lease End Date</label>
                        <input type="date" name="lease_end_date" id="lease_end_date" value="{{ old('lease_end_date') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('lease_end_date') border-red-500 @enderror">
                        @error('lease_end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Move In Date <span class="text-red-500">*</span></label>
                        <input type="date" name="move_in_date" id="move_in_date" value="{{ old('move_in_date') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('move_in_date') border-red-500 @enderror">
                        @error('move_in_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Move Out Date</label>
                        <input type="date" name="move_out_date" id="move_out_date" value="{{ old('move_out_date') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('move_out_date') border-red-500 @enderror">
                        @error('move_out_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 pb-2 border-b border-gray-200 mt-8">Financial Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rent Amount (₦) <span class="text-red-500">*</span></label>
                        <input type="number" name="rent_amount" id="rent_amount" value="{{ old('rent_amount') }}" min="0" step="0.01" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent_amount') border-red-500 @enderror">
                        @error('rent_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deposit Amount (₦) <span class="text-red-500">*</span></label>
                        <input type="number" name="deposit_amount" id="deposit_amount" value="{{ old('deposit_amount') }}" min="0" step="0.01" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deposit_amount') border-red-500 @enderror">
                        @error('deposit_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="notice_given" {{ old('status') == 'notice_given' ? 'selected' : '' }}>Notice Given</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="evicted" {{ old('status') == 'evicted' ? 'selected' : '' }}>Evicted</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notice Period (days)</label>
                        <input type="number" name="notice_period_days" id="notice_period_days" value="{{ old('notice_period_days', 30) }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notice_period_days') border-red-500 @enderror">
                        @error('notice_period_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.tenants.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create Tenant
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-populate rent amount from property data
    const propertySelect = document.getElementById('property_id');
    const rentInput = document.getElementById('rent_amount');

    propertySelect.addEventListener('change', function() {
        if (this.value) {
            // You can replace this with actual Ajax call to get property details
            // For now it's a placeholder
            fetch(`/api/properties/${this.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.rent_amount_per_unit && !rentInput.value) {
                        rentInput.value = data.rent_amount_per_unit;
                    }
                })
                .catch(error => console.error('Error fetching property details:', error));
        }
    });

    // When user_id is selected, disable personal fields
    const userSelect = document.getElementById('user_id');
    const personalFields = ['first_name', 'last_name', 'email'];

    userSelect.addEventListener('change', function() {
        const isDisabled = !!this.value;
        personalFields.forEach(field => {
            document.getElementById(field).disabled = isDisabled;
            document.getElementById(field).required = !isDisabled;
        });
    });

    // Initialize fields based on initial selection
    if (userSelect.value) {
        personalFields.forEach(field => {
            document.getElementById(field).disabled = true;
            document.getElementById(field).required = false;
        });
    }
});
</script>
@endsection
