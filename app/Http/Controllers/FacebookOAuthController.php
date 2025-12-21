<?php

namespace App\Http\Controllers;

use App\Services\FacebookService;
use App\Repositories\ConnectedPlatformRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Facebook\Facebook;

class FacebookOAuthController extends Controller
{
    public function __construct(
        protected FacebookService $facebookService,
        protected ConnectedPlatformRepository $platformRepo
    ) {}

    /**
     * Redirect to Facebook OAuth
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->scopes([
                'pages_show_list',
                'pages_read_engagement',
                'pages_manage_metadata',
                'pages_read_user_content',
                'public_profile'
            ])
            ->redirect();
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function handleFacebookCallback()
    {
        try {
            $socialiteUser = Socialite::driver('facebook')->user();
            
            // Store platform connection
            $this->facebookService->connectPlatform(Auth::id(), [
                'platform_user_id' => $socialiteUser->getId(),
                'platform_username' => $socialiteUser->getName(),
                'access_token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken ?? null,
                'token_expires_at' => $socialiteUser->expiresIn 
                    ? now()->addSeconds($socialiteUser->expiresIn) 
                    : null,
                'permissions' => json_encode($socialiteUser->getRaw()['permissions'] ?? []),
            ]);

            return redirect()->route('facebook.select-pages')
                ->with('success', 'Facebook account connected successfully! Now select your pages.');

        } catch (\Exception $e) {
            \Log::error('Facebook OAuth failed: ' . $e->getMessage());
            return redirect()->route('dashboard.settings')
                ->with('error', 'Failed to connect Facebook account. Please try again.');
        }
    }

    /**
     * Show page selection interface
     */
    public function selectPages()
    {
        try {
            $platform = $this->platformRepo->getActivePlatform(Auth::id(), 'facebook');
            
            if (!$platform) {
                return redirect()->route('dashboard.settings')
                    ->with('error', 'Please connect your Facebook account first.');
            }

            // Get user's pages from Facebook
            $fb = new Facebook([
                'app_id' => config('services.facebook.client_id'),
                'app_secret' => config('services.facebook.client_secret'),
                'default_graph_version' => 'v18.0',
            ]);

            $response = $fb->get('/me/accounts', $platform->access_token);
            $pages = $response->getDecodedBody()['data'] ?? [];

            return view('facebook.select-pages', compact('pages'));

        } catch (\Exception $e) {
            \Log::error('Failed to fetch Facebook pages: ' . $e->getMessage());
            return redirect()->route('dashboard.settings')
                ->with('error', 'Failed to fetch pages. Please reconnect your Facebook account.');
        }
    }

    /**
     * Connect selected pages
     */
    public function connectPages(Request $request)
    {
        $validated = $request->validate([
            'pages' => 'required|array|min:1',
            'pages.*.page_id' => 'required|string',
            'pages.*.page_name' => 'required|string',
            'pages.*.page_access_token' => 'required|string',
            'pages.*.followers_count' => 'nullable|integer',
        ]);

        try {
            $connected = 0;
            foreach ($validated['pages'] as $pageData) {
                $this->facebookService->connectPage(Auth::id(), $pageData);
                $connected++;
            }

            return redirect()->route('dashboard.content')
                ->with('success', "Successfully connected {$connected} Facebook page(s)!");

        } catch (\Exception $e) {
            \Log::error('Failed to connect pages: ' . $e->getMessage());
            return redirect()->route('dashboard.settings')
                ->with('error', 'Failed to connect pages. Please try again.');
        }
    }

    /**
     * Disconnect Facebook platform
     */
    public function disconnect()
    {
        try {
            $platform = $this->platformRepo->getActivePlatform(Auth::id(), 'facebook');
            
            if ($platform) {
                $this->platformRepo->update($platform->id, ['status' => 'disconnected']);
            }

            return redirect()->route('dashboard.settings')
                ->with('success', 'Facebook account disconnected successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to disconnect Facebook: ' . $e->getMessage());
            return redirect()->route('dashboard.settings')
                ->with('error', 'Failed to disconnect Facebook account.');
        }
    }
}

