<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'metric_type',
        'component',
        'score',
        'true_positives',
        'true_negatives',
        'false_positives',
        'false_negatives',
        'total_samples',
        'period_start',
        'period_end',
        'config_snapshot',
    ];

    protected $casts = [
        'score' => 'decimal:4',
        'period_start' => 'date',
        'period_end' => 'date',
        'config_snapshot' => 'array',
    ];

    /**
     * Calculate accuracy
     */
    public function getAccuracy(): float
    {
        $total = $this->true_positives + $this->true_negatives + $this->false_positives + $this->false_negatives;
        
        if ($total === 0) {
            return 0;
        }

        return ($this->true_positives + $this->true_negatives) / $total;
    }

    /**
     * Calculate precision
     */
    public function getPrecision(): float
    {
        $denominator = $this->true_positives + $this->false_positives;
        
        if ($denominator === 0) {
            return 0;
        }

        return $this->true_positives / $denominator;
    }

    /**
     * Calculate recall
     */
    public function getRecall(): float
    {
        $denominator = $this->true_positives + $this->false_negatives;
        
        if ($denominator === 0) {
            return 0;
        }

        return $this->true_positives / $denominator;
    }

    /**
     * Calculate F1 score
     */
    public function getF1Score(): float
    {
        $precision = $this->getPrecision();
        $recall = $this->getRecall();
        
        if ($precision + $recall === 0) {
            return 0;
        }

        return 2 * ($precision * $recall) / ($precision + $recall);
    }

    /**
     * Get performance summary
     */
    public function getPerformanceSummary(): array
    {
        return [
            'accuracy' => round($this->getAccuracy(), 4),
            'precision' => round($this->getPrecision(), 4),
            'recall' => round($this->getRecall(), 4),
            'f1_score' => round($this->getF1Score(), 4),
            'total_samples' => $this->total_samples,
        ];
    }
}
