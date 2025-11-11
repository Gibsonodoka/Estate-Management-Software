@extends('layouts.estate-app')

@section('title', 'Tenants')
@section('header', 'Tenants')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Tenants in {{ $estate->name }}</h2>
    <p class="text-gray-600 mt-1">Manage your estate tenants</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Property</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lease Period</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rent</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tenants as $tenant)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($tenant->user->name) }}" alt="">
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $tenant->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $tenant->user->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $tenant->property->property_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $tenant->lease_start_date ? $tenant->lease_start_date->format('M d, Y') : 'N/A' }}
                    to {{ $tenant->lease_end_date ? $tenant->lease_end_date->format('M d, Y') : 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    ₦{{ number_format($tenant->rent_amount, 2) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($tenant->status == 'active')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($tenant->status) }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">No tenants found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $tenants->links() }}
</div>
@endsection
