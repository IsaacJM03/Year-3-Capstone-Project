<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use app\Models\Donation;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;


    protected $fillable = [
        'name',
        'email',
        'password',
        'role',          // donor_individual, donor_company, receiver, volunteer, admin (Page 3)
        'org_name',      // temporary field for registration payload (Page 3)
        'organization_id', // Link to Organization model if user is part of one
        'avatar_url',    // For PUT /users/me (Page 6)
        'phone_number',  // For PUT /users/me (Page 6)
    ];

    // Relationships

    /**
     * Get the organization this user belongs to (if any).
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Check if the user has the specified role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
    
    // ... other standard methods


    // /**
    //  * The attributes that are mass assignable.
    //  *
    //  * @var list<string>
    //  */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    //     'role',
    //     'phone',
    //     'address',
    //     'organization',
    //     'verified',
    // ];

    // /**
    //  * The attributes that should be hidden for serialization.
    //  *
    //  * @var list<string>
    //  */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    // /**
    //  * Get the attributes that should be cast.
    //  *
    //  * @return array<string, string>
    //  */
    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //         'verified' => 'boolean',
    //     ];
    // }

    // /**
    //  * Get the donations for the user.
    //  */
    // public function donations()
    // {
    //     return $this->hasMany(Donation::class, 'donor_id');
    // }

    // /**
    //  * Get the claims for the user.
    //  */
    // public function claims()
    // {
    //     return $this->hasMany(DonationClaim::class, 'receiver_id');
    // }

    // /**
    //  * Get the campaigns for the user.
    //  */
    // public function campaigns()
    // {
    //     return $this->hasMany(Campaign::class, 'creator_id');
    // }
}
