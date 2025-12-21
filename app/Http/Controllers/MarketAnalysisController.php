<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MarketAnalysis\CompetitorAnalysisService;
use App\Services\MarketAnalysis\MarketAnalysisService;
use App\Models\CompetitorAnalysis;
use App\Models\MarketInsight;
use App\Models\BrandSetting;

class MarketAnalysisController extends Controller
{
    protected $competitorService;
    protected $marketService;

    public function __construct(
        CompetitorAnalysisService $competitorService,
        MarketAnalysisService $marketService
    ) {
        $this->competitorService = $competitorService;
        $this->marketService = $marketService;
    }

    /**
     * Market analysis dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's brand settings
        $brandSettings = BrandSetting::where('user_id', $user->id)->first();
        $industry = $brandSettings->industry ?? 'general';
        
        // Get competitor analyses
        $competitors = CompetitorAnalysis::where('user_id', $user->id)
            ->orderBy('last_analyzed_at', 'desc')
            ->get();
        
        // Get industry benchmarks
        $benchmarks = $this->marketService->getIndustryBenchmarks($industry);
        
        // Get cached SWOT if available
        $swot = MarketInsight::where('user_id', $user->id)
            ->where('insight_type', 'swot')
            ->valid()
            ->first();
        
        return view('marketing-studio.market-analysis.index', [
            'competitors' => $competitors,
            'benchmarks' => $benchmarks,
            'swot' => $swot,
            'industry' => $industry,
        ]);
    }

    /**
     * Analyze a competitor
     */
    public function analyzeCompetitor(Request $request)
    {
        $validated = $request->validate([
            'competitor_name' => 'required|string|max:255',
            'facebook_url' => 'required|string',
            'industry' => 'nullable|string|max:255',
            'manual_follower_count' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        \Log::info('Analyzing competitor', [
            'user_id' => $user->id,
            'competitor_name' => $validated['competitor_name'],
            'facebook_url' => $validated['facebook_url'],
            'manual_follower_count' => $validated['manual_follower_count'] ?? 'none',
        ]);
        
        $result = $this->competitorService->analyzeCompetitor(
            $user->id,
            $validated['facebook_url'],
            $validated['competitor_name'],
            $validated['manual_follower_count'] ?? null
        );

        \Log::info('Competitor analysis result', [
            'success' => $result['success'],
            'data' => $result['data'] ?? null,
        ]);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Competitor analyzed successfully',
                'data' => $result['data'],
                'cached' => $result['cached'] ?? false,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Failed to analyze competitor',
        ], 400);
    }

    /**
     * Compare with competitor
     */
    public function compareCompetitor(Request $request)
    {
        $validated = $request->validate([
            'competitor_id' => 'required|exists:competitor_analyses,id',
        ]);

        $user = Auth::user();
        $competitor = CompetitorAnalysis::where('id', $validated['competitor_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Get user's Facebook page data
        $userPage = \App\Models\FacebookPage::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$userPage) {
            return response()->json([
                'success' => false,
                'message' => 'Please connect your Facebook page first',
            ], 400);
        }

        // Get user page data as array
        $userPageData = [
            'followers_count' => 0, // Would need to fetch from Facebook API
            'page_id' => $userPage->page_id,
        ];
        
        // Compare
        $comparison = $this->competitorService->compareWithCompetitor(
            $userPageData,
            $competitor
        );

        return response()->json([
            'success' => true,
            'comparison' => $comparison,
        ]);
    }

    /**
     * Generate SWOT analysis
     */
    public function generateSWOT(Request $request)
    {
        $user = Auth::user();
        
        // Get brand settings
        $brandSettings = BrandSetting::where('user_id', $user->id)->first();
        
        if (!$brandSettings) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your brand profile first',
            ], 400);
        }

        $businessContext = [
            'industry' => $brandSettings->industry,
            'target_audience' => $brandSettings->target_audience,
            'products_services' => $brandSettings->brand_voice ?? 'N/A',
            'unique_value_proposition' => $brandSettings->unique_selling_points ?? 'N/A',
        ];

        // Get competitor data
        $competitors = CompetitorAnalysis::where('user_id', $user->id)->get();
        $competitorData = $competitors->map(function ($comp) {
            return [
                'name' => $comp->competitor_name,
                'followers' => $comp->page_data['followers_count'] ?? 0,
                'engagement_rate' => $comp->engagement_metrics['avg_engagement_rate'] ?? 0,
            ];
        })->toArray();

        $result = $this->marketService->generateSWOTAnalysis(
            $user->id,
            $brandSettings->industry,
            $businessContext,
            $competitorData
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'swot' => $result['data'],
                'cached' => $result['cached'] ?? false,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Failed to generate SWOT analysis',
        ], 400);
    }

    /**
     * Get content opportunities
     */
    public function getOpportunities(Request $request)
    {
        $user = Auth::user();
        
        // Get brand settings
        $brandSettings = BrandSetting::where('user_id', $user->id)->first();
        
        if (!$brandSettings || !$brandSettings->industry) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your brand profile and set your industry first in Settings',
                'opportunities' => [],
            ]);
        }

        // Get user's Facebook page metrics
        $userPage = \App\Models\FacebookPage::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        $userMetrics = [
            'posting_frequency' => 3, // Calculate from content table
            'engagement_rate' => 1.2, // Calculate from Facebook insights
        ];

        if ($userPage) {
            // Try to get real metrics from user's content
            $contentCount = \App\Models\Content::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
            $userMetrics['posting_frequency'] = $contentCount;
        }

        $result = $this->marketService->identifyContentOpportunities(
            $user->id,
            $brandSettings->industry,
            $userMetrics
        );

        return response()->json($result);
    }

    /**
     * Delete competitor analysis
     */
    public function deleteCompetitor($id)
    {
        $user = Auth::user();
        
        $competitor = CompetitorAnalysis::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $competitor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Competitor analysis deleted',
        ]);
    }

    /**
     * Refresh competitor analysis
     */
    public function refreshCompetitor($id)
    {
        $user = Auth::user();
        
        $competitor = CompetitorAnalysis::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Force refresh by deleting and re-analyzing
        $name = $competitor->competitor_name;
        $facebookUrl = "https://facebook.com/" . $competitor->facebook_page_id;
        
        // Keep manual follower count if it was manually entered
        $manualCount = null;
        if (isset($competitor->page_data['manual_input']) && $competitor->page_data['manual_input']) {
            $manualCount = (string)($competitor->page_data['followers_count'] ?? 0);
        }
        
        $competitor->delete();

        $result = $this->competitorService->analyzeCompetitor(
            $user->id,
            $facebookUrl,
            $name,
            $manualCount
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Competitor analysis refreshed',
                'data' => $result['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Failed to refresh analysis',
        ], 400);
    }
}
