<?php

namespace Tests\Unit\Marketing\APIs;

use App\Services\Marketing\APIs\OpenAIService;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class OpenAIServiceTest extends TestCase
{
    protected OpenAIService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OpenAIService();
    }

    /** @test */
    public function it_can_check_if_configured()
    {
        config(['services.openai.api_key' => 'test-key']);
        $service = new OpenAIService();
        
        $this->assertTrue($service->isConfigured());
    }

    /** @test */
    public function it_returns_error_when_api_fails()
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['error' => 'API error'], 500),
        ]);

        $result = $this->service->chat('System prompt', 'User message');

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    /** @test */
    public function it_can_parse_json_response()
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"test": "value"}']]
                ],
                'usage' => ['total_tokens' => 100]
            ], 200),
        ]);

        $result = $this->service->chatJson('System', 'User');

        $this->assertTrue($result['success']);
        $this->assertEquals(['test' => 'value'], $result['data']);
    }
}
