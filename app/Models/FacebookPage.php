<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class FacebookPage extends Model
{
    protected $fillable = [
        'user_id',
        'connected_platform_id',
        'page_id',
        'page_name',
        'page_access_token',
        'page_category',
        'followers_count',
        'status',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function connectedPlatform(): BelongsTo
    {
        return $this->belongsTo(ConnectedPlatform::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    /**
     * Accessors & Mutators
     */
    public function getPageAccessTokenAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setPageAccessTokenAttribute($value): void
    {
        $this->attributes['page_access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Helper methods
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function disconnect(): void
    {
        $this->update(['status' => 'disconnected']);
    }
}
