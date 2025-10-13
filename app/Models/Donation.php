<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'title',
        'description',
        'category',
        'quantity',
        'unit',
        'expiry_date',
        'status',
        'pickup_location',
        'latitude',
        'longitude',
        'image_url',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Get the donor that owns the donation.
     */
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    /**
     * Get the claims for the donation.
     */
    public function claims()
    {
        return $this->hasMany(DonationClaim::class);
    }
}
