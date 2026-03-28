<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'integer',
    ];

    /**
     * Automatically hash the password when creating/updating.
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            // only hash if not already hashed
            $this->attributes['password'] =
                strlen($value) === 60 && preg_match('/^\$2y\$/', $value)
                    ? $value
                    : bcrypt($value);
        }
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return (int) $this->role === 1;
    }

    /**
     * JWT identifier
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * JWT custom claims
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
