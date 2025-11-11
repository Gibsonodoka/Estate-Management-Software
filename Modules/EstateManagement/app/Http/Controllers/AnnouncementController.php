<?php
// ============================================
// FILE: Modules/EstateManagement/app/Http/Controllers/AnnouncementController.php
// ============================================

namespace Modules\EstateManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Estate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * Get all announcements for an estate
     */
    public function index(Request $request, $estateId)
    {
        $query = Announcement::where('estate_id', $estateId)
            ->with(['creator', 'estate']);

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by target audience
        if ($request->has('target_audience')) {
            $query->where('target_audience', $request->target_audience);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Only active announcements for non-admin users
        if ($request->has('active_only') && $request->active_only) {
            $query->active();
        }

        $announcements = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $announcements
        ], 200);
    }

    /**
     * Create new announcement
     */
    public function store(Request $request, $estateId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'sometimes|in:low,normal,high,urgent',
            'target_audience' => 'sometimes|in:all,landlords,tenants,security',
            'published_at' => 'sometimes|date',
            'expires_at' => 'sometimes|date|after:published_at',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify estate exists
        $estate = Estate::find($estateId);
        if (!$estate) {
            return response()->json([
                'success' => false,
                'message' => 'Estate not found'
            ], 404);
        }

        $announcement = Announcement::create([
            'estate_id' => $estateId,
            'created_by' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority ?? 'normal',
            'target_audience' => $request->target_audience ?? 'all',
            'is_active' => true,
            'published_at' => $request->published_at ?? now(),
            'expires_at' => $request->expires_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully',
            'data' => ['announcement' => $announcement->load('creator')]
        ], 201);
    }

    /**
     * Get single announcement
     */
    public function show($estateId, $id)
    {
        $announcement = Announcement::where('estate_id', $estateId)
            ->with(['creator', 'estate'])
            ->find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['announcement' => $announcement]
        ], 200);
    }

    /**
     * Update announcement
     */
    public function update(Request $request, $estateId, $id)
    {
        $announcement = Announcement::where('estate_id', $estateId)->find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'priority' => 'sometimes|in:low,normal,high,urgent',
            'target_audience' => 'sometimes|in:all,landlords,tenants,security',
            'is_active' => 'sometimes|boolean',
            'published_at' => 'sometimes|date',
            'expires_at' => 'sometimes|date|after:published_at',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $announcement->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully',
            'data' => ['announcement' => $announcement]
        ], 200);
    }

    /**
     * Delete announcement
     */
    public function destroy($estateId, $id)
    {
        $announcement = Announcement::where('estate_id', $estateId)->find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully'
        ], 200);
    }

    /**
     * Get announcements for current user based on their role
     */
    public function getMyAnnouncements(Request $request, $estateId)
    {
        $user = $request->user();

        $announcements = Announcement::where('estate_id', $estateId)
            ->active()
            ->where(function($query) use ($user) {
                $query->where('target_audience', 'all')
                      ->orWhere('target_audience', $user->role . 's'); // landlords, tenants, etc.
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['announcements' => $announcements]
        ], 200);
    }

    /**
     * Toggle announcement active status
     */
    public function toggleActive($estateId, $id)
    {
        $announcement = Announcement::where('estate_id', $estateId)->find($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        }

        $announcement->update([
            'is_active' => !$announcement->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => $announcement->is_active ? 'Announcement activated' : 'Announcement deactivated',
            'data' => ['announcement' => $announcement]
        ], 200);
    }
}
