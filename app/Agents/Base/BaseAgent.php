<?php

namespace App\Agents\Base;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

abstract class BaseAgent
{
    protected string $name;
    protected string $description;

    /**
     * Execute the agent's main task
     *
     * @param mixed ...$params
     * @return mixed
     */
    abstract public function execute(...$params);

    /**
     * Call GPT-4 with a prompt
     *
     * @param string $prompt
     * @param string $model
     * @param int $maxTokens
     * @return string|null
     */
    protected function callGPT(string $prompt, string $model = 'gpt-4o', int $maxTokens = 4000): ?string
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert market research analyst. Provide detailed, actionable insights based on the data provided.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => $maxTokens,
                'temperature' => 0.7,
            ]);

            return $response['choices'][0]['message']['content'] ?? null;
        } catch (\Exception $e) {
            Log::error("GPT-4 API error in {$this->name}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Call GPT-4 and expect JSON response
     *
     * @param string $prompt
     * @param string $model
     * @param int $maxTokens
     * @return array|null
     */
    protected function callGPTJson(string $prompt, string $model = 'gpt-4o', int $maxTokens = 4000): ?array
    {
        try {
            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert market research analyst. Always respond with valid JSON only, no markdown or additional text.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => $maxTokens,
                'temperature' => 0.7,
                'response_format' => ['type' => 'json_object'],
            ]);

            $content = $response['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                return null;
            }

            return json_decode($content, true);
        } catch (\Exception $e) {
            Log::error("GPT-4 JSON API error in {$this->name}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Log agent progress
     *
     * @param string $message
     * @return void
     */
    protected function log(string $message): void
    {
        Log::info("[{$this->name}] {$message}");
    }

    /**
     * Get agent name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get agent description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
