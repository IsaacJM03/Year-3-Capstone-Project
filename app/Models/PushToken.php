<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores Firebase Cloud Messaging (FCM) tokens for push notifications.
 */
class PushToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'platform', // android|ios
        'device_id', // UUID
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

