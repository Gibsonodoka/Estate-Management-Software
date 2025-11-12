@extends('layouts.app')

@section('title', 'Create New Landlord')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create New Landlord</h2>
        <a href="{{ route('admin.landlords.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Landlords
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.landlords.store') }}" method="POST">
            @csrf

            <!-- Form Instructions -->
            <div class="mb-6 bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            You can either link an existing user account or create a new landlord without an account.
                            If you need to create a new user account for this landlord, please create the user first.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6" x-data="{ activeTab: '{{ old('create_account', 'no') === 'yes' ? 'create_account' : 'existing_account' }}' }">
                <nav class="flex -mb-px">
                    <button type="button" @click="activeTab = 'existing_account'"
                            :class="{ 'border-b-2 border-blue-500 text-blue-600': activeTab === 'existing_account', 'text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent': activeTab !== 'existing_account' }"
                            class="py-4 px-6 font-medium text-sm focus:outline-none transition-colors duration-200">
                        <i class="fas fa-user mr-2"></i> Link Existing User
                    </button>
                    <button type="button" @click="activeTab = 'new_landlord'"
                            :class="{ 'border-b-2 border-blue-500 text-blue-600': activeTab === 'new_landlord', 'text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent': activeTab !== 'new_landlord' }"
                            class="py-4 px-6 font-medium text-sm focus:outline-none transition-colors duration-200">
                        <i class="fas fa-user-plus mr-2"></i> New Landlord
                    </button>
                </nav>
            </div>

            <div x-data="{ activeTab: '{{ old('create_account', 'no') === 'yes' ? 'create_account' : 'existing_account' }}', accountType: '{{ old('is_company', '0') === '1' ? 'company' : 'individual' }}', createUserAccount: {{ old('create_account', 'no') === 'yes' ? 'true' : 'false' }} }">
                <!-- Existing User Section -->
                <div x-show="activeTab === 'existing_account'" class="mb-6">
                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                        <select name="user_id" id="user_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select an existing user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <input type="hidden" name="create_account" x-bind:value="createUserAccount ? 'yes' : 'no'">
                </div>

                <!-- New Landlord Section -->
                <div x-show="activeTab === 'new_landlord'">
                    <!-- Account Type Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Landlord Type</label>
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_company" value="0" class="text-blue-600 focus:ring-blue-500"
                                       x-model="accountType" x-on:change="accountType = 'individual'">
                                <span class="ml-2">Individual</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_company" value="1" class="text-blue-600 focus:ring-blue-500"
                                       x-model="accountType" x-on:change="accountType = 'company'">
                                <span class="ml-2">Company</span>
                            </label>
                        </div>
                    </div>

                    <!-- Individual Fields -->
                    <div x-show="accountType === 'individual'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Company Fields -->
                    <div x-show="accountType === 'company'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('company_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                            <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('contact_person')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alternative_phone" class="block text-sm font-medium text-gray-700 mb-1">Alternative Phone</label>
                            <input type="text" name="alternative_phone" id="alternative_phone" value="{{ old('alternative_phone') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('alternative_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Create User Account Checkbox -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="create_account" id="create_account" value="yes"
                                   class="text-blue-600 focus:ring-blue-500 h-4 w-4"
                                   x-model="createUserAccount">
                            <label for="create_account" class="ml-2 block text-sm text-gray-700">
                                Create user account for this landlord
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            This will create a user account with the email and send a password reset link.
                        </p>
                    </div>
                </div>

                <!-- Common Fields -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="estate_id" class="block text-sm font-medium text-gray-700 mb-1">Estate</label>
                            <select name="estate_id" id="estate_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select an estate</option>
                                @foreach($estates as $estate)
                                    <option value="{{ $estate->id }}" {{ old('estate_id') == $estate->id ? 'selected' : '' }}>
                                        {{ $estate->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estate_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State/Region</label>
                            <input type="text" name="state" id="state" value="{{ old('state') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('state')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" name="country" id="country" value="{{ old('country', 'Nigeria') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('country')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1">Postal/Zip Code</label>
                            <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('zip_code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Banking Details Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Banking Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('bank_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                            <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('account_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                            <input type="text" name="account_name" id="account_name" value="{{ old('account_name') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('account_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Fields -->
                <div class="mb-6">
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center mb-4">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               class="text-blue-600 focus:ring-blue-500 h-4 w-4" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.landlords.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Create Landlord
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('landlordForm', () => ({
            activeTab: '{{ old('create_account', 'no') === 'yes' ? 'create_account' : 'existing_account' }}',
            accountType: '{{ old('is_company', '0') === '1' ? 'company' : 'individual' }}',
            createUserAccount: {{ old('create_account', 'no') === 'yes' ? 'true' : 'false' }}
        }));
    });
</script>
@endsection
