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
        'food_type',
        'quantity',
        'unit',
        'expiry_date',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'status',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'datetime',
            'pickup_latitude' => 'decimal:8',
            'pickup_longitude' => 'decimal:8',
        ];
    }

    /**
     * Relationship: Donation belongs to a donor (User).
     */
    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    /**
     * Relationship: Donation has many claims.
     */
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    /**
     * Scope: Get available donations.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope: Get donations within a radius (in km).
     */
    public function scopeNearby($query, $latitude, $longitude, $radius = 10)
    {
        $haversine = "( 6371 * acos( cos( radians(?) ) * 
            cos( radians( pickup_latitude ) ) * 
            cos( radians( pickup_longitude ) - radians(?) ) + 
            sin( radians(?) ) * 
            sin( radians( pickup_latitude ) ) ) )";

        return $query
            ->selectRaw("*, {$haversine} AS distance", [$latitude, $longitude, $latitude])
            ->whereRaw("{$haversine} <= ?", [$latitude, $longitude, $latitude, $radius])
            ->orderByRaw('distance');
    }
}
