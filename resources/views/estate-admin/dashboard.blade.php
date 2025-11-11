@extends('layouts.estate-app')

@section('content')
<!-- Professional Welcome Section -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $estate->name }}</h1>
            <p class="text-sm text-gray-600 mt-1">{{ $estate->address }}, {{ $estate->city }}, {{ $estate->state }}</p>
        </div>
        <a href="{{ route('estate.settings') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Estate Settings
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Properties -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 uppercase">Total Properties</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['properties'] }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    <span class="text-green-600 font-semibold">{{ $stats['available_properties'] }}</span> Available
                    <span class="mx-2">•</span>
                    <span class="text-yellow-600 font-semibold">{{ $stats['occupied_properties'] }}</span> Occupied
                </p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Tenants -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 uppercase">Active Tenants</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['tenants'] }}</p>
                <p class="text-sm text-purple-600 mt-2">
                    <span class="font-semibold">Current Residents</span>
                </p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 uppercase">Total Revenue</p>
                <p class="text-3xl font-bold text-green-600 mt-2">₦{{ number_format($stats['total_revenue'], 2) }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    <span class="text-yellow-600 font-semibold">{{ $stats['pending_payments'] }}</span> Pending
                </p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Maintenance Requests -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 uppercase">Pending Maintenance</p>
                <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['maintenance_requests'] }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    <span class="font-semibold">Requires Attention</span>
                </p>
            </div>
            <div class="bg-red-100 rounded-full p-4">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('estate.properties.create') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-center">
            <div class="bg-blue-100 rounded-lg p-3 mr-4">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Add Property</p>
                <p class="text-sm text-gray-600">Register new property</p>
            </div>
        </div>
    </a>

    <a href="{{ route('estate.tenants.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-center">
            <div class="bg-purple-100 rounded-lg p-3 mr-4">
                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Manage Tenants</p>
                <p class="text-sm text-gray-600">View tenant records</p>
            </div>
        </div>
    </a>

    <a href="{{ route('estate.announcements.create') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-center">
            <div class="bg-green-100 rounded-lg p-3 mr-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900">New Announcement</p>
                <p class="text-sm text-gray-600">Notify residents</p>
            </div>
        </div>
    </a>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Payments -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                <a href="{{ route('estate.payments.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
        </div>
        <div class="p-6">
            @forelse($recentPayments as $payment)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($payment->user->name) }}" class="h-10 w-10 rounded-full">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->property->property_number ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-green-600">₦{{ number_format($payment->amount, 2) }}</p>
                    <p class="text-xs text-gray-500">{{ $payment->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-8">No recent payments</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Maintenance -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Maintenance</h3>
                <a href="{{ route('estate.maintenance.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
        </div>
        <div class="p-6">
            @forelse($recentMaintenance as $maintenance)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $maintenance->title }}</p>
                        <p class="text-xs text-gray-500">{{ $maintenance->property->property_number }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $maintenance->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($maintenance->status) }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">{{ $maintenance->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-8">No maintenance requests</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
