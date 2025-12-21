<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class ConnectedPlatform extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'platform_user_id',
        'platform_username',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'status',
        'permissions',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'permissions' => 'array',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facebookPages(): HasMany
    {
        return $this->hasMany(FacebookPage::class);
    }

    /**
     * Accessors & Mutators
     */
    public function getAccessTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setAccessTokenAttribute($value): void
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getRefreshTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setRefreshTokenAttribute($value): void
    {
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Helper methods
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->token_expires_at === null || $this->token_expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    public function disconnect(): void
    {
        $this->update(['status' => 'disconnected']);
    }
}
