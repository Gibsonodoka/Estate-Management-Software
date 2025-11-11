<?php

namespace App\Http\Controllers\EstateAdmin;

use App\Http\Controllers\Controller;
use App\Models\PaymentRecord;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $estate = auth()->user()->estate;

        $payments = PaymentRecord::where('estate_id', $estate->id)
            ->with(['user', 'property'])
            ->latest()
            ->paginate(15);

        return view('estate-admin.payments.index', compact('payments', 'estate'));
    }
}
