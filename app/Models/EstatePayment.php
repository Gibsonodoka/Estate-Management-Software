<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstatePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'estate_id',
        'name',
        'description',
        'amount',
        'frequency',
        'applies_to',
        'effective_from',
        'effective_to',
        'is_active',
        'is_mandatory',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
    ];

    // Relationships
    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function paymentRecords()
    {
        return $this->hasMany(PaymentRecord::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>', now());
            });
    }
}

