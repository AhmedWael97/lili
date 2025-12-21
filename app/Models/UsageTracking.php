<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageTracking extends Model
{
    protected $table = 'usage_tracking';

    protected $fillable = [
        'user_id',
        'month_year',
        'posts_count',
        'comment_replies_count',
        'messages_count',
        'ad_spend_total',
    ];

    protected $casts = [
        'ad_spend_total' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper methods
     */
    public function incrementPosts(int $amount = 1): void
    {
        $this->increment('posts_count', $amount);
    }

    public function incrementCommentReplies(int $amount = 1): void
    {
        $this->increment('comment_replies_count', $amount);
    }

    public function incrementMessages(int $amount = 1): void
    {
        $this->increment('messages_count', $amount);
    }

    public function addAdSpend(float $amount): void
    {
        $this->increment('ad_spend_total', $amount);
    }

    public static function getOrCreateForCurrentMonth(int $userId): self
    {
        return self::firstOrCreate([
            'user_id' => $userId,
            'month_year' => now()->format('Y-m'),
        ]);
    }
}
