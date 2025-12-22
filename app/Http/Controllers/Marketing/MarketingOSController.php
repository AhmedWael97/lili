<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Competitor;
use App\Models\StrategyPlan;
use App\Services\Marketing\Agents\OrchestratorAgent;
use App\Services\Marketing\Agents\CompetitorIntelligenceAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MarketingOSController extends Controller
{
    protected OrchestratorAgent $orchestrator;
    protected CompetitorIntelligenceAgent $competitorAgent;

    public function __construct(
        OrchestratorAgent $orchestrator,
        CompetitorIntelligenceAgent $competitorAgent
    ) {
        $this->orchestrator = $orchestrator;
        $this->competitorAgent = $competitorAgent;
    }

    /**
     * Marketing OS Dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        $brand = Brand::with([
            'competitors.keywords',
            'competitors.backlinks',
            'competitors.socialProfiles'
        ])->where('user_id', $user->id)->first();
        
        $strategies = StrategyPlan::whereHas('brand', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->orderBy('created_at', 'desc')->get();

        return view('marketing.index', [
            'brand' => $brand,
            'strategies' => $strategies,
        ]);
    }

    /**
     * Brand Setup Form
     */
    public function setupBrand()
    {
        $user = Auth::user();
        $brand = Brand::where('user_id', $user->id)->first();

        return view('marketing.setup-brand', [
            'brand' => $brand,
        ]);
    }

    /**
     * Save or Update Brand
     */
    public function storeBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url',
            'industry' => 'required|string|max:255',
            'country' => 'required|string|max:2',
            'currency' => 'nullable|string|max:3',
            'description' => 'nullable|string',
            'target_audience' => 'nullable|array',
            'value_proposition' => 'nullable|array',
            'products_services' => 'nullable|array',
            'monthly_budget' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();

        $brand = Brand::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        // Auto-determine budget tier
        $brand->budget_tier = $brand->determineBudgetTier();
        $brand->save();

        return response()->json([
            'success' => true,
            'message' => 'Brand profile saved successfully',
            'brand' => $brand,
        ]);
    }

    /**
     * Generate Complete Strategy
     */
    public function generateStrategy(Request $request)
    {
        try {
            $user = Auth::user();
            $brand = Brand::where('user_id', $user->id)->firstOrFail();

            Log::info("Generating strategy for brand: {$brand->id}");

            // Run orchestrator
            $result = $this->orchestrator->generateStrategy($brand);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'],
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Strategy generated successfully!',
                'strategy_id' => $result['strategy_plan_id'],
                'data' => $result['data'],
            ]);

        } catch (\Exception $e) {
            Log::error('Strategy generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate strategy: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View Strategy Details
     */
    public function viewStrategy($id)
    {
        $user = Auth::user();
        
        $strategy = StrategyPlan::whereHas('brand', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->findOrFail($id);

        return view('marketing.strategy-detail', [
            'strategy' => $strategy,
        ]);
    }

    /**
     * Add Competitor
     */
    public function addCompetitor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'required|url',
        ]);

        $user = Auth::user();
        $brand = Brand::where('user_id', $user->id)->firstOrFail();

        // Analyze competitor
        $result = $this->competitorAgent->analyze([
            'name' => $validated['name'],
            'website' => $validated['website'],
            'industry' => $brand->industry,
        ]);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze competitor: ' . $result['error'],
            ], 400);
        }

        // Save competitor
        $competitor = Competitor::create([
            'brand_id' => $brand->id,
            'name' => $validated['name'],
            'website' => $validated['website'],
            'positioning' => $result['data']['positioning'] ?? null,
            'messaging' => $result['data']['messaging'] ?? null,
            'pricing_signals' => $result['data']['pricing_signals'] ?? null,
            'channels' => $result['data']['channels'] ?? null,
            'seo_data' => $result['data']['seo_data'] ?? null,
            'content_strategy' => $result['data']['content_strategy'] ?? null,
            'strengths' => $result['data']['strengths'] ?? null,
            'weaknesses' => $result['data']['weaknesses'] ?? null,
            'analyzed_at' => now(),
        ]);

        // Fetch and store detailed keywords, backlinks, and social profiles
        $this->storeDetailedCompetitorData($competitor, $validated['website']);

        return response()->json([
            'success' => true,
            'message' => 'Competitor analyzed and saved',
            'competitor' => $competitor,
        ]);
    }

    /**
     * Store detailed competitor data (keywords, backlinks, social)
     */
    protected function storeDetailedCompetitorData($competitor, $website)
    {
        $domain = parse_url($website, PHP_URL_HOST) ?? $website;
        $domain = preg_replace('/^www\./', '', $domain);

        // Fetch and store organic keywords (top 20)
        $semrush = app(\App\Services\Marketing\APIs\SEMrushService::class);
        $organicKeywords = $semrush->getOrganicKeywords($domain, 'us', 20);
        
        if ($organicKeywords['success'] && isset($organicKeywords['keywords'])) {
            foreach ($organicKeywords['keywords'] as $kw) {
                \App\Models\CompetitorKeyword::create([
                    'competitor_id' => $competitor->id,
                    'type' => 'organic',
                    'keyword' => $kw['Ph'] ?? $kw['keyword'] ?? '',
                    'position' => $kw['Po'] ?? $kw['position'] ?? null,
                    'search_volume' => $kw['Nq'] ?? $kw['search_volume'] ?? null,
                    'cpc' => $kw['Cp'] ?? $kw['cpc'] ?? null,
                    'url' => $kw['Ur'] ?? $kw['url'] ?? null,
                    'traffic' => $kw['Tr'] ?? $kw['traffic'] ?? null,
                    'traffic_cost' => $kw['Tc'] ?? $kw['traffic_cost'] ?? null,
                ]);
            }
        }

        // Fetch and store paid keywords (top 10)
        $paidKeywords = $semrush->getPaidKeywords($domain, 'us', 10);
        
        if ($paidKeywords['success'] && isset($paidKeywords['keywords'])) {
            foreach ($paidKeywords['keywords'] as $kw) {
                \App\Models\CompetitorKeyword::create([
                    'competitor_id' => $competitor->id,
                    'type' => 'paid',
                    'keyword' => $kw['Ph'] ?? $kw['keyword'] ?? '',
                    'position' => $kw['Po'] ?? $kw['position'] ?? null,
                    'search_volume' => $kw['Nq'] ?? $kw['search_volume'] ?? null,
                    'cpc' => $kw['Cp'] ?? $kw['cpc'] ?? null,
                    'url' => $kw['Ur'] ?? $kw['url'] ?? null,
                    'traffic' => $kw['Tr'] ?? $kw['traffic'] ?? null,
                    'traffic_cost' => $kw['Tc'] ?? $kw['traffic_cost'] ?? null,
                ]);
            }
        }

        // Fetch and store backlinks (top 50)
        $ahrefs = app(\App\Services\Marketing\APIs\AhrefsService::class);
        $backlinks = $ahrefs->getBacklinks($domain, 50);
        
        if ($backlinks['success'] && isset($backlinks['backlinks'])) {
            foreach ($backlinks['backlinks'] as $bl) {
                \App\Models\CompetitorBacklink::create([
                    'competitor_id' => $competitor->id,
                    'source_url' => $bl['source_url'] ?? '',
                    'target_url' => $bl['target_url'] ?? '',
                    'anchor_text' => $bl['anchor_text'] ?? null,
                    'domain_rating' => $bl['domain_rating'] ?? null,
                    'url_rating' => $bl['url_rating'] ?? null,
                    'link_type' => $bl['link_type'] ?? null,
                    'first_seen' => $bl['first_seen'] ?? null,
                    'last_seen' => $bl['last_seen'] ?? null,
                ]);
            }
        }

        // Store mock social profiles (real scraping would require additional services)
        $this->storeMockSocialProfiles($competitor, $domain);
    }

    /**
     * Store mock social profiles
     */
    protected function storeMockSocialProfiles($competitor, $domain)
    {
        $platforms = ['facebook', 'instagram', 'twitter', 'linkedin'];
        
        foreach ($platforms as $platform) {
            \App\Models\CompetitorSocialProfile::create([
                'competitor_id' => $competitor->id,
                'platform' => $platform,
                'profile_url' => "https://{$platform}.com/{$domain}",
                'username' => str_replace('.', '', $domain),
                'followers' => rand(10000, 500000),
                'following' => rand(100, 5000),
                'posts_count' => rand(500, 5000),
                'engagement_rate' => rand(200, 800) / 100,
                'avg_likes' => rand(500, 10000),
                'avg_comments' => rand(50, 1000),
                'posting_frequency' => ['daily', 'weekly', '2-3 times/week'][array_rand(['daily', 'weekly', '2-3 times/week'])],
                'content_themes' => ['product launches', 'customer stories', 'industry news'],
                'last_scraped' => now(),
            ]);
        }
    }

    /**
     * Delete Competitor
     */
    public function deleteCompetitor($id)
    {
        $user = Auth::user();
        
        $competitor = Competitor::whereHas('brand', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->findOrFail($id);

        $competitor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Competitor deleted successfully',
        ]);
    }

    /**
     * Get competitor keywords
     */
    public function getCompetitorKeywords($id)
    {
        $user = Auth::user();
        
        $competitor = Competitor::whereHas('brand', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->findOrFail($id);

        $organic = $competitor->organicKeywords()->orderBy('position')->limit(100)->get();
        $paid = $competitor->paidKeywords()->orderBy('position')->limit(50)->get();

        return response()->json([
            'success' => true,
            'organic' => $organic,
            'paid' => $paid,
        ]);
    }

    /**
     * Get competitor backlinks
     */
    public function getCompetitorBacklinks($id)
    {
        $user = Auth::user();
        
        $competitor = Competitor::whereHas('brand', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->findOrFail($id);

        $backlinks = $competitor->backlinks()
            ->orderBy('domain_rating', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'backlinks' => $backlinks,
        ]);
    }

    /**
     * Get competitor social profiles
     */
    public function getCompetitorSocial($id)
    {
        $user = Auth::user();
        
        $competitor = Competitor::whereHas('brand', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->findOrFail($id);

        $profiles = $competitor->socialProfiles()->get();

        return response()->json([
            'success' => true,
            'profiles' => $profiles,
        ]);
    }

    /**
     * Export Strategy as PDF
     */
    public function exportStrategy($id)
    {
        $user = Auth::user();
        
        $strategy = StrategyPlan::whereHas('brand', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->findOrFail($id);

        // For Phase 1, return JSON. Later implement PDF generation
        return response()->json([
            'success' => true,
            'strategy' => $strategy,
        ]);
    }
}
