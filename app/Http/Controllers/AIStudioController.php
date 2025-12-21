<?php

namespace App\Http\Controllers;

use App\Models\AgentConfiguration;
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
        $strategies = \App\Models\Strategy::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        $usageSummary = $this->usageService->getUsageSummary(Auth::id());
        
        return view('marketing-studio.index', compact('strategies', 'usageSummary'));
    }

    /**
     * Delete a strategy
     */
    public function deleteStrategy($id)
    {
        try {
            $strategy = \App\Models\Strategy::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();
            
            $strategy->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Strategy deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete strategy',
            ], 500);
        }
    }

    /**
     * Show strategy generator
     */
    public function strategyForm()
    {
        $user = Auth::user();
        $brandSettings = $user->brandSetting;
        $usageSummary = $this->usageService->getUsageSummary($user->id);
        
        // Get marketing agent configuration if exists
        $agentConfig = AgentConfiguration::where('user_id', $user->id)
            ->where('agent_code', 'marketing')
            ->where('is_complete', true)
            ->first();
        
        return view('marketing-studio.strategy', compact('brandSettings', 'usageSummary', 'agentConfig'));
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
            'agent_config' => 'nullable|array',
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
            
            // Get brand settings and agent type for model configuration
            $brandSettings = Auth::user()->brandSetting;
            $agentType = \App\Models\AgentType::where('code', 'marketing')->first();
            $aiModel = $agentType ? ($agentType->model_config['strategy_model'] ?? $agentType->ai_model) : 'gpt-4o';
            
            // Get agent configuration if passed
            $agentConfig = $validated['agent_config'] ?? null;
            
            // Decode JSON strings if they exist in agent config
            if ($agentConfig) {
                if (isset($agentConfig['target_audience']) && is_string($agentConfig['target_audience'])) {
                    $agentConfig['target_audience'] = json_decode($agentConfig['target_audience'], true);
                }
                if (isset($agentConfig['marketing_goals']) && is_string($agentConfig['marketing_goals'])) {
                    $agentConfig['marketing_goals'] = json_decode($agentConfig['marketing_goals'], true);
                }
                if (isset($agentConfig['focus_keywords']) && is_string($agentConfig['focus_keywords'])) {
                    $agentConfig['focus_keywords'] = json_decode($agentConfig['focus_keywords'], true);
                }
            }
            
            // Build context with priority: form data > agent_config > brand settings > defaults
            $targetAudience = $validated['target_audience'] ?? null;
            if (!$targetAudience && $agentConfig && isset($agentConfig['target_audience'])) {
                $ta = $agentConfig['target_audience'];
                $targetAudience = implode(', ', array_filter([
                    $ta['age'] ?? '',
                    $ta['location'] ?? '',
                    $ta['interests'] ?? ''
                ]));
            }
            if (!$targetAudience) {
                $targetAudience = $brandSettings ? $brandSettings->target_audience : 'General audience';
            }
            
            // Get Facebook page analytics if available
            $followerCount = 0;
            $engagementRate = 0;
            $topPostTypes = 'Images, Videos';
            $peakTimes = 'Based on industry standards';
            
            $user = Auth::user();
            $facebookPage = $user->facebookPages()->where('status', 'active')->first();
            if ($facebookPage) {
                $followerCount = $facebookPage->follower_count ?? 0;
                // You can add more analytics from the Facebook page if available
            }
            
            $context = [
                'brand_name' => $validated['brand_name'] ?? ($agentConfig['business_name'] ?? ($brandSettings ? $brandSettings->brand_name : null)) ?? 'Your Brand',
                'industry' => $validated['industry'] ?? ($agentConfig['industry'] ?? ($brandSettings ? $brandSettings->industry : null)) ?? 'General',
                'target_audience' => $targetAudience,
                'business_goals' => $validated['business_goals'] ?? ($agentConfig && isset($agentConfig['marketing_goals']) ? implode(', ', $agentConfig['marketing_goals']) : ($brandSettings ? $brandSettings->business_goals : null)) ?? 'Increase engagement',
                'brand_tone' => $validated['brand_tone'] ?? ($agentConfig['brand_tone'] ?? ($brandSettings ? $brandSettings->brand_tone : null)) ?? 'professional',
                'voice_characteristics' => $agentConfig['brand_personality'] ?? ($brandSettings ? $brandSettings->voice_characteristics : ''),
                'key_messages' => $agentConfig['unique_value_proposition'] ?? ($brandSettings ? $brandSettings->key_messages : ''),
                'pain_points' => $agentConfig['pain_points'] ?? '',
                'products_services' => $agentConfig['products_services'] ?? '',
                'visual_style' => $brandSettings ? $brandSettings->visual_style : '',
                'preferred_language' => $brandSettings ? $brandSettings->preferred_language : 'en',
                'monthly_budget' => $brandSettings ? $brandSettings->monthly_budget : null,
                'forbidden_words' => $request->forbidden_words ?? ($agentConfig['topics_to_avoid'] ?? ($brandSettings ? $brandSettings->forbidden_words : null)) ?? '',
                'focus_keywords' => $agentConfig && isset($agentConfig['focus_keywords']) ? implode(', ', $agentConfig['focus_keywords']) : '',
                'task_description' => "Create a comprehensive {$validated['days']}-day content strategy. Analyze the target audience demographics, industry trends, and typical social media behavior patterns to determine: 1) The optimal posting times for maximum engagement, 2) The best content types (image, video, carousel, text, etc.) for this specific audience and industry, 3) The ideal posting frequency. Base your recommendations on market research and audience behavior analysis.",
                // Analytics fields required by StrategistAgentService
                'follower_count' => $followerCount,
                'engagement_rate' => $engagementRate,
                'top_post_types' => $topPostTypes,
                'peak_times' => $peakTimes,
            ];

            $strategy = $this->strategist->createStrategy($context, $aiModel);
            
            // Track usage (1 post counted for strategy generation)
            $this->usageService->trackPost(Auth::id());
            
            // Save strategy to database
            $strategyRecord = \App\Models\Strategy::create([
                'user_id' => Auth::id(),
                'title' => "{$context['brand_name']} - {$validated['days']}-Day Strategy",
                'days' => $validated['days'],
                'content_calendar' => $strategy['content_calendar'] ?? [],
                'strategic_recommendations' => $strategy['strategic_recommendations'] ?? [],
                'brand_context' => $context,
                'status' => 'draft',
                'content_total' => count($strategy['content_calendar'] ?? []),
            ]);

            return response()->json([
                'success' => true,
                'strategy' => $strategy,
                'strategy_id' => $strategyRecord->id,
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
            
            // Show detailed error in development mode
            $errorMessage = config('app.debug') 
                ? $e->getMessage() 
                : 'Failed to generate strategy. Please check your OpenAI API key.';
            
            return response()->json([
                'success' => false,
                'error' => $errorMessage,
            ], 500);
        }
    }

    /**
     * Show content creator
     */
    public function contentForm()
    {
        $user = Auth::user();
        $brandSettings = $user->brandSetting;
        
        // Get marketing agent configuration if exists
        $agentConfig = AgentConfiguration::where('user_id', $user->id)
            ->where('agent_code', 'marketing')
            ->where('is_complete', true)
            ->first();
            
        return view('marketing-studio.content', compact('agentConfig', 'brandSettings'));
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
            'agent_config' => 'nullable|array',
        ]);

        try {
            // Get agent configuration if passed
            $agentConfig = $validated['agent_config'] ?? null;
            
            // Decode JSON strings if they exist in agent config
            if ($agentConfig) {
                if (isset($agentConfig['target_audience']) && is_string($agentConfig['target_audience'])) {
                    $agentConfig['target_audience'] = json_decode($agentConfig['target_audience'], true);
                }
                if (isset($agentConfig['focus_keywords']) && is_string($agentConfig['focus_keywords'])) {
                    $agentConfig['focus_keywords'] = json_decode($agentConfig['focus_keywords'], true);
                }
            }
            
            // Get brand settings for language and image preferences
            $brandSettings = Auth::user()->brandSetting;
            $preferredLanguage = $brandSettings ? $brandSettings->preferred_language : 'en';
            $preferredLanguage = $preferredLanguage ?? 'en';
            $languageName = $preferredLanguage === 'ar' ? 'Arabic' : ($preferredLanguage === 'en' ? 'English' : 'the specified language');
            
            // Get agent type for model configuration
            $agentType = \App\Models\AgentType::where('code', 'marketing')->first();
            $copywritingModel = $agentType ? ($agentType->model_config['copywriting_model'] ?? $agentType->ai_model) : 'gpt-4o-mini';
            $creativeModel = $agentType ? ($agentType->model_config['creative_model'] ?? $agentType->ai_model) : 'gpt-4o-mini';
            
            // Generate caption with agent config context
            $captionContext = [
                'brand_name' => $validated['brand_name'],
                'brand_tone' => $validated['tone'],
                'voice_characteristics' => $agentConfig['brand_personality'] ?? "{$validated['tone']}, engaging, authentic",
                'target_audience' => $validated['target_audience'],
                'key_messages' => $agentConfig['unique_value_proposition'] ?? $validated['topic'],
                'pain_points' => $agentConfig['pain_points'] ?? '',
                'products_services' => $agentConfig['products_services'] ?? '',
                'forbidden_words' => $agentConfig['topics_to_avoid'] ?? $request->forbidden_words ?? '',
                'focus_keywords' => $agentConfig && isset($agentConfig['focus_keywords']) ? implode(', ', $agentConfig['focus_keywords']) : '',
                'preferred_language' => $preferredLanguage,
                'max_length' => 2200,
                'required_elements' => 'hashtags, CTA',
                'cta_required' => true,
                'task_description' => "Write an engaging Facebook post about: {$validated['topic']}. IMPORTANT: Write the entire post in {$languageName} language. All text, hashtags, and call-to-action must be in {$languageName}.",
            ];

            $captionResult = $this->copywriter->generateCaption($captionContext, $copywritingModel);

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
                    'primary_colors' => $brandSettings && $brandSettings->primary_colors ? $brandSettings->primary_colors : '#1877F2, #42B72A, #FFFFFF',
                    'visual_style' => $brandSettings && $brandSettings->image_style ? $brandSettings->image_style : ($brandSettings && $brandSettings->visual_style ? $brandSettings->visual_style : 'modern, clean, professional'),
                    'logo_usage' => $brandSettings && $brandSettings->logo_in_images ? 'include' : 'optional',
                    'image_mood' => $brandSettings && $brandSettings->image_mood ? $brandSettings->image_mood : $validated['tone'],
                    'image_composition' => $brandSettings && $brandSettings->image_composition ? $brandSettings->image_composition : 'dynamic and eye-catching',
                    'preferred_elements' => $brandSettings && $brandSettings->preferred_elements ? $brandSettings->preferred_elements : '',
                    'avoid_elements' => $brandSettings && $brandSettings->avoid_elements ? $brandSettings->avoid_elements : '',
                    'text_in_image' => $brandSettings && $brandSettings->text_in_images ? $brandSettings->text_in_images : 'minimal',
                    'aspect_ratio' => $brandSettings && $brandSettings->image_aspect_ratio ? $brandSettings->image_aspect_ratio : '1:1',
                    'post_caption' => $result['caption'],
                    'post_objective' => 'engagement',
                    'target_audience' => $validated['target_audience'],
                    'task_description' => "Create a compelling " . ($brandSettings && $brandSettings->image_aspect_ratio ? $brandSettings->image_aspect_ratio : '1:1') . " image for: {$validated['topic']}. Style: " . ($brandSettings && $brandSettings->image_style ? $brandSettings->image_style : 'professional') . ". Mood: " . ($brandSettings && $brandSettings->image_mood ? $brandSettings->image_mood : 'engaging') . ".",
                ];

                try {
                    $result['image_url'] = $this->creative->generateImage($imageContext, $creativeModel);
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
            'strategy_id' => 'nullable|exists:strategies,id',
        ]);

        try {
            $results = $this->bulkGenerator->generateFromStrategy(
                Auth::id(),
                $validated['strategy_calendar'],
                $validated['brand_context'],
                $validated['strategy_id'] ?? null
            );

            $successCount = collect($results)->where('status', 'success')->count();
            $failedCount = collect($results)->where('status', 'failed')->count();
            
            // Update strategy status if provided
            if (isset($validated['strategy_id'])) {
                $strategy = \App\Models\Strategy::find($validated['strategy_id']);
                if ($strategy) {
                    $strategy->update([
                        'content_generated' => $successCount,
                        'status' => $successCount > 0 ? 'completed' : 'draft',
                    ]);
                }
            }

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
    
    /**
     * Delete content
     */
    public function deleteContent($id)
    {
        try {
            $content = \App\Models\Content::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();
            
            $content->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Content deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete content',
            ], 500);
        }
    }
    
    /**
     * Update content
     */
    public function updateContent(Request $request, $id)
    {
        $validated = $request->validate([
            'caption' => 'required|string',
            'image_url' => 'nullable|string',
        ]);

        try {
            $content = \App\Models\Content::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();
            
            $content->update([
                'caption' => $validated['caption'],
                'image_url' => $validated['image_url'] ?? $content->image_url,
            ]);
            
            return response()->json([
                'success' => true,
                'content' => $content,
                'message' => 'Content updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update content',
            ], 500);
        }
    }
}
