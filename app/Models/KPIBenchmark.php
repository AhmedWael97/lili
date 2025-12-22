<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KPIBenchmark extends Model
{
    protected $table = 'kpi_benchmarks';

    protected $fillable = [
        'industry',
        'country',
        'channel',
        'avg_engagement_rate',
        'avg_ctr',
        'avg_cpm',
        'avg_cpc',
        'avg_conversion_rate',
        'posts_per_week',
        'follower_growth_rate',
        'best_content_types',
        'best_posting_times',
        'source',
    ];

    protected $casts = [
        'avg_engagement_rate' => 'decimal:4',
        'avg_ctr' => 'decimal:4',
        'avg_cpm' => 'decimal:2',
        'avg_cpc' => 'decimal:2',
        'avg_conversion_rate' => 'decimal:4',
        'follower_growth_rate' => 'decimal:2',
        'best_content_types' => 'array',
        'best_posting_times' => 'array',
    ];

    /**
     * Get benchmarks for specific industry and country
     */
    public static function getBenchmarks(string $industry, ?string $country = null, ?string $channel = null): array
    {
        $query = self::where('industry', $industry);

        if ($country) {
            $query->where(function($q) use ($country) {
                $q->where('country', $country)
                  ->orWhereNull('country');
            });
        }

        if ($channel) {
            $query->where('channel', $channel);
        }

        return $query->get()->toArray();
    }
}
