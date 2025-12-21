<?php

namespace App\Http\Controllers;

use App\Services\AI\StrategistAgentService;
use App\Services\AI\CopywriterAgentService;
use App\Services\AI\CreativeAgentService;
use App\Services\BulkContentGenerationService;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIStudioController extends Controller
{
    public function __construct(
        protected StrategistAgentService $strategist,
        protected CopywriterAgentService $copywriter,
        protected CreativeAgentService $creative,
        protected BulkContentGenerationService $bulkGenerator,
        protected UsageService $usageService
    ) {}

    /**
     * Show AI Studio dashboard
     */
    public function index()
    {
        return view('ai-studio.index');
    }

    /**
     * Show strategy generator
     */
    public function strategyForm()
    {
        $brandSettings = Auth::user()->brandSetting;
        $usageSummary = $this->usageService->getUsageSummary(Auth::id());
        
        return view('ai-studio.strategy', compact('brandSettings', 'usageSummary'));
    }

    /**
     * Generate marketing strategy
     */
    public function generateStrategy(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'target_audience' => 'nullable|string|max:500',
            'business_goals' => 'nullable|string|max:500',
            'brand_tone' => 'nullable|in:professional,casual,friendly,authoritative',
            'days' => 'required|integer|min:1|max:30',
        ]);

        try {
            // Check usage limits
            $limitCheck = $this->usageService->hasExceededLimit(Auth::id(), 'post');
            if ($limitCheck['exceeded']) {
                return response()->json([
                    'success' => false,
                    'error' => 'You have reached your monthly content generation limit. Please upgrade your plan.',
                    'limit_info' => $limitCheck,
                ], 403);
            }
            
            // Get brand settings
            $brandSettings = Auth::user()->brandSetting;
            
            // Use saved brand settings as defaults, override with request data if provided
            $context = [
                'brand_name' => $validated['brand_name'] ?? ($brandSettings ? $brandSettings->brand_name : null) ?? 'Your Brand',
                'industry' => $validated['industry'] ?? ($brandSettings ? $brandSettings->industry : null) ?? 'General',
                'target_audience' => $validated['target_audience'] ?? ($brandSettings ? $brandSettings->target_audience : null) ?? 'General audience',
                'business_goals' => $validated['business_goals'] ?? ($brandSettings ? $brandSettings->business_goals : null) ?? 'Increase engagement',
                'brand_tone' => $validated['brand_tone'] ?? ($brandSettings ? $brandSettings->brand_tone : null) ?? 'professional',
                'voice_characteristics' => $brandSettings ? $brandSettings->voice_characteristics : '',
                'key_messages' => $brandSettings ? $brandSettings->key_messages : '',
                'visual_style' => $brandSettings ? $brandSettings->visual_style : '',
                'preferred_language' => $brandSettings ? $brandSettings->preferred_language : 'en',
                'monthly_budget' => $brandSettings ? $brandSettings->monthly_budget : null,
                'follower_count' => $request->follower_count ?? 1000,
                'engagement_rate' => $request->engagement_rate ?? '4.5',
                'top_post_types' => 'image, video, carousel',
                'peak_times' => '9 AM, 1 PM, 7 PM',
                'forbidden_words' => $request->forbidden_words ?? ($brandSettings ? $brandSettings->forbidden_words : null) ?? '',
                'task_description' => "Create a comprehensive {$validated['days']}-day content strategy",
            ];

            $strategy = $this->strategist->createStrategy($context);
            
            // Track usage (1 post counted for strategy generation)
            $this->usageService->trackPost(Auth::id());

            return response()->json([
                'success' => true,
                'strategy' => $strategy,
                'brand_context' => [
                    'brand_name' => $context['brand_name'],
                    'brand_tone' => $context['brand_tone'],
                    'target_audience' => $context['target_audience'],
                    'business_goals' => $context['business_goals'],
                    'voice_characteristics' => $context['voice_characteristics'],
                    'key_messages' => $context['key_messages'],
                    'visual_style' => $context['visual_style'],
                    'forbidden_words' => $context['forbidden_words'],
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Strategy generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate strategy. Please check your OpenAI API key.',
            ], 500);
        }
    }

    /**
     * Show content creator
     */
    public function contentForm()
    {
        return view('ai-studio.content');
    }

    /**
     * Generate content (caption + image)
     */
    public function generateContent(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'topic' => 'required|string|max:500',
            'tone' => 'required|in:professional,casual,friendly,authoritative',
            'target_audience' => 'required|string|max:500',
            'include_image' => 'boolean',
        ]);

        try {
            // Generate caption
            $captionContext = [
                'brand_name' => $validated['brand_name'],
                'brand_tone' => $validated['tone'],
                'voice_characteristics' => "{$validated['tone']}, engaging, authentic",
                'target_audience' => $validated['target_audience'],
                'key_messages' => $validated['topic'],
                'forbidden_words' => $request->forbidden_words ?? '',
                'max_length' => 2200,
                'required_elements' => 'hashtags, CTA',
                'cta_required' => true,
                'task_description' => "Write an engaging Facebook post about: {$validated['topic']}",
            ];

            $captionResult = $this->copywriter->generateCaption($captionContext);

            $result = [
                'caption' => $captionResult['caption'] ?? '',
                'hashtags' => $captionResult['hashtags'] ?? [],
                'cta' => $captionResult['cta'] ?? '',
                'character_count' => $captionResult['character_count'] ?? 0,
                'image_url' => null,
            ];

            // Generate image if requested
            if ($validated['include_image'] ?? false) {
                $imageContext = [
                    'brand_name' => $validated['brand_name'],
                    'primary_colors' => '#1877F2, #42B72A, #FFFFFF',
                    'visual_style' => 'modern, clean, professional',
                    'logo_usage' => 'optional',
                    'image_mood' => $validated['tone'],
                    'post_caption' => $result['caption'],
                    'post_objective' => 'engagement',
                    'target_audience' => $validated['target_audience'],
                    'text_allowed' => 'minimal',
                    'task_description' => "Create a compelling image for: {$validated['topic']}",
                ];

                try {
                    $result['image_url'] = $this->creative->generateImage($imageContext);
                } catch (\Exception $e) {
                    \Log::warning('Image generation failed: ' . $e->getMessage());
                    $result['image_error'] = 'Image generation failed, but caption was created successfully.';
                }
            }

            return response()->json([
                'success' => true,
                'content' => $result,
            ]);

        } catch (\Exception $e) {
            \Log::error('Content generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate content: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save generated content as draft
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'caption' => 'required|string',
            'image_url' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        try {
            $content = \App\Models\Content::create([
                'user_id' => Auth::id(),
                'facebook_page_id' => null, // No page yet - can be assigned later
                'content_type' => 'post',
                'caption' => $validated['caption'],
                'image_url' => $validated['image_url'] ?? null,
                'status' => 'draft',
                'metadata' => $validated['metadata'] ?? [],
                'agent_used' => 'ai_studio',
            ]);

            return response()->json([
                'success' => true,
                'content' => $content,
                'message' => 'Content saved as draft successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to save content: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate all content from strategy calendar
     */
    public function generateAllContent(Request $request)
    {
        $validated = $request->validate([
            'strategy_calendar' => 'required|array',
            'brand_context' => 'required|array',
        ]);

        try {
            $results = $this->bulkGenerator->generateFromStrategy(
                Auth::id(),
                $validated['strategy_calendar'],
                $validated['brand_context']
            );

            $successCount = collect($results)->where('status', 'success')->count();
            $failedCount = collect($results)->where('status', 'failed')->count();

            return response()->json([
                'success' => true,
                'results' => $results,
                'summary' => [
                    'total' => count($results),
                    'success' => $successCount,
                    'failed' => $failedCount,
                ],
                'message' => "Generated {$successCount} content drafts successfully!",
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk content generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate content: ' . $e->getMessage(),
            ], 500);
        }
    }
}
