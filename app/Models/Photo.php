<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores photo URLs associated with Donations (or other models).
 * This makes managing multiple photos per donation easier.
 */
class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'resource_type', // e.g., 'donation', 'pickup_proof'
        'url',
        'thumbnail_url',
    ];

    // Can be used for a polymorphic relationship if needed later
    public function resource()
    {
        return $this->morphTo();
    }
}

