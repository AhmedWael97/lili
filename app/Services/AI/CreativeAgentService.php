<?php

namespace App\Services\AI;

class CreativeAgentService
{
    public function __construct(
        protected OpenAIService $openAI
    ) {}

    /**
     * Generate image prompt for DALL-E
     */
    public function generateImagePrompt(array $context, string $model = 'gpt-4o-mini'): string
    {
        $systemPrompt = $this->getSystemPrompt();
        $userPrompt = $this->buildPrompt($context);

        $result = $this->openAI->generateJSON($userPrompt, $systemPrompt, $model);
        
        return $result['dalle_prompt'] ?? '';
    }

    /**
     * Generate image directly
     */
    public function generateImage(array $context, string $model = 'gpt-4o-mini'): string
    {
        $prompt = $this->generateImagePrompt($context, $model);
        
        // Determine size based on aspect ratio
        $aspectRatio = $context['aspect_ratio'] ?? '1:1';
        $size = match($aspectRatio) {
            '16:9' => '1792x1024',
            '4:5' => '1024x1280',
            '9:16' => '1024x1792',
            default => '1024x1024',
        };
        
        return $this->openAI->generateImage($prompt, [
            'size' => $size,
            'quality' => 'standard',
        ]);
    }

    /**
     * Get system prompt
     */
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are a Creative AI Agent specializing in visual content generation for social media.

Your role is to:
1. Generate image prompts for DALL-E 3 that align with brand aesthetics
2. Ensure visual consistency with brand colors and style
3. Create attention-grabbing visuals optimized for Facebook
4. Consider accessibility (avoid text-heavy images)
5. Match visual tone with copy and brand voice
6. Suggest image dimensions and formats
7. Provide alternative visual concepts

Always respond in JSON format with DALL-E prompts and creative notes.
PROMPT;
    }

    /**
     * Build user prompt
     */
    protected function buildPrompt(array $context): string
    {
        $aspectRatio = $context['aspect_ratio'] ?? '1:1';
        $dimensions = match($aspectRatio) {
            '16:9' => '1792x1024',
            '4:5' => '1024x1280',
            '9:16' => '1024x1792',
            default => '1024x1024',
        };
        
        return <<<PROMPT
Brand Visual Guidelines:
- Brand Name: {$context['brand_name']}
- Primary Colors: {$context['primary_colors']}
- Visual Style: {$context['visual_style']}
- Logo Usage: {$context['logo_usage']}
- Image Mood: {$context['image_mood']}
- Composition: {$context['image_composition']}
- Preferred Elements: {$context['preferred_elements']}
- Avoid These Elements: {$context['avoid_elements']}

Content Context:
- Post Caption: {$context['post_caption']}
- Post Objective: {$context['post_objective']}
- Target Audience: {$context['target_audience']}

Technical Requirements:
- Platform: Facebook
- Format: PNG/JPEG
- Dimensions: {$dimensions}
- Text in Image: {$context['text_in_image']}

Task: {$context['task_description']}

Generate DALL-E prompt in JSON format with:
- dalle_prompt (detailed, descriptive image generation prompt - minimum 150 characters)
- alternative_prompts (array of 2-3 alternatives)
- recommended_dimensions
- accessibility_notes
- brand_alignment

Remember: DALL-E prompts should be very descriptive and detailed for best results.
PROMPT;
    }
}
