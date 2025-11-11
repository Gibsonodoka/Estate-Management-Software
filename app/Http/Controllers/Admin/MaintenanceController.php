<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::with(['property', 'tenant'])->latest()->paginate(15);
        return view('admin.maintenance.index', compact('requests'));
    }

    public function show(MaintenanceRequest $maintenance)
    {
        $maintenance->load(['property', 'tenant']);
        return view('admin.maintenance.show', compact('maintenance'));
    }

    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $maintenance->update($validated);

        return redirect()->route('admin.maintenance.index')->with('success', 'Maintenance request updated!');
    }
}
