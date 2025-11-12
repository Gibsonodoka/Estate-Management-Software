@extends('layouts.estate-app')

@section('title', 'Edit Tenant')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Edit Tenant</h1>
        <a href="{{ route('estate.tenants.show', $tenant) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Tenant Details
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="m-0"><i class="fas fa-edit me-2"></i>Edit Tenant Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('estate.tenants.update', $tenant) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Tenant and Property Information Column -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Tenant & Property</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- User Account Selection -->
                                        <div class="mb-3">
                                            <label for="user_id" class="form-label">User Account</label>
                                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                                <option value="">-- No User Account --</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ old('user_id', $tenant->user_id) == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">The user account associated with this tenant.</small>
                                        </div>

                                        <!-- Property Selection -->
                                        <div class="mb-3">
                                            <label for="property_id" class="form-label">Property <span class="text-danger">*</span></label>
                                            <select name="property_id" id="property_id" class="form-select @error('property_id') is-invalid @enderror" required>
                                                <option value="">-- Select Property --</option>
                                                @foreach($properties as $property)
                                                    <option value="{{ $property->id }}" {{ old('property_id', $tenant->property_id) == $property->id ? 'selected' : '' }}>
                                                        {{ $property->property_name }} - {{ $property->fullAddress ?? $property->street }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('property_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Tenant Status -->
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="notice_given" {{ old('status', $tenant->status) == 'notice_given' ? 'selected' : '' }}>Notice Given</option>
                                                <option value="inactive" {{ old('status', $tenant->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="evicted" {{ old('status', $tenant->status) == 'evicted' ? 'selected' : '' }}>Evicted</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Lease Information Card -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Lease Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Lease Start Date -->
                                        <div class="mb-3">
                                            <label for="lease_start_date" class="form-label">Lease Start Date <span class="text-danger">*</span></label>
                                            <input type="date" name="lease_start_date" id="lease_start_date" class="form-control @error('lease_start_date') is-invalid @enderror"
                                                value="{{ old('lease_start_date', $tenant->lease_start_date ? $tenant->lease_start_date->format('Y-m-d') : '') }}" required>
                                            @error('lease_start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Lease End Date -->
                                        <div class="mb-3">
                                            <label for="lease_end_date" class="form-label">Lease End Date <span class="text-danger">*</span></label>
                                            <input type="date" name="lease_end_date" id="lease_end_date" class="form-control @error('lease_end_date') is-invalid @enderror"
                                                value="{{ old('lease_end_date', $tenant->lease_end_date ? $tenant->lease_end_date->format('Y-m-d') : '') }}" required>
                                            @error('lease_end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Move In Date -->
                                        <div class="mb-3">
                                            <label for="move_in_date" class="form-label">Move In Date <span class="text-danger">*</span></label>
                                            <input type="date" name="move_in_date" id="move_in_date" class="form-control @error('move_in_date') is-invalid @enderror"
                                                value="{{ old('move_in_date', $tenant->move_in_date ? $tenant->move_in_date->format('Y-m-d') : '') }}" required>
                                            @error('move_in_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Move Out Date -->
                                        <div class="mb-3">
                                            <label for="move_out_date" class="form-label">Move Out Date</label>
                                            <input type="date" name="move_out_date" id="move_out_date" class="form-control @error('move_out_date') is-invalid @enderror"
                                                value="{{ old('move_out_date', $tenant->move_out_date ? $tenant->move_out_date->format('Y-m-d') : '') }}">
                                            @error('move_out_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Leave blank if tenant has not moved out yet.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial and Notice Information Column -->
                            <div class="col-md-6">
                                <!-- Financial Information Card -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Financial Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Rent Amount -->
                                        <div class="mb-3">
                                            <label for="rent_amount" class="form-label">Rent Amount (₦) <span class="text-danger">*</span></label>
                                            <input type="number" name="rent_amount" id="rent_amount" class="form-control @error('rent_amount') is-invalid @enderror"
                                                value="{{ old('rent_amount', $tenant->rent_amount) }}" min="0" step="0.01" required>
                                            @error('rent_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Deposit Amount -->
                                        <div class="mb-3">
                                            <label for="deposit_amount" class="form-label">Deposit Amount (₦) <span class="text-danger">*</span></label>
                                            <input type="number" name="deposit_amount" id="deposit_amount" class="form-control @error('deposit_amount') is-invalid @enderror"
                                                value="{{ old('deposit_amount', $tenant->deposit_amount) }}" min="0" step="0.01" required>
                                            @error('deposit_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Notice Information Card -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Notice Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Notice Date -->
                                        <div class="mb-3">
                                            <label for="notice_date" class="form-label">Notice Date</label>
                                            <input type="date" name="notice_date" id="notice_date" class="form-control @error('notice_date') is-invalid @enderror"
                                                value="{{ old('notice_date', $tenant->notice_date ? $tenant->notice_date->format('Y-m-d') : '') }}">
                                            @error('notice_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">The date when the tenant gave notice to vacate.</small>
                                        </div>

                                        <!-- Notice Period -->
                                        <div class="mb-3">
                                            <label for="notice_period_days" class="form-label">Notice Period (Days)</label>
                                            <input type="number" name="notice_period_days" id="notice_period_days" class="form-control @error('notice_period_days') is-invalid @enderror"
                                                value="{{ old('notice_period_days', $tenant->notice_period_days) }}" min="0">
                                            @error('notice_period_days')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">The required notice period in days.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information Card -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Additional Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Notes -->
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea name="notes" id="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $tenant->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Any additional notes about this tenant.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('estate.tenants.show', $tenant) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Tenant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // When status changes to 'notice_given', set today's date as notice_date if empty
        const statusSelect = document.getElementById('status');
        const noticeDateInput = document.getElementById('notice_date');

        statusSelect.addEventListener('change', function() {
            if (this.value === 'notice_given' && !noticeDateInput.value) {
                const today = new Date();
                const formattedDate = today.toISOString().substring(0, 10); // YYYY-MM-DD
                noticeDateInput.value = formattedDate;
            }
        });
    });
</script>
@endsection
