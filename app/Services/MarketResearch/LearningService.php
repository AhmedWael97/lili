<?php

namespace App\Services\MarketResearch;

use App\Models\CompetitorFeedback;
use App\Models\ValidationFeedback;
use App\Models\LearningMetric;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Learning Service
 * Continuously improves validation algorithms based on user feedback
 */
class LearningService
{
    /**
     * Learn from recent feedback and adjust scoring weights
     */
    public function learnFromFeedback(int $days = 30): array
    {
        Log::info('Starting learning process', ['days' => $days]);

        $results = [
            'metrics_calculated' => [],
            'adjustments_made' => [],
            'improvement_rate' => 0,
        ];

        // Calculate metrics for each component
        $components = [
            'search_verification',
            'relevance_validation',
            'quality_scoring',
            'spam_detection',
            'duplicate_detection',
        ];

        foreach ($components as $component) {
            $metrics = $this->calculateComponentMetrics($component, $days);
            
            if ($metrics) {
                $results['metrics_calculated'][] = $component;
                $this->saveMetrics($component, $metrics);
                
                // Identify if adjustments are needed
                if ($metrics['accuracy'] < 0.8) {
                    $adjustment = $this->suggestAdjustment($component, $metrics);
                    $results['adjustments_made'][] = $adjustment;
                }
            }
        }

        // Calculate overall improvement
        $results['improvement_rate'] = $this->calculateImprovementRate($days);

        Log::info('Learning process completed', $results);

        return $results;
    }

    /**
     * Calculate metrics for a specific component
     */
    private function calculateComponentMetrics(string $component, int $days): ?array
    {
        $startDate = Carbon::now()->subDays($days);
        
        $validationFeedback = ValidationFeedback::where('validation_type', $component)
            ->where('validated_at', '>=', $startDate)
            ->whereNotNull('system_prediction')
            ->whereNotNull('user_verdict')
            ->get();

        if ($validationFeedback->isEmpty()) {
            return null;
        }

        $truePositives = 0;
        $trueNegatives = 0;
        $falsePositives = 0;
        $falseNegatives = 0;

        foreach ($validationFeedback as $feedback) {
            if ($feedback->system_prediction && $feedback->user_verdict) {
                $truePositives++;
            } elseif (!$feedback->system_prediction && !$feedback->user_verdict) {
                $trueNegatives++;
            } elseif ($feedback->system_prediction && !$feedback->user_verdict) {
                $falsePositives++;
            } elseif (!$feedback->system_prediction && $feedback->user_verdict) {
                $falseNegatives++;
            }
        }

        $total = $truePositives + $trueNegatives + $falsePositives + $falseNegatives;
        
        $accuracy = $total > 0 ? ($truePositives + $trueNegatives) / $total : 0;
        $precision = ($truePositives + $falsePositives) > 0 ? $truePositives / ($truePositives + $falsePositives) : 0;
        $recall = ($truePositives + $falseNegatives) > 0 ? $truePositives / ($truePositives + $falseNegatives) : 0;
        $f1Score = ($precision + $recall) > 0 ? 2 * ($precision * $recall) / ($precision + $recall) : 0;

        return [
            'accuracy' => $accuracy,
            'precision' => $precision,
            'recall' => $recall,
            'f1_score' => $f1Score,
            'true_positives' => $truePositives,
            'true_negatives' => $trueNegatives,
            'false_positives' => $falsePositives,
            'false_negatives' => $falseNegatives,
            'total_samples' => $total,
        ];
    }

    /**
     * Save metrics to database
     */
    private function saveMetrics(string $component, array $metrics): void
    {
        $periodEnd = Carbon::now()->toDateString();
        $periodStart = Carbon::now()->subDays(30)->toDateString();

        foreach (['accuracy', 'precision', 'recall', 'f1_score'] as $metricType) {
            LearningMetric::create([
                'metric_type' => $metricType,
                'component' => $component,
                'score' => $metrics[$metricType],
                'true_positives' => $metrics['true_positives'],
                'true_negatives' => $metrics['true_negatives'],
                'false_positives' => $metrics['false_positives'],
                'false_negatives' => $metrics['false_negatives'],
                'total_samples' => $metrics['total_samples'],
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ]);
        }
    }

