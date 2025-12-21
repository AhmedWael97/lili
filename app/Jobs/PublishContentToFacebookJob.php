<?php

namespace App\Jobs;

use App\Models\Content;
use App\Services\FacebookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishContentToFacebookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Content $content
    ) {}

    /**
     * Execute the job.
     */
    public function handle(FacebookService $facebookService): void
    {
        try {
            $page = $this->content->facebookPage;
            
            if (!$page) {
                throw new \Exception('Facebook page not found');
            }

            $fb = new \Facebook\Facebook([
                'app_id' => config('services.facebook.client_id'),
                'app_secret' => config('services.facebook.client_secret'),
                'default_graph_version' => 'v18.0',
            ]);

            $data = ['message' => $this->content->caption];

            // Add image if exists
            if ($this->content->media_url) {
                $data['url'] = $this->content->media_url;
                $endpoint = "/{$page->page_id}/photos";
            } else {
                $endpoint = "/{$page->page_id}/feed";
            }

            // Publish to Facebook
            $response = $fb->post($endpoint, $data, $page->page_access_token);
            $graphNode = $response->getGraphNode();

            // Update content with Facebook post ID
            $this->content->update([
                'status' => 'published',
                'published_at' => now(),
                'platform_post_id' => $graphNode['id'] ?? null,
            ]);

            Log::info("Content published to Facebook", [
                'content_id' => $this->content->id,
                'post_id' => $graphNode['id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to publish content to Facebook", [
                'content_id' => $this->content->id,
                'error' => $e->getMessage(),
            ]);

            $this->content->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
