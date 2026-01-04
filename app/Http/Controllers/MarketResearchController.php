<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMarketResearch;
use App\Models\ResearchRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketResearchController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the market research form
     */
    public function index()
    {
        return view('market-research.index');
    }

    /**
     * Submit a new research request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_idea' => 'required|string|min:10|max:500',
            'location' => 'required|string|min:2|max:255',
        ]);

        // Create research request
        $researchRequest = ResearchRequest::create([
            'user_id' => Auth::id(),
            'business_idea' => $validated['business_idea'],
            'location' => $validated['location'],
            'status' => 'pending',
        ]);

        // Dispatch job to process research
        ProcessMarketResearch::dispatch($researchRequest);

        return redirect()->route('market-research.show', $researchRequest->id)
            ->with('success', 'Market research started! This will take 10-15 minutes.');
    }

    /**
     * Show research progress/results
     */
    public function show($id)
    {
        $request = ResearchRequest::with([
            'competitors.reviews',
            'competitors.pricing',
            'forumDiscussions',
            'marketData',
            'customerInsights',
            'report'
        ])->findOrFail($id);

        // Check authorization if user is logged in
        if (Auth::check() && $request->user_id && $request->user_id !== Auth::id()) {
            abort(403);
        }

        // Calculate initial progress for processing state
        $initialProgress = [
            'competitors' => $request->competitors->count(),
            'reviews' => $request->competitors->sum(function($competitor) {
                return $competitor->reviews->count();
            }),
            'pricing_tiers' => $request->competitors->sum(function($competitor) {
                return $competitor->pricing->count();
            }),
            'forum_discussions' => $request->forumDiscussions->count(),
            'has_market_data' => $request->marketData !== null,
            'has_insights' => $request->customerInsights !== null,
            'has_report' => $request->report !== null,
        ];

        return view('market-research.show', compact('request', 'initialProgress'));
    }

    /**
     * Get research status (for AJAX polling)
     */
    public function status($id)
    {
        $request = ResearchRequest::with([
            'competitors',
            'forumDiscussions',
            'marketData',
            'customerInsights',
            'report'
        ])->findOrFail($id);

        $competitorsCount = $request->competitors->count();
        $reviewsCount = $request->competitors->sum(function($competitor) {
            return $competitor->reviews->count();
        });
        $pricingCount = $request->competitors->sum(function($competitor) {
            return $competitor->pricing->count();
        });
        $forumsCount = $request->forumDiscussions->count();
        
        return response()->json([
            'status' => $request->status,
            'started_at' => $request->started_at,
            'completed_at' => $request->completed_at,
            'error_message' => $request->error_message,
            'progress' => [
                'competitors' => $competitorsCount,
                'reviews' => $reviewsCount,
                'pricing_tiers' => $pricingCount,
                'forum_discussions' => $forumsCount,
                'has_market_data' => $request->marketData !== null,
                'has_insights' => $request->customerInsights !== null,
                'has_report' => $request->report !== null,
            ]
        ]);
    }

    /**
     * Retry a failed research request
     */
    public function retry($id)
    {
        $request = ResearchRequest::findOrFail($id);

        // Check authorization
        if (Auth::check() && $request->user_id && $request->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow retry for failed requests
        if ($request->status !== 'failed') {
            return redirect()->route('market-research.show', $id)
                ->with('error', 'This research is not in failed state.');
        }

        // Reset status and clear error
        $request->update([
            'status' => 'pending',
            'error_message' => null,
            'started_at' => null,
        ]);

        // Dispatch job again
        ProcessMarketResearch::dispatch($request);

        return redirect()->route('market-research.show', $id)
            ->with('success', 'Research restarted! This will continue from where it left off.');
    }

    /**
     * Download report as PDF
     */
    public function downloadPdf($id)
    {
        $researchRequest = ResearchRequest::with('report')->findOrFail($id);

        // Check authorization
        if (Auth::check() && $researchRequest->user_id && $researchRequest->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$researchRequest->report) {
            abort(404, 'Report not found');
        }

        $pdf = \PDF::loadView('market-research.pdf', [
            'request' => $researchRequest,
            'report' => $researchRequest->report,
        ]);

        $filename = 'market-research-' . $researchRequest->id . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show user's research history
     */
    public function history()
    {
        $requests = ResearchRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('market-research.history', compact('requests'));
    }
}
