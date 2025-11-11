<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $estate = auth()->user()->estate;

        $requests = MaintenanceRequest::whereHas('property', function($q) use ($estate) {
            $q->where('estate_id', $estate->id);
        })->with(['property', 'tenant'])
        ->latest()
        ->paginate(15);

        return view('estate-admin.maintenance.index', compact('requests', 'estate'));
    }

    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        // Ensure maintenance belongs to user's estate
        if ($maintenance->property->estate_id !== auth()->user()->estate_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $maintenance->update($validated);

        return redirect()->route('estate.maintenance.index')->with('success', 'Maintenance request updated!');
    }
}
