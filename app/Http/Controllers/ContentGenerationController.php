<?php

namespace App\Http\Controllers;

use App\Services\AI\StrategistAgentService;
use App\Services\AI\CopywriterAgentService;
use App\Services\AI\CreativeAgentService;
use App\Services\ContentService;
use App\Services\UsageService;
use App\Repositories\FacebookPageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentGenerationController extends Controller
{
    public function __construct(
        protected StrategistAgentService $strategist,
        protected CopywriterAgentService $copywriter,
        protected CreativeAgentService $creative,
        protected ContentService $contentService,
        protected FacebookPageRepository $pageRepo,
        protected UsageService $usageService
    ) {}

    /**
     * Show content generation form
     */
    public function create()
    {
        $pages = $this->pageRepo->getUserPages(Auth::id());
        $usageSummary = $this->usageService->getUsageSummary(Auth::id());
        
        return view('content.create', compact('pages', 'usageSummary'));
    }

    /**
     * Generate content with AI
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'facebook_page_id' => 'required|exists:facebook_pages,id',
            'content_type' => 'required|in:post,story,reel',
            'topic' => 'required|string|max:500',
            'tone' => 'nullable|in:professional,casual,friendly,authoritative',
            'include_image' => 'boolean',
        ]);

        $user = Auth::user();
        
        // Check usage limits
        $limitCheck = $this->usageService->hasExceededLimit($user->id, 'post');
        if ($limitCheck['exceeded']) {
            return response()->json([
                'success' => false,
                'error' => 'You have reached your monthly content generation limit. Please upgrade your plan.',
                'limit_info' => $limitCheck,
            ], 403);
        }
        
        $page = $this->pageRepo->find($validated['facebook_page_id']);
        $brandSettings = $user->brandSetting;
        
        // Get agent type for model configuration
        $agentType = \App\Models\AgentType::where('code', 'marketing')->first();
        $copywritingModel = $agentType ? ($agentType->model_config['copywriting_model'] ?? $agentType->ai_model) : 'gpt-4o-mini';
        $creativeModel = $agentType ? ($agentType->model_config['creative_model'] ?? $agentType->ai_model) : 'gpt-4o-mini';

        // Build context for AI agents using brand settings
        $context = [
            'brand_name' => $brandSettings ? $brandSettings->brand_name : null ?? $user->company ?? $page->page_name,
            'industry' => $brandSettings ? $brandSettings->industry : null ?? 'General',
            'brand_tone' => $validated['tone'] ?? ($brandSettings ? $brandSettings->brand_tone : null) ?? 'professional',
            'target_audience' => $brandSettings ? $brandSettings->target_audience : null ?? 'General audience',
            'business_goals' => $brandSettings ? $brandSettings->business_goals : null ?? 'Increase engagement',
            'voice_characteristics' => $brandSettings ? $brandSettings->voice_characteristics : null ?? 'engaging, authentic',
            'key_messages' => $brandSettings ? $brandSettings->key_messages : null ?? $validated['topic'],
            'forbidden_words' => $brandSettings ? $brandSettings->forbidden_words : null ?? '',
            'preferred_language' => $brandSettings ? $brandSettings->preferred_language : null ?? 'en',
            'follower_count' => $page->follower_count,
            'engagement_rate' => '4.5',
            'top_post_types' => 'image, video',
            'peak_times' => '9 AM, 1 PM, 7 PM',
            'task_description' => "Create a {$validated['content_type']} about: {$validated['topic']}",
            'max_length' => 2200,
            'required_elements' => 'CTA, hashtags',
            'cta_required' => true,
        ];

        // Step 1: Generate caption with Copywriter Agent
        $copyResult = $this->copywriter->generateCaption($context, $copywritingModel);

        $content = [
            'caption' => $copyResult['caption'] ?? '',
            'hashtags' => $copyResult['hashtags'] ?? [],
            'cta' => $copyResult['cta'] ?? '',
            'image_url' => null,
        ];

        // Step 2: Generate image if requested
        if ($validated['include_image']) {
            $imageContext = array_merge($context, [
                'post_caption' => $content['caption'],
                'post_objective' => 'engagement',
                'primary_colors' => $brandSettings ? $brandSettings->primary_colors : null ?? '#1877F2, #42B72A',
                'secondary_color' => $brandSettings ? $brandSettings->secondary_color : null ?? '#42B72A',
                'visual_style' => $brandSettings ? $brandSettings->visual_style : null ?? 'modern, clean',
                'font_family' => $brandSettings ? $brandSettings->font_family : null ?? 'Inter',
                'logo_usage' => ($brandSettings && $brandSettings->logo_in_images) ? 'required' : 'optional',
                'image_mood' => $context['brand_tone'],
                'text_allowed' => 'minimal',
                'task_description' => "Create an image for: {$validated['topic']}",
            ]);

            try {
                $imageUrl = $this->creative->generateImage($imageContext, $creativeModel);
                
                // Apply logo overlay if enabled and logo exists
                if ($brandSettings && $brandSettings->logo_in_images && $brandSettings->logo_path && $imageUrl) {
                    $logoPath = storage_path('app/public/' . $brandSettings->logo_path);
                    if (file_exists($logoPath)) {
                        $content['image_url'] = $this->overlayLogo($imageUrl, $logoPath);
                    } else {
                        $content['image_url'] = $imageUrl;
                    }
                } else {
                    $content['image_url'] = $imageUrl;
                }
            } catch (\Exception $e) {
                \Log::error('Image generation failed: ' . $e->getMessage());
            }
        }

        // Step 3: Track usage
        $this->usageService->trackPost($user->id);
        
        // Step 4: Save as draft
        $newContent = $this->contentService->createContent($user->id, [
            'facebook_page_id' => $validated['facebook_page_id'],
            'content_type' => $validated['content_type'],
            'caption' => $content['caption'],
            'media_url' => $content['image_url'],
            'status' => 'draft',
            'metadata' => [
                'hashtags' => $content['hashtags'],
                'cta' => $content['cta'],
                'tone' => $validated['tone'],
                'topic' => $validated['topic'],
            ],
        ]);

        return response()->json([
            'success' => true,
            'content' => $newContent,
            'preview' => $content,
        ]);
    }

    /**
     * Schedule content for publishing
     */
    public function schedule(Request $request, int $contentId)
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $content = $this->contentService->scheduleContent(
            $contentId,
            Auth::id(),
            $validated['scheduled_at']
        );

        return response()->json([
            'success' => true,
            'content' => $content,
            'message' => 'Content scheduled successfully',
        ]);
    }

    /**
     * Publish content immediately
     */
    public function publish(int $contentId)
    {
        $content = $this->contentService->publishContent($contentId, Auth::id());

        return response()->json([
            'success' => true,
            'content' => $content,
            'message' => 'Content published successfully',
        ]);
    }

    /**
     * Overlay logo on generated image
     */
    protected function overlayLogo(string $imageUrl, string $logoPath): string
    {
        try {
            // Download the AI-generated image
            $imageData = file_get_contents($imageUrl);
            $canvas = imagecreatefromstring($imageData);
            
            if (!$canvas) {
                return $imageUrl;
            }

            // Load logo
            $logoExt = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $logo = match($logoExt) {
                'png' => imagecreatefrompng($logoPath),
                'jpg', 'jpeg' => imagecreatefromjpeg($logoPath),
                'gif' => imagecreatefromgif($logoPath),
                default => null,
            };

            if (!$logo) {
                imagedestroy($canvas);
                return $imageUrl;
            }

            // Get dimensions
            $canvasWidth = imagesx($canvas);
            $canvasHeight = imagesy($canvas);
            $logoWidth = imagesx($logo);
            $logoHeight = imagesy($logo);

            // Resize logo to 10% of canvas width
            $newLogoWidth = (int)($canvasWidth * 0.10);
            $newLogoHeight = (int)($logoHeight * ($newLogoWidth / $logoWidth));

            // Position: bottom-right with 20px padding
            $x = $canvasWidth - $newLogoWidth - 20;
            $y = $canvasHeight - $newLogoHeight - 20;

            // Overlay logo
            imagecopyresampled(
                $canvas, $logo,
                $x, $y,
                0, 0,
                $newLogoWidth, $newLogoHeight,
                $logoWidth, $logoHeight
            );

            // Save to storage
            $filename = 'generated_' . uniqid() . '.png';
            $savePath = storage_path('app/public/generated/' . $filename);
            
            // Create directory if it doesn't exist
            if (!file_exists(dirname($savePath))) {
                mkdir(dirname($savePath), 0755, true);
            }

            imagepng($canvas, $savePath);

            // Cleanup
            imagedestroy($canvas);
            imagedestroy($logo);

            return asset('storage/generated/' . $filename);

        } catch (\Exception $e) {
            \Log::error('Logo overlay failed: ' . $e->getMessage());
            return $imageUrl;
        }
    }
}
