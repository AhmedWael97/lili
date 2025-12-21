<?php

namespace App\Repositories;

use App\Models\FacebookPage;
use Illuminate\Database\Eloquent\Collection;

class FacebookPageRepository extends BaseRepository
{
    public function __construct(FacebookPage $model)
    {
        parent::__construct($model);
    }

    /**
     * Get user's Facebook pages
     */
    public function getUserPages(int $userId, ?string $status = null): Collection
    {
        $query = $this->model->where('user_id', $userId);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get active pages
     */
    public function getActivePages(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Find by page ID
     */
    public function findByPageId(string $pageId, int $userId): ?FacebookPage
    {
        return $this->model
            ->where('page_id', $pageId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Connect new page
     */
    public function connectPage(array $pageData): FacebookPage
    {
        return $this->create($pageData);
    }

    /**
     * Disconnect page
     */
    public function disconnectPage(int $pageId): bool
    {
        return $this->update($pageId, ['status' => 'disconnected']);
    }

    /**
     * Update page token
     */
    public function updateToken(int $pageId, string $newToken): bool
    {
        return $this->update($pageId, [
            'page_access_token' => $newToken,
        ]);
    }

    /**
     * Update followers count
     */
    public function updateFollowersCount(int $pageId, int $count): bool
    {
        return $this->update($pageId, [
            'followers_count' => $count,
        ]);
    }

    /**
     * Get pages with content count
     */
    public function getPagesWithContentCount(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->withCount('contents')
            ->get();
    }

    /**
     * Check if user has reached page limit
     */
    public function hasReachedLimit(int $userId, int $limit): bool
    {
        $count = $this->model
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->count();
        
        return $count >= $limit;
    }
}
