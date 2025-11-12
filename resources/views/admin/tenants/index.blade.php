@extends('layouts.app')

@section('title', 'Tenants')
@section('header', 'Tenants Management')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">All Tenants</h2>
            <p class="text-gray-600 mt-1">Manage tenants across all estates</p>
        </div>
        <a href="{{ route('admin.tenants.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Tenant
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <select name="estate" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Estates</option>
                @foreach($estates ?? [] as $estate)
                    <option value="{{ $estate->id }}" {{ request('estate') == $estate->id ? 'selected' : '' }}>
                        {{ $estate->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="notice_given" {{ request('status') == 'notice_given' ? 'selected' : '' }}>Notice Given</option>
                <option value="moved_out" {{ request('status') == 'moved_out' ? 'selected' : '' }}>Moved Out</option>
                <option value="evicted" {{ request('status') == 'evicted' ? 'selected' : '' }}>Evicted</option>
            </select>
            <select name="property" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Properties</option>
                @foreach($properties ?? [] as $property)
                    <option value="{{ $property->id }}" {{ request('property') == $property->id ? 'selected' : '' }}>
                        {{ $property->property_name }}
                    </option>
                @endforeach
            </select>
            <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
            </select>
            <div>
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg w-full">Filter</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S/N</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Landlord</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lease Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tenants as $index => $tenant)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $tenants->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($tenant->user)
                                            {{ $tenant->user->name }}
                                        @else
                                            {{ $tenant->first_name }} {{ $tenant->last_name }}
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($tenant->user)
                                            {{ $tenant->user->email }}
                                        @else
                                            {{ $tenant->email }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $tenant->property->property_name }}</div>
                            <div class="text-xs text-gray-500">{{ $tenant->property->property_type }} • {{ $tenant->property->bedrooms_per_unit }}BR</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $tenant->property->estate->name }}</div>
                            <div class="text-xs text-gray-500">{{ $tenant->property->estate->city ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $tenant->landlord->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>
                                @if($tenant->lease_start_date && $tenant->lease_end_date)
                                    {{ $tenant->lease_start_date->format('M d, Y') }} - {{ $tenant->lease_end_date->format('M d, Y') }}
                                    <div class="text-xs text-gray-500">
                                        @if($tenant->lease_end_date->isPast())
                                            <span class="text-red-600">Expired</span>
                                        @else
                                            {{ $tenant->lease_end_date->diffForHumans() }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">No lease</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">₦{{ number_format($tenant->rent_amount, 2) }}</div>
                            @if($tenant->deposit_amount > 0)
                                <div class="text-xs text-gray-500">Deposit: ₦{{ number_format($tenant->deposit_amount, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($tenant->status == 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @elseif($tenant->status == 'notice_given')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Notice Given</span>
                            @elseif($tenant->status == 'moved_out')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Moved Out</span>
                            @elseif($tenant->status == 'evicted')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Evicted</span>
                            @endif

                            @if($tenant->status == 'notice_given' && $tenant->notice_date)
                                <div class="text-xs text-gray-500 mt-1">
                                    Notice: {{ $tenant->notice_date->format('M d, Y') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to remove this tenant?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="mt-4 text-gray-500">No tenants found. Add your first tenant.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.tenants.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-block">
                                    Add New Tenant
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($tenants->hasPages())
    <div class="mt-6">
        {{ $tenants->links() }}
    </div>
    @endif
</div>
@endsection
