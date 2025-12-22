<?php

namespace App\Services\Marketing\Agents;

use App\Services\Marketing\APIs\OpenAIService;
use App\Services\Marketing\APIs\SimilarWebService;
use App\Services\Marketing\APIs\SEMrushService;
use App\Services\Marketing\APIs\AhrefsService;
use Illuminate\Support\Facades\Log;

/**
 * Competitor Intelligence Agent
 * Identifies and analyzes competitors
 */
class CompetitorIntelligenceAgent
{
    protected OpenAIService $openai;
    protected SimilarWebService $similarweb;
    protected SEMrushService $semrush;
    protected AhrefsService $ahrefs;

    protected string $systemPrompt = <<<'PROMPT'
You are a **Competitor Intelligence AI**.

Your task is to:
- Identify main competitors
- Analyze their positioning, messaging, pricing signals, and channels
- Detect strengths and weaknesses

You must analyze:
- SEO presence
- Content strategy
- Paid vs organic focus
- Value propositions

Rules:
- Use only publicly observable signals
- Do not guess private metrics
- Compare competitors relative to each other

Output in JSON format:
{
  "positioning": {"statement": "text", "differentiation": "text"},
  "messaging": {"key_messages": ["msg1", "msg2"], "tone": "description"},
  "channels": ["channel1", "channel2"],
  "strengths": ["strength1", "strength2"],
  "weaknesses": ["weakness1", "weakness2"],
  "content_strategy": {"types": ["blog", "video"], "frequency": "description", "themes": ["theme1"]},
  "pricing_signals": {"strategy": "premium|mid|budget", "notes": "observations"}
}
PROMPT;

    public function __construct(
        OpenAIService $openai,
        SimilarWebService $similarweb,
        SEMrushService $semrush,
        AhrefsService $ahrefs
    ) {
        $this->openai = $openai;
        $this->similarweb = $similarweb;
        $this->semrush = $semrush;
        $this->ahrefs = $ahrefs;
    }

    /**
     * Analyze competitor
     */
    public function analyze(array $params): array
    {
        try {
            $competitorName = $params['name'] ?? '';
            $website = $params['website'] ?? '';
            $industry = $params['industry'] ?? '';

            // Gather data from multiple sources
            $dataPoints = $this->gatherCompetitorData($website);

            // Use AI to analyze and structure the data
            $userMessage = $this->buildUserMessage($competitorName, $website, $industry, $dataPoints);

            $result = $this->openai->chatJson($this->systemPrompt, $userMessage, [
                'temperature' => 0.5,
            ]);

            if (!$result['success']) {
                return $this->getErrorResponse($result['error']);
            }

            // Merge AI analysis with gathered data
            $analysis = $result['data'];
            $analysis['seo_data'] = $dataPoints;

            return [
                'success' => true,
                'data' => $analysis,
                'agent' => 'CompetitorIntelligenceAgent',
            ];

        } catch (\Exception $e) {
            Log::error('CompetitorIntelligenceAgent error: ' . $e->getMessage());
            return $this->getErrorResponse($e->getMessage());
        }
    }

    /**
     * Gather data from APIs
     */
    protected function gatherCompetitorData(string $website): array
    {
        if (empty($website)) {
            return [];
        }

        $domain = $this->extractDomain($website);
        $data = [];

        // SimilarWeb data
        $similarwebData = $this->similarweb->getWebsiteData($domain);
        if ($similarwebData['success']) {
            $data['traffic'] = $similarwebData;
        }

        // SEMrush data
        $semrushData = $this->semrush->getDomainOverview($domain);
        if ($semrushData['success']) {
            $data['seo'] = $semrushData;
        }

        // Ahrefs data
        $ahrefsData = $this->ahrefs->getDomainMetrics($domain);
        if ($ahrefsData['success']) {
            $data['backlinks'] = $ahrefsData;
        }

        return $data;
    }

    /**
     * Build user message
     */
    protected function buildUserMessage(string $name, string $website, string $industry, array $dataPoints): string
    {
        $message = "Competitor Name: {$name}\n";
        $message .= "Website: {$website}\n";
        $message .= "Industry: {$industry}\n\n";

        if (!empty($dataPoints)) {
            $message .= "Data from analysis:\n";
            $message .= json_encode($dataPoints, JSON_PRETTY_PRINT) . "\n\n";
        }

        $message .= "Based on this information and your knowledge of the industry, provide a comprehensive competitor analysis.";

        return $message;
    }

    /**
     * Extract domain from URL
     */
    protected function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? $url;
    }

    protected function getErrorResponse(string $error): array
    {
        return [
            'success' => false,
            'error' => $error,
            'agent' => 'CompetitorIntelligenceAgent',
        ];
    }
}
