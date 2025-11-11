<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'estate_id',
        'landlord_id',
        'property_number',
        'street',
        'property_type',
        'bedrooms',
        'bathrooms',
        'rent_amount',
        'rent_period',
        'status',
        'is_listed',
        'description',
        'size_sqm',
        'features',
        'floor_number',
        'available_from',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'size_sqm' => 'decimal:2',
        'is_listed' => 'boolean',
        'features' => 'array',
        'available_from' => 'date',
    ];

    // Relationships
    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function currentTenant()
    {
        return $this->hasOne(Tenant::class)->where('status', 'active');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function listing()
    {
        return $this->hasOne(PropertyListing::class);
    }

    public function paymentRecords()
    {
        return $this->hasMany(PaymentRecord::class);
    }

    // Scopes
    public function scopeVacant($query)
    {
        return $query->where('status', 'vacant');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeListed($query)
    {
        return $query->where('is_listed', true);
    }

    public function scopeInEstate($query, $estateId)
    {
        return $query->where('estate_id', $estateId);
    }

    // Helpers
    public function isVacant()
    {
        return $this->status === 'vacant';
    }

    public function isOccupied()
    {
        return $this->status === 'occupied';
    }
}
