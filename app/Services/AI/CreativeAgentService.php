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
    public function generateImagePrompt(array $context): string
    {
        $systemPrompt = $this->getSystemPrompt();
        $userPrompt = $this->buildPrompt($context);

        $result = $this->openAI->generateJSON($userPrompt, $systemPrompt);
        
        return $result['dalle_prompt'] ?? '';
    }

    /**
     * Generate image directly
     */
    public function generateImage(array $context): string
    {
        $prompt = $this->generateImagePrompt($context);
        
        return $this->openAI->generateImage($prompt, [
            'size' => '1024x1024',
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
        return <<<PROMPT
Brand Visual Guidelines:
- Brand Name: {$context['brand_name']}
- Primary Colors: {$context['primary_colors']}
- Visual Style: {$context['visual_style']}
- Logo Usage: {$context['logo_usage']}
- Image Mood: {$context['image_mood']}

Content Context:
- Post Caption: {$context['post_caption']}
- Post Objective: {$context['post_objective']}
- Target Audience: {$context['target_audience']}

Technical Requirements:
- Platform: Facebook (recommended 1200x630px)
- Format: PNG/JPEG
- Text in Image: {$context['text_allowed']}

Task: {$context['task_description']}

Generate DALL-E prompt in JSON format with:
- dalle_prompt (detailed image generation prompt)
- alternative_prompts (array of 2-3 alternatives)
- recommended_dimensions
- accessibility_notes
- brand_alignment
PROMPT;
    }
}
