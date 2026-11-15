<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'manager_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /** @return HasMany<User, $this> */
    public function directReports(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /** @return HasMany<ServiceRequest, $this> */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'requester_id');
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function isManagerOf(User $user): bool
    {
        return $this->hasRole(UserRole::Manager) && $user->manager_id === $this->id;
    }
}