<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'requester_id', 'assignee_id', 'title', 'description',
        'category', 'status', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RequestStatus::class,
            'submitted_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /** @return HasMany<RequestComment, $this> */
    public function comments(): HasMany
    {
        return $this->hasMany(RequestComment::class);
    }

    /** @return HasMany<RequestActivity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(RequestActivity::class);
    }
}