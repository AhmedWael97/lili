<?php

namespace App\Services\AI;

class CommunityManagerAgentService
{
    public function __construct(
        protected OpenAIService $openAI
    ) {}

    /**
     * Generate comment reply
     */
    public function generateReply(array $context): array
    {
        $systemPrompt = $this->getSystemPrompt();
        $userPrompt = $this->buildPrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt);
    }

    /**
     * Check if needs escalation
     */
    public function shouldEscalate(array $context): bool
    {
        $result = $this->generateReply($context);
        return $result['should_escalate'] ?? false;
    }

    /**
     * Get system prompt
     */
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are a Community Manager AI Agent responsible for engaging with the audience in a helpful, authentic way.

Your role is to:
1. Respond to comments on posts with relevant, engaging replies
2. Answer questions about products/services accurately
3. Handle complaints with empathy and professionalism
4. Foster positive community interactions
5. Identify urgent issues that need human escalation
6. Maintain brand voice in all interactions
7. Encourage further engagement

⚠️ ESCALATION RULES:
- Refund requests → Escalate to human
- Legal threats → Escalate immediately
- Racist/hateful content → Report and escalate
- Complex technical issues → Escalate to support
- Repeatedly dissatisfied customer → Escalate

Always respond in JSON format with reply text and escalation status.
PROMPT;
    }

    /**
     * Build user prompt
     */
    protected function buildPrompt(array $context): string
    {
        return <<<PROMPT
Brand Communication Guidelines:
- Brand Name: {$context['brand_name']}
- Response Tone: {$context['response_tone']}
- Response Length: {$context['response_length']}
- Emoji Usage: {$context['emoji_usage']}

Comment Context:
- Original Post: {$context['original_post']}
- Comment Text: {$context['comment_text']}
- Commenter Name: {$context['commenter_name']}
- Comment Sentiment: {$context['sentiment']}
- Previous Interactions: {$context['previous_interactions']}

Knowledge Base:
- Product Info: {$context['product_info']}
- FAQs: {$context['faqs']}
- Current Promotions: {$context['promotions']}

Generate appropriate reply in JSON format with:
- reply_text (the actual reply)
- should_escalate (boolean)
- escalation_reason (if applicable)
- sentiment_tone
- follow_up_action
PROMPT;
    }
}
