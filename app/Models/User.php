<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'region_id',
        'phone',
        'photo',
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

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'Admin';
    }

    public function isAgent(): bool
    {
        return $this->role && $this->role->name === 'Agent';
    }

    public function isManager(): bool
    {
        return $this->role && $this->role->name === 'Manager';
    }
}
