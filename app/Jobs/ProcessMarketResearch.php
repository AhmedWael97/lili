<?php

namespace App\Jobs;

use App\Agents\MarketResearch\CompetitorFinderAgent;
use App\Agents\MarketResearch\CustomerInsightsAgent;
use App\Agents\MarketResearch\ForumScraperAgent;
use App\Agents\MarketResearch\MarketAnalysisAgent;
use App\Agents\MarketResearch\OpportunityAnalysisAgent;
use App\Agents\MarketResearch\PricingScraperAgent;
use App\Agents\MarketResearch\ReportGeneratorAgent;
use App\Agents\MarketResearch\ReviewScraperAgent;
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

    public int $timeout = 1800; // 30 minutes
    public int $tries = 1;

    private ResearchRequest $request;

    public function __construct(ResearchRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the market research process
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            Log::info("Starting market research for request #{$this->request->id}");

            // Update status
            $this->request->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            // Step 1: Find Competitors (2-3 minutes)
            $this->updateProgress('Finding competitors...', 10);
            $competitors = $this->findCompetitors();
            Log::info("Found " . count($competitors) . " competitors");

            // Step 2: Scrape Reviews (3-4 minutes)
            $this->updateProgress('Scraping reviews...', 30);
            $reviewCount = $this->scrapeReviews($competitors);
            Log::info("Scraped {$reviewCount} reviews");

            // Step 3: Scrape Pricing (2-3 minutes)
            $this->updateProgress('Analyzing pricing...', 50);
            $pricingCount = $this->scrapePricing($competitors);
            Log::info("Found {$pricingCount} pricing tiers");

            // Step 4: Forum Research (2-3 minutes)
            $this->updateProgress('Researching forums...', 60);
            $forumCount = $this->scrapeForums();
            Log::info("Found {$forumCount} forum discussions");

            // Step 5: Market Analysis (1-2 minutes)
            $this->updateProgress('Analyzing market...', 75);
            $marketData = $this->analyzeMarket($competitors);
            Log::info("Market analysis completed");

            // Step 6: Customer Insights (1-2 minutes)
            $this->updateProgress('Generating customer insights...', 85);
            $customerInsights = $this->generateCustomerInsights();
            Log::info("Customer insights generated");

            // Step 7: Opportunity Analysis (1 minute)
            $this->updateProgress('Identifying opportunities...', 90);
            $opportunityAnalysis = $this->analyzeOpportunities();
            Log::info("Opportunity analysis completed");

            // Step 8: Generate Report (1 minute)
            $this->updateProgress('Generating report...', 95);
            $report = $this->generateReport($opportunityAnalysis);
            Log::info("Report generated");

            // Complete
            $this->request->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->updateProgress('Complete!', 100);

            Log::info("Market research completed for request #{$this->request->id}");

        } catch (\Exception $e) {
            Log::error("Market research failed for request #{$this->request->id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            $this->request->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Step 1: Find competitors
     *
     * @return array
     */
    private function findCompetitors(): array
    {
        $agent = app(CompetitorFinderAgent::class);
        return $agent->execute($this->request);
    }

    /**
     * Step 2: Scrape reviews
     *
     * @param array $competitors
     * @return int
     */
    private function scrapeReviews(array $competitors): int
    {
        $agent = app(ReviewScraperAgent::class);
        return $agent->execute($competitors);
    }

    /**
     * Step 3: Scrape pricing
     *
     * @param array $competitors
     * @return int
     */
    private function scrapePricing(array $competitors): int
    {
        $agent = app(PricingScraperAgent::class);
        return $agent->execute($competitors);
    }

    /**
     * Step 4: Scrape forums
     *
     * @return int
     */
    private function scrapeForums(): int
    {
        $agent = app(ForumScraperAgent::class);
        return $agent->execute($this->request);
    }

    /**
     * Step 5: Analyze market
     *
     * @param array $competitors
     * @return mixed
     */
    private function analyzeMarket(array $competitors)
    {
        $agent = app(MarketAnalysisAgent::class);
        return $agent->execute($this->request, $competitors);
    }

    /**
     * Step 6: Generate customer insights
     *
     * @return mixed
     */
    private function generateCustomerInsights()
    {
        $agent = app(CustomerInsightsAgent::class);
        return $agent->execute($this->request);
    }

    /**
     * Step 7: Analyze opportunities
     *
     * @return array
     */
    private function analyzeOpportunities(): array
    {
        // Refresh request to get all relationships
        $this->request->refresh();

        $agent = app(OpportunityAnalysisAgent::class);
        return $agent->execute($this->request);
    }

    /**
     * Step 8: Generate report
     *
     * @param array $opportunityAnalysis
     * @return mixed
     */
    private function generateReport(array $opportunityAnalysis)
    {
        // Refresh request one final time
        $this->request->refresh();

        $agent = app(ReportGeneratorAgent::class);
        return $agent->execute($this->request, $opportunityAnalysis);
    }

    /**
     * Update progress (could be used for real-time updates)
     *
     * @param string $message
     * @param int $percentage
     * @return void
     */
    private function updateProgress(string $message, int $percentage): void
    {
        Log::info("[Progress {$percentage}%] {$message}");

        // Here you could broadcast an event for real-time UI updates
        // broadcast(new MarketResearchProgress($this->request->id, $message, $percentage));
    }

    /**
     * Handle job failure
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed for research request #{$this->request->id}: " . $exception->getMessage());

        $this->request->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
