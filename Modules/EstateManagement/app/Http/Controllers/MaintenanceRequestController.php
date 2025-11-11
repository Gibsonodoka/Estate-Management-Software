<?php
// ============================================
// FILE: Modules/EstateManagement/app/Http/Controllers/MaintenanceRequestController.php
// ============================================

namespace Modules\EstateManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaintenanceRequestController extends Controller
{
    /**
     * Get all maintenance requests
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = MaintenanceRequest::with(['property', 'tenant', 'landlord']);

        // Filter based on user role
        if ($user->isTenant()) {
            $query->where('tenant_id', $user->id);
        } elseif ($user->isLandlord()) {
            $query->where('landlord_id', $user->id);
        } elseif ($user->isEstateAdmin() && $user->estate_id) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('estate_id', $user->estate_id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by property
        if ($request->has('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $requests = $query->orderBy('priority', 'desc')
            ->orderBy('reported_date', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $requests
        ], 200);
    }

    /**
     * Create maintenance request (Tenant)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:plumbing,electrical,structural,appliance,general,emergency',
            'priority' => 'sometimes|in:low,medium,high,emergency',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $property = Property::find($request->property_id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'property_id' => $request->property_id,
            'tenant_id' => $request->user()->id,
            'landlord_id' => $property->landlord_id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority ?? 'medium',
            'status' => 'pending',
            'reported_date' => now(),
        ]);

        // TODO: Send notification to landlord

        return response()->json([
            'success' => true,
            'message' => 'Maintenance request submitted successfully',
            'data' => ['maintenance_request' => $maintenanceRequest->load(['property', 'landlord'])]
        ], 201);
    }

    /**
     * Get single maintenance request
     */
    public function show($id)
    {
        $maintenanceRequest = MaintenanceRequest::with(['property', 'tenant', 'landlord'])
            ->find($id);

        if (!$maintenanceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance request not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['maintenance_request' => $maintenanceRequest]
        ], 200);
    }

    /**
     * Update maintenance request (Landlord/Estate Admin)
     */
    public function update(Request $request, $id)
    {
        $maintenanceRequest = MaintenanceRequest::find($id);

        if (!$maintenanceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance request not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,acknowledged,in_progress,completed,cancelled',
            'priority' => 'sometimes|in:low,medium,high,emergency',
            'scheduled_date' => 'sometimes|date',
            'completed_date' => 'sometimes|date',
            'landlord_notes' => 'sometimes|string',
            'resolution_notes' => 'sometimes|string',
            'cost' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $maintenanceRequest->update($request->all());

        // TODO: Send notification to tenant about status update

        return response()->json([
            'success' => true,
            'message' => 'Maintenance request updated successfully',
            'data' => ['maintenance_request' => $maintenanceRequest]
        ], 200);
    }

    /**
     * Delete maintenance request
     */
    public function destroy($id)
    {
        $maintenanceRequest = MaintenanceRequest::find($id);

        if (!$maintenanceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance request not found'
            ], 404);
        }

        $maintenanceRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maintenance request deleted successfully'
        ], 200);
    }

    /**
     * Acknowledge maintenance request (Landlord)
     */
    public function acknowledge($id)
    {
        $maintenanceRequest = MaintenanceRequest::find($id);

        if (!$maintenanceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance request not found'
            ], 404);
        }

        $maintenanceRequest->update([
            'status' => 'acknowledged'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance request acknowledged',
            'data' => ['maintenance_request' => $maintenanceRequest]
        ], 200);
    }

    /**
     * Mark as completed
     */
    public function markCompleted(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'resolution_notes' => 'required|string',
            'cost' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $maintenanceRequest = MaintenanceRequest::find($id);

        if (!$maintenanceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance request not found'
            ], 404);
        }

        $maintenanceRequest->update([
            'status' => 'completed',
            'completed_date' => now(),
            'resolution_notes' => $request->resolution_notes,
            'cost' => $request->cost ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance request marked as completed',
            'data' => ['maintenance_request' => $maintenanceRequest]
        ], 200);
    }

    /**
     * Get statistics
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        $query = MaintenanceRequest::query();

        if ($user->isLandlord()) {
            $query->where('landlord_id', $user->id);
        } elseif ($user->isTenant()) {
            $query->where('tenant_id', $user->id);
        } elseif ($user->isEstateAdmin() && $user->estate_id) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('estate_id', $user->estate_id);
            });
        }

        $stats = [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'emergency' => (clone $query)->where('priority', 'emergency')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => ['statistics' => $stats]
        ], 200);
    }
}
