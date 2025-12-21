<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SocialBladeService
{
    protected $clientId;
    protected $token;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.socialblade.client_id');
        $this->token = config('services.socialblade.token');
        $this->baseUrl = config('services.socialblade.base_url');
    }

    /**
     * Get Facebook page statistics from Social Blade
     */
    public function getFacebookStats(string $username): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('Social Blade API key not configured');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Authorization' => 'Bearer ' . $this->token,
            ])->get("{$this->baseUrl}/facebook/statistics", [
                'query' => $username,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'username' => $data['username'] ?? $username,
                    'followers' => $data['followers'] ?? 0,
                    'likes' => $data['likes'] ?? 0,
                    'posts' => $data['uploads'] ?? 0,
                    'engagement_rate' => $data['engagementRate'] ?? 0,
                    'source' => 'socialblade',
                ];
            }

            Log::warning('Social Blade API error: ' . $response->status() . ' - ' . $response->body());
            return null;

        } catch (Exception $e) {
            Log::error('Social Blade API failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Instagram statistics from Social Blade
     */
    public function getInstagramStats(string $username): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Authorization' => 'Bearer ' . $this->token,
            ])->get("{$this->baseUrl}/instagram/statistics", [
                'query' => $username,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'username' => $data['username'] ?? $username,
                    'followers' => $data['followers'] ?? 0,
                    'following' => $data['following'] ?? 0,
                    'posts' => $data['uploads'] ?? 0,
                    'engagement_rate' => $data['engagementRate'] ?? 0,
                    'source' => 'socialblade',
                ];
            }

            return null;

        } catch (Exception $e) {
            Log::error('Social Blade Instagram API failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get YouTube channel statistics from Social Blade
     */
    public function getYouTubeStats(string $username): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Authorization' => 'Bearer ' . $this->token,
            ])->get("{$this->baseUrl}/youtube/statistics", [
                'query' => $username,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'username' => $data['username'] ?? $username,
                    'subscribers' => $data['subscribers'] ?? 0,
                    'views' => $data['views'] ?? 0,
                    'videos' => $data['uploads'] ?? 0,
                    'engagement_rate' => $data['engagementRate'] ?? 0,
                    'source' => 'socialblade',
                ];
            }

            return null;

        } catch (Exception $e) {
            Log::error('Social Blade YouTube API failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Twitter/X statistics from Social Blade
     */
    public function getTwitterStats(string $username): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Authorization' => 'Bearer ' . $this->token,
            ])->get("{$this->baseUrl}/twitter/statistics", [
                'query' => $username,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'username' => $data['username'] ?? $username,
                    'followers' => $data['followers'] ?? 0,
                    'following' => $data['following'] ?? 0,
                    'tweets' => $data['statuses'] ?? 0,
                    'engagement_rate' => $data['engagementRate'] ?? 0,
                    'source' => 'socialblade',
                ];
            }

            return null;

        } catch (Exception $e) {
            Log::error('Social Blade Twitter API failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if API key is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->token);
    }
}
