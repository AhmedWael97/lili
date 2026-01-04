<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_request_id',
        'executive_summary',
        'report_sections',
        'opportunities',
        'risks',
        'recommendations',
        'action_plan',
        'pdf_path',
        'competitor_count',
        'review_count',
    ];

    protected $casts = [
        'report_sections' => 'array',
        'opportunities' => 'array',
        'risks' => 'array',
        'recommendations' => 'array',
        'action_plan' => 'array',
        'competitor_count' => 'integer',
        'review_count' => 'integer',
    ];

    /**
     * Get the research request that owns the report.
     */
    public function researchRequest(): BelongsTo
    {
        return $this->belongsTo(ResearchRequest::class);
    }

    /**
     * Get the full PDF URL.
     */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }
}
