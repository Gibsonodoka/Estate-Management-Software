@extends('layouts.estate-app')

@section('content')
<div class="mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Estate Settings</h1>
        <p class="text-sm text-gray-600 mt-1">Manage your estate information and preferences</p>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
    {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Settings Navigation -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <nav class="space-y-1">
                <a href="#general" class="flex items-center px-4 py-3 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    General Information
                </a>
                <a href="#subscription" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Subscription
                </a>
            </nav>
        </div>

        <!-- Estate Stats -->
        <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Estate Statistics</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Estate UCI</span>
                    <span class="font-medium text-gray-900">{{ $estate->uci ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Created</span>
                    <span class="font-medium text-gray-900">{{ $estate->created_at ? $estate->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Properties</span>
                    <span class="font-medium text-gray-900">{{ $estate->properties ? $estate->properties->count() : 0 }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Active Tenants</span>
                    <span class="font-medium text-gray-900">{{ $estate->tenants ? $estate->tenants->count() : 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm">
            <form action="{{ route('estate.settings.update') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div id="general" class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">General Information</h2>

                    <!-- Estate Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Estate Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $estate->name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="mb-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Street Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="address"
                               id="address"
                               value="{{ old('address', $estate->address) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                               required>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City & State -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="city"
                                   id="city"
                                   value="{{ old('city', $estate->city) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('city') border-red-500 @enderror"
                                   required>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                State <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="state"
                                   id="state"
                                   value="{{ old('state', $estate->state) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('state') border-red-500 @enderror"
                                   required>
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Monthly Fee -->
                    <div class="mb-6">
                        <label for="monthly_fee" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Monthly Fee (₦)
                        </label>
                        <input type="number"
                               name="monthly_fee"
                               id="monthly_fee"
                               value="{{ old('monthly_fee', $estate->monthly_fee) }}"
                               step="0.01"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('monthly_fee') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('monthly_fee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">This is the default monthly estate management fee</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Estate Description
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                  placeholder="Brief description of your estate...">{{ old('description', $estate->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="subscription" class="mb-8 pt-8 border-t border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Subscription Information</h2>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Current Subscription Status</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Status: <span class="font-semibold">{{ $estate->subscription_status ? ucfirst($estate->subscription_status) : 'Not Set' }}</span></p>
                                    @if($estate->subscription_expires_at)
                                    <p class="mt-1">Expires: <span class="font-semibold">{{ $estate->subscription_expires_at->format('M d, Y') }}</span></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600">
                        To upgrade or modify your subscription plan, please contact support or visit the billing section.
                    </p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="window.location.reload()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
