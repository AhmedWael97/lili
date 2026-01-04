<?php

namespace App\Http\Controllers;

use App\Models\ResearchRequest;
use App\Models\Competitor;
use App\Services\MarketResearch\FeedbackService;
use App\Services\MarketResearch\LearningService;
use Illuminate\Http\Request;

class FeedbackWebController extends Controller
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
     * Show verification page for a research request
     */
    public function showVerification(int $researchRequestId)
    {
        $researchRequest = ResearchRequest::with(['competitors.feedbacks'])->findOrFail($researchRequestId);
        $competitors = $researchRequest->competitors;

        $verifiedCount = $competitors->filter(function ($competitor) {
            return $competitor->feedbacks->isNotEmpty();
        })->count();

        return view('market-research.verify', [
            'researchRequestId' => $researchRequestId,
            'researchRequest' => $researchRequest,
            'competitors' => $competitors,
            'verifiedCount' => $verifiedCount,
            'totalCount' => $competitors->count(),
        ]);
    }

    /**
     * Submit feedback for a competitor
     */
    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'competitor_id' => 'required|exists:competitors,id',
            'research_request_id' => 'required|exists:research_requests,id',
            'feedback_type' => 'required|in:relevance,data_quality,accuracy,completeness,duplicate,spam',
            'is_relevant' => 'nullable|boolean',
            'is_useful' => 'nullable|boolean',
            'is_accurate' => 'nullable|boolean',
            'is_duplicate' => 'nullable|boolean',
            'is_spam' => 'nullable|boolean',
            'overall_rating' => 'nullable|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            $this->feedbackService->submitCompetitorFeedback($validated);

            // Check if all competitors have been verified
            $researchRequest = ResearchRequest::with('competitors.feedbacks')->findOrFail($validated['research_request_id']);
            $totalCompetitors = $researchRequest->competitors->count();
            $verifiedCompetitors = $researchRequest->competitors->filter(function ($competitor) {
                return $competitor->feedbacks->isNotEmpty();
            })->count();

            // If all competitors are verified and research is pending verification, mark as completed
            if ($verifiedCompetitors >= $totalCompetitors && $researchRequest->needsVerification()) {
                $researchRequest->markAsCompleted();
                
                return redirect()
                    ->route('market-research.report', ['id' => $researchRequest->id])
                    ->with('success', 'All data verified! Your report is ready.');
            }

            return redirect()
                ->route('market-research.verify', $validated['research_request_id'])
                ->with('success', 'Thank you! Your feedback has been recorded.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to submit feedback. Please try again.');
        }
    }

    /**
     * Show performance dashboard
     */
    public function showPerformance()
    {
        $dashboard = $this->learningService->getPerformanceDashboard();

        return view('market-research.performance', [
            'dashboard' => $dashboard
        ]);
    }

    /**
     * Trigger algorithm training
     */
    public function trainAlgorithm(Request $request)
    {
        try {
            $results = $this->learningService->trainValidationModel();

            return redirect()
                ->route('feedback.performance')
                ->with('success', 'Algorithm re-trained successfully! New thresholds and patterns have been learned.');
        } catch (\Exception $e) {
            return redirect()
                ->route('feedback.performance')
                ->with('error', 'Training failed. Please try again later.');
        }
    }
}
