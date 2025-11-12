<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'uci',
        'estate_id',
        'is_active',
        'is_verified',
        'verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function ownedEstates()
    {
        return $this->hasMany(Estate::class, 'admin_id');
    }

    /**
     * Get the landlord record associated with this user.
     */
    public function landlordRecord()
    {
        return $this->hasOne(Landlord::class, 'user_id');
    }

    /**
     * Get the properties associated with this user through their landlord record.
     * This handles the transition from direct property relationship to
     * going through the landlord model.
     */
    public function properties()
    {
        // If the user has a landlord record, get properties through that
        if ($this->landlordRecord) {
            return $this->hasManyThrough(
                Property::class,
                Landlord::class,
                'user_id', // Foreign key on landlords table
                'landlord_id', // Foreign key on properties table
                'id', // Local key on users table
                'id' // Local key on landlords table
            );
        }

        // Legacy support for direct landlord relationship
        // This will be used during migration and can be removed later
        return $this->hasMany(Property::class, 'landlord_id');
    }

    public function tenantRecord()
    {
        return $this->hasOne(Tenant::class, 'user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'tenant_id');
    }

    public function visitorLogs()
    {
        return $this->hasMany(VisitorLog::class, 'host_user_id');
    }

    public function agentProfile()
    {
        return $this->hasOne(AgentProfile::class);
    }

    /**
     * Get the landlord profile for this user.
     * This is an accessor that provides a convenient way to access
     * the landlord record.
     */
    public function getLandlordProfileAttribute()
    {
        return $this->landlordRecord;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeInEstate($query, $estateId)
    {
        return $query->where('estate_id', $estateId);
    }

    /**
     * Scope to find users that have landlord records.
     */
    public function scopeLandlords($query)
    {
        return $query->whereHas('landlordRecord');
    }

    // Helpers
    public function isSiteAdmin()
    {
        return $this->role === 'site_admin';
    }

    public function isEstateAdmin()
    {
        return $this->role === 'estate_admin';
    }

    public function isLandlord()
    {
        return $this->role === 'landlord' || $this->landlordRecord !== null;
    }

    public function isTenant()
    {
        return $this->role === 'tenant';
    }

    public function isSecurity()
    {
        return $this->role === 'security';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    /**
     * Check if the user has a landlord profile.
     */
    public function hasLandlordProfile()
    {
        return $this->landlordRecord !== null;
    }
}
