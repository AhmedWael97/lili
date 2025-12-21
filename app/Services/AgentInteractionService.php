<?php

namespace App\Services;

use App\Models\User;
use App\Models\AgentType;
use App\Models\AgentInteraction;
use App\Models\UserAgent;
use Illuminate\Support\Facades\DB;

class AgentInteractionService
{
    /**
     * Log an agent interaction
     */
    public function logInteraction(
        User $user,
        string $agentCode,
        string $action,
        array $inputData,
        $outputData,
        int $tokensUsed = 0,
        int $executionTimeMs = 0,
        bool $success = true,
        ?string $errorMessage = null,
        array $metadata = []
    ): AgentInteraction {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        // Update user agent last used timestamp
        $userAgent = UserAgent::where('user_id', $user->id)
            ->where('agent_type_id', $agentType->id)
            ->first();

        if ($userAgent) {
            $userAgent->markAsUsed();
        }

        // Create interaction log
        return AgentInteraction::create([
            'user_id' => $user->id,
            'agent_type_id' => $agentType->id,
            'action' => $action,
            'input_data' => $inputData,
            'output_data' => is_array($outputData) ? $outputData : ['response' => $outputData],
            'tokens_used' => $tokensUsed,
            'execution_time_ms' => $executionTimeMs,
            'success' => $success,
            'error_message' => $errorMessage,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Record user feedback on an interaction
     */
    public function recordFeedback(
        int $interactionId,
        string $feedback,
        ?string $comment = null
    ): void {
        $interaction = AgentInteraction::findOrFail($interactionId);

        $interaction->update([
            'feedback' => $feedback,
            'feedback_comment' => $comment,
        ]);
    }

    /**
     * Get agent analytics for a user
     */
    public function getAgentAnalytics(User $user, string $agentCode, int $days = 30): array
    {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        $interactions = AgentInteraction::where('user_id', $user->id)
            ->where('agent_type_id', $agentType->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        return [
            'total_interactions' => $interactions->count(),
            'successful_interactions' => $interactions->where('success', true)->count(),
            'failed_interactions' => $interactions->where('success', false)->count(),
            'total_tokens_used' => $interactions->sum('tokens_used'),
            'avg_execution_time_ms' => $interactions->avg('execution_time_ms'),
            'positive_feedback' => $interactions->where('feedback', 'positive')->count(),
            'negative_feedback' => $interactions->where('feedback', 'negative')->count(),
            'neutral_feedback' => $interactions->where('feedback', 'neutral')->count(),
        ];
    }

    /**
     * Get recent interactions for a user and agent
     */
    public function getRecentInteractions(User $user, string $agentCode, int $limit = 10)
    {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        return AgentInteraction::where('user_id', $user->id)
            ->where('agent_type_id', $agentType->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get successful patterns (for ML learning)
     */
    public function getSuccessfulPatterns(string $agentCode, int $limit = 100): array
    {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        $successfulInteractions = AgentInteraction::where('agent_type_id', $agentType->id)
            ->where('success', true)
            ->where('feedback', 'positive')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $successfulInteractions->map(function ($interaction) {
            return [
                'input' => $interaction->input_data,
                'output' => $interaction->output_data,
                'tokens_used' => $interaction->tokens_used,
                'execution_time_ms' => $interaction->execution_time_ms,
                'feedback_comment' => $interaction->feedback_comment,
            ];
        })->toArray();
    }

    /**
     * Get failed patterns (for improvement)
     */
    public function getFailedPatterns(string $agentCode, int $limit = 100): array
    {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        $failedInteractions = AgentInteraction::where('agent_type_id', $agentType->id)
            ->where(function ($query) {
                $query->where('success', false)
                      ->orWhere('feedback', 'negative');
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $failedInteractions->map(function ($interaction) {
            return [
                'input' => $interaction->input_data,
                'output' => $interaction->output_data,
                'error_message' => $interaction->error_message,
                'feedback_comment' => $interaction->feedback_comment,
            ];
        })->toArray();
    }

    /**
     * Get interaction by ID (for detailed view)
     */
    public function getInteraction(int $interactionId): AgentInteraction
    {
        return AgentInteraction::with(['user', 'agentType'])->findOrFail($interactionId);
    }

    /**
     * Export interaction data for ML training
     */
    public function exportTrainingData(string $agentCode, array $filters = []): array
    {
        $agentType = AgentType::where('code', $agentCode)->firstOrFail();

        $query = AgentInteraction::where('agent_type_id', $agentType->id);

        // Apply filters
        if (isset($filters['success'])) {
            $query->where('success', $filters['success']);
        }

        if (isset($filters['feedback'])) {
            $query->where('feedback', $filters['feedback']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($interaction) {
                return [
                    'id' => $interaction->id,
                    'action' => $interaction->action,
                    'input' => $interaction->input_data,
                    'output' => $interaction->output_data,
                    'tokens_used' => $interaction->tokens_used,
                    'execution_time_ms' => $interaction->execution_time_ms,
                    'success' => $interaction->success,
                    'feedback' => $interaction->feedback,
                    'feedback_comment' => $interaction->feedback_comment,
                    'metadata' => $interaction->metadata,
                    'created_at' => $interaction->created_at->toIso8601String(),
                ];
            })
            ->toArray();
    }
}
