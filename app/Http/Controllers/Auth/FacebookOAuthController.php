<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FacebookService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;

class FacebookOAuthController extends Controller
{
    public function __construct(
        protected FacebookService $facebookService
    ) {}

    /**
     * Redirect to Facebook OAuth
     */
    public function redirect()
    {
        return Socialite::driver('facebook')
            ->scopes([
                'pages_show_list',
                'pages_manage_posts',
                'pages_read_engagement',
                'pages_manage_engagement',
                'pages_messaging',
                'read_page_mailboxes',
            ])
            ->redirect();
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function callback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Please login first to connect Facebook.');
            }

            // Connect Facebook platform
            $this->facebookService->connectPlatform($user->id, [
                'platform_user_id' => $facebookUser->getId(),
                'platform_username' => $facebookUser->getName(),
                'access_token' => $facebookUser->token,
                'refresh_token' => $facebookUser->refreshToken ?? null,
                'token_expires_at' => $facebookUser->expiresIn ? now()->addSeconds($facebookUser->expiresIn) : null,
                'permissions' => $facebookUser->approvedScopes ?? null,
            ]);

            // Fetch user's Facebook pages
            $this->fetchAndConnectPages($user->id, $facebookUser->token);

            return redirect()->route('dashboard.platforms')
                ->with('success', 'Facebook account connected successfully!');

        } catch (Exception $e) {
            return redirect()->route('dashboard.platforms')
                ->with('error', 'Failed to connect Facebook: ' . $e->getMessage());
        }
    }

    /**
     * Fetch and connect Facebook pages
     */
    protected function fetchAndConnectPages(int $userId, string $userAccessToken)
    {
        try {
            $fb = new \Facebook\Facebook([
                'app_id' => config('services.facebook.client_id'),
                'app_secret' => config('services.facebook.client_secret'),
                'default_graph_version' => 'v18.0',
            ]);

            // Get pages user manages
            $response = $fb->get('/me/accounts', $userAccessToken);
            $pages = $response->getDecodedBody()['data'] ?? [];

            $connectedPlatform = $this->facebookService->getUserPages($userId)->first()?->connectedPlatform;

            foreach ($pages as $pageData) {
                $this->facebookService->connectPage($userId, [
                    'connected_platform_id' => $connectedPlatform->id ?? null,
                    'page_id' => $pageData['id'],
                    'page_name' => $pageData['name'],
                    'page_access_token' => $pageData['access_token'],
                    'page_category' => $pageData['category'] ?? null,
                    'permissions' => $pageData['perms'] ?? null,
                ]);
            }

        } catch (Exception $e) {
            \Log::error('Failed to fetch Facebook pages: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Facebook
     */
    public function disconnect()
    {
        $user = Auth::user();
        
        // Disconnect all pages
        $pages = $this->facebookService->getUserPages($user->id);
        
        foreach ($pages as $page) {
            $this->facebookService->disconnectPage($page->id, $user->id);
        }

        return redirect()->route('dashboard.platforms')
            ->with('success', 'Facebook disconnected successfully.');
    }
}
