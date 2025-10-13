<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonationClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'receiver_id',
        'claim_status',
        'pickup_time',
        'notes',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
    ];

    /**
     * Get the donation that owns the claim.
     */
    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    /**
     * Get the receiver that owns the claim.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
