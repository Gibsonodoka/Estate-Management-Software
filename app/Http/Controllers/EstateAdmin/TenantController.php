<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants in this estate
     */
    public function index()
    {
        $estate = auth()->user()->estate;

        $tenants = Tenant::whereHas('property', function($q) use ($estate) {
            $q->where('estate_id', $estate->id);
        })->with(['user', 'property', 'landlord'])
        ->latest()
        ->paginate(15);

        return view('estate-admin.tenants.index', compact('tenants', 'estate'));
    }

    /**
     * Show form for creating a new tenant
     */
    public function create()
    {
        $estate = auth()->user()->estate;

        $properties = Property::where('estate_id', $estate->id)
            ->orderBy('property_name')
            ->get();

        // Changed from 'tenant' to 'tenantRecord' to match the relationship in User model
        $users = User::whereDoesntHave('tenantRecord')
            ->orderBy('name')
            ->get();

        return view('estate-admin.tenants.create', compact('properties', 'users', 'estate'));
    }

    /**
     * Store a newly created tenant
     */
    public function store(Request $request)
    {
        $estate = auth()->user()->estate;

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'required_without:user_id|string|max:255|nullable',
            'last_name' => 'required_without:user_id|string|max:255|nullable',
            'email' => 'required_without:user_id|email|unique:users,email|nullable',
            'phone' => 'nullable|string|max:20',
            'lease_start_date' => 'nullable|date',
            'lease_end_date' => 'nullable|date|after:lease_start_date',
            'move_in_date' => 'required|date',
            'move_out_date' => 'nullable|date|after:move_in_date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,notice_given,inactive,evicted',
            'notice_period_days' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'create_account' => 'sometimes|boolean',
        ]);

        // Verify property belongs to this estate
        $property = Property::findOrFail($validated['property_id']);
        if ($property->estate_id != $estate->id) {
            return redirect()->route('estate.tenants.create')
                ->with('error', 'This property does not belong to your estate.');
        }

        // Set landlord based on property
        $validated['landlord_id'] = $property->landlord_id;

        // Create user account if requested
        if ($request->create_account && !isset($validated['user_id']) && isset($validated['email'])) {
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make(Str::random(10)),
            ]);

            $user->assignRole('tenant');
            $validated['user_id'] = $user->id;

            // TODO: Send email notification with password reset link
        }

        // Remove fields not in tenant model
        unset($validated['first_name'], $validated['last_name'], $validated['email'], $validated['create_account']);

        // Create tenant record
        $tenant = Tenant::create($validated);

        // Update property occupancy if needed
        if ($tenant->isActive() && $property->status == 'vacant') {
            $property->update(['status' => 'occupied']);
        }

        return redirect()->route('estate.tenants.index')
            ->with('success', 'Tenant added successfully!');
    }

    /**
     * Display the specified tenant
     */
    public function show(Tenant $tenant)
    {
        $estate = auth()->user()->estate;

        // Verify property belongs to this estate
        if ($tenant->property->estate_id != $estate->id) {
            return redirect()->route('estate.tenants.index')
                ->with('error', 'This tenant does not belong to your estate.');
        }

        $tenant->load(['property', 'user', 'landlord']);

        // TODO: Load payments when payment model is implemented
        // $tenant->load(['property', 'user', 'landlord', 'payments']);

        return view('estate-admin.tenants.show', compact('tenant', 'estate'));
    }

    /**
     * Show form for editing the specified tenant
     */
    public function edit(Tenant $tenant)
    {
        $estate = auth()->user()->estate;

        // Verify property belongs to this estate
        if ($tenant->property->estate_id != $estate->id) {
            return redirect()->route('estate.tenants.index')
                ->with('error', 'This tenant does not belong to your estate.');
        }

        $properties = Property::where('estate_id', $estate->id)
            ->orderBy('property_name')
            ->get();

        // Changed from 'tenant' to 'tenantRecord' to match the relationship in User model
        $users = User::where(function($query) use ($tenant) {
            $query->whereDoesntHave('tenantRecord')
                  ->orWhere('id', $tenant->user_id);
        })->orderBy('name')->get();

        return view('estate-admin.tenants.edit', compact('tenant', 'properties', 'users', 'estate'));
    }

    /**
     * Update the specified tenant
     */
    public function update(Request $request, Tenant $tenant)
    {
        $estate = auth()->user()->estate;

        // Verify property belongs to this estate
        if ($tenant->property->estate_id != $estate->id) {
            return redirect()->route('estate.tenants.index')
                ->with('error', 'This tenant does not belong to your estate.');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'user_id' => 'nullable|exists:users,id',
            'lease_start_date' => 'nullable|date',
            'lease_end_date' => 'nullable|date|after_or_equal:lease_start_date',
            'move_in_date' => 'required|date',
            'move_out_date' => 'nullable|date|after_or_equal:move_in_date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,notice_given,inactive,evicted',
            'notice_date' => 'nullable|date',
            'notice_period_days' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Verify new property belongs to this estate
        $property = Property::findOrFail($validated['property_id']);
        if ($property->estate_id != $estate->id) {
            return redirect()->route('estate.tenants.edit', $tenant)
                ->with('error', 'This property does not belong to your estate.');
        }

        // Check if property has changed
        $propertyChanged = $tenant->property_id != $validated['property_id'];

        // Update landlord if property has changed
        if ($propertyChanged) {
            $validated['landlord_id'] = $property->landlord_id;
        }

        // Make lease end date null if start date is null
        if (empty($validated['lease_start_date'])) {
            $validated['lease_end_date'] = null;
        }

        // Update tenant record
        $previousStatus = $tenant->status;
        $tenant->update($validated);

        // Update properties status if needed
        if ($propertyChanged || $previousStatus != $validated['status']) {
            $this->updatePropertyStatuses($tenant, $previousStatus, $propertyChanged);
        }

        return redirect()->route('estate.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Remove the specified tenant
     */
    public function destroy(Tenant $tenant)
    {
        $estate = auth()->user()->estate;

        // Verify property belongs to this estate
        if ($tenant->property->estate_id != $estate->id) {
            return redirect()->route('estate.tenants.index')
                ->with('error', 'This tenant does not belong to your estate.');
        }

        // Get property for status update
        $property = $tenant->property;
        $wasActive = $tenant->isActive();

        // Delete tenant
        $tenant->delete();

        // Update property status if needed
        if ($wasActive) {
            $activeTenantsCount = Tenant::active()->inProperty($property->id)->count();

            if ($activeTenantsCount == 0) {
                $property->update(['status' => 'vacant']);
            }
        }

        return redirect()->route('estate.tenants.index')
            ->with('success', 'Tenant removed successfully!');
    }

    /**
     * Helper to update property statuses when tenant changes
     */
    private function updatePropertyStatuses(Tenant $tenant, $previousStatus, $propertyChanged)
    {
        // Get current and previous properties
        $currentProperty = Property::findOrFail($tenant->property_id);
        $previousProperty = $propertyChanged
            ? Property::findOrFail($tenant->getOriginal('property_id'))
            : $currentProperty;

        // Update previous property status if tenant was active
        if ($propertyChanged && $previousStatus == 'active') {
            $activeTenantsCount = Tenant::active()->inProperty($previousProperty->id)->count();
            if ($activeTenantsCount == 0) {
                $previousProperty->update(['status' => 'vacant']);
            }
        }

        // Update current property status if tenant is now active
        if ($tenant->isActive() && $currentProperty->status == 'vacant') {
            $currentProperty->update(['status' => 'occupied']);
        }
    }
}
