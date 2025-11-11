<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EstateController extends Controller
{
    public function index()
    {
        $estates = Estate::with('admin')->latest()->paginate(15);
        return view('admin.estates.index', compact('estates'));
    }

    public function create()
    {
        $admins = User::where('role', 'estate_admin')->orWhere('role', 'site_admin')->get();
        return view('admin.estates.create', compact('admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'admin_id' => 'required|exists:users,id',
            'monthly_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['uci'] = 'EST-' . strtoupper(Str::random(8));
        $validated['subscription_status'] = 'trial';
        $validated['subscription_starts_at'] = now();
        $validated['subscription_expires_at'] = now()->addDays(30);
        $validated['is_active'] = true;

        Estate::create($validated);

        return redirect()->route('admin.estates.index')->with('success', 'Estate created successfully!');
    }

    public function show(Estate $estate)
    {
        $estate->load(['properties', 'users']);
        return view('admin.estates.show', compact('estate'));
    }

    public function edit(Estate $estate)
    {
        $admins = User::where('role', 'estate_admin')->orWhere('role', 'site_admin')->get();
        return view('admin.estates.edit', compact('estate', 'admins'));
    }

    public function update(Request $request, Estate $estate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'admin_id' => 'required|exists:users,id',
            'monthly_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $estate->update($validated);

        return redirect()->route('admin.estates.index')->with('success', 'Estate updated successfully!');
    }

    public function destroy(Estate $estate)
    {
        $estate->delete();
        return redirect()->route('admin.estates.index')->with('success', 'Estate deleted successfully!');
    }
}
