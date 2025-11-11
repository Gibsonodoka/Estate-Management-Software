<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $estate = auth()->user()->estate;
        $announcements = Announcement::where('estate_id', $estate->id)
            ->latest()
            ->paginate(15);

        return view('estate-admin.announcements.index', compact('announcements', 'estate'));
    }

    public function create()
    {
        $estate = auth()->user()->estate;
        return view('estate-admin.announcements.create', compact('estate'));
    }

    public function store(Request $request)
    {
        $estate = auth()->user()->estate;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'target_audience' => 'required|in:all,tenants,landlords,security',
        ]);

        $validated['estate_id'] = $estate->id;
        $validated['created_by'] = auth()->id();

        Announcement::create($validated);

        return redirect()->route('estate.announcements.index')->with('success', 'Announcement created successfully!');
    }
}
