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
        return $this->belongsTo(User::class);
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
}
