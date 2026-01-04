<?php

namespace App\Services\MarketResearch;

use App\Models\CompetitorFeedback;
use App\Models\ValidationFeedback;
use App\Models\Competitor;
use App\Models\ResearchRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Feedback Service
 * Collects and processes user feedback on data quality
 */
class FeedbackService
{
    /**
     * Submit feedback for a competitor
     */
    public function submitCompetitorFeedback(array $data): CompetitorFeedback
    {
        Log::info('Competitor feedback submitted', [
            'competitor_id' => $data['competitor_id'],
            'feedback_type' => $data['feedback_type'] ?? 'general'
        ]);

        $feedback = CompetitorFeedback::create([
            'competitor_id' => $data['competitor_id'],
            'research_request_id' => $data['research_request_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'feedback_type' => $data['feedback_type'] ?? 'relevance',
            'is_useful' => $data['is_useful'] ?? null,
            'is_relevant' => $data['is_relevant'] ?? null,
            'is_accurate' => $data['is_accurate'] ?? null,
            'is_duplicate' => $data['is_duplicate'] ?? false,
            'is_spam' => $data['is_spam'] ?? false,
            'field_corrections' => $data['field_corrections'] ?? null,
            'overall_rating' => $data['overall_rating'] ?? null,
            'comments' => $data['comments'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'verified_at' => now(),
        ]);

        // Update competitor's trust score based on feedback
        $this->updateCompetitorTrustScore($feedback->competitor_id);

        return $feedback;
    }

    /**
     * Submit validation feedback
     */
    public function submitValidationFeedback(array $data): ValidationFeedback
    {
        Log::info('Validation feedback submitted', [
            'validation_type' => $data['validation_type'],
            'system_prediction' => $data['system_prediction'] ?? null,
            'user_verdict' => $data['user_verdict'] ?? null
        ]);

        return ValidationFeedback::create([
            'research_request_id' => $data['research_request_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'validation_type' => $data['validation_type'],
            'item_identifier' => $data['item_identifier'],
            'system_score' => $data['system_score'] ?? null,
            'system_prediction' => $data['system_prediction'] ?? null,
            'user_verdict' => $data['user_verdict'] ?? null,
            'features' => $data['features'] ?? null,
            'correction_data' => $data['correction_data'] ?? null,
            'validated_at' => now(),
        ]);
    }

    /**
     * Batch submit feedback for multiple competitors
     */
    public function batchSubmitFeedback(int $researchRequestId, array $feedbackItems): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'feedback_ids' => [],
        ];

        DB::beginTransaction();

        try {
            foreach ($feedbackItems as $item) {
                $item['research_request_id'] = $researchRequestId;
                
                $feedback = $this->submitCompetitorFeedback($item);
                $results['feedback_ids'][] = $feedback->id;
                $results['success']++;
            }

            DB::commit();

            Log::info('Batch feedback submitted', [
                'research_request_id' => $researchRequestId,
                'total_items' => count($feedbackItems),
                'success' => $results['success']
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Batch feedback failed', [
                'error' => $e->getMessage()
            ]);
            
            $results['failed'] = count($feedbackItems);
        }

        return $results;
    }

    /**
     * Get feedback statistics for a competitor
     */
    public function getCompetitorFeedbackStats(int $competitorId): array
    {
        $feedback = CompetitorFeedback::where('competitor_id', $competitorId)->get();

        if ($feedback->isEmpty()) {
            return [
                'total_feedback' => 0,
                'positive_count' => 0,
                'negative_count' => 0,
                'average_rating' => 0,
                'trust_score' => 50, // Default neutral score
            ];
        }

        $positive = $feedback->filter(function ($f) {
            return $f->is_useful && $f->is_relevant && !$f->is_spam;
        })->count();

        $negative = $feedback->filter(function ($f) {
            return !$f->is_useful || !$f->is_relevant || $f->is_spam;
        })->count();

        $ratings = $feedback->whereNotNull('overall_rating')->pluck('overall_rating');
        $averageRating = $ratings->isNotEmpty() ? $ratings->average() : 0;

        return [
            'total_feedback' => $feedback->count(),
            'positive_count' => $positive,
            'negative_count' => $negative,
            'average_rating' => round($averageRating, 2),
            'trust_score' => $this->calculateTrustScore($positive, $negative, $feedback->count()),
            'spam_reports' => $feedback->where('is_spam', true)->count(),
            'duplicate_reports' => $feedback->where('is_duplicate', true)->count(),
        ];
    }

    /**
     * Get feedback summary for a research request
     */
    public function getResearchRequestFeedbackSummary(int $researchRequestId): array
    {
        $feedback = CompetitorFeedback::where('research_request_id', $researchRequestId)->get();
        $validationFeedback = ValidationFeedback::where('research_request_id', $researchRequestId)->get();

        $competitorIds = $feedback->pluck('competitor_id')->unique();
        
        $competitorsFeedback = [];
        foreach ($competitorIds as $competitorId) {
            $competitorsFeedback[$competitorId] = $this->getCompetitorFeedbackStats($competitorId);
        }

        return [
            'total_competitors_reviewed' => $competitorIds->count(),
            'total_feedback_items' => $feedback->count(),
            'positive_feedback' => $feedback->where('is_useful', true)->count(),
            'negative_feedback' => $feedback->where('is_useful', false)->count(),
            'validation_accuracy' => $this->calculateValidationAccuracy($validationFeedback),
            'competitors_feedback' => $competitorsFeedback,
            'needs_improvement' => $this->identifyImprovementAreas($feedback, $validationFeedback),
        ];
    }

    /**
     * Calculate trust score based on feedback
     */
    private function calculateTrustScore(int $positive, int $negative, int $total): int
    {
        if ($total === 0) {
            return 50; // Neutral
        }

        // Calculate percentage of positive feedback
        $positiveRate = ($positive / $total) * 100;

        // Adjust by confidence (more feedback = more confident)
        $confidence = min(1, $total / 10); // 10+ feedback = full confidence
        
        // Score from 0-100
        $score = 50 + (($positiveRate - 50) * $confidence);

        return (int) round(max(0, min(100, $score)));
    }

    /**
     * Update competitor's trust score
     */
    private function updateCompetitorTrustScore(int $competitorId): void
    {
        $stats = $this->getCompetitorFeedbackStats($competitorId);
        
        // Store trust score in competitor metadata or a new column
        $competitor = Competitor::find($competitorId);
        
        if ($competitor) {
            // You might want to add a trust_score column to competitors table
            // For now, we'll calculate it on-demand
            Log::info('Trust score updated', [
                'competitor_id' => $competitorId,
                'trust_score' => $stats['trust_score']
            ]);
        }
    }

    /**
     * Calculate validation accuracy
     */
    private function calculateValidationAccuracy($validationFeedback): float
    {
        if ($validationFeedback->isEmpty()) {
            return 0;
        }

        $correct = $validationFeedback->filter(function ($f) {
            return $f->wasCorrect();
        })->count();

        return round(($correct / $validationFeedback->count()) * 100, 2);
    }

    /**
     * Identify areas that need improvement
     */
    private function identifyImprovementAreas($feedback, $validationFeedback): array
    {
        $issues = [];

        // Check for high spam/duplicate rates
        $spamRate = $feedback->where('is_spam', true)->count() / max(1, $feedback->count());
        if ($spamRate > 0.1) {
            $issues[] = [
                'area' => 'spam_detection',
                'severity' => 'high',
                'message' => 'High spam detection rate - improve spam filtering'
            ];
        }

        $duplicateRate = $feedback->where('is_duplicate', true)->count() / max(1, $feedback->count());
        if ($duplicateRate > 0.1) {
            $issues[] = [
                'area' => 'duplicate_detection',
                'severity' => 'high',
                'message' => 'High duplicate rate - improve deduplication'
            ];
        }

        // Check for low relevance
        $relevantCount = $feedback->where('is_relevant', true)->count();
        $relevanceRate = $relevantCount / max(1, $feedback->count());
        if ($relevanceRate < 0.7) {
            $issues[] = [
                'area' => 'relevance_validation',
                'severity' => 'medium',
                'message' => 'Low relevance rate - improve competitor matching'
            ];
        }

        // Check validation accuracy
        $accuracy = $this->calculateValidationAccuracy($validationFeedback);
        if ($accuracy < 70) {
            $issues[] = [
                'area' => 'validation_accuracy',
                'severity' => 'high',
                'message' => "Validation accuracy is ${accuracy}% - needs improvement"
            ];
        }

        return $issues;
    }

    /**
     * Get common field corrections
     */
    public function getCommonFieldCorrections(string $fieldName, int $limit = 10): array
    {
        $corrections = CompetitorFeedback::whereNotNull('field_corrections')
            ->get()
            ->pluck('field_corrections')
            ->filter(function ($corrections) use ($fieldName) {
                return isset($corrections[$fieldName]);
            })
            ->map(function ($corrections) use ($fieldName) {
                return $corrections[$fieldName];
            })
            ->groupBy(function ($value) {
                return $value;
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take($limit);

        return $corrections->toArray();
    }

    /**
     * Export feedback data for analysis
     */
    public function exportFeedbackData(int $researchRequestId): array
    {
        $feedback = CompetitorFeedback::with('competitor')
            ->where('research_request_id', $researchRequestId)
            ->get();

        return $feedback->map(function ($f) {
            return [
                'competitor_id' => $f->competitor_id,
                'competitor_name' => $f->competitor->business_name ?? 'Unknown',
                'feedback_type' => $f->feedback_type,
                'is_useful' => $f->is_useful,
                'is_relevant' => $f->is_relevant,
                'is_accurate' => $f->is_accurate,
                'is_spam' => $f->is_spam,
                'is_duplicate' => $f->is_duplicate,
                'rating' => $f->overall_rating,
                'comments' => $f->comments,
                'verified_at' => $f->verified_at->toIso8601String(),
            ];
        })->toArray();
    }
}
