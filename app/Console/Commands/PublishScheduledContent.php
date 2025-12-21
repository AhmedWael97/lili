<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Jobs\PublishContentToFacebookJob;
use Illuminate\Console\Command;

class PublishScheduledContent extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'content:publish-scheduled';

    /**
     * The console command description.
     */
    protected $description = 'Publish all scheduled content that is due';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dueContent = Content::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->with('facebookPage')
            ->get();

        if ($dueContent->isEmpty()) {
            $this->info('No scheduled content due for publishing.');
            return 0;
        }

        $this->info("Found {$dueContent->count()} content items to publish...");

        foreach ($dueContent as $content) {
            try {
                PublishContentToFacebookJob::dispatch($content);
                $this->info("Dispatched job for content ID: {$content->id}");
            } catch (\Exception $e) {
                $this->error("Failed to dispatch job for content ID {$content->id}: {$e->getMessage()}");
            }
        }

        $this->info('All scheduled content jobs dispatched successfully.');
        return 0;
    }
}
