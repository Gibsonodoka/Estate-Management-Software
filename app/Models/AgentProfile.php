<?php

// ============================================
// FILE: app/Models/AgentProfile.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'agency_name',
        'license_number',
        'bio',
        'office_address',
        'office_phone',
        'service_areas',
        'average_rating',
        'total_ratings',
        'properties_listed',
        'properties_sold',
        'total_earnings',
        'is_verified',
        'verified_at',
        'verification_status',
        'verification_notes',
    ];

    protected $casts = [
        'service_areas' => 'array',
        'average_rating' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(PropertyListing::class, 'agent_id', 'user_id');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePendingVerification($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function updateRating($newRating)
    {
        $totalRatings = $this->total_ratings;
        $currentAverage = $this->average_rating;

        $newTotal = $totalRatings + 1;
        $newAverage = (($currentAverage * $totalRatings) + $newRating) / $newTotal;

        $this->update([
            'average_rating' => round($newAverage, 2),
            'total_ratings' => $newTotal,
        ]);
    }
}
