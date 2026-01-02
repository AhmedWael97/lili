<?php

namespace App\Jobs;

use App\Agents\MarketResearch\CompetitorFinderAgent;
use App\Agents\MarketResearch\SocialIntelligenceAgent;
use App\Agents\MarketResearch\MarketAnalysisAgent;
use App\Agents\MarketResearch\ReportGeneratorAgent;
use App\Models\ResearchRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMarketResearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900; // 15 minutes
    public $tries = 3; // Retry up to 3 times
    public $backoff = [60, 180, 300]; // Wait 1min, 3min, 5min between retries

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ResearchRequest $researchRequest
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting market research processing', [
            'request_id' => $this->researchRequest->id,
            'business_idea' => $this->researchRequest->business_idea,
            'location' => $this->researchRequest->location,
        ]);

        try {
            // Mark as processing
            $this->researchRequest->markAsProcessing();

            // ============================================
            // STEP 1: Find Competitors
            // ============================================
            Log::info('Step 1: Finding competitors...', ['request_id' => $this->researchRequest->id]);
            
            $competitorFinder = app(CompetitorFinderAgent::class);
            
            try {
                $competitorsData = $competitorFinder->findCompetitors($this->researchRequest);
            } catch (\Exception $e) {
                Log::error('Competitor finding failed', [
                    'request_id' => $this->researchRequest->id,
                    'error' => $e->getMessage(),
                    'attempt' => $this->attempts(),
                ]);
                
                // If this is the last attempt, mark as failed
                if ($this->attempts() >= $this->tries) {
                    $this->researchRequest->markAsFailed();
                }
                
                throw $e; // Re-throw to trigger retry
            }
            
            if (empty($competitorsData)) {
                Log::warning('No competitors found', [
                    'request_id' => $this->researchRequest->id
                ]);
                
                $this->researchRequest->markAsFailed();
                return;
            }

            // Save competitors to database
            $savedCompetitors = $competitorFinder->saveCompetitors($competitorsData);

            Log::info('Competitors saved', [
                'request_id' => $this->researchRequest->id,
                'count' => count($savedCompetitors)
            ]);

            // ============================================
            // STEP 2: Analyze Social Media Presence
            // ============================================
            Log::info('Step 2: Analyzing social media...', ['request_id' => $this->researchRequest->id]);
            
            $socialAgent = app(SocialIntelligenceAgent::class);
            $socialAgent->analyzeAllCompetitors($savedCompetitors);

            Log::info('Social intelligence complete', [
                'request_id' => $this->researchRequest->id
            ]);

            // ============================================
            // STEP 3: Generate Market Analysis
            // ============================================
            Log::info('Step 3: Generating market analysis...', ['request_id' => $this->researchRequest->id]);
            
            $marketAgent = app(MarketAnalysisAgent::class);
            $marketAnalysis = $marketAgent->analyzeMarket($this->researchRequest);

            Log::info('Market analysis complete', [
                'request_id' => $this->researchRequest->id,
                'competition_level' => $marketAnalysis->competition_level
            ]);

            // ============================================
            // STEP 4: Generate Final Report
            // ============================================
            Log::info('Step 4: Generating final report...', ['request_id' => $this->researchRequest->id]);
            
            $reportAgent = app(ReportGeneratorAgent::class);
            $report = $reportAgent->generateReport($this->researchRequest);

            Log::info('Report generated', [
                'request_id' => $this->researchRequest->id,
                'report_id' => $report->id
            ]);

            // ============================================
            // COMPLETE
            // ============================================
            $this->researchRequest->markAsCompleted();

            Log::info('Market research completed successfully', [
                'request_id' => $this->researchRequest->id,
                'competitors_found' => count($savedCompetitors),
                'report_id' => $report->id,
                'duration' => now()->diffInSeconds($this->researchRequest->created_at) . 's'
            ]);

        } catch (\Exception $e) {
            Log::error('Market research processing failed', [
                'request_id' => $this->researchRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->researchRequest->markAsFailed();
            
            throw $e;
        }
    }
}
