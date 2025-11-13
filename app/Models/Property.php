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
        'landlord_id', // Now references landlords table
        'property_name',
        'property_type',
        'units',
        'bedrooms_per_unit',
        'bathrooms_per_unit',
        'size_sqm',
        'size_unit',
        'street',
        'street_name',
        'street_number',
        'rent_amount_per_unit',
        'rent_period',
        'status',
        'description',
        'utilities_included',
        'features',
        'floor_number',
        'available_from',
        'is_listed',
        // Legacy fields
        'old_property_number',
        'old_bedrooms',
        'old_bathrooms',
        'size',
        'old_rent_amount',
    ];

    protected $casts = [
        'rent_amount_per_unit' => 'decimal:2',
        'size_sqm' => 'decimal:2',
        'is_listed' => 'boolean',
        'utilities_included' => 'array',
        'features' => 'array',
        'available_from' => 'date',
        'units' => 'integer',
        'bedrooms_per_unit' => 'integer',
        'bathrooms_per_unit' => 'integer',
        'floor_number' => 'integer',
    ];

    // Relationships
    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    /**
     * Get the landlord that owns the property.
     * This now references the Landlord model instead of User model.
     */
    public function landlord()
    {
        return $this->belongsTo(Landlord::class, 'landlord_id');
    }

    /**
     * Get the user associated with the landlord.
     * Convenience method for backward compatibility.
     */
    public function landlordUser()
    {
        return $this->landlord ? $this->landlord->user : null;
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function activeTenants()
    {
        return $this->hasMany(Tenant::class)->where('status', 'active');
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

    // Computed Attributes
    public function getTotalBedroomsAttribute()
    {
        if ($this->units && $this->bedrooms_per_unit) {
            return $this->units * $this->bedrooms_per_unit;
        }
        return null;
    }

    public function getTotalRentPotentialAttribute()
    {
        return $this->units * $this->rent_amount_per_unit;
    }

    public function getOccupiedUnitsAttribute()
    {
        return $this->activeTenants()->count();
    }

    public function getVacantUnitsAttribute()
    {
        return $this->units - $this->getOccupiedUnitsAttribute();
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->street_number,
            $this->street_name ?: $this->street,
            $this->estate ? $this->estate->name : null,
        ]);
        return implode(', ', $parts);
    }

    /**
     * Get the landlord's display name.
     */
    public function getLandlordNameAttribute()
    {
        if ($this->landlord) {
            if ($this->landlord->is_company) {
                return $this->landlord->company_name . ' (Company)';
            }
            return $this->landlord->contact_person ??
                   ($this->landlord->user ? $this->landlord->user->name : 'Unknown');
        }
        return 'No Landlord';
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeVacant($query)
    {
        return $query->where('status', 'vacant');
    }

    public function scopeListed($query)
    {
        return $query->where('is_listed', true);
    }

    public function scopeInEstate($query, $estateId)
    {
        return $query->where('estate_id', $estateId);
    }

    /**
     * Scope a query to only include properties owned by a specific landlord.
     */
    public function scopeByLandlord($query, $landlordId)
    {
        return $query->where('landlord_id', $landlordId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('property_type', $type);
    }

    // Helper Methods
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isOccupied()
    {
        return $this->status === 'occupied';
    }

    public function isVacant()
    {
        return $this->status === 'vacant';
    }

    public function hasVacantUnits()
    {
        return $this->getVacantUnitsAttribute() > 0;
    }

    public function getOccupancyRate()
    {
        if ($this->units == 0) return 0;
        return ($this->getOccupiedUnitsAttribute() / $this->units) * 100;
    }

    /**
     * Check if property has a landlord assigned.
     */
    public function hasLandlord()
    {
        return $this->landlord_id !== null;
    }
}
