<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MarketResearch\FeedbackService;
use App\Services\MarketResearch\LearningService;
use App\Models\ResearchRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    private FeedbackService $feedbackService;
    private LearningService $learningService;

    public function __construct(
        FeedbackService $feedbackService,
        LearningService $learningService
    ) {
        $this->feedbackService = $feedbackService;
        $this->learningService = $learningService;
    }

    /**
     * Submit feedback for a competitor
     * POST /api/feedback/competitor
     */
    public function submitCompetitorFeedback(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'competitor_id' => 'required|exists:competitors,id',
            'research_request_id' => 'required|exists:research_requests,id',
            'feedback_type' => 'required|in:relevance,data_quality,accuracy,completeness,duplicate,spam',
            'is_useful' => 'nullable|boolean',
            'is_relevant' => 'nullable|boolean',
            'is_accurate' => 'nullable|boolean',
            'is_duplicate' => 'nullable|boolean',
            'is_spam' => 'nullable|boolean',
            'overall_rating' => 'nullable|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
            'field_corrections' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $feedback = $this->feedbackService->submitCompetitorFeedback($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully',
                'feedback_id' => $feedback->id,
                'data' => $feedback
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit feedback',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch submit feedback for multiple competitors
     * POST /api/feedback/batch
     */
    public function batchSubmitFeedback(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'research_request_id' => 'required|exists:research_requests,id',
            'feedback_items' => 'required|array|min:1',
            'feedback_items.*.competitor_id' => 'required|exists:competitors,id',
            'feedback_items.*.feedback_type' => 'required|in:relevance,data_quality,accuracy,completeness,duplicate,spam',
            'feedback_items.*.is_useful' => 'nullable|boolean',
            'feedback_items.*.is_relevant' => 'nullable|boolean',
            'feedback_items.*.is_accurate' => 'nullable|boolean',
            'feedback_items.*.overall_rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $results = $this->feedbackService->batchSubmitFeedback(
                $request->research_request_id,
                $request->feedback_items
            );

            return response()->json([
                'success' => true,
                'message' => 'Batch feedback submitted',
                'results' => $results
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit batch feedback',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get feedback statistics for a competitor
     * GET /api/feedback/competitor/{competitorId}
     */
    public function getCompetitorFeedback(int $competitorId): JsonResponse
    {
        try {
            $stats = $this->feedbackService->getCompetitorFeedbackStats($competitorId);

            return response()->json([
                'success' => true,
                'competitor_id' => $competitorId,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve feedback',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get feedback summary for a research request
     * GET /api/feedback/research-request/{researchRequestId}
     */
    public function getResearchRequestFeedback(int $researchRequestId): JsonResponse
    {
        try {
            $summary = $this->feedbackService->getResearchRequestFeedbackSummary($researchRequestId);

            return response()->json([
                'success' => true,
                'research_request_id' => $researchRequestId,
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve feedback summary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export feedback data
     * GET /api/feedback/export/{researchRequestId}
     */
    public function exportFeedback(int $researchRequestId): JsonResponse
    {
        try {
            $data = $this->feedbackService->exportFeedbackData($researchRequestId);

            return response()->json([
                'success' => true,
                'research_request_id' => $researchRequestId,
                'feedback_data' => $data,
                'exported_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to export feedback',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance dashboard
     * GET /api/feedback/performance
     */
    public function getPerformanceDashboard(): JsonResponse
    {
        try {
            $dashboard = $this->learningService->getPerformanceDashboard();

            return response()->json([
                'success' => true,
                'dashboard' => $dashboard
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve performance dashboard',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trigger learning process
     * POST /api/feedback/learn
     */
    public function triggerLearning(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);

        try {
            $results = $this->learningService->learnFromFeedback($days);

            return response()->json([
                'success' => true,
                'message' => 'Learning process completed',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Learning process failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Train validation model
     * POST /api/feedback/train
     */
    public function trainModel(): JsonResponse
    {
        try {
            $results = $this->learningService->trainValidationModel();

            return response()->json([
                'success' => true,
                'message' => 'Validation model trained successfully',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Model training failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current adjusted thresholds
     * GET /api/feedback/thresholds
     */
    public function getThresholds(): JsonResponse
    {
        try {
            $thresholds = $this->learningService->getAdjustedThresholds();

            return response()->json([
                'success' => true,
                'thresholds' => $thresholds,
                'note' => 'These thresholds are automatically adjusted based on user feedback'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve thresholds',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
