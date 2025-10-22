<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents the order/pickup tracking lifecycle for a Donation.
 */
class Pickup extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'requester_org_id',
        'assigned_driver_id', // ID of the volunteer/user assigned
        'status',             // pending | assigned | in_progress | picked | delivered | cancelled
        'pickup_proof_url',   // URL to the photo proof
        'delivery_proof_url', // URL to the photo proof
        'recipient_staff_name', // Recipient confirmation flow (Page 2)
        'client_id',          // For offline idempotency of status updates (Page 7)
    ];

    // Relationships

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function requesterOrganization()
    {
        return $this->belongsTo(Organization::class, 'requester_org_id');
    }

    public function assignedDriver()
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }
}

