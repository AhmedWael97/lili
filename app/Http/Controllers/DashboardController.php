<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\FacebookPage;
use App\Models\Package;
use App\Repositories\ContentRepository;
use App\Repositories\UsageTrackingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected ContentRepository $contentRepo,
        protected UsageTrackingRepository $usageRepo
    ) {}

    /**
     * Show main dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $currentMonth = now()->format('Y-m');
        
        // Get user's subscription and package details
        $subscription = $user->subscription;
        $packageFeatures = $subscription ? $subscription->getFeatures() : [];
        
        // Get usage tracking for current month
        $usage = $this->usageRepo->getByUserAndMonth($user->id, $currentMonth);
        
        // Get connected Facebook pages
        $facebookPages = $user->facebookPages()
            ->where('status', 'active')
            ->withCount('contents')
            ->get();
        
        // Get recent content
        $recentContent = $this->contentRepo->getByUser($user->id, 5);
        
        // Calculate stats
        $stats = [
            'total_posts' => Content::where('user_id', $user->id)->count(),
            'posts_this_month' => $usage?->posts_created ?? 0,
            'connected_pages' => $facebookPages->count(),
            'total_engagement' => $this->calculateTotalEngagement($user->id),
        ];
        
        // Get package limits
        $limits = [
            'facebook_pages' => $packageFeatures['facebook_pages'] ?? 1,
            'posts_per_month' => $packageFeatures['posts_per_month'] ?? 10,
            'comment_replies_per_month' => $packageFeatures['comment_replies_per_month'] ?? 50,
        ];
        
        return view('dashboard.index', compact(
            'user',
            'subscription',
            'packageFeatures',
            'usage',
            'facebookPages',
            'recentContent',
            'stats',
            'limits'
        ));
    }

    /**
     * Show content management page
     */
    public function content()
    {
        $user = Auth::user();
        
        $contents = $this->contentRepo->getByUser($user->id, 50);
        $facebookPages = $user->facebookPages()->where('status', 'active')->get();
        
        return view('dashboard.content', compact('contents', 'facebookPages'));
    }

    /**
     * Show AI agents page
     */
    public function agents()
    {
        $user = Auth::user();
        $subscription = $user->subscription;
        $packageFeatures = $subscription ? $subscription->getFeatures() : [];
        
        // Get available agents based on package
        $availableAgents = $packageFeatures['agents'] ?? ['copywriter'];
        
        // Define all agents with their details
        $agents = [
            'strategist' => [
                'name' => 'Strategist Agent',
                'icon' => '🎯',
                'description' => 'Analyzes your page and creates content strategies',
                'status' => in_array('strategist', $availableAgents) ? 'active' : 'locked',
                'stats' => [
                    'calendars_created' => 12,
                    'strategies_deployed' => 5,
                ],
            ],
            'copywriter' => [
                'name' => 'Copywriter Agent',
                'icon' => '✍️',
                'description' => 'Writes engaging captions and ad copy',
                'status' => 'active',
                'stats' => [
                    'posts_created' => Content::where('user_id', $user->id)->count(),
                    'approval_rate' => 92,
                ],
            ],
            'creative' => [
                'name' => 'Creative Agent',
                'icon' => '🎨',
                'description' => 'Generates images using AI',
                'status' => in_array('creative', $availableAgents) ? 'active' : 'locked',
                'stats' => [
                    'images_created' => 0,
                    'average_quality' => 'Excellent',
                ],
            ],
            'community-manager' => [
                'name' => 'Community Manager Agent',
                'icon' => '💬',
                'description' => 'Responds to comments and messages',
                'status' => in_array('community-manager', $availableAgents) ? 'active' : 'locked',
                'stats' => [
                    'replies_sent' => 0,
                    'avg_response_time' => '2 min',
                ],
            ],
            'ads' => [
                'name' => 'Ads Agent',
                'icon' => '📊',
                'description' => 'Creates and optimizes ad campaigns',
                'status' => in_array('ads', $availableAgents) ? 'active' : 'locked',
                'stats' => [
                    'campaigns_active' => 0,
                    'total_spend' => 0,
                    'roas' => 0,
                ],
            ],
        ];
        
        return view('dashboard.agents', compact('agents', 'availableAgents'));
    }

    /**
     * Show platforms page
     */
    public function platforms()
    {
        $user = Auth::user();
        $subscription = $user->subscription;
        $packageFeatures = $subscription ? $subscription->getFeatures() : [];
        
        $facebookPages = $user->facebookPages()
            ->where('status', 'active')
            ->with('contents')
            ->get();
        
        $connectedPlatforms = $user->connectedPlatforms()
            ->where('status', 'active')
            ->get()
            ->groupBy('platform');
        
        $limits = [
            'facebook_pages' => $packageFeatures['facebook_pages'] ?? 1,
        ];
        
        return view('dashboard.platforms', compact(
            'facebookPages',
            'connectedPlatforms',
            'limits'
        ));
    }

    /**
     * Show analytics page
     */
    public function analytics()
    {
        $user = Auth::user();
        
        // Get date range (last 30 days by default)
        $startDate = request('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));
        
        // Get analytics data
        $analyticsData = [
            'total_reach' => 0,
            'total_engagement' => 0,
            'new_followers' => 0,
            'posts_published' => Content::where('user_id', $user->id)
                ->where('status', 'published')
                ->whereBetween('published_at', [$startDate, $endDate])
                ->count(),
            'comments_received' => 0,
            'messages_received' => 0,
        ];
        
        // Get top performing posts
        $topPosts = Content::where('user_id', $user->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();
        
        return view('dashboard.analytics', compact(
            'analyticsData',
            'topPosts',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $user = Auth::user();
        $brandSettings = $user->brandSettings;
        
        // Get marketing agent configuration if exists
        $agentConfig = \App\Models\AgentConfiguration::where('user_id', $user->id)
            ->where('agent_code', 'marketing')
            ->where('is_complete', true)
            ->first();
        
        return view('dashboard.settings', compact('user', 'brandSettings', 'agentConfig'));
    }

    /**
     * Show billing page
     */
    public function billing()
    {
        $user = Auth::user();
        $subscription = $user->subscription;
        $currentMonth = now()->format('Y-m');
        
        // Get usage for current month
        $usage = $this->usageRepo->getByUserAndMonth($user->id, $currentMonth);
        
        // Get package features
        $packageFeatures = $subscription ? $subscription->getFeatures() : [];
        
        // Get all available packages for upgrades
        $allPackages = Package::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();
        
        // Get billing history (mock data for now)
        $billingHistory = [];
        
        return view('dashboard.billing', compact(
            'subscription',
            'usage',
            'packageFeatures',
            'allPackages',
            'billingHistory'
        ));
    }

    /**
     * Calculate total engagement for user
     */
    private function calculateTotalEngagement($userId)
    {
        // Engagement data is stored in metadata JSON field
        // For now, return 0 until we implement metadata aggregation
        return 0;
    }

    /**
     * Show agent dashboard with all active agents
     */
    public function agentDashboard()
    {
        $user = Auth::user();
        
        // Get active agents with their types
        $activeAgents = $user->userAgents()
            ->where('status', 'active')
            ->with('agentType')
            ->get();
        
        // Get available agent slots
        $package = $user->subscription?->package;
        $totalSlots = $package?->agent_slots ?? 1;
        $usedSlots = $activeAgents->count();
        $availableSlots = $totalSlots === -1 ? 'unlimited' : max(0, $totalSlots - $usedSlots);
        
        // Get recent interactions for each agent
        $recentInteractions = [];
        foreach ($activeAgents as $userAgent) {
            $recentInteractions[$userAgent->agent_type_id] = $user->agentInteractions()
                ->where('agent_type_id', $userAgent->agent_type_id)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        }
        
        return view('agents.dashboard', compact(
            'activeAgents',
            'totalSlots',
            'usedSlots',
            'availableSlots',
            'recentInteractions'
        ));
    }
}
