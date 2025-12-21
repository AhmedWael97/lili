<?php

namespace App\Services;

use App\Repositories\ContentRepository;
use App\Repositories\FacebookPageRepository;
use App\Repositories\UsageTrackingRepository;
use App\Repositories\AuditLogRepository;
use App\Models\Content;
use Illuminate\Support\Facades\DB;

class ContentService
{
    public function __construct(
        protected ContentRepository $contentRepo,
        protected FacebookPageRepository $pageRepo,
        protected UsageTrackingRepository $usageRepo,
        protected AuditLogRepository $auditRepo
    ) {}

    /**
     * Create content
     */
    public function createContent(int $userId, array $data): Content
    {
        // Verify user owns the Facebook page
        $page = $this->pageRepo->find($data['facebook_page_id']);
        
        if (!$page || $page->user_id !== $userId) {
            throw new \Exception('Invalid Facebook page');
        }

        return DB::transaction(function () use ($userId, $data) {
            $content = $this->contentRepo->create([
                'user_id' => $userId,
                'facebook_page_id' => $data['facebook_page_id'],
                'content_type' => $data['content_type'] ?? 'post',
                'caption' => $data['caption'],
                'image_url' => $data['image_url'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'agent_used' => $data['agent_used'] ?? null,
            ]);

            $this->auditRepo->log(
                'content_created',
                "Content created: {$content->content_type}",
                $userId,
                'Content',
                $content->id
            );

            return $content;
        });
    }

    /**
     * Update content
     */
    public function updateContent(int $contentId, int $userId, array $data): bool
    {
        $content = $this->contentRepo->find($contentId);
        
        if (!$content || $content->user_id !== $userId) {
            throw new \Exception('Content not found or access denied');
        }

        if ($content->status === 'published') {
            throw new \Exception('Cannot edit published content');
        }

        $result = $this->contentRepo->update($contentId, $data);

        if ($result) {
            $this->auditRepo->log(
                'content_updated',
                "Content updated",
                $userId,
                'Content',
                $contentId
            );
        }

        return $result;
    }

    /**
     * Schedule content for publishing
     */
    public function scheduleContent(int $contentId, int $userId, string $scheduledAt): bool
    {
        $content = $this->contentRepo->find($contentId);
        
        if (!$content || $content->user_id !== $userId) {
            throw new \Exception('Content not found or access denied');
        }

        $result = $this->contentRepo->schedule($contentId, $scheduledAt);

        if ($result) {
            $this->auditRepo->log(
                'content_scheduled',
                "Content scheduled for {$scheduledAt}",
                $userId,
                'Content',
                $contentId
            );
        }

        return $result;
    }

    /**
     * Publish content immediately
     */
    public function publishContent(int $contentId, int $userId): Content
    {
        $content = $this->contentRepo->find($contentId);
        
        if (!$content || $content->user_id !== $userId) {
            throw new \Exception('Content not found or access denied');
        }

        if ($content->status === 'published') {
            throw new \Exception('Content already published');
        }

        // This will be handled by Facebook service in Sprint 2
        // For now, just mark as scheduled for immediate publishing
        $this->contentRepo->schedule($contentId, now()->toDateTimeString());

        $this->auditRepo->log(
            'content_publish_requested',
            "Content queued for publishing",
            $userId,
            'Content',
            $contentId
        );

        return $content->fresh();
    }

    /**
     * Delete content
     */
    public function deleteContent(int $contentId, int $userId): bool
    {
        $content = $this->contentRepo->find($contentId);
        
        if (!$content || $content->user_id !== $userId) {
            throw new \Exception('Content not found or access denied');
        }

        if ($content->status === 'published') {
            throw new \Exception('Cannot delete published content');
        }

        $result = $this->contentRepo->delete($contentId);

        if ($result) {
            $this->auditRepo->log(
                'content_deleted',
                "Content deleted",
                $userId,
                'Content',
                $contentId
            );
        }

        return $result;
    }

    /**
     * Get user's content with pagination
     */
    public function getUserContent(int $userId, ?string $status = null, int $page = 1)
    {
        return $this->contentRepo->getUserContent($userId, $status);
    }

    /**
     * Get content by page
     */
    public function getContentByPage(int $pageId, int $userId, ?string $status = null)
    {
        // Verify user owns the page
        $page = $this->pageRepo->find($pageId);
        
        if (!$page || $page->user_id !== $userId) {
            throw new \Exception('Invalid Facebook page');
        }

        return $this->contentRepo->getByFacebookPage($pageId, $status);
    }

    /**
     * Get content statistics
     */
    public function getStatistics(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        return $this->contentRepo->getStatistics($userId, $startDate, $endDate);
    }

    /**
     * Get drafts
     */
    public function getDrafts(int $userId)
    {
        return $this->contentRepo->getDrafts($userId);
    }

    /**
     * Get scheduled content
     */
    public function getScheduled(int $userId)
    {
        return $this->contentRepo->getScheduled($userId);
    }
}
