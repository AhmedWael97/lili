<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AI\StrategistAgentService;
use App\Services\AI\CopywriterAgentService;
use App\Services\AI\CreativeAgentService;
use App\Services\AI\CommunityManagerAgentService;
use App\Services\AI\AdsAgentService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /**
     * Generate content strategy
     */
    public function generateStrategy(Request $request, StrategistAgentService $strategist)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string',
            'industry' => 'required|string',
            'target_audience' => 'required|string',
            'business_goals' => 'required|string',
            'days' => 'integer|min:1|max:30',
        ]);

        try {
            $context = array_merge($validated, [
                'brand_tone' => $request->brand_tone ?? 'professional',
                'follower_count' => $request->follower_count ?? 0,
                'engagement_rate' => $request->engagement_rate ?? '0',
                'top_post_types' => $request->top_post_types ?? 'image, video',
                'peak_times' => $request->peak_times ?? '9 AM, 1 PM, 7 PM',
                'forbidden_words' => $request->forbidden_words ?? '',
                'task_description' => 'Create a strategic content plan',
            ]);

            $strategy = $strategist->generateContentCalendar($context, $validated['days'] ?? 7);

            return response()->json([
                'success' => true,
                'data' => $strategy,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate strategy: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate post caption
     */
    public function generateCaption(Request $request, CopywriterAgentService $copywriter)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string',
            'topic' => 'required|string',
            'tone' => 'required|in:professional,casual,friendly,authoritative',
            'target_audience' => 'required|string',
        ]);

        try {
            $context = [
                'brand_name' => $validated['brand_name'],
                'brand_tone' => $validated['tone'],
                'voice_characteristics' => "{$validated['tone']}, engaging",
                'target_audience' => $validated['target_audience'],
                'key_messages' => $validated['topic'],
                'forbidden_words' => $request->forbidden_words ?? '',
                'max_length' => 2200,
                'required_elements' => 'hashtags, CTA',
                'cta_required' => true,
                'task_description' => "Write a post about: {$validated['topic']}",
            ];

            $result = $copywriter->generateCaption($context);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate caption: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate image
     */
    public function generateImage(Request $request, CreativeAgentService $creative)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string',
            'topic' => 'required|string',
            'visual_style' => 'string',
        ]);

        try {
            $context = [
                'brand_name' => $validated['brand_name'],
                'primary_colors' => $request->primary_colors ?? '#1877F2, #42B72A',
                'visual_style' => $validated['visual_style'] ?? 'modern, clean',
                'logo_usage' => 'optional',
                'image_mood' => $request->tone ?? 'professional',
                'post_caption' => $validated['topic'],
                'post_objective' => 'engagement',
                'target_audience' => $request->target_audience ?? 'general audience',
                'text_allowed' => 'minimal',
                'task_description' => "Create an image for: {$validated['topic']}",
            ];

            $imageUrl = $creative->generateImage($context);

            return response()->json([
                'success' => true,
                'data' => ['image_url' => $imageUrl],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate comment reply
     */
    public function generateReply(Request $request, CommunityManagerAgentService $communityManager)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string',
            'comment_text' => 'required|string',
            'original_post' => 'required|string',
        ]);

        try {
            $context = [
                'brand_name' => $validated['brand_name'],
                'response_tone' => $request->tone ?? 'friendly',
                'response_length' => 'short',
                'emoji_usage' => 'moderate',
                'comment_text' => $validated['comment_text'],
                'original_post' => $validated['original_post'],
                'commenter_name' => $request->commenter_name ?? 'User',
                'sentiment' => $request->sentiment ?? 'neutral',
                'previous_interactions' => $request->previous_interactions ?? 'none',
                'product_info' => $request->product_info ?? '',
                'faqs' => $request->faqs ?? '',
                'promotions' => $request->promotions ?? '',
            ];

            $result = $communityManager->generateReply($context);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate reply: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate ad campaign
     */
    public function generateCampaign(Request $request, AdsAgentService $adsAgent)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string',
            'campaign_objective' => 'required|string',
            'budget' => 'required|numeric|min:1',
            'duration' => 'required|string',
            'target_audience' => 'required|string',
            'product_service' => 'required|string',
        ]);

        try {
            $context = array_merge($validated, [
                'usp' => $request->usp ?? '',
                'previous_ctr' => $request->previous_ctr ?? '0',
                'previous_cpc' => $request->previous_cpc ?? '0',
                'previous_roas' => $request->previous_roas ?? '0',
                'top_ad_types' => $request->top_ad_types ?? 'image, video',
                'task_description' => 'Create a comprehensive ad campaign',
            ]);

            $campaign = $adsAgent->generateCampaign($context);

            return response()->json([
                'success' => true,
                'data' => $campaign,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate campaign: ' . $e->getMessage(),
            ], 500);
        }
    }
}
