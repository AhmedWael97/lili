<?php

namespace Tests\Unit\Marketing\APIs;

use App\Services\Marketing\APIs\SEMrushService;
use Tests\TestCase;

class SEMrushServiceTest extends TestCase
{
    protected SEMrushService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SEMrushService();
    }

    /** @test */
    public function it_returns_mock_data_when_not_configured()
    {
        config(['services.semrush.api_key' => null]);
        $service = new SEMrushService();

        $result = $service->getDomainOverview('example.com');

        $this->assertTrue($result['success']);
        $this->assertEquals('example.com', $result['domain']);
        $this->assertArrayHasKey('organic_keywords', $result);
        $this->assertTrue($result['mock']);
    }

    /** @test */
    public function it_can_check_if_configured()
    {
        config(['services.semrush.api_key' => null]);
        $service = new SEMrushService();
        
        $this->assertFalse($service->isConfigured());

        config(['services.semrush.api_key' => 'test-key']);
        $service = new SEMrushService();
        
        $this->assertTrue($service->isConfigured());
    }
}
