<?php

namespace App\Services\AI;

use OpenAI;

class OpenAIService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('services.openai.api_key'));
    }

    /**
     * Generate text completion
     */
    public function generateText(string $prompt, array $options = []): string
    {
        $response = $this->client->chat()->create([
            'model' => $options['model'] ?? 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $options['system'] ?? 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $options['max_tokens'] ?? 2000,
            'temperature' => $options['temperature'] ?? 0.7,
        ]);

        return $response->choices[0]->message->content;
    }

    /**
     * Generate image with DALL-E
     */
    public function generateImage(string $prompt, array $options = []): string
    {
        $response = $this->client->images()->create([
            'model' => $options['model'] ?? 'dall-e-3',
            'prompt' => $prompt,
            'size' => $options['size'] ?? '1024x1024',
            'quality' => $options['quality'] ?? 'standard',
            'n' => 1,
        ]);

        return $response->data[0]->url;
    }

    /**
     * Parse JSON response
     */
    public function generateJSON(string $prompt, string $systemPrompt = '', string $model = 'gpt-4o'): array
    {
        $response = $this->client->chat()->create([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt ?: 'You are a helpful assistant that responds in JSON format.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        $content = $response->choices[0]->message->content;
        return json_decode($content, true);
    }
}
