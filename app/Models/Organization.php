<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a company donor or a receiver organization.
 */
class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'lat',
        'lng',
        'contact_person',
        'verification_doc_url',
        'verified', // Boolean flag, admin-only update
        'type', // e.g., 'receiver', 'donor_company'
        'csr_info', // extra profile field for donor companies
    ];

    protected $casts = [
        'verified' => 'boolean',
    ];

    // Relationships

    /**
     * Get the users/staff associated with this organization.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the pickups requested by this organization.
     */
    public function pickups()
    {
        return $this->hasMany(Pickup::class, 'requester_org_id');
    }
}

