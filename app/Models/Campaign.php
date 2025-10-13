<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'creator_id',
        'goal_amount',
        'raised_amount',
        'deadline',
        'status',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'deadline' => 'date',
    ];

    /**
     * Get the creator that owns the campaign.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
