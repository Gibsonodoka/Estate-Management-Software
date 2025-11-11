<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\Property;
use App\Models\User;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'estates' => Estate::count(),
            'properties' => Property::count(),
            'users' => User::count(),
            'tenants' => Tenant::where('status', 'active')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
