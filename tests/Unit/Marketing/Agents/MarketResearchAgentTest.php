<?php

namespace Tests\Unit\Marketing\Agents;

use App\Models\Brand;
use App\Models\User;
use App\Services\Marketing\Agents\MarketResearchAgent;
use App\Services\Marketing\APIs\OpenAIService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class MarketResearchAgentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_analyze_market()
    {
        $mockOpenAI = Mockery::mock(OpenAIService::class);
        $mockOpenAI->shouldReceive('chatJson')
            ->once()
            ->andReturn([
                'success' => true,
                'data' => [
                    'market_overview' => 'Test overview',
                    'maturity_level' => 'growing',
                    'key_trends' => ['trend1', 'trend2'],
                ],
            ]);

        $agent = new MarketResearchAgent($mockOpenAI);

        $result = $agent->analyze([
            'industry' => 'Technology',
            'country' => 'US',
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('MarketResearchAgent', $result['agent']);
        $this->assertArrayHasKey('market_overview', $result['data']);
    }

    /** @test */
    public function it_handles_api_errors()
    {
        $mockOpenAI = Mockery::mock(OpenAIService::class);
        $mockOpenAI->shouldReceive('chatJson')
            ->once()
            ->andReturn([
                'success' => false,
                'error' => 'API error',
            ]);

        $agent = new MarketResearchAgent($mockOpenAI);

        $result = $agent->analyze([
            'industry' => 'Technology',
            'country' => 'US',
        ]);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }
}