    /**
     * Suggest adjustments based on metrics
     */
    private function suggestAdjustment(string $component, array $metrics): array
    {
        $adjustment = [
            'component' => $component,
            'current_accuracy' => round($metrics['accuracy'], 3),
            'recommendations' => [],
        ];

        // High false positives - system too lenient
        if ($metrics['false_positives'] > $metrics['false_negatives'] * 1.5) {
            $adjustment['recommendations'][] = [
                'issue' => 'Too many false positives',
                'action' => 'Increase minimum quality threshold',
                'suggested_change' => 'Increase threshold by 5-10 points',
            ];
        }

        // High false negatives - system too strict
        if ($metrics['false_negatives'] > $metrics['false_positives'] * 1.5) {
            $adjustment['recommendations'][] = [
                'issue' => 'Too many false negatives',
                'action' => 'Decrease minimum quality threshold',
                'suggested_change' => 'Decrease threshold by 5-10 points',
            ];
        }

        // Low precision - need better filtering
        if ($metrics['precision'] < 0.7) {
            $adjustment['recommendations'][] = [
                'issue' => 'Low precision',
                'action' => 'Improve filtering criteria',
                'suggested_change' => 'Add more validation checks',
            ];
        }

        // Low recall - missing good results
        if ($metrics['recall'] < 0.7) {
            $adjustment['recommendations'][] = [
                'issue' => 'Low recall',
                'action' => 'Broaden acceptance criteria',
                'suggested_change' => 'Reduce strictness of filters',
            ];
        }

        return $adjustment;
    }

    /**
     * Calculate improvement rate over time
     */
    private function calculateImprovementRate(int $days): float
    {
        $previousPeriod = LearningMetric::where('period_end', '<', Carbon::now()->subDays($days)->toDateString())
            ->where('metric_type', 'accuracy')
            ->orderBy('period_end', 'desc')
            ->limit(10)
            ->avg('score');

        $currentPeriod = LearningMetric::where('period_end', '>=', Carbon::now()->subDays($days)->toDateString())
            ->where('metric_type', 'accuracy')
            ->avg('score');

        if (!$previousPeriod || !$currentPeriod) {
            return 0;
        }

        $improvement = (($currentPeriod - $previousPeriod) / $previousPeriod) * 100;
        
        return round($improvement, 2);
    }

    /**
     * Get adjusted quality thresholds based on feedback
     */
    public function getAdjustedThresholds(): array
    {
        $cacheKey = 'adjusted_thresholds';
        
        return Cache::remember($cacheKey, 3600, function () {
            $thresholds = [
                'search_quality_min' => 60,
                'competitor_quality_min' => 50,
                'relevance_score_min' => 60,
                'spam_confidence_min' => 0.7,
            ];

            // Adjust based on recent feedback
            $recentFeedback = CompetitorFeedback::where('verified_at', '>=', Carbon::now()->subDays(30))->get();

            if ($recentFeedback->isEmpty()) {
                return $thresholds;
            }

            // If many false positives (system approved spam), increase threshold
            $spamCount = $recentFeedback->where('is_spam', true)->count();
            $spamRate = $spamCount / $recentFeedback->count();

            if ($spamRate > 0.1) {
                $thresholds['search_quality_min'] += 10;
                $thresholds['spam_confidence_min'] += 0.1;
            }

            // If many irrelevant results, increase relevance threshold
            $irrelevantCount = $recentFeedback->where('is_relevant', false)->count();
            $irrelevantRate = $irrelevantCount / $recentFeedback->count();

            if ($irrelevantRate > 0.2) {
                $thresholds['relevance_score_min'] += 10;
            }

            // If mostly positive feedback, can slightly decrease thresholds
            $positiveCount = $recentFeedback->where('is_useful', true)->where('is_relevant', true)->count();
            $positiveRate = $positiveCount / $recentFeedback->count();

            if ($positiveRate > 0.9 && $recentFeedback->count() > 20) {
                $thresholds['search_quality_min'] = max(50, $thresholds['search_quality_min'] - 5);
                $thresholds['competitor_quality_min'] = max(40, $thresholds['competitor_quality_min'] - 5);
            }

            Log::info('Adjusted thresholds calculated', $thresholds);

            return $thresholds;
        });
    }

