<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Landlord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'estate_id',
        'company_name',
        'contact_person',
        'phone',
        'alternative_phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'notes',
        'bank_name',
        'account_number',
        'account_name',
        'is_company',
        'is_active',
    ];

    protected $casts = [
        'is_company' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user associated with the landlord.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => $this->contact_person ?? $this->company_name ?? 'No User Account',
            'email' => $this->email ?? null
        ]);
    }

    /**
     * Get the estate this landlord belongs to.
     */
    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    /**
     * Get the properties owned by this landlord.
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id', 'id');
    }

    /**
     * Get the tenants associated with this landlord's properties.
     */
    public function tenants()
    {
        return $this->hasManyThrough(Tenant::class, Property::class, 'landlord_id', 'property_id');
    }

    /**
     * Get the maintenance requests for this landlord's properties.
     */
    public function maintenanceRequests()
    {
        return $this->hasManyThrough(MaintenanceRequest::class, Property::class, 'landlord_id', 'property_id');
    }

    /**
     * Get the payment records for this landlord.
     */
    public function paymentRecords()
    {
        return $this->hasManyThrough(PaymentRecord::class, Property::class, 'landlord_id', 'property_id');
    }

    /**
     * Get the total properties count.
     */
    public function getPropertiesCountAttribute()
    {
        return $this->properties()->count();
    }

    /**
     * Get the total tenants count.
     */
    public function getTenantsCountAttribute()
    {
        return $this->tenants()->count();
    }

    /**
     * Get the active tenants count.
     */
    public function getActiveTenantsCountAttribute()
    {
        return $this->tenants()->where('status', 'active')->count();
    }

    /**
     * Get the vacant properties count.
     */
    public function getVacantPropertiesCountAttribute()
    {
        return $this->properties()->where('status', 'vacant')->count();
    }

    /**
     * Get the occupied properties count.
     */
    public function getOccupiedPropertiesCountAttribute()
    {
        return $this->properties()->where('status', 'occupied')->count();
    }

    /**
     * Get the landlord's full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->is_company) {
            return $this->company_name;
        }

        return $this->user ? $this->user->name : $this->contact_person;
    }

    /**
     * Get the display name based on whether it's a company or individual.
     */
    public function getDisplayNameAttribute()
    {
        if ($this->is_company) {
            return $this->company_name . ' (Company)';
        }

        return $this->user ? $this->user->name : $this->contact_person;
    }

    /**
     * Get the landlord's contact information.
     */
    public function getContactInfoAttribute()
    {
        $email = $this->email ?? ($this->user ? $this->user->email : null);
        $phone = $this->phone ?? ($this->user ? $this->user->phone : null);

        $parts = [];
        if ($email) {
            $parts[] = $email;
        }
        if ($phone) {
            $parts[] = $phone;
        }

        return implode(' | ', $parts);
    }

    /**
     * Scope a query to only include active landlords.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include landlords from a specific estate.
     */
    public function scopeInEstate($query, $estateId)
    {
        return $query->where('estate_id', $estateId);
    }

    /**
     * Scope a query to only include company landlords.
     */
    public function scopeCompanies($query)
    {
        return $query->where('is_company', true);
    }

    /**
     * Scope a query to only include individual landlords.
     */
    public function scopeIndividuals($query)
    {
        return $query->where('is_company', false);
    }
}
