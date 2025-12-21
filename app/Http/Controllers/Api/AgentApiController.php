<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\StrategistAgentService;
use App\Services\AI\CopywriterAgentService;
use App\Services\AI\CreativeAgentService;
use App\Services\AI\CommunityManagerAgentService;
use App\Services\AI\AdsAgentService;
use App\Repositories\BrandSettingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentApiController extends Controller
{
    public function __construct(
        protected StrategistAgentService $strategist,
        protected CopywriterAgentService $copywriter,
        protected CreativeAgentService $creative,
        protected CommunityManagerAgentService $communityManager,
        protected AdsAgentService $ads,
        protected BrandSettingRepository $brandRepo
    ) {}

    /**
     * Generate content strategy
     */
    public function strategy(Request $request)
    {
        $validated = $request->validate([
            'task_description' => 'required|string',
            'follower_count' => 'nullable|integer',
            'engagement_rate' => 'nullable|numeric',
        ]);

        $context = array_merge(
            $this->brandRepo->getAIContext(Auth::id()),
            $validated
        );

        $result = $this->strategist->createStrategy($context);

        return response()->json($result);
    }

    /**
     * Generate caption
     */
    public function caption(Request $request)
    {
        $validated = $request->validate([
            'task_description' => 'required|string',
            'max_length' => 'nullable|integer|max:2200',
            'required_elements' => 'nullable|string',
            'cta_required' => 'nullable|boolean',
        ]);

        $context = array_merge(
            $this->brandRepo->getAIContext(Auth::id()),
            $validated
        );

        $result = $this->copywriter->generateCaption($context);

        return response()->json($result);
    }

    /**
     * Generate image prompt
     */
    public function imagePrompt(Request $request)
    {
        $validated = $request->validate([
            'post_caption' => 'required|string',
            'post_objective' => 'required|string',
            'image_mood' => 'nullable|string',
        ]);

        $context = array_merge(
            $this->brandRepo->getAIContext(Auth::id()),
            $validated,
            ['task_description' => 'Generate image for social media post']
        );

        $prompt = $this->creative->generateImagePrompt($context);

        return response()->json(['dalle_prompt' => $prompt]);
    }

    /**
     * Generate image
     */
    public function image(Request $request)
    {
        $validated = $request->validate([
            'post_caption' => 'required|string',
            'post_objective' => 'required|string',
            'image_mood' => 'nullable|string',
        ]);

        $context = array_merge(
            $this->brandRepo->getAIContext(Auth::id()),
            $validated,
            ['task_description' => 'Generate image for social media post']
        );

        $imageUrl = $this->creative->generateImage($context);

        return response()->json(['image_url' => $imageUrl]);
    }

    /**
     * Generate comment reply
     */
    public function commentReply(Request $request)
    {
        $validated = $request->validate([
            'original_post' => 'required|string',
            'comment_text' => 'required|string',
            'commenter_name' => 'required|string',
            'sentiment' => 'nullable|in:positive,neutral,negative',
        ]);

        $context = array_merge(
            $this->brandRepo->getAIContext(Auth::id()),
            $validated,
            [
                'response_tone' => 'friendly',
                'response_length' => 'concise',
                'emoji_usage' => 'moderate',
                'previous_interactions' => 'none',
                'product_info' => '',
                'faqs' => '',
                'promotions' => '',
            ]
        );

        $result = $this->communityManager->generateReply($context);

        return response()->json($result);
    }

    /**
     * Generate ad campaign
     */
    public function adCampaign(Request $request)
    {
        $validated = $request->validate([
            'campaign_objective' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'duration' => 'required|string',
            'product_service' => 'required|string',
            'usp' => 'required|string',
        ]);

        $context = array_merge(
            $this->brandRepo->getAIContext(Auth::id()),
            $validated,
            [
                'task_description' => 'Create Facebook ad campaign',
                'previous_ctr' => '2.5',
                'previous_cpc' => '0.50',
                'previous_roas' => '3.0',
                'top_ad_types' => 'image, video',
            ]
        );

        $result = $this->ads->generateCampaign($context);

        return response()->json($result);
    }
}
