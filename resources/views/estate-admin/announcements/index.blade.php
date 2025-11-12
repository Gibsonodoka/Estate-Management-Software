@extends('layouts.estate-app')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Announcements</h1>
            <p class="text-sm text-gray-600 mt-1">Manage estate announcements and notifications</p>
        </div>
        <a href="{{ route('estate.announcements.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Announcement
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
    {{ session('success') }}
</div>
@endif

<!-- Announcements List -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    @forelse($announcements as $announcement)
    <div class="p-6 border-b border-gray-200 last:border-0 hover:bg-gray-50 transition">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h3>

                    <!-- Priority Badge -->
                    <span class="ml-3 px-3 py-1 text-xs font-semibold rounded-full
                        {{ $announcement->priority == 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $announcement->priority == 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $announcement->priority == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $announcement->priority == 'low' ? 'bg-blue-100 text-blue-800' : '' }}">
                        {{ ucfirst($announcement->priority) }} Priority
                    </span>

                    <!-- Target Audience Badge -->
                    <span class="ml-2 px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                        {{ ucfirst($announcement->target_audience) }}
                    </span>
                </div>

                <p class="text-gray-700 mb-3">{{ Str::limit($announcement->content, 200) }}</p>

                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Posted by {{ $announcement->creator->name ?? 'Admin' }}</span>
                    <span class="mx-2">•</span>
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $announcement->created_at->format('M d, Y \a\t h:i A') }}</span>
                </div>
            </div>

            <div class="ml-4">
                <button onclick="if(confirm('Are you sure you want to delete this announcement?')) { document.getElementById('delete-form-{{ $announcement->id }}').submit(); }"
                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                    Delete
                </button>
                <form id="delete-form-{{ $announcement->id }}" action="{{ route('estate.announcements.destroy', $announcement) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No announcements</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new announcement.</p>
        <div class="mt-6">
            <a href="{{ route('estate.announcements.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Announcement
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($announcements->hasPages())
<div class="mt-6">
    {{ $announcements->links() }}
</div>
@endif
@endsection
