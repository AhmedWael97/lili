<?php

namespace App\Agents\Base;

use App\Services\AI\OpenAIService;
use App\Services\AgentInteractionService;

/**
 * Base Agent Class
 * All specialized agents (Marketing, QA, Developer, etc.) extend this
 */
abstract class BaseAgent
{
    protected string $agentType;
    protected string $agentName;
    protected int $experienceYears = 20;
    
    public function __construct(
        protected OpenAIService $openAI,
        protected AgentInteractionService $interactionService
    ) {}

    /**
     * Get agent system prompt (each agent overrides this)
     */
    abstract protected function getSystemPrompt(): string;

    /**
     * Build user prompt with context
     */
    abstract protected function buildPrompt(array $context): string;

    /**
     * Execute agent task
     */
    public function execute(array $context): array
    {
        $startTime = microtime(true);
        $user = auth()->user();
        
        try {
            $systemPrompt = $this->getSystemPrompt();
            $userPrompt = $this->buildPrompt($context);

            $response = $this->openAI->generateJSON($userPrompt, $systemPrompt);
            
            $executionTimeMs = (int) ((microtime(true) - $startTime) * 1000);
            
            // Log successful interaction
            if ($user) {
                $this->interactionService->logInteraction(
                    user: $user,
                    agentCode: $this->agentType,
                    action: $context['action'] ?? 'execute',
                    inputData: [
                        'context' => $context,
                        'system_prompt' => substr($systemPrompt, 0, 500), // First 500 chars
                        'user_prompt' => substr($userPrompt, 0, 500),
                    ],
                    outputData: $response,
                    tokensUsed: $response['usage']['total_tokens'] ?? 0,
                    executionTimeMs: $executionTimeMs,
                    success: true,
                    metadata: $this->getMetadata()
                );
            }
            
            return $response;
            
        } catch (\Exception $e) {
            $executionTimeMs = (int) ((microtime(true) - $startTime) * 1000);
            
            // Log failed interaction
            if ($user) {
                $this->interactionService->logInteraction(
                    user: $user,
                    agentCode: $this->agentType,
                    action: $context['action'] ?? 'execute',
                    inputData: [
                        'context' => $context,
                    ],
                    outputData: [],
                    tokensUsed: 0,
                    executionTimeMs: $executionTimeMs,
                    success: false,
                    errorMessage: $e->getMessage(),
                    metadata: $this->getMetadata()
                );
            }
            
            throw $e;
        }
    }

    /**
     * Get agent metadata
     */
    public function getMetadata(): array
    {
        return [
            'type' => $this->agentType,
            'name' => $this->agentName,
            'experience_years' => $this->experienceYears,
        ];
    }

    /**
     * Get language name from code
     */
    protected function getLanguageName(string $code): string
    {
        $languages = [
            'en' => 'English',
            'ar' => 'Arabic',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
        ];

        return $languages[$code] ?? 'English';
    }
}
