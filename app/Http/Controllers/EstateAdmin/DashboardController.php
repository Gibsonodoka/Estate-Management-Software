<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\PaymentRecord;
use App\Models\MaintenanceRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $estate = $user->estate;

        if (!$estate) {
            abort(403, 'No estate assigned');
        }

        $stats = [
            'properties' => 0,
            'available_properties' => 0,
            'occupied_properties' => 0,
            'tenants' => 0,
            'total_revenue' => 0,
            'pending_payments' => 0,
            'maintenance_requests' => 0,
        ];

        $recentPayments = collect([]);
        $recentMaintenance = collect([]);

        return view('estate-admin.dashboard', compact('estate', 'stats', 'recentPayments', 'recentMaintenance'));
    }
}
