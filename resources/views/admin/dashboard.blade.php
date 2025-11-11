@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-600 mt-1">Here's what's happening with your estates today.</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">{{ now()->format('l, F j, Y') }}</p>
            <p class="text-xs text-gray-500">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Estates -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 uppercase">Total Estates</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['estates'] ?? 0 }}</p>
                <p class="text-sm text-green-600 mt-2">
                    <span class="font-semibold">↑ Active</span>
                </p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Properties -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 uppercase">Total Properties</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['properties'] ?? 0 }}</p>
                <p class="text-sm text-blue-600 mt-2">
                    <span class="font-semibold">Managed</span>
                </p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 22V12h6v10"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Tenants -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 uppercase">Active Tenants</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['tenants'] ?? 0 }}</p>
                <p class="text-sm text-purple-600 mt-2">
                    <span class="font-semibold">Current</span>
                </p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 uppercase">Total Users</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['users'] ?? 0 }}</p>
                <p class="text-sm text-yellow-600 mt-2">
                    <span class="font-semibold">Registered</span>
                </p>
            </div>
            <div class="bg-yellow-100 rounded-full p-4">
                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-3">
            <a href="{{ route('admin.estates.create') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition duration-150">
                <div class="bg-blue-500 rounded-lg p-3 mr-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Add New Estate</p>
                    <p class="text-sm text-gray-600">Create a new estate profile</p>
                </div>
            </a>

            <a href="{{ route('admin.properties.create') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition duration-150">
                <div class="bg-green-500 rounded-lg p-3 mr-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Add New Property</p>
                    <p class="text-sm text-gray-600">Register a new property</p>
                </div>
            </a>

            <a href="{{ route('admin.tenants.index') }}" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition duration-150">
                <div class="bg-purple-500 rounded-lg p-3 mr-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Manage Tenants</p>
                    <p class="text-sm text-gray-600">View all tenant records</p>
                </div>
            </a>

            <a href="{{ route('admin.payments.index') }}" class="flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition duration-150">
                <div class="bg-yellow-500 rounded-lg p-3 mr-4">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Payment Records</p>
                    <p class="text-sm text-gray-600">View payment history</p>
                </div>
            </a>
        </div>
    </div>

    <!-- System Overview -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">System Overview</h3>
        <div class="space-y-4">
            <!-- Occupancy Rate -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Occupancy Rate</span>
                    <span class="text-sm font-semibold text-gray-900">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>

            <!-- Maintenance Requests -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Pending Maintenance</span>
                    <span class="text-sm font-semibold text-gray-900">0</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>

            <!-- Payment Collection -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Payment Collection Rate</span>
                    <span class="text-sm font-semibold text-gray-900">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Recent Activity</h4>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="bg-gray-200 rounded-full p-2 mr-3">
                            <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">No recent activity</p>
                            <p class="text-xs text-gray-500">Start by adding estates and properties</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert for getting started -->
@if(($stats['estates'] ?? 0) == 0)
<div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
    <div class="flex items-start">
        <svg class="h-6 w-6 text-blue-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Getting Started</h3>
            <p class="text-blue-800 mb-4">Welcome to your Estate Management Platform! To get started, you'll need to:</p>
            <ol class="list-decimal list-inside space-y-2 text-blue-800">
                <li>Create your first estate</li>
                <li>Add properties to your estates</li>
                <li>Register tenants and assign them to properties</li>
                <li>Set up payment tracking</li>
            </ol>
            <div class="mt-4">
                <a href="{{ route('admin.estates.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-150">
                    Create Your First Estate
                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
