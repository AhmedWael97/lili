<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResearchRequest;
use App\Jobs\ProcessMarketResearch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MarketResearchController extends Controller
{
    /**
     * Submit a new market research request
     */
    public function submitResearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'business_idea' => 'required|string|min:10|max:500',
            'location' => 'required|string|min:3|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create research request
        $researchRequest = ResearchRequest::create([
            'user_id' => $request->user()?->id,
            'business_idea' => $request->business_idea,
            'location' => $request->location,
            'status' => 'pending',
        ]);

        // Dispatch job to process research asynchronously
        ProcessMarketResearch::dispatch($researchRequest);

        return response()->json([
            'success' => true,
            'message' => 'Research request submitted successfully',
            'data' => [
                'request_id' => $researchRequest->id,
                'status' => $researchRequest->status,
                'estimated_time' => '2-3 minutes',
            ]
        ], 201);
    }

    /**
     * Get the status of a research request
     */
    public function getStatus(int $requestId): JsonResponse
    {
        $researchRequest = ResearchRequest::find($requestId);

        if (!$researchRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Research request not found',
            ], 404);
        }

        $response = [
            'success' => true,
            'data' => [
                'request_id' => $researchRequest->id,
                'status' => $researchRequest->status,
                'business_idea' => $researchRequest->business_idea,
                'location' => $researchRequest->location,
                'created_at' => $researchRequest->created_at->toIso8601String(),
                'completed_at' => $researchRequest->completed_at?->toIso8601String(),
            ]
        ];

        // If completed, include competitor count
        if ($researchRequest->status === 'completed') {
            $response['data']['competitors_found'] = $researchRequest->competitors()->count();
            $response['data']['report_available'] = $researchRequest->report()->exists();
        }

        return response()->json($response);
    }

    /**
     * Get the full report for a completed research request
     */
    public function getReport(int $requestId): JsonResponse
    {
        $researchRequest = ResearchRequest::with([
            'competitors.socialMetrics',
            'competitors.socialIntelligence',
            'marketAnalysis',
            'report'
        ])->find($requestId);

        if (!$researchRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Research request not found',
            ], 404);
        }

        if ($researchRequest->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Research is still in progress',
                'data' => [
                    'status' => $researchRequest->status,
                ]
            ], 400);
        }

        if (!$researchRequest->report) {
            return response()->json([
                'success' => false,
                'message' => 'Report not available',
            ], 404);
        }

        

        return response()->json([
            'success' => true,
            'data' => [
                'request_id' => $researchRequest->id,
                'business_idea' => $researchRequest->business_idea,
                'location' => $researchRequest->location,
                'completed_at' => $researchRequest->completed_at->toIso8601String(),
                
                // Executive summary
                'executive_summary' => $researchRequest->report->executive_summary,
                
                // Market analysis
                'market_analysis' => $researchRequest->marketAnalysis,
                
                // Competitors
                'competitors' => $researchRequest->competitors->map(function ($competitor) {
                    // Get social metrics collection (multiple rows per competitor, one per platform)
                    $metricsCollection = $competitor->socialMetrics;
                    
                    // Build metrics object from collection
                    $metrics = [];
                    if ($metricsCollection->isNotEmpty()) {
                        foreach ($metricsCollection as $metric) {
                            $platform = $metric->platform;
                            $metrics[$platform . '_followers'] = $metric->followers ?? 0;
                            $metrics[$platform . '_engagement'] = $metric->avg_engagement_rate ?? 0;
                            $metrics[$platform . '_posts'] = $metric->posts_count ?? 0;
                        }
                    }
                    
                    return [
                        'id' => $competitor->id,
                        'business_name' => $competitor->business_name,
                        'website' => $competitor->website,
                        'social_media' => [
                            'facebook' => $competitor->facebook_handle,
                            'instagram' => $competitor->instagram_handle,
                            'twitter' => $competitor->twitter_handle,
                            'linkedin' => $competitor->linkedin_url,
                        ],
                        'metrics' => $metrics,
                        'intelligence' => $competitor->socialIntelligence,
                        'relevance_score' => $competitor->relevance_score,
                    ];
                })->sortByDesc('relevance_score')->values(),
                
                // Recommendations
                'recommendations' => $researchRequest->report->recommendations,
                
                // Action plan
                'action_plan' => $researchRequest->report->action_plan,
                
                // PDF download
                'pdf_url' => $researchRequest->report->pdf_url,
            ]
        ]);
    }

    /**
     * List all research requests for the authenticated user
     */
    public function listRequests(Request $request): JsonResponse
    {
        $query = ResearchRequest::query();

        // Filter by user if authenticated
        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        }

        $requests = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $requests->map(function ($req) {
                return [
                    'id' => $req->id,
                    'business_idea' => $req->business_idea,
                    'location' => $req->location,
                    'status' => $req->status,
                    'created_at' => $req->created_at->toIso8601String(),
                    'completed_at' => $req->completed_at?->toIso8601String(),
                ];
            }),
            'pagination' => [
                'total' => $requests->total(),
                'per_page' => $requests->perPage(),
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
            ]
        ]);
    }
}
