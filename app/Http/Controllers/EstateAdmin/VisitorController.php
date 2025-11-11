<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\VisitorLog;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index()
    {
        $estate = auth()->user()->estate;
        $visitors = VisitorLog::where('estate_id', $estate->id)
            ->with(['host', 'security'])
            ->latest()
            ->paginate(15);

        return view('estate-admin.visitors.index', compact('visitors', 'estate'));
    }
}
