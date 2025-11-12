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
    <h1 class="text-2xl font-bold text-gray-900 mt-3">Edit Tenant</h1>
    <p class="text-sm text-gray-600 mt-1">Update tenant details for {{ $tenant->user->name ?? $tenant->first_name . ' ' . $tenant->last_name }}</p>
</div>

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('estate.tenants.update', $tenant) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- User Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tenant Information</h3>

                <div class="mb-4">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User Account</label>
                    <select name="user_id" id="user_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">-- No User Account --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (old('user_id', $tenant->user_id) == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">You can associate this tenant with a user account or leave it unlinked.</p>
                </div>
            </div>

            <!-- Property and Lease Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Property and Lease Details</h3>

                <div class="mb-4">
                    <label for="property_id" class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                    <select name="property_id" id="property_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        <option value="">-- Select a Property --</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ (old('property_id', $tenant->property_id) == $property->id) ? 'selected' : '' }}>
                                {{ $property->property_name }} ({{ $property->bedrooms_per_unit }}BR, {{ $property->property_type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="lease_start_date" class="block text-sm font-medium text-gray-700 mb-1">Lease Start Date</label>
                        <input type="date" name="lease_start_date" id="lease_start_date" value="{{ old('lease_start_date', $tenant->lease_start_date->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    </div>

                    <div class="mb-4">
                        <label for="lease_end_date" class="block text-sm font-medium text-gray-700 mb-1">Lease End Date</label>
                        <input type="date" name="lease_end_date" id="lease_end_date" value="{{ old('lease_end_date', $tenant->lease_end_date->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="move_in_date" class="block text-sm font-medium text-gray-700 mb-1">Move In Date</label>
                        <input type="date" name="move_in_date" id="move_in_date" value="{{ old('move_in_date', $tenant->move_in_date->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    </div>

                    <div class="mb-4">
                        <label for="move_out_date" class="block text-sm font-medium text-gray-700 mb-1">Move Out Date</label>
                        <input type="date" name="move_out_date" id="move_out_date" value="{{ old('move_out_date', $tenant->move_out_date ? $tenant->move_out_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <p class="text-xs text-gray-500 mt-1">Optional. Leave blank if not applicable.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="rent_amount" class="block text-sm font-medium text-gray-700 mb-1">Rent Amount (₦)</label>
                        <input type="number" step="0.01" min="0" name="rent_amount" id="rent_amount" value="{{ old('rent_amount', $tenant->rent_amount) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    </div>

                    <div class="mb-4">
                        <label for="deposit_amount" class="block text-sm font-medium text-gray-700 mb-1">Security Deposit (₦)</label>
                        <input type="number" step="0.01" min="0" name="deposit_amount" id="deposit_amount" value="{{ old('deposit_amount', $tenant->deposit_amount) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Tenant Status</label>
                    <select name="status" id="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="notice_given" {{ old('status', $tenant->status) == 'notice_given' ? 'selected' : '' }}>Notice Given</option>
                        <option value="moved_out" {{ old('status', $tenant->status) == 'moved_out' ? 'selected' : '' }}>Moved Out</option>
                        <option value="evicted" {{ old('status', $tenant->status) == 'evicted' ? 'selected' : '' }}>Evicted</option>
                    </select>
                </div>

                <div id="notice-section" class="mb-4 {{ old('status', $tenant->status) != 'notice_given' ? 'hidden' : '' }}">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="notice_date" class="block text-sm font-medium text-gray-700 mb-1">Notice Date</label>
                            <input type="date" name="notice_date" id="notice_date" value="{{ old('notice_date', $tenant->notice_date ? $tenant->notice_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <div>
                            <label for="notice_period_days" class="block text-sm font-medium text-gray-700 mb-1">Notice Period (Days)</label>
                            <input type="number" min="0" name="notice_period_days" id="notice_period_days" value="{{ old('notice_period_days', $tenant->notice_period_days) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('notes', $tenant->notes) }}</textarea>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('estate.tenants.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition mr-2">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Update Tenant</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelect = document.getElementById('status');
        const noticeSection = document.getElementById('notice-section');

        // Toggle notice section
        statusSelect.addEventListener('change', function() {
            if (this.value === 'notice_given') {
                noticeSection.classList.remove('hidden');
            } else {
                noticeSection.classList.add('hidden');
            }
        });
    });
</script>
@endsection
