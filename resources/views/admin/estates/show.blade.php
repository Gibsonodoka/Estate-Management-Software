@extends('layouts.app')

@section('title', 'Estate Details')
@section('header', $estate->name)

@section('content')
<div class="space-y-6">
    <!-- Estate Info Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $estate->name }}</h2>
                <p class="text-gray-600 mt-1">UCI: {{ $estate->uci }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.estates.edit', $estate) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Estate
                </a>
                @if($estate->is_active)
                    <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg font-semibold">Active</span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-800 rounded-lg font-semibold">Inactive</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Location</h3>
                <p class="text-gray-900">{{ $estate->address }}</p>
                <p class="text-gray-900">{{ $estate->city }}, {{ $estate->state }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Estate Admin</h3>
                <p class="text-gray-900">{{ $estate->admin->name ?? 'N/A' }}</p>
                <p class="text-gray-600">{{ $estate->admin->email ?? '' }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Subscription Status</h3>
                <p class="text-gray-900 capitalize">{{ $estate->subscription_status }}</p>
                <p class="text-gray-600">Expires: {{ $estate->subscription_expires_at ? $estate->subscription_expires_at->format('M d, Y') : 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Monthly Fee</h3>
                <p class="text-gray-900">₦{{ number_format($estate->monthly_fee ?? 0, 2) }}</p>
            </div>
        </div>

        @if($estate->description)
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
            <p class="text-gray-900">{{ $estate->description }}</p>
        </div>
        @endif
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Properties</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $estate->properties->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $estate->users->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Created</p>
                    <p class="text-lg font-bold text-gray-900">{{ $estate->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Properties -->
    @if($estate->properties->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Properties</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Property #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($estate->properties->take(5) as $property)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $property->property_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($property->type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₦{{ number_format($property->rent_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $property->status == 'available' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($property->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
