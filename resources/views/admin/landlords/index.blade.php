@extends('layouts.app')

@section('title', 'Landlords Management')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Landlords</h2>
        <a href="{{ route('admin.landlords.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Add Landlord
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.landlords.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Search by name, email, phone..."
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="estate_id" class="block text-sm font-medium text-gray-700 mb-1">Estate</label>
                <select name="estate_id" id="estate_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Estates</option>
                    @foreach(App\Models\Estate::orderBy('name')->get() as $estate)
                        <option value="{{ $estate->id }}" {{ request('estate_id') == $estate->id ? 'selected' : '' }}>
                            {{ $estate->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>Company</option>
                    <option value="individual" {{ request('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Landlords Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($landlords as $landlord)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        @if($landlord->is_company)
                            <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-1 rounded">Company</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">Individual</span>
                        @endif

                        @if($landlord->is_active)
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded ml-1">Active</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded ml-1">Inactive</span>
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.landlords.edit', $landlord) }}" class="text-gray-500 hover:text-blue-600" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('admin.landlords.show', $landlord) }}" class="text-gray-500 hover:text-blue-600" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>

                <div class="p-4">
                    <div class="flex items-start mb-4">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4">
                            <span class="text-lg font-bold">
                                {{ substr($landlord->is_company ? $landlord->company_name : ($landlord->contact_person ?? $landlord->user->name ?? 'N/A'), 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 line-clamp-1">
                                {{ $landlord->is_company ? $landlord->company_name : ($landlord->contact_person ?? $landlord->user->name ?? 'N/A') }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                {{ $landlord->estate->name ?? 'No Estate' }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        @if($landlord->email)
                            <div class="flex text-sm">
                                <i class="fas fa-envelope text-gray-500 w-5 mt-1"></i>
                                <span class="text-gray-700 ml-2 line-clamp-1">{{ $landlord->email }}</span>
                            </div>
                        @endif

                        @if($landlord->phone)
                            <div class="flex text-sm">
                                <i class="fas fa-phone text-gray-500 w-5 mt-1"></i>
                                <span class="text-gray-700 ml-2">{{ $landlord->phone }}</span>
                            </div>
                        @endif

                        <div class="flex text-sm">
                            <i class="fas fa-building text-gray-500 w-5 mt-1"></i>
                            <span class="text-gray-700 ml-2">{{ $landlord->properties_count ?? 0 }} Properties</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-200 flex justify-between">
                        <a href="{{ route('admin.landlords.properties', $landlord) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-building mr-1"></i> Properties
                        </a>
                        <a href="{{ route('admin.landlords.tenants', $landlord) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-users mr-1"></i> Tenants
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-user-tie text-4xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-800 mb-1">No Landlords Found</h3>
                <p class="text-gray-600 mb-4">No landlords match your search criteria or none have been added yet.</p>
                <a href="{{ route('admin.landlords.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Add Your First Landlord
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $landlords->links() }}
    </div>
</div>
@endsection
