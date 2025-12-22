<?php

namespace App\Services\Marketing\APIs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenAIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-4-turbo-preview');
    }

    /**
     * Generate completion with system and user messages
     */
    public function chat(string $systemPrompt, string $userMessage, array $options = []): array
    {
        try {
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ];

            $payload = array_merge([
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ], $options);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post("{$this->baseUrl}/chat/completions", $payload);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API error: ' . $response->body());
            }

            $data = $response->json();

            return [
                'success' => true,
                'content' => $data['choices'][0]['message']['content'] ?? '',
                'usage' => $data['usage'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate JSON response (for structured outputs)
     */
    public function chatJson(string $systemPrompt, string $userMessage, array $options = []): array
    {
        $options['response_format'] = ['type' => 'json_object'];
        
        $result = $this->chat($systemPrompt, $userMessage, $options);

        if ($result['success']) {
            try {
                $result['data'] = json_decode($result['content'], true);
            } catch (\Exception $e) {
                $result['success'] = false;
                $result['error'] = 'Failed to parse JSON response';
            }
        }

        return $result;
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
