<?php

// ============================================
// FILE: app/Models/VisitorLog.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'estate_id',
        'host_user_id',
        'security_user_id',
        'visitor_name',
        'visitor_phone',
        'visitor_id_type',
        'visitor_id_number',
        'vehicle_plate',
        'visit_purpose',
        'check_in_time',
        'check_out_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function security()
    {
        return $this->belongsTo(User::class, 'security_user_id');
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'checked_in')
            ->whereNull('check_out_time');
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', 'checked_out')
            ->whereNotNull('check_out_time');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in_time', today());
    }

    public function isCheckedIn()
    {
        return $this->status === 'checked_in' && !$this->check_out_time;
    }
}
