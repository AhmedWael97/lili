<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryProfile extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'platform_popularity',
        'cpm_benchmarks',
        'avg_cpm',
        'purchasing_power',
        'languages',
        'cultural_notes',
        'regulations',
    ];

    protected $casts = [
        'platform_popularity' => 'array',
        'cpm_benchmarks' => 'array',
        'avg_cpm' => 'decimal:2',
        'purchasing_power' => 'array',
        'languages' => 'array',
        'cultural_notes' => 'array',
        'regulations' => 'array',
    ];

    /**
     * Get top platforms for this country
     */
    public function getTopPlatforms(int $limit = 3): array
    {
        if (!$this->platform_popularity) {
            return [];
        }

        arsort($this->platform_popularity);
        return array_slice($this->platform_popularity, 0, $limit, true);
    }
}
