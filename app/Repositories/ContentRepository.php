<?php

namespace App\Repositories;

use App\Models\Content;
use Illuminate\Database\Eloquent\Collection;

class ContentRepository extends BaseRepository
{
    public function __construct(Content $model)
    {
        parent::__construct($model);
    }

    /**
     * Get user's content
     */
    public function getUserContent(int $userId, ?string $status = null)
    {
        $query = $this->model->where('user_id', $userId);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Get content by user with limit
     */
    public function getByUser(int $userId, int $limit = 15)
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get content by page
     */
    public function getByFacebookPage(int $pageId, ?string $status = null): Collection
    {
        $query = $this->model->where('facebook_page_id', $pageId);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get draft content
     */
    public function getDrafts(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->draft()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get scheduled content
     */
    public function getScheduled(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->scheduled()
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }

    /**
     * Get content due for publishing
     */
    public function getDueForPublishing(): Collection
    {
        return $this->model
            ->dueForPublishing()
            ->with(['user', 'facebookPage'])
            ->get();
    }

    /**
     * Get published content
     */
    public function getPublished(int $userId, ?int $limit = null)
    {
        $query = $this->model
            ->where('user_id', $userId)
            ->published()
            ->orderBy('published_at', 'desc');
        
        return $limit ? $query->limit($limit)->get() : $query->paginate(15);
    }

    /**
     * Get content by agent
     */
    public function getByAgent(string $agentName, int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('agent_used', $agentName)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mark as published
     */
    public function markAsPublished(int $contentId, string $platformPostId): bool
    {
        return $this->update($contentId, [
            'status' => 'published',
            'published_at' => now(),
            'platform_post_id' => $platformPostId,
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(int $contentId, string $errorMessage): bool
    {
        return $this->update($contentId, [
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Schedule content
     */
    public function schedule(int $contentId, string $scheduledAt): bool
    {
        return $this->update($contentId, [
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Get content statistics for user
     */
    public function getStatistics(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = $this->model->where('user_id', $userId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return [
            'total' => $query->count(),
            'published' => (clone $query)->where('status', 'published')->count(),
            'scheduled' => (clone $query)->where('status', 'scheduled')->count(),
            'draft' => (clone $query)->where('status', 'draft')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
        ];
    }
}
