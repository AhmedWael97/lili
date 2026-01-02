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
        'report_data',
        'recommendations',
        'action_plan',
        'pdf_path',
    ];

    protected $casts = [
        'report_data' => 'array',
        'recommendations' => 'array',
        'action_plan' => 'array',
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
