<?php
// ============================================
// FILE: Modules/UserManagement/app/Http/Controllers/UserController.php
// ============================================

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get all users (Admin/Moderator only)
     */
    public function index(Request $request)
    {
        $query = User::query()->with(['estate', 'agentProfile']);

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by estate
        if ($request->has('estate_id')) {
            $query->where('estate_id', $request->estate_id);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('uci', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $users
        ], 200);
    }

    /**
     * Get single user
     */
    public function show($id)
    {
        $user = User::with(['estate', 'properties', 'agentProfile'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['user' => $user]
        ], 200);
    }

    /**
     * Update user (Admin only)
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:users,email,' . $id,
            'phone' => 'sometimes|string|unique:users,phone,' . $id,
            'role' => 'sometimes|in:user,landlord,tenant,estate_admin,security,agent,site_admin,moderator',
            'is_active' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => ['user' => $user]
        ], 200);
    }

    /**
     * Delete user (Admin only)
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }

    /**
     * Get users in same estate (for messaging)
     */
    public function getEstateUsers(Request $request)
    {
        $user = $request->user();

        if (!$user->estate_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not part of any estate'
            ], 400);
        }

        $users = User::where('estate_id', $user->estate_id)
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->select('id', 'name', 'email', 'phone', 'role', 'uci')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['users' => $users]
        ], 200);
    }
}
