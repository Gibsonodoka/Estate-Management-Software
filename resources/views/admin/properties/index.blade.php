@extends('layouts.app')

@section('title', 'Properties')
@section('header', 'Properties Management')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">All Properties</h2>
            <p class="text-gray-600 mt-1">Manage estate properties and rentals</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- View Toggle -->
            <div class="bg-gray-100 p-1 rounded-lg flex items-center">
                <button type="button" id="list-view-btn" class="px-3 py-1.5 rounded-md text-gray-600 flex items-center text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    List
                </button>
                <button type="button" id="grid-view-btn" class="px-3 py-1.5 rounded-md text-gray-600 flex items-center text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Grid
                </button>
            </div>

            <a href="{{ route('admin.properties.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Property
            </a>
        </div>
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
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <select name="estate" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Estates</option>
                @foreach($estates ?? [] as $estate)
                    <option value="{{ $estate->id }}" {{ request('estate') == $estate->id ? 'selected' : '' }}>
                        {{ $estate->name }}
                    </option>
                @endforeach
            </select>

            <select name="landlord_id" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Landlords</option>
                @foreach($landlords ?? [] as $landlord)
                    <option value="{{ $landlord->id }}" {{ request('landlord_id') == $landlord->id ? 'selected' : '' }}>
                        @if($landlord->is_company)
                            {{ $landlord->company_name }} (Company)
                        @else
                            {{ $landlord->contact_person ?? ($landlord->user ? $landlord->user->name : 'Unknown') }}
                        @endif
                    </option>
                @endforeach
            </select>

            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="vacant" {{ request('status') == 'vacant' ? 'selected' : '' }}>Vacant</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
            </select>

            <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Categories</option>
                <option value="residential" {{ request('type') == 'residential' ? 'selected' : '' }}>Residential</option>
                <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
            </select>

            <select name="property_type" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Types</option>
                <option value="apartment" {{ request('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                <option value="duplex" {{ request('property_type') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                <option value="bungalow" {{ request('property_type') == 'bungalow' ? 'selected' : '' }}>Bungalow</option>
                <option value="flat" {{ request('property_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                <option value="penthouse" {{ request('property_type') == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                <option value="studio" {{ request('property_type') == 'studio' ? 'selected' : '' }}>Studio</option>
            </select>

            <div>
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg w-full">Filter</button>
            </div>
        </form>
    </div>

    <!-- Table View (Default) -->
    <div id="list-view" class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S/N</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Landlord</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenants</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($properties as $index => $property)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $properties->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $property->property_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $property->old_property_number ?? $property->property_number ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $property->estate->name }}</div>
                            <div class="text-sm text-gray-500">{{ $property->estate->city }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($property->landlord)
                                <div class="text-sm text-gray-900">
                                    @if($property->landlord->is_company)
                                        <span class="inline-flex items-center">
                                            {{ $property->landlord->company_name }}
                                            <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full bg-purple-100 text-purple-800">Company</span>
                                        </span>
                                    @else
                                        {{ $property->landlord->contact_person ?? ($property->landlord->user ? $property->landlord->user->name : 'Unknown') }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($property->landlord->phone)
                                        <span class="mr-1">{{ $property->landlord->phone }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-500">No landlord</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($property->type ?? 'Residential') }}
                            </span>
                            @if($property->property_type)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 mt-1">
                                    {{ ucfirst($property->property_type) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $property->bedrooms_per_unit ?? $property->old_bedrooms ?? 0 }}BR / {{ $property->bathrooms_per_unit ?? $property->old_bathrooms ?? 0 }}BA
                            @if($property->size_sqm)
                                <br>{{ number_format($property->size_sqm) }} {{ $property->size_unit ?? 'sqm' }}
                            @elseif($property->size)
                                <br>{{ number_format($property->size) }} {{ $property->size_unit ?? 'sqft' }}
                            @endif
                            @if($property->units > 1)
                                <br>{{ $property->units }} units
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">₦{{ number_format($property->rent_amount_per_unit ?? $property->old_rent_amount, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $property->rent_period ? ucfirst($property->rent_period) : 'per month' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($property->status == 'available')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                            @elseif($property->status == 'occupied')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Occupied</span>
                            @elseif($property->status == 'vacant')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Vacant</span>
                            @elseif($property->status == 'reserved')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Reserved</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Maintenance</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="font-semibold">{{ $property->activeTenants()->count() ?? 0 }}</span> active
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.properties.edit', $property) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <a href="{{ route('admin.properties.show', $property) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this property? This action cannot be undone.')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="mt-4 text-gray-500">No properties found. Add your first property.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.properties.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-block">
                                    Add New Property
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grid View (Hidden by Default) -->
    <div id="grid-view" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 hidden">
        @forelse($properties as $index => $property)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <div class="flex items-center">
                            <span class="bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 rounded-full mr-2">
                                #{{ $properties->firstItem() + $index }}
                            </span>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $property->property_name }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="font-medium">Estate:</span> {{ $property->estate->name }}
                        </p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $property->status == 'available' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $property->status == 'occupied' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $property->status == 'vacant' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $property->status == 'maintenance' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $property->status == 'reserved' ? 'bg-purple-100 text-purple-800' : '' }}">
                        {{ ucfirst($property->status) }}
                    </span>
                </div>

                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>{{ $property->full_address ?: 'Address not set' }}</span>
                </div>

                <!-- Add Landlord Info to Grid View -->
                <div class="mt-2 flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>
                        @if($property->landlord)
                            @if($property->landlord->is_company)
                                {{ $property->landlord->company_name }} <span class="text-xs text-purple-600">(Company)</span>
                            @else
                                {{ $property->landlord->contact_person ?? ($property->landlord->user ? $property->landlord->user->name : 'Unknown') }}
                            @endif
                        @else
                            <span class="text-gray-500">No landlord assigned</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Property Type</p>
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst($property->property_type) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Units</p>
                        <p class="text-sm font-medium text-gray-900">{{ $property->units }} {{ $property->units == 1 ? 'Unit' : 'Units' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Bedrooms</p>
                        <p class="text-sm font-medium text-gray-900">{{ $property->bedrooms_per_unit ?? $property->old_bedrooms ?? '0' }} BR</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Bathrooms</p>
                        <p class="text-sm font-medium text-gray-900">{{ $property->bathrooms_per_unit ?? $property->old_bathrooms ?? '0' }} BA</p>
                    </div>
                </div>

                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-gray-600">Occupancy</span>
                        <span class="text-xs font-semibold text-gray-900">{{ $property->activeTenants()->count() }}/{{ $property->units }} Units</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($property->activeTenants()->count() / max(1, $property->units)) * 100 }}%"></div>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 rounded-lg mb-4">
                    <p class="text-xs text-gray-600 mb-1">Rent</p>
                    <p class="text-2xl font-bold text-blue-600">₦{{ number_format($property->rent_amount_per_unit ?? $property->old_rent_amount, 2) }}</p>
                    <p class="text-xs text-gray-600">{{ ucfirst($property->rent_period) }}</p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <a href="{{ route('admin.properties.edit', $property) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Edit
                </a>
                <a href="{{ route('admin.properties.show', $property) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                    Details
                </a>
                <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Are you sure you want to delete this property?')"
                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No properties</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first property.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.properties.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Property
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($properties->hasPages())
    <div class="mt-6">
        {{ $properties->links() }}
    </div>
    @endif

    <script>
        // View toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const listViewBtn = document.getElementById('list-view-btn');
            const gridViewBtn = document.getElementById('grid-view-btn');
            const listView = document.getElementById('list-view');
            const gridView = document.getElementById('grid-view');

            // Set initial state from local storage or default to list view
            const currentView = localStorage.getItem('propertyViewPreferenceAdmin') || 'list';

            function setView(view) {
                // Save preference to local storage
                localStorage.setItem('propertyViewPreferenceAdmin', view);

                if (view === 'list') {
                    listView.classList.remove('hidden');
                    gridView.classList.add('hidden');
                    listViewBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-700');
                    listViewBtn.classList.remove('text-gray-600');
                    gridViewBtn.classList.remove('bg-white', 'shadow-sm', 'text-gray-700');
                    gridViewBtn.classList.add('text-gray-600');
                } else {
                    gridView.classList.remove('hidden');
                    listView.classList.add('hidden');
                    gridViewBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-700');
                    gridViewBtn.classList.remove('text-gray-600');
                    listViewBtn.classList.remove('bg-white', 'shadow-sm', 'text-gray-700');
                    listViewBtn.classList.add('text-gray-600');
                }
            }

            // Set initial view
            setView(currentView);

            // Add click event listeners
            listViewBtn.addEventListener('click', function() {
                setView('list');
            });

            gridViewBtn.addEventListener('click', function() {
                setView('grid');
            });
        });
    </script>
</div>
@endsection
