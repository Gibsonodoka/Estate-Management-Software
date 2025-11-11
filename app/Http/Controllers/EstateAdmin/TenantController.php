<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $estate = auth()->user()->estate;

        $tenants = Tenant::whereHas('property', function($q) use ($estate) {
            $q->where('estate_id', $estate->id);
        })->with(['user', 'property', 'landlord'])
        ->latest()
        ->paginate(15);

        return view('estate-admin.tenants.index', compact('tenants', 'estate'));
    }
}
