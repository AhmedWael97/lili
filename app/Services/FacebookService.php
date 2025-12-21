<?php

namespace App\Services;

use App\Repositories\FacebookPageRepository;
use App\Repositories\ConnectedPlatformRepository;
use App\Repositories\AuditLogRepository;
use App\Models\FacebookPage;
use Illuminate\Support\Facades\DB;

class FacebookService
{
    public function __construct(
        protected FacebookPageRepository $pageRepo,
        protected ConnectedPlatformRepository $platformRepo,
        protected AuditLogRepository $auditRepo
    ) {}

    /**
     * Connect Facebook platform (OAuth callback)
     */
    public function connectPlatform(int $userId, array $data): void
    {
        DB::transaction(function () use ($userId, $data) {
            // Create or update connected platform
            $platform = $this->platformRepo->connectPlatform([
                'user_id' => $userId,
                'platform' => 'facebook',
                'platform_user_id' => $data['platform_user_id'],
                'platform_username' => $data['platform_username'] ?? null,
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'] ?? null,
                'token_expires_at' => $data['token_expires_at'] ?? null,
                'status' => 'active',
                'permissions' => $data['permissions'] ?? null,
            ]);

            $this->auditRepo->log(
                'platform_connected',
                "Connected Facebook account",
                $userId,
                'ConnectedPlatform',
                $platform->id
            );
        });
    }

    /**
     * Connect Facebook page
     */
    public function connectPage(int $userId, array $pageData): FacebookPage
    {
        return DB::transaction(function () use ($userId, $pageData) {
            // Check if page already exists
            $existingPage = $this->pageRepo->findByPageId($pageData['page_id'], $userId);
            
            if ($existingPage) {
                // Update token if page exists
                $this->pageRepo->updateToken($existingPage->id, $pageData['page_access_token']);
                $this->pageRepo->update($existingPage->id, ['status' => 'active']);
                
                return $existingPage->fresh();
            }

            // Create new page connection
            $page = $this->pageRepo->connectPage([
                'user_id' => $userId,
                'connected_platform_id' => $pageData['connected_platform_id'],
                'page_id' => $pageData['page_id'],
                'page_name' => $pageData['page_name'],
                'page_access_token' => $pageData['page_access_token'],
                'page_category' => $pageData['page_category'] ?? null,
                'followers_count' => $pageData['followers_count'] ?? 0,
                'status' => 'active',
                'permissions' => $pageData['permissions'] ?? null,
            ]);

            $this->auditRepo->log(
                'facebook_page_connected',
                "Connected Facebook page: {$page->page_name}",
                $userId,
                'FacebookPage',
                $page->id
            );

            return $page;
        });
    }

    /**
     * Disconnect Facebook page
     */
    public function disconnectPage(int $pageId, int $userId): bool
    {
        $page = $this->pageRepo->find($pageId);
        
        if (!$page || $page->user_id !== $userId) {
            throw new \Exception('Page not found or access denied');
        }

        $result = $this->pageRepo->disconnectPage($pageId);

        if ($result) {
            $this->auditRepo->log(
                'facebook_page_disconnected',
                "Disconnected Facebook page: {$page->page_name}",
                $userId,
                'FacebookPage',
                $pageId
            );
        }

        return $result;
    }

    /**
     * Get user's connected pages
     */
    public function getUserPages(int $userId, ?string $status = 'active')
    {
        return $this->pageRepo->getUserPages($userId, $status);
    }

    /**
     * Get active pages count
     */
    public function getActivePagesCount(int $userId): int
    {
        return $this->pageRepo->getActivePages($userId)->count();
    }

    /**
     * Check if user can connect more pages
     */
    public function canConnectMorePages(int $userId, int $limit): bool
    {
        return !$this->pageRepo->hasReachedLimit($userId, $limit);
    }

    /**
     * Update page followers count
     */
    public function updatePageFollowers(int $pageId, int $count): bool
    {
        return $this->pageRepo->updateFollowersCount($pageId, $count);
    }

    /**
     * Get page with content statistics
     */
    public function getPageWithStats(int $pageId, int $userId): ?FacebookPage
    {
        $page = $this->pageRepo->find($pageId);
        
        if (!$page || $page->user_id !== $userId) {
            return null;
        }

        $page->loadCount('contents');
        
        return $page;
    }
}
