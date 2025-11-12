@extends('layouts.estate-app')

@section('content')
<div class="mb-6">
    <div class="flex items-center">
        <a href="{{ route('estate.tenants.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Tenants
        </a>
    </div>
    <div class="flex justify-between items-center mt-3">
        <h1 class="text-2xl font-bold text-gray-900">Tenant Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('estate.tenants.edit', $tenant) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Tenant
            </a>
            <form action="{{ route('estate.tenants.destroy', $tenant) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium" onclick="return confirm('Are you sure you want to delete this tenant?')">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </form>
        </div>
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
    <!-- Tenant Information -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Tenant Information</h2>
        </div>

        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="flex-shrink-0 h-14 w-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if($tenant->user)
                            {{ $tenant->user->name }}
                        @else
                            {{ $tenant->first_name }} {{ $tenant->last_name }}
                        @endif
                    </h3>
                    <div class="text-gray-600">
                        @if($tenant->status == 'active')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        @elseif($tenant->status == 'notice_given')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Notice Given</span>
                        @elseif($tenant->status == 'moved_out')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Moved Out</span>
                        @elseif($tenant->status == 'evicted')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Evicted</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($tenant->user)
            <div class="mb-4">
                <h4 class="text-xs font-medium uppercase text-gray-500 mb-2">User Account</h4>
                <div class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                    <a href="mailto:{{ $tenant->user->email }}" class="text-blue-600 hover:underline">{{ $tenant->user->email }}</a>
                </div>

                <div class="flex items-center space-x-2 mt-2">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <a href="tel:{{ $tenant->user->phone }}" class="text-gray-700">{{ $tenant->user->phone }}</a>
                </div>

                <div class="mt-2 flex items-center space-x-2">
                    <span class="text-xs text-gray-500">User ID: {{ $tenant->user->id }}</span>
                    <a href="#" class="text-xs text-blue-600 hover:underline">View User Profile</a>
                </div>
            </div>
            @endif

            <div class="mb-4">
                <h4 class="text-xs font-medium uppercase text-gray-500 mb-2">Status Details</h4>

                @if($tenant->status == 'notice_given' && $tenant->notice_date)
                <div class="mb-2">
                    <span class="text-sm text-gray-600">Notice Given:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $tenant->notice_date->format('M d, Y') }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-sm text-gray-600">Notice Period:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $tenant->notice_period_days }} days</span>
                </div>
                <div class="mb-2">
                    <span class="text-sm text-gray-600">Expected Move Out:</span>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ $tenant->notice_date->addDays($tenant->notice_period_days)->format('M d, Y') }}
                    </span>
                </div>
                @endif

                <div class="mt-4">
                    <span class="text-sm text-gray-600">Created:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $tenant->created_at->format('M d, Y') }}</span>
                </div>

                <div class="mt-2">
                    <span class="text-sm text-gray-600">Last Updated:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $tenant->updated_at->format('M d, Y') }}</span>
                </div>
            </div>

            @if($tenant->notes)
            <div class="mt-4">
                <h4 class="text-xs font-medium uppercase text-gray-500 mb-2">Notes</h4>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-700">{{ $tenant->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Lease Information -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Lease Information</h2>
        </div>

        <div class="p-6">
            <div class="mb-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-xs font-medium uppercase text-gray-500 mb-1">Lease Start Date</h4>
                        <p class="text-sm font-semibold text-gray-900">{{ $tenant->lease_start_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-medium uppercase text-gray-500 mb-1">Lease End Date</h4>
                        <p class="text-sm font-semibold text-gray-900">{{ $tenant->lease_end_date->format('M d, Y') }}</p>
                    </div>
                </div>

                <div class="mt-2 mb-4">
                    <span class="text-sm text-gray-600">Lease Duration:</span>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ $tenant->lease_start_date->diffInMonths($tenant->lease_end_date) }} months
                    </span>
                </div>

                <div class="h-2 bg-gray-200 rounded-full mt-2">
                    @php
                        $totalDays = $tenant->lease_start_date->diffInDays($tenant->lease_end_date);
                        $daysElapsed = $tenant->lease_start_date->diffInDays(now());
                        $percentComplete = min(100, max(0, ($daysElapsed / max(1, $totalDays)) * 100));
                    @endphp
                    <div class="h-2 bg-blue-600 rounded-full" style="width: {{ $percentComplete }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>{{ $tenant->lease_start_date->format('M d, Y') }}</span>
                    <span>{{ $tenant->lease_end_date->format('M d, Y') }}</span>
                </div>

                <div class="mt-4 p-3 {{ $tenant->lease_end_date->isPast() ? 'bg-red-50' : ($tenant->lease_end_date->diffInDays(now()) < 30 ? 'bg-yellow-50' : 'bg-gray-50') }} rounded-lg">
                    @if($tenant->lease_end_date->isPast())
                        <p class="text-sm text-red-600">Lease expired {{ $tenant->lease_end_date->diffForHumans() }}</p>
                    @elseif($tenant->lease_end_date->diffInDays(now()) < 30)
                        <p class="text-sm text-yellow-600">Lease expires in {{ $tenant->lease_end_date->diffForHumans() }}</p>
                    @else
                        <p class="text-sm text-gray-600">Lease expires in {{ $tenant->lease_end_date->diffForHumans() }}</p>
                    @endif
                </div>
            </div>

            <div class="mb-4 mt-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-xs font-medium uppercase text-gray-500 mb-1">Move In Date</h4>
                        <p class="text-sm font-semibold text-gray-900">{{ $tenant->move_in_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-medium uppercase text-gray-500 mb-1">Move Out Date</h4>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $tenant->move_out_date ? $tenant->move_out_date->format('M d, Y') : 'Not specified' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-4 mt-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-xs font-medium uppercase text-gray-500 mb-1">Monthly Rent</h4>
                        <p class="text-lg font-bold text-gray-900">₦{{ number_format($tenant->rent_amount, 2) }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-medium uppercase text-gray-500 mb-1">Security Deposit</h4>
                        <p class="text-lg font-bold text-gray-900">₦{{ number_format($tenant->deposit_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-xs font-medium uppercase text-gray-500 mb-2">Landlord</h4>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $tenant->landlord->name }}</p>
                        <p class="text-xs text-gray-500">{{ $tenant->landlord->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Information -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Property Information</h2>
                <a href="{{ route('estate.properties.show', $tenant->property) }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">View Property</a>
            </div>
        </div>

        <div class="p-6">
            <div class="flex items-start space-x-4 mb-6">
                <div class="flex-shrink-0 h-14 w-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $tenant->property->property_name }}</h3>
                    <div class="flex items-center text-gray-600 text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ ucfirst($tenant->property->property_type) }}
                        </span>
                        <span class="mx-2">•</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $tenant->property->status == 'available' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $tenant->property->status == 'occupied' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $tenant->property->status == 'vacant' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $tenant->property->status == 'maintenance' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $tenant->property->status == 'reserved' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ ucfirst($tenant->property->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-xs font-medium uppercase text-gray-500 mb-2">Property Address</h4>
                <p class="text-sm text-gray-900">
                    @if($tenant->property->street_number || $tenant->property->street_name || $tenant->property->street)
                        {{ $tenant->property->street_number }} {{ $tenant->property->street_name ?: $tenant->property->street }}<br>
                        {{ $tenant->property->estate->name }}
                    @else
                        {{ $tenant->property->estate->name }}
                    @endif
                </p>
            </div>

            <div class="mb-4 mt-6">
                <h4 class="text-xs font-medium uppercase text-gray-500 mb-2">Property Details</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Bedrooms:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $tenant->property->bedrooms_per_unit }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Bathrooms:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $tenant->property->bathrooms_per_unit }}</span>
                    </div>
                </div>

                @if($tenant->property->size_sqm)
                <div class="mt-2">
                    <span class="text-sm text-gray-600">Size:</span>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ number_format($tenant->property->size_sqm) }} {{ $tenant->property->size_unit ?: 'sqm' }}
                    </span>
                </div>
                @endif

                @if($tenant->property->utilities_included)
                <div class="mt-4">
                    <h5 class="text-sm font-medium text-gray-700 mb-1">Utilities Included</h5>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tenant->property->utilities_included as $utility)
                        <span class="px-2 py-1 rounded-full text-xs bg-blue-50 text-blue-700">{{ ucfirst($utility) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($tenant->property->features)
                <div class="mt-4">
                    <h5 class="text-sm font-medium text-gray-700 mb-1">Features</h5>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tenant->property->features as $feature)
                        <span class="px-2 py-1 rounded-full text-xs bg-green-50 text-green-700">{{ ucfirst($feature) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="mt-6">
                <h4 class="text-xs font-medium uppercase text-gray-500 mb-2">Other Tenants</h4>
                @php
                    $otherTenants = $tenant->property->tenants()->where('id', '!=', $tenant->id)->where('status', 'active')->get();
                @endphp

                @if($otherTenants->count() > 0)
                <div class="space-y-3">
                    @foreach($otherTenants as $otherTenant)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <a href="{{ route('estate.tenants.show', $otherTenant) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                @if($otherTenant->user)
                                    {{ $otherTenant->user->name }}
                                @else
                                    {{ $otherTenant->first_name }} {{ $otherTenant->last_name }}
                                @endif
                            </a>
                            <p class="text-xs text-gray-500">Since {{ $otherTenant->move_in_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">No other active tenants in this property.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Maintenance Requests and Payment History would go here in additional rows -->
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <!-- Payment History -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Payment History</h2>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All Payments</a>
            </div>
        </div>

        <div class="p-6">
            <!-- Replace with actual payment records when implemented -->
            <p class="text-sm text-gray-500">Payment records will be displayed here once implemented.</p>

            <!-- Placeholder for future payment records -->
            <div class="mt-4 hidden">
                <div class="space-y-3">
                    <!-- Example payment record -->
                    <div class="p-3 border border-gray-200 rounded-lg flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900">November 2023 Rent</p>
                            <p class="text-xs text-gray-500">Paid on Nov 2, 2023</p>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-green-600">₦100,000.00</span>
                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Requests -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Maintenance Requests</h2>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All Requests</a>
            </div>
        </div>

        <div class="p-6">
            <!-- Replace with actual maintenance requests when implemented -->
            <p class="text-sm text-gray-500">Maintenance requests will be displayed here once implemented.</p>

            <!-- Placeholder for future maintenance requests -->
            <div class="mt-4 hidden">
                <div class="space-y-3">
                    <!-- Example maintenance request -->
                    <div class="p-3 border border-gray-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <p class="text-sm font-medium text-gray-900">Water Leak in Bathroom</p>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Submitted on Oct 15, 2023</p>
                        <p class="text-sm text-gray-600 mt-2">Water leaking from sink pipe. Plumber scheduled for Friday.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
