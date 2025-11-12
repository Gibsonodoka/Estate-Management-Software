@extends('layouts.estate-app')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Announcement</h1>
            <p class="text-sm text-gray-600 mt-1">Post a new announcement to residents</p>
        </div>
        <a href="{{ route('estate.announcements.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Announcements
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm">
    <form action="{{ route('estate.announcements.store') }}" method="POST" class="p-6">
        @csrf

        <!-- Title -->
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                Announcement Title <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                   placeholder="e.g., Estate Maintenance Notice"
                   required>
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Priority -->
        <div class="mb-6">
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                Priority Level <span class="text-red-500">*</span>
            </label>
            <select name="priority"
                    id="priority"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('priority') border-red-500 @enderror"
                    required>
                <option value="">Select Priority</option>
                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - General Information</option>
                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium - Important Notice</option>
                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Requires Attention</option>
                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent - Immediate Action Required</option>
            </select>
            @error('priority')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Target Audience -->
        <div class="mb-6">
            <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">
                Target Audience <span class="text-red-500">*</span>
            </label>
            <select name="target_audience"
                    id="target_audience"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('target_audience') border-red-500 @enderror"
                    required>
                <option value="">Select Audience</option>
                <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>All Residents</option>
                <option value="tenants" {{ old('target_audience') == 'tenants' ? 'selected' : '' }}>Tenants Only</option>
                <option value="landlords" {{ old('target_audience') == 'landlords' ? 'selected' : '' }}>Property Owners Only</option>
                <option value="security" {{ old('target_audience') == 'security' ? 'selected' : '' }}>Security Personnel</option>
            </select>
            @error('target_audience')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Content -->
        <div class="mb-6">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                Announcement Content <span class="text-red-500">*</span>
            </label>
            <textarea name="content"
                      id="content"
                      rows="8"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-500 @enderror"
                      placeholder="Write your announcement here..."
                      required>{{ old('content') }}</textarea>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Be clear and concise. Include all necessary details and any action items.</p>
        </div>

        <!-- Preview Box -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Preview Tips</span>
            </div>
            <ul class="text-sm text-gray-600 space-y-1 ml-7">
                <li>• Announcements will be displayed on residents' dashboards</li>
                <li>• High priority and urgent announcements will be highlighted</li>
                <li>• All announcements are timestamped automatically</li>
            </ul>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('estate.announcements.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                Publish Announcement
            </button>
        </div>
    </form>
</div>
@endsection
