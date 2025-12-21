<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentInteraction extends Model
{
    protected $fillable = [
        'user_id',
        'agent_type_id',
        'action',
        'input_data',
        'output_data',
        'tokens_used',
        'execution_time_ms',
        'feedback',
        'feedback_comment',
        'success',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'success' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agent type
     */
    public function agentType(): BelongsTo
    {
        return $this->belongsTo(AgentType::class);
    }

    /**
     * Add positive feedback
     */
    public function addPositiveFeedback(?string $comment = null): void
    {
        $this->update([
            'feedback' => 'positive',
            'feedback_comment' => $comment,
        ]);
    }

    /**
     * Add negative feedback
     */
    public function addNegativeFeedback(?string $comment = null): void
    {
        $this->update([
            'feedback' => 'negative',
            'feedback_comment' => $comment,
        ]);
    }
}