    /**
     * Get learned spam patterns from feedback
     */
    public function getLearnedSpamPatterns(): array
    {
        $cacheKey = 'learned_spam_patterns';
        
        return Cache::remember($cacheKey, 3600, function () {
            $spamFeedback = CompetitorFeedback::where('is_spam', true)
                ->whereNotNull('metadata')
                ->limit(100)
                ->get();

            $patterns = [
                'keywords' => [],
                'url_patterns' => [],
                'title_patterns' => [],
            ];

            foreach ($spamFeedback as $feedback) {
                $metadata = $feedback->metadata;
                
                if (isset($metadata['title'])) {
                    // Extract common words from spam titles
                    $words = str_word_count(strtolower($metadata['title']), 1);
                    foreach ($words as $word) {
                        if (strlen($word) > 3) {
                            $patterns['keywords'][$word] = ($patterns['keywords'][$word] ?? 0) + 1;
                        }
                    }
                }
            }

            // Keep only patterns that appear frequently
            $patterns['keywords'] = array_filter($patterns['keywords'], function ($count) {
                return $count >= 3;
            });

            return $patterns;
        });
    }

    /**
     * Get performance dashboard data
     */
    public function getPerformanceDashboard(): array
    {
        $last30Days = LearningMetric::where('period_end', '>=', Carbon::now()->subDays(30)->toDateString())
            ->orderBy('period_end', 'desc')
            ->get();

        $componentPerformance = [];
        
        foreach ($last30Days->groupBy('component') as $component => $metrics) {
            $latestMetric = $metrics->first();
            
            $componentPerformance[$component] = [
                'accuracy' => round($metrics->where('metric_type', 'accuracy')->avg('score'), 3),
                'precision' => round($metrics->where('metric_type', 'precision')->avg('score'), 3),
                'recall' => round($metrics->where('metric_type', 'recall')->avg('score'), 3),
                'f1_score' => round($metrics->where('metric_type', 'f1_score')->avg('score'), 3),
                'total_samples' => $latestMetric->total_samples ?? 0,
                'status' => $this->getComponentStatus($metrics->where('metric_type', 'accuracy')->avg('score')),
            ];
        }

        return [
            'overall_improvement' => $this->calculateImprovementRate(30),
            'component_performance' => $componentPerformance,
            'total_feedback_items' => CompetitorFeedback::count() + ValidationFeedback::count(),
            'feedback_last_30_days' => CompetitorFeedback::where('verified_at', '>=', Carbon::now()->subDays(30))->count(),
            'current_thresholds' => $this->getAdjustedThresholds(),
        ];
    }

    /**
     * Get component status based on accuracy
     */
    private function getComponentStatus(float $accuracy): string
    {
        if ($accuracy >= 0.9) return 'excellent';
        if ($accuracy >= 0.8) return 'good';
        if ($accuracy >= 0.7) return 'fair';
        return 'needs_improvement';
    }

    /**
     * Train validation model with recent feedback
     */
    public function trainValidationModel(): array
    {
        Log::info('Training validation model with feedback data');

        $results = $this->learnFromFeedback(30);
        
        // Update cached thresholds
        Cache::forget('adjusted_thresholds');
        Cache::forget('learned_spam_patterns');
        
        $newThresholds = $this->getAdjustedThresholds();
        $newPatterns = $this->getLearnedSpamPatterns();

        Log::info('Validation model trained', [
            'new_thresholds' => $newThresholds,
            'spam_patterns_count' => count($newPatterns['keywords'])
        ]);

        return [
            'status' => 'success',
            'learning_results' => $results,
            'new_thresholds' => $newThresholds,
            'new_patterns_learned' => count($newPatterns['keywords']),
            'trained_at' => now()->toIso8601String(),
        ];
    }
}
