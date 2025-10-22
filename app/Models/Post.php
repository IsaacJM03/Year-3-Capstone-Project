<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores internal posts or history of social sharing actions.
 */
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'donation_id', // Optional
        'text',
        'shared_public',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}

