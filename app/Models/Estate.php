<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'uci',
        'address',
        'city',
        'state',
        'country',
        'admin_id',
        'subscription_status',
        'subscription_starts_at',
        'subscription_expires_at',
        'monthly_fee',
        'is_active',
        'description',
        'amenities',
    ];

    protected $casts = [
        'subscription_starts_at' => 'date',
        'subscription_expires_at' => 'date',
        'monthly_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'amenities' => 'array',
    ];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function estatePayments()
    {
        return $this->hasMany(EstatePayment::class);
    }

    public function paymentRecords()
    {
        return $this->hasMany(PaymentRecord::class);
    }

    public function visitorLogs()
    {
        return $this->hasMany(VisitorLog::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSubscribed($query)
    {
        return $query->where('subscription_status', 'active')
            ->where('subscription_expires_at', '>', now());
    }

    // Helpers
    public function isSubscriptionActive()
    {
        return $this->subscription_status === 'active'
            && $this->subscription_expires_at
            && $this->subscription_expires_at->isFuture();
    }

    public function vacantProperties()
    {
        return $this->properties()->where('status', 'vacant')->where('is_listed', true);
    }
}
