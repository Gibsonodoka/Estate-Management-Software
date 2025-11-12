<?php

namespace App\Http\Controllers\Admin;

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
     * Display a listing of all tenants
     */
    public function index()
    {
        $tenants = Tenant::with(['property', 'user', 'landlord'])->latest()->paginate(15);
        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show form for creating a new tenant
     */
    public function create()
    {
        $properties = Property::orderBy('property_name')->get();
        $users = User::whereDoesntHave('tenantRecord')->orderBy('name')->get();

        return view('admin.tenants.create', compact('properties', 'users'));
    }

    /**
     * Store a newly created tenant
     */
    public function store(Request $request)
    {
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

        // Get property to set landlord
        $property = Property::findOrFail($validated['property_id']);
        $validated['landlord_id'] = $property->landlord_id;

        // Create user account if requested
        if ($request->create_account && !$validated['user_id'] && isset($validated['email'])) {
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

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant added successfully!');
    }

    /**
     * Display the specified tenant
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['property', 'user', 'landlord']);

        // TODO: Load payments when payment model is implemented
        // $tenant->load(['property', 'user', 'landlord', 'payments']);

        return view('admin.tenants.show', compact('tenant'));
    }

    /**
     * Show form for editing the specified tenant
     */
    public function edit(Tenant $tenant)
    {
        $properties = Property::orderBy('property_name')->get();
        $users = User::where(function($query) use ($tenant) {
            $query->whereDoesntHave('tenantRecord')
                  ->orWhere('id', $tenant->user_id);
        })->orderBy('name')->get();

        return view('admin.tenants.edit', compact('tenant', 'properties', 'users'));
    }

    /**
     * Update the specified tenant
     */
    public function update(Request $request, Tenant $tenant)
    {
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

        // Check if property has changed
        $propertyChanged = $tenant->property_id != $validated['property_id'];

        // Get property to set landlord
        if ($propertyChanged) {
            $property = Property::findOrFail($validated['property_id']);
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

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Remove the specified tenant
     */
    public function destroy(Tenant $tenant)
    {
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

        return redirect()->route('admin.tenants.index')
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
