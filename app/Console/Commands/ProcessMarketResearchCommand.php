<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMarketResearch;
use App\Models\ResearchRequest;
use Illuminate\Console\Command;

class ProcessMarketResearchCommand extends Command
{
    protected $signature = 'market-research:process {id : The research request ID}';
    protected $description = 'Manually process a market research request (for debugging)';

    public function handle(): int
    {
        $id = $this->argument('id');
        
        $researchRequest = ResearchRequest::find($id);
        
        if (!$researchRequest) {
            $this->error("Research request #{$id} not found.");
            return 1;
        }
        
        $this->info("Processing research request #{$id}");
        $this->info("Business Idea: {$researchRequest->business_idea}");
        $this->info("Location: {$researchRequest->location}");
        $this->info("Current Status: {$researchRequest->status}");
        $this->newLine();
        
        try {
            $this->info('Dispatching job to queue...');
            ProcessMarketResearch::dispatch($researchRequest);
            
            $this->info('✓ Job dispatched successfully!');
            $this->info('Monitor the queue worker to see progress.');
            $this->newLine();
            $this->comment('Tip: Check logs with: tail -f storage/logs/laravel.log');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to dispatch job:');
            $this->error($e->getMessage());
            
            return 1;
        }
    }
}
