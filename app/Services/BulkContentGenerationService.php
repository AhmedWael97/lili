<?php

namespace App\Services;

use App\Services\AI\CopywriterAgentService;
use App\Services\AI\CreativeAgentService;
use App\Models\Content;
use App\Models\BrandSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BulkContentGenerationService
{
    public function __construct(
        protected CopywriterAgentService $copywriter,
        protected CreativeAgentService $creative
    ) {}

    /**
     * Generate content for entire strategy calendar
     */
    public function generateFromStrategy(int $userId, array $strategyCalendar, array $brandContext): array
    {
        $results = [];
        $brandSettings = BrandSetting::where('user_id', $userId)->first();

        foreach ($strategyCalendar as $index => $day) {
            try {
                $result = $this->generateDayContent($userId, $day, $brandContext, $brandSettings);
                $results[] = [
                    'day' => $day['day'] ?? "Day " . ($index + 1),
                    'status' => 'success',
                    'content_id' => $result['content_id'],
                    'caption' => $result['caption'],
                    'image_url' => $result['image_url'] ?? null,
                ];
            } catch (\Exception $e) {
                Log::error("Failed to generate content for day: " . ($day['day'] ?? $index), [
                    'error' => $e->getMessage(),
                    'user_id' => $userId,
                ]);

                $results[] = [
                    'day' => $day['day'] ?? "Day " . ($index + 1),
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Generate content for a single day
     */
    protected function generateDayContent(int $userId, array $dayData, array $brandContext, ?BrandSetting $brandSettings): array
    {
        // Build context for copywriter
        $context = [
            'brand_name' => $brandContext['brand_name'] ?? 'Brand',
            'brand_tone' => $brandContext['brand_tone'] ?? 'friendly',
            'target_audience' => $brandContext['target_audience'] ?? '',
            'content_type' => $dayData['content_type'] ?? 'post',
            'topic' => $dayData['topic'] ?? '',
            'objective' => $dayData['objective'] ?? '',
            'platform' => 'facebook',
            'max_length' => 500,
            'include_hashtags' => true,
            'include_cta' => true,
        ];

        // Generate caption
        $captionResult = $this->copywriter->writeCopyFromStrategy($context);

        // Determine if we need an image based on content type
        $needsImage = in_array(strtolower($dayData['content_type'] ?? 'image'), ['image', 'video', 'carousel']);
        $imageUrl = null;

        if ($needsImage) {
            $imageContext = [
                'brand_name' => $brandContext['brand_name'] ?? 'Brand',
                'primary_colors' => $brandSettings->primary_colors ?? '#1877F2, #42B72A',
                'visual_style' => $brandSettings->visual_style ?? 'modern, clean',
                'logo_usage' => $brandSettings->logo_in_images ? 'required' : 'optional',
                'image_mood' => $brandContext['brand_tone'] ?? 'friendly',
                'post_caption' => $captionResult['caption'] ?? '',
                'post_objective' => $dayData['objective'] ?? '',
                'target_audience' => $brandContext['target_audience'] ?? '',
                'text_allowed' => 'minimal',
                'task_description' => "Create image for: {$dayData['topic']}",
            ];

            try {
                $imageUrl = $this->creative->generateImage($imageContext);

                // Apply logo if needed
                if ($brandSettings && $brandSettings->logo_in_images && $brandSettings->logo_path) {
                    $imageUrl = $this->overlayLogo($imageUrl, $brandSettings->logo_path);
                }
            } catch (\Exception $e) {
                Log::warning('Image generation failed for day content', [
                    'error' => $e->getMessage(),
                    'topic' => $dayData['topic'],
                ]);
            }
        }

        // Save as draft
        $content = Content::create([
            'user_id' => $userId,
            'facebook_page_id' => null,
            'content_type' => $dayData['content_type'] ?? 'post',
            'caption' => $captionResult['caption'] ?? '',
            'image_url' => $imageUrl,
            'status' => 'draft',
            'agent_used' => 'bulk_generator',
            'metadata' => [
                'strategy_day' => $dayData['day'] ?? '',
                'strategy_time' => $dayData['time'] ?? '',
                'topic' => $dayData['topic'] ?? '',
                'objective' => $dayData['objective'] ?? '',
                'hashtags' => $captionResult['hashtags'] ?? [],
                'cta' => $captionResult['cta'] ?? '',
            ],
        ]);

        return [
            'content_id' => $content->id,
            'caption' => $captionResult['caption'] ?? '',
            'image_url' => $imageUrl,
        ];
    }

    /**
     * Overlay logo on generated image
     */
    protected function overlayLogo(string $imageUrl, string $logoPath): string
    {
        try {
            // Download the AI-generated image
            $imageContent = file_get_contents($imageUrl);
            if (!$imageContent) {
                return $imageUrl;
            }

            $tempImage = tempnam(sys_get_temp_dir(), 'ai_image_');
            file_put_contents($tempImage, $imageContent);

            // Load images
            $canvas = imagecreatefromstring($imageContent);
            if (!$canvas) {
                return $imageUrl;
            }

            $logoFullPath = storage_path('app/public/' . $logoPath);
            if (!file_exists($logoFullPath)) {
                return $imageUrl;
            }

            // Determine logo image type
            $logoExt = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $logo = match($logoExt) {
                'png' => imagecreatefrompng($logoFullPath),
                'jpg', 'jpeg' => imagecreatefromjpeg($logoFullPath),
                default => null,
            };

            if (!$logo) {
                return $imageUrl;
            }

            // Calculate logo position (bottom-right corner with padding)
            $canvasWidth = imagesx($canvas);
            $canvasHeight = imagesy($canvas);
            $logoWidth = imagesx($logo);
            $logoHeight = imagesy($logo);

            // Resize logo to 10% of image width
            $newLogoWidth = (int)($canvasWidth * 0.1);
            $newLogoHeight = (int)($logoHeight * ($newLogoWidth / $logoWidth));

            $logoResized = imagecreatetruecolor($newLogoWidth, $newLogoHeight);
            imagealphablending($logoResized, false);
            imagesavealpha($logoResized, true);
            imagecopyresampled($logoResized, $logo, 0, 0, 0, 0, $newLogoWidth, $newLogoHeight, $logoWidth, $logoHeight);

            // Position in bottom-right with 20px padding
            $destX = $canvasWidth - $newLogoWidth - 20;
            $destY = $canvasHeight - $newLogoHeight - 20;

            // Merge logo onto canvas
            imagecopy($canvas, $logoResized, $destX, $destY, 0, 0, $newLogoWidth, $newLogoHeight);

            // Save the final image
            $finalPath = 'generated/' . uniqid('branded_') . '.png';
            $fullPath = storage_path('app/public/' . $finalPath);
            
            // Ensure directory exists
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            imagepng($canvas, $fullPath);

            // Cleanup
            imagedestroy($canvas);
            imagedestroy($logo);
            imagedestroy($logoResized);
            @unlink($tempImage);

            return asset('storage/' . $finalPath);

        } catch (\Exception $e) {
            Log::error('Logo overlay failed', ['error' => $e->getMessage()]);
            return $imageUrl;
        }
    }
}
