<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'landlord_id',
        'move_in_date',
        'move_out_date',
        'lease_start_date',
        'lease_end_date',
        'rent_amount',
        'deposit_amount',
        'status',
        'notice_date',
        'notice_period_days',
        'notes',
    ];

    protected $casts = [
        'move_in_date' => 'date',
        'move_out_date' => 'date',
        'lease_start_date' => 'date',
        'lease_end_date' => 'date',
        'notice_date' => 'date',
        'rent_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'No User Account',
            'email' => null
        ]);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // Helpers
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function hasGivenNotice()
    {
        return $this->status === 'notice_given';
    }

    public function hasLease()
    {
        return $this->lease_start_date && $this->lease_end_date;
    }

    public function getRemainingLeaseAttribute()
    {
        if (!$this->hasLease()) {
            return null;
        }

        if (now()->gt($this->lease_end_date)) {
            return 0;
        }

        return now()->diffInDays($this->lease_end_date);
    }

    public function getLeaseStatusAttribute()
    {
        if (!$this->hasLease()) {
            return 'No Lease';
        }

        if (now()->lt($this->lease_start_date)) {
            return 'Future Lease';
        }

        if (now()->gt($this->lease_end_date)) {
            return 'Expired';
        }

        $percentComplete = $this->getLeasePercentCompleteAttribute();

        if ($percentComplete < 25) {
            return 'Early Stage';
        } elseif ($percentComplete < 75) {
            return 'Mid Lease';
        } else {
            return 'Late Stage';
        }
    }

    public function getLeasePercentCompleteAttribute()
    {
        if (!$this->hasLease()) {
            return 0;
        }

        $totalDays = $this->lease_end_date->diffInDays($this->lease_start_date);

        if ($totalDays === 0) {
            return 100;
        }

        $daysElapsed = now()->diffInDays($this->lease_start_date);
        return min(100, max(0, ($daysElapsed / $totalDays) * 100));
    }
}
