<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\Landlord;
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LandlordController extends Controller
{
    /**
     * Display a listing of the landlords in this estate.
     */
    public function index()
    {
        $estate = auth()->user()->estate;

        if (!$estate) {
            return redirect()->route('estate.dashboard')
                ->with('error', 'No estate associated with this account.');
        }

        $landlords = Landlord::with(['user'])
            ->where('estate_id', $estate->id)
            ->withCount('properties')
            ->latest()
            ->paginate(15);

        return view('estate-admin.landlords.index', compact('landlords', 'estate'));
    }

    /**
     * Show the form for creating a new landlord.
     */
    public function create()
    {
        $estate = auth()->user()->estate;

        if (!$estate) {
            return redirect()->route('estate.dashboard')
                ->with('error', 'No estate associated with this account.');
        }

        $users = User::whereDoesntHave('landlordRecord')
            ->orderBy('name')
            ->get();

        return view('estate-admin.landlords.create', compact('users', 'estate'));
    }

    /**
     * Store a newly created landlord in storage.
     */
    public function store(Request $request)
    {
        $estate = auth()->user()->estate;

        if (!$estate) {
            return redirect()->route('estate.dashboard')
                ->with('error', 'No estate associated with this account.');
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'required_without:user_id|string|max:255|nullable',
            'last_name' => 'required_without:user_id|string|max:255|nullable',
            'email' => 'required_without:user_id|email|unique:users,email|nullable',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'alternative_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:100',
            'is_company' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'create_account' => 'sometimes|boolean',
        ]);

        // Create user account if requested
        if ($request->create_account && !$validated['user_id'] && isset($validated['email'])) {
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make(Str::random(10)),
                'estate_id' => $estate->id,
                'role' => 'landlord',
            ]);

            // Assign landlord role
            $user->assignRole('landlord');

            $validated['user_id'] = $user->id;

            // TODO: Send email notification with password reset link
        }

        // Prepare landlord data
        $landlordData = [
            'user_id' => $validated['user_id'],
            'estate_id' => $estate->id,
            'company_name' => $validated['company_name'] ?? null,
            'contact_person' => $validated['contact_person'] ?? ($validated['first_name'] && $validated['last_name'] ? $validated['first_name'] . ' ' . $validated['last_name'] : null),
            'phone' => $validated['phone'] ?? null,
            'alternative_phone' => $validated['alternative_phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'account_name' => $validated['account_name'] ?? null,
            'is_company' => $validated['is_company'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ];

        // Create the landlord
        $landlord = Landlord::create($landlordData);

        return redirect()->route('estate.landlords.index')
            ->with('success', 'Landlord created successfully.');
    }

    /**
     * Display the specified landlord.
     */
    public function show(Landlord $landlord)
    {
        $estate = auth()->user()->estate;

        // Verify landlord belongs to this estate
        if ($landlord->estate_id != $estate->id) {
            return redirect()->route('estate.landlords.index')
                ->with('error', 'This landlord does not belong to your estate.');
        }

        $landlord->load(['user', 'properties']);

        $properties = $landlord->properties()
            ->withCount('activeTenants')
            ->paginate(10);

        return view('estate-admin.landlords.show', compact('landlord', 'properties', 'estate'));
    }

    /**
     * Show the form for editing the specified landlord.
     */
    public function edit(Landlord $landlord)
    {
        $estate = auth()->user()->estate;

        // Verify landlord belongs to this estate
        if ($landlord->estate_id != $estate->id) {
            return redirect()->route('estate.landlords.index')
                ->with('error', 'This landlord does not belong to your estate.');
        }

        $users = User::where(function($query) use ($landlord) {
                $query->whereDoesntHave('landlordRecord')
                      ->orWhere('id', $landlord->user_id);
            })
            ->orderBy('name')
            ->get();

        return view('estate-admin.landlords.edit', compact('landlord', 'users', 'estate'));
    }

    /**
     * Update the specified landlord in storage.
     */
    public function update(Request $request, Landlord $landlord)
    {
        $estate = auth()->user()->estate;

        // Verify landlord belongs to this estate
        if ($landlord->estate_id != $estate->id) {
            return redirect()->route('estate.landlords.index')
                ->with('error', 'This landlord does not belong to your estate.');
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email,' . ($landlord->user_id ?? ''),
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:100',
            'is_company' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // Update the landlord
        $landlord->update($validated);

        // Update user information if applicable
        if ($landlord->user_id && $landlord->user) {
            $user = $landlord->user;

            if (!$landlord->is_company && $landlord->contact_person) {
                $user->name = $landlord->contact_person;
            }

            if ($landlord->email) {
                $user->email = $landlord->email;
            }

            if ($landlord->phone) {
                $user->phone = $landlord->phone;
            }

            $user->save();
        }

        return redirect()->route('estate.landlords.show', $landlord)
            ->with('success', 'Landlord updated successfully.');
    }

    /**
     * Remove the specified landlord from storage.
     */
    public function destroy(Landlord $landlord)
    {
        $estate = auth()->user()->estate;

        // Verify landlord belongs to this estate
        if ($landlord->estate_id != $estate->id) {
            return redirect()->route('estate.landlords.index')
                ->with('error', 'This landlord does not belong to your estate.');
        }

        // Check if landlord has properties
        $propertiesCount = $landlord->properties()->count();

        if ($propertiesCount > 0) {
            return redirect()->route('estate.landlords.show', $landlord)
                ->with('error', 'Cannot delete landlord with associated properties. Please reassign or delete the properties first.');
        }

        $landlord->delete();

        return redirect()->route('estate.landlords.index')
            ->with('success', 'Landlord deleted successfully.');
    }

    /**
     * Display a listing of the landlord's properties.
     */
    public function properties(Landlord $landlord)
    {
        $estate = auth()->user()->estate;

        // Verify landlord belongs to this estate
        if ($landlord->estate_id != $estate->id) {
            return redirect()->route('estate.landlords.index')
                ->with('error', 'This landlord does not belong to your estate.');
        }

        $properties = $landlord->properties()
            ->withCount('activeTenants')
            ->latest()
            ->paginate(15);

        return view('estate-admin.landlords.properties', compact('landlord', 'properties', 'estate'));
    }

    /**
     * Display a listing of the landlord's tenants.
     */
    public function tenants(Landlord $landlord)
    {
        $estate = auth()->user()->estate;

        // Verify landlord belongs to this estate
        if ($landlord->estate_id != $estate->id) {
            return redirect()->route('estate.landlords.index')
                ->with('error', 'This landlord does not belong to your estate.');
        }

        $tenants = $landlord->tenants()
            ->with(['property', 'user'])
            ->latest()
            ->paginate(15);

        return view('estate-admin.landlords.tenants', compact('landlord', 'tenants', 'estate'));
    }

    /**
     * Display a listing of the landlord's maintenance requests.
     */
    public function maintenanceRequests(Landlord $landlord)
    {
        $estate = auth()->user()->estate;

        // Verify landlord belongs to this estate
        if ($landlord->estate_id != $estate->id) {
            return redirect()->route('estate.landlords.index')
                ->with('error', 'This landlord does not belong to your estate.');
        }

        $maintenanceRequests = $landlord->maintenanceRequests()
            ->with(['property', 'tenant'])
            ->latest()
            ->paginate(15);

        return view('estate-admin.landlords.maintenance-requests', compact('landlord', 'maintenanceRequests', 'estate'));
    }

    /**
     * Display a listing of the landlord's payment records.
     */
    public function paymentRecords(Landlord $landlord)
    {
        $estate = auth()->user()->estate;

        // Verify landlord belongs to this estate
        if ($landlord->estate_id != $estate->id) {
            return redirect()->route('estate.landlords.index')
                ->with('error', 'This landlord does not belong to your estate.');
        }

        $paymentRecords = $landlord->paymentRecords()
            ->with(['property', 'tenant'])
            ->latest()
            ->paginate(15);

        return view('estate-admin.landlords.payment-records', compact('landlord', 'paymentRecords', 'estate'));
    }
}
