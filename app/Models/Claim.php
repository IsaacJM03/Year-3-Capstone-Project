<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'receiver_id',
        'status',
        'pickup_time',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'pickup_time' => 'datetime',
        ];
    }

    /**
     * Relationship: Claim belongs to a donation.
     */
    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    /**
     * Relationship: Claim belongs to a receiver (User).
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
