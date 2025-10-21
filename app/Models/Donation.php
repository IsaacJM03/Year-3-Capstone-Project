<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pickup;

/**
 * Represents a listing created by a donor (individual or company).
 */
class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', // For offline idempotency (UUID)
        'donor_id',
        'donor_type', // e.g., 'donor_individual', 'donor_company'
        'title',
        'description',
        'quantity_est_kg',
        'storage_condition',
        'pickup_from', // ISO8601
        'pickup_to',   // ISO8601
        'lat',
        'lng',
        'status',      // available | requested | reserved | cancelled | completed
        'visibility',  // public | private
    ];

    protected $casts = [
        'quantity_est_kg' => 'float',
        'pickup_from' => 'datetime',
        'pickup_to' => 'datetime',
    ];

    // Relationships

    /**
     * Get the donor (User or Organization) associated with the Donation.
     */
    public function donor()
    {
        // Assuming donor_type will map to a user (individual) or organization (company)
        if ($this->donor_type === 'donor_individual') {
            return $this->belongsTo(User::class, 'donor_id');
        } elseif ($this->donor_type === 'donor_company') {
            return $this->belongsTo(Organization::class, 'donor_id'); // Assuming Organization is also a donor type
        }
        // Fallback or handle different types
        return $this->belongsTo(User::class, 'donor_id');
    }

    /**
     * Get the current active pickup/claim for this donation.
     */
    public function currentPickup()
    {
        // Assuming a Donation can have many Pickup records but only one active one
        return $this->hasOne(Pickup::class)->whereIn('status', ['pending', 'assigned', 'in_progress', 'picked']);
    }

    /**
     * Get the associated photo URLs.
     */
    public function photos()
    {
        return $this->hasMany(Photo::class, 'resource_id')->where('resource_type', 'donation');
    }
}

