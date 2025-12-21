<?php

namespace App\Jobs;

use App\Models\Content;
use App\Services\AI\CopywriterAgentService;
use App\Services\AI\CreativeAgentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 180;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Content $content,
        public array $context
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        CopywriterAgentService $copywriter,
        CreativeAgentService $creative
    ): void {
        try {
            // Generate caption
            $copyResult = $copywriter->generateCaption($this->context);

            $updates = [
                'caption' => $copyResult['caption'] ?? '',
                'metadata' => array_merge($this->content->metadata ?? [], [
                    'hashtags' => $copyResult['hashtags'] ?? [],
                    'cta' => $copyResult['cta'] ?? '',
                    'ai_generated' => true,
                ]),
            ];

            // Generate image if required
            if ($this->context['include_image'] ?? false) {
                $imageContext = array_merge($this->context, [
                    'post_caption' => $updates['caption'],
                    'post_objective' => 'engagement',
                    'primary_colors' => '#1877F2, #42B72A',
                    'visual_style' => 'modern, clean',
                    'logo_usage' => 'optional',
                    'text_allowed' => 'minimal',
                ]);

                try {
                    $updates['media_url'] = $creative->generateImage($imageContext);
                } catch (\Exception $e) {
                    Log::warning('Image generation failed in job', [
                        'content_id' => $this->content->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->content->update($updates);

            Log::info('Content generated successfully', [
                'content_id' => $this->content->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Content generation job failed', [
                'content_id' => $this->content->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
