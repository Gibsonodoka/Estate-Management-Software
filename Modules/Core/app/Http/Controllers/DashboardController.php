<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Estate;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\MaintenanceRequest;
use App\Models\PaymentRecord;
use App\Models\VisitorLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics based on user role
     */
    public function index(Request $request)
    {
        $user = $request->user();

        switch ($user->role) {
            case 'site_admin':
                return $this->siteAdminDashboard();
            case 'estate_admin':
                return $this->estateAdminDashboard($user);
            case 'landlord':
                return $this->landlordDashboard($user);
            case 'tenant':
                return $this->tenantDashboard($user);
            case 'security':
                return $this->securityDashboard($user);
            case 'agent':
                return $this->agentDashboard($user);
            default:
                return $this->userDashboard($user);
        }
    }

    /**
     * Site Admin Dashboard
     */
    private function siteAdminDashboard()
    {
        $stats = [
            'total_estates' => Estate::count(),
            'active_estates' => Estate::where('is_active', true)->count(),
            'total_properties' => Property::count(),
            'total_users' => User::count(),
            'total_agents' => User::where('role', 'agent')->count(),
            'pending_agent_verifications' => \App\Models\AgentProfile::where('verification_status', 'pending')->count(),
            'total_tenants' => Tenant::where('status', 'active')->count(),
            'total_payments_this_month' => PaymentRecord::whereMonth('created_at', now()->month)
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        $recent_estates = Estate::with('admin')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'recent_estates' => $recent_estates,
            ]
        ], 200);
    }

    /**
     * Estate Admin Dashboard
     */
    private function estateAdminDashboard($user)
    {
        if (!$user->estate_id) {
            return response()->json([
                'success' => false,
                'message' => 'Estate not assigned'
            ], 400);
        }

        $estate = Estate::find($user->estate_id);

        $stats = [
            'total_properties' => Property::where('estate_id', $user->estate_id)->count(),
            'vacant_properties' => Property::where('estate_id', $user->estate_id)
                ->where('status', 'vacant')
                ->count(),
            'occupied_properties' => Property::where('estate_id', $user->estate_id)
                ->where('status', 'occupied')
                ->count(),
            'total_tenants' => Tenant::whereHas('property', function($q) use ($user) {
                $q->where('estate_id', $user->estate_id);
            })->where('status', 'active')->count(),
            'pending_maintenance' => MaintenanceRequest::whereHas('property', function($q) use ($user) {
                $q->where('estate_id', $user->estate_id);
            })->where('status', 'pending')->count(),
            'visitors_today' => VisitorLog::where('estate_id', $user->estate_id)
                ->whereDate('check_in_time', today())
                ->count(),
            'subscription_expires' => $estate->subscription_expires_at,
            'subscription_status' => $estate->subscription_status,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'estate' => $estate,
                'statistics' => $stats,
            ]
        ], 200);
    }

    /**
     * Landlord Dashboard
     */
    private function landlordDashboard($user)
    {
        $properties = Property::where('landlord_id', $user->id)->get();

        $stats = [
            'total_properties' => $properties->count(),
            'occupied_properties' => $properties->where('status', 'occupied')->count(),
            'vacant_properties' => $properties->where('status', 'vacant')->count(),
            'total_tenants' => Tenant::where('landlord_id', $user->id)
                ->where('status', 'active')
                ->count(),
            'pending_maintenance' => MaintenanceRequest::where('landlord_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'expected_rent_this_month' => $properties->where('status', 'occupied')->sum('rent_amount'),
        ];

        $recent_tenants = Tenant::where('landlord_id', $user->id)
            ->with(['user', 'property'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'properties' => $properties,
                'recent_tenants' => $recent_tenants,
            ]
        ], 200);
    }

    /**
     * Tenant Dashboard
     */
    private function tenantDashboard($user)
    {
        $tenantRecord = Tenant::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['property', 'landlord'])
            ->first();

        if (!$tenantRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No active tenancy found'
            ], 404);
        }

        $stats = [
            'rent_amount' => $tenantRecord->rent_amount,
            'lease_end_date' => $tenantRecord->lease_end_date,
            'pending_maintenance' => MaintenanceRequest::where('tenant_id', $user->id)
                ->where('status', '!=', 'completed')
                ->count(),
            'pending_payments' => PaymentRecord::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'total_paid_this_year' => PaymentRecord::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'tenancy' => $tenantRecord,
                'statistics' => $stats,
            ]
        ], 200);
    }

    /**
     * Security Dashboard
     */
    private function securityDashboard($user)
    {
        $stats = [
            'checked_in_now' => VisitorLog::where('estate_id', $user->estate_id)
                ->where('status', 'checked_in')
                ->whereNull('check_out_time')
                ->count(),
            'visitors_today' => VisitorLog::where('estate_id', $user->estate_id)
                ->whereDate('check_in_time', today())
                ->count(),
            'visitors_this_week' => VisitorLog::where('estate_id', $user->estate_id)
                ->whereBetween('check_in_time', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        $current_visitors = VisitorLog::where('estate_id', $user->estate_id)
            ->where('status', 'checked_in')
            ->with('host')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'current_visitors' => $current_visitors,
            ]
        ], 200);
    }

    /**
     * Agent Dashboard
     */
    private function agentDashboard($user)
    {
        $profile = $user->agentProfile;

        $stats = [
            'total_listings' => \App\Models\PropertyListing::where('agent_id', $user->id)->count(),
            'active_listings' => \App\Models\PropertyListing::where('agent_id', $user->id)
                ->where('status', 'available')
                ->count(),
            'rented_sold' => \App\Models\PropertyListing::where('agent_id', $user->id)
                ->whereIn('status', ['rented', 'sold'])
                ->count(),
            'total_views' => \App\Models\PropertyListing::where('agent_id', $user->id)
                ->sum('views_count'),
            'average_rating' => $profile ? $profile->average_rating : 0,
            'verification_status' => $profile ? $profile->verification_status : 'not_created',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'profile' => $profile,
            ]
        ], 200);
    }

    /**
     * Regular User Dashboard
     */
    private function userDashboard($user)
    {
        $stats = [
            'available_listings' => \App\Models\PropertyListing::available()->count(),
            'featured_listings' => \App\Models\PropertyListing::featured()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
            ]
        ], 200);
    }
}
