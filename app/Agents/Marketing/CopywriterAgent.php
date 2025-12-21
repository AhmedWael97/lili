<?php

namespace App\Services\AI;

class CopywriterAgentService
{
    public function __construct(
        protected OpenAIService $openAI
    ) {}

    /**
     * Generate post caption
     */
    public function generateCaption(array $context): array
    {
        $systemPrompt = $this->getSystemPrompt();
        $userPrompt = $this->buildPrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt);
    }

    /**
     * Generate comment reply
     */
    public function generateCommentReply(array $context): string
    {
        $context['task_description'] = "Generate an appropriate reply to this comment";
        
        $result = $this->generateCaption($context);
        
        return $result['reply_text'] ?? $result['caption'] ?? '';
    }

    /**
     * Write copy from strategy calendar day
     */
    public function writeCopyFromStrategy(array $context): array
    {
        $context['task_description'] = "Write engaging Facebook post about: {$context['topic']}. Objective: {$context['objective']}";
        $context['voice_characteristics'] = $context['voice_characteristics'] ?? 'engaging, authentic';
        $context['key_messages'] = $context['key_messages'] ?? '';
        $context['forbidden_words'] = $context['forbidden_words'] ?? '';
        $context['required_elements'] = $context['include_hashtags'] ? 'hashtags' : '';
        $context['cta_required'] = $context['include_cta'] ?? true;
        
        return $this->generateCaption($context);
    }

    /**
     * Get system prompt
     */
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are an Expert Social Media Copywriter AI Agent specializing in creating engaging, conversion-focused content.

Your role is to:
1. Write compelling post captions that match the brand voice
2. Create attention-grabbing headlines and hooks
3. Write persuasive ad copy optimized for conversions
4. Craft engaging comment replies that foster community
5. Write message responses that are helpful and brand-aligned
6. Use appropriate emojis, hashtags, and calls-to-action
7. Optimize copy length for platform best practices

Always respond in JSON format with the generated copy and metadata.
PROMPT;
    }

    /**
     * Build user prompt
     */
    protected function buildPrompt(array $context): string
    {
        $language = $context['preferred_language'] ?? 'en';
        $languageNames = [
            'en' => 'English', 'ar' => 'Arabic', 'es' => 'Spanish', 'fr' => 'French',
            'de' => 'German', 'it' => 'Italian', 'pt' => 'Portuguese', 
            'zh' => 'Chinese', 'ja' => 'Japanese', 'ko' => 'Korean',
        ];
        $languageName = $languageNames[$language] ?? 'English';
        
        return <<<PROMPT
Brand Voice Guidelines:
- Brand Name: {$context['brand_name']}
- Tone: {$context['brand_tone']}
- Voice Characteristics: {$context['voice_characteristics']}
- Target Audience: {$context['target_audience']}
- Key Messages: {$context['key_messages']}
- Words to Avoid: {$context['forbidden_words']}

Content Requirements:
- Platform: Facebook
- Max Length: {$context['max_length']} characters
- Must Include: {$context['required_elements']}
- CTA Required: {$context['cta_required']}

Task: {$context['task_description']}

IMPORTANT: Generate ALL text in {$languageName} language only.

Generate copy in JSON format with:
- caption (the main text in {$languageName})
- character_count
- hashtags (array in {$languageName})
- cta (call to action in {$languageName})
- tone_analysis
PROMPT;
    }
}
