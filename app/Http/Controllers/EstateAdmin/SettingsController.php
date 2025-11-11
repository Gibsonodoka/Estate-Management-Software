<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $estate = auth()->user()->estate;
        return view('estate-admin.settings', compact('estate'));
    }

    public function update(Request $request)
    {
        $estate = auth()->user()->estate;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'monthly_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $estate->update($validated);

        return redirect()->route('estate.settings')->with('success', 'Estate settings updated successfully!');
    }
}
