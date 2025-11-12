@extends('layouts.estate-app')

@section('title', 'Tenant Details')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Tenant Details</h1>
        <div>
            <a href="{{ route('estate.tenants.edit', $tenant) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Tenant
            </a>
            <a href="{{ route('estate.tenants.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Tenants
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Tenant Profile Info -->
        <div class="col-xl-4 col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="m-0"><i class="fas fa-user me-2"></i>Tenant Profile</h5>
                    @if($tenant->status === 'active')
                        <span class="badge bg-success">Active</span>
                    @elseif($tenant->status === 'notice_given')
                        <span class="badge bg-warning">Notice Given</span>
                    @elseif($tenant->status === 'moved_out')
                        <span class="badge bg-secondary">Moved Out</span>
                    @elseif($tenant->status === 'evicted')
                        <span class="badge bg-danger">Evicted</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar-placeholder">
                                <span class="initials">{{ substr($tenant->user->name ?? 'N/A', 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ $tenant->user->name ?? 'No User Account' }}</h5>
                            <p class="text-muted mb-0">
                                @if($tenant->user)
                                    <i class="fas fa-envelope me-1"></i> {{ $tenant->user->email }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        @if($tenant->user && $tenant->user->phone)
                            <p class="mb-1">
                                <i class="fas fa-phone me-2"></i> {{ $tenant->user->phone }}
                            </p>
                        @endif
                    </div>

                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <th width="40%">Move In Date:</th>
                                <td>{{ $tenant->move_in_date->format('d M, Y') }}</td>
                            </tr>
                            @if($tenant->move_out_date)
                            <tr>
                                <th>Move Out Date:</th>
                                <td>{{ $tenant->move_out_date->format('d M, Y') }}</td>
                            </tr>
                            @endif

                            @if($tenant->lease_start_date)
                            <tr>
                                <th>Lease Start:</th>
                                <td>{{ $tenant->lease_start_date->format('d M, Y') }}</td>
                            </tr>
                            @else
                            <tr>
                                <th>Lease Start:</th>
                                <td>N/A</td>
                            </tr>
                            @endif

                            @if($tenant->lease_end_date)
                            <tr>
                                <th>Lease End:</th>
                                <td>{{ $tenant->lease_end_date->format('d M, Y') }}</td>
                            </tr>
                            @else
                            <tr>
                                <th>Lease End:</th>
                                <td>N/A</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Tenant Since:</th>
                                <td>{{ $tenant->created_at->format('d M, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $tenant->updated_at->format('d M, Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lease Details -->
        <div class="col-xl-4 col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="m-0"><i class="fas fa-file-contract me-2"></i>Lease Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        @if($tenant->lease_start_date && $tenant->lease_end_date)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Lease Period:</span>
                                <span class="fw-bold">
                                    {{ $tenant->lease_end_date->diffInMonths($tenant->lease_start_date) }} months
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                @php
                                    $totalDays = $tenant->lease_end_date->diffInDays($tenant->lease_start_date);
                                    $daysElapsed = now()->diffInDays($tenant->lease_start_date);
                                    $percentComplete = ($daysElapsed / $totalDays) * 100;
                                    $percentComplete = min(100, max(0, $percentComplete));
                                @endphp
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentComplete }}%"
                                    aria-valuenow="{{ $percentComplete }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>{{ $tenant->lease_start_date->format('M d, Y') }}</small>
                                <small>{{ $tenant->lease_end_date->format('M d, Y') }}</small>
                            </div>
                        @else
                            <div class="alert alert-light">
                                <i class="fas fa-info-circle"></i> No lease information available
                            </div>
                        @endif
                    </div>

                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Rent Amount:</th>
                                <td class="fw-bold">₦{{ number_format($tenant->rent_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Deposit:</th>
                                <td>₦{{ number_format($tenant->deposit_amount, 2) }}</td>
                            </tr>
                            @if($tenant->notice_date)
                            <tr>
                                <th>Notice Given:</th>
                                <td>{{ $tenant->notice_date->format('d M, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Notice Period:</th>
                                <td>{{ $tenant->notice_period_days }} days</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    @if($tenant->notes)
                    <div class="mt-3">
                        <h6>Notes:</h6>
                        <p class="text-muted mb-0">{{ $tenant->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Buttons & Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="m-0"><i class="fas fa-tasks me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-file-invoice"></i> View Invoices
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-wrench"></i> Maintenance Requests
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-info btn-sm w-100 mt-2">
                                <i class="fas fa-envelope"></i> Send Message
                            </a>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('estate.tenants.destroy', $tenant) }}" method="POST" class="mt-2"
                                onsubmit="return confirm('Are you sure you want to delete this tenant? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    <i class="fas fa-trash"></i> Remove Tenant
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Details -->
        <div class="col-xl-4 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="m-0"><i class="fas fa-home me-2"></i>Property Details</h5>
                </div>
                <div class="card-body">
                    @if($tenant->property)
                    <h5 class="fw-bold mb-3">{{ $tenant->property->property_name }}</h5>
                    <p class="text-muted mb-3">
                        {{ $tenant->property->fullAddress }}
                    </p>

                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Property Type:</th>
                                <td>{{ ucfirst($tenant->property->property_type) }}</td>
                            </tr>
                            <tr>
                                <th>Bedrooms:</th>
                                <td>{{ $tenant->property->bedrooms_per_unit ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Bathrooms:</th>
                                <td>{{ $tenant->property->bathrooms_per_unit ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Floor:</th>
                                <td>{{ $tenant->property->floor_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Size:</th>
                                <td>
                                    @if($tenant->property->size_sqm)
                                        {{ $tenant->property->size_sqm }} {{ $tenant->property->size_unit ?? 'sqm' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($tenant->property->status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($tenant->property->status === 'occupied')
                                        <span class="badge bg-primary">Occupied</span>
                                    @elseif($tenant->property->status === 'vacant')
                                        <span class="badge bg-warning">Vacant</span>
                                    @elseif($tenant->property->status === 'maintenance')
                                        <span class="badge bg-danger">Maintenance</span>
                                    @elseif($tenant->property->status === 'reserved')
                                        <span class="badge bg-info">Reserved</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if($tenant->property->description)
                    <div class="mt-3">
                        <h6>Description:</h6>
                        <p class="text-muted mb-0">{{ $tenant->property->description }}</p>
                    </div>
                    @endif

                    @if($tenant->property->features && count($tenant->property->features))
                    <div class="mt-3">
                        <h6>Features:</h6>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($tenant->property->features as $feature)
                            <span class="badge bg-light text-dark">{{ $feature }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('estate.properties.show', $tenant->property) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> View Property
                        </a>
                    </div>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i> No property associated with this tenant.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Landlord Information -->
            @if($tenant->landlord)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="m-0"><i class="fas fa-user-tie me-2"></i>Landlord Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar-placeholder bg-secondary">
                                <span class="initials">{{ substr($tenant->landlord->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ $tenant->landlord->name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-envelope me-1"></i> {{ $tenant->landlord->email }}
                            </p>
                        </div>
                    </div>

                    @if($tenant->landlord->phone)
                    <p class="mb-2">
                        <i class="fas fa-phone me-2"></i> {{ $tenant->landlord->phone }}
                    </p>
                    @endif

                    <a href="#" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-envelope me-1"></i> Contact Landlord
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .avatar-placeholder {
        width: 50px;
        height: 50px;
        background-color: #6c757d;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .initials {
        text-transform: uppercase;
    }
</style>
@endsection
