<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * 'role' is deliberately NOT fillable: a user must never be able
     * to promote themselves by adding role=admin or role=librarian to a form.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The member profile linked to this user login.
     */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    /**
     * Role helpers
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLibrarian(): bool
    {
        return $this->role === 'librarian';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
