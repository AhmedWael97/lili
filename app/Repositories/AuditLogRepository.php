<?php

namespace App\Repositories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Collection;

class AuditLogRepository extends BaseRepository
{
    public function __construct(AuditLog $model)
    {
        parent::__construct($model);
    }

    /**
     * Create audit log entry
     */
    public function log(
        string $action,
        string $description,
        ?int $userId = null,
        ?string $resourceType = null,
        ?int $resourceId = null,
        ?array $metadata = null
    ): AuditLog {
        return $this->create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get user's audit logs
     */
    public function getUserLogs(int $userId, int $limit = 50)
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get logs by action
     */
    public function getByAction(string $action, ?int $limit = null)
    {
        $query = $this->model
            ->where('action', $action)
            ->orderBy('created_at', 'desc');
        
        return $limit ? $query->limit($limit)->get() : $query->paginate(50);
    }

    /**
     * Get logs by resource
     */
    public function getByResource(string $resourceType, int $resourceId): Collection
    {
        return $this->model
            ->where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent logs for admin dashboard
     */
    public function getRecentLogs(int $limit = 100)
    {
        return $this->model
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search logs
     */
    public function search(string $term, ?string $action = null)
    {
        $query = $this->model->where('description', 'like', "%{$term}%");
        
        if ($action) {
            $query->where('action', $action);
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(50);
    }
}
