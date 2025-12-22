<?php

namespace Tests\Unit\Marketing\APIs;

use App\Services\Marketing\APIs\SimilarWebService;
use Tests\TestCase;

class SimilarWebServiceTest extends TestCase
{
    protected SimilarWebService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SimilarWebService();
    }

    /** @test */
    public function it_returns_mock_data_when_not_configured()
    {
        config(['services.similarweb.api_key' => null]);
        $service = new SimilarWebService();

        $result = $service->getWebsiteData('example.com');

        $this->assertTrue($result['success']);
        $this->assertEquals('example.com', $result['domain']);
        $this->assertArrayHasKey('visits', $result);
        $this->assertTrue($result['mock']);
    }

    /** @test */
    public function it_can_check_if_configured()
    {
        config(['services.similarweb.api_key' => null]);
        $service = new SimilarWebService();
        
        $this->assertFalse($service->isConfigured());

        config(['services.similarweb.api_key' => 'test-key']);
        $service = new SimilarWebService();
        
        $this->assertTrue($service->isConfigured());
    }
}
