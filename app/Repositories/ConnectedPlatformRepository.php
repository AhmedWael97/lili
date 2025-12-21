<?php

namespace App\Repositories;

use App\Models\ConnectedPlatform;
use Illuminate\Database\Eloquent\Collection;

class ConnectedPlatformRepository extends BaseRepository
{
    public function __construct(ConnectedPlatform $model)
    {
        parent::__construct($model);
    }

    /**
     * Get user's connected platforms
     */
    public function getUserPlatforms(int $userId, ?string $status = null): Collection
    {
        $query = $this->model->where('user_id', $userId);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->get();
    }

    /**
     * Find by platform and user
     */
    public function findByPlatform(int $userId, string $platform): ?ConnectedPlatform
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('platform', $platform)
            ->first();
    }

    /**
     * Connect platform
     */
    public function connectPlatform(array $platformData): ConnectedPlatform
    {
        return $this->create($platformData);
    }

    /**
     * Disconnect platform
     */
    public function disconnectPlatform(int $platformId): bool
    {
        return $this->update($platformId, ['status' => 'disconnected']);
    }

    /**
     * Update tokens
     */
    public function updateTokens(int $platformId, string $accessToken, ?string $refreshToken = null, ?string $expiresAt = null): bool
    {
        $data = [
            'access_token' => $accessToken,
            'status' => 'active',
        ];
        
        if ($refreshToken) {
            $data['refresh_token'] = $refreshToken;
        }
        
        if ($expiresAt) {
            $data['token_expires_at'] = $expiresAt;
        }
        
        return $this->update($platformId, $data);
    }

    /**
     * Get expired tokens
     */
    public function getExpiredTokens(): Collection
    {
        return $this->model
            ->where('status', 'active')
            ->where('token_expires_at', '<', now())
            ->get();
    }

    /**
     * Check if platform is connected
     */
    public function isPlatformConnected(int $userId, string $platform): bool
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('platform', $platform)
            ->where('status', 'active')
            ->exists();
    }
}
