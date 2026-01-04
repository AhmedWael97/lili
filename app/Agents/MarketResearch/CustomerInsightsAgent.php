<?php

namespace App\Agents\MarketResearch;

use App\Agents\Base\BaseAgent;
use App\Models\CustomerInsight;
use App\Models\ResearchRequest;

class CustomerInsightsAgent extends BaseAgent
{
    protected string $name = 'CustomerInsightsAgent';
    protected string $description = 'Analyzes reviews and forums to generate customer insights and personas';

    /**
     * Generate customer insights
     *
     * @param ResearchRequest $request
     * @return CustomerInsight
     */
    public function execute(...$params): CustomerInsight
    {
        /** @var ResearchRequest $request */
        $request = $params[0];

        $this->log("Generating customer insights for {$request->business_idea}");

        // Gather all review and forum data
        $reviewData = $this->gatherReviewData($request);
        $forumData = $this->gatherForumData($request);

        // Analyze with GPT-4
        $insights = $this->analyzeCustomerData($request, $reviewData, $forumData);

        // Create customer insights record
        $customerInsight = CustomerInsight::create([
            'research_request_id' => $request->id,
            'customer_personas' => $insights['customer_personas'] ?? [],
            'pain_points' => $insights['pain_points'] ?? [],
            'needs' => $insights['needs'] ?? [],
            'feature_requests' => $insights['feature_requests'] ?? [],
            'buying_factors' => $insights['buying_factors'] ?? [],
            'satisfaction_drivers' => $insights['satisfaction_drivers'] ?? [],
            'common_complaints' => $insights['common_complaints'] ?? [],
            'purchase_decision_process' => $insights['purchase_decision_process'] ?? [],
            'marketing_channels' => $insights['marketing_channels'] ?? [],
            'sentiment_summary' => $insights['sentiment_summary'] ?? '',
        ]);

        $this->log("Customer insights generated");

        return $customerInsight;
    }

    /**
     * Gather review data from all competitors
     *
     * @param ResearchRequest $request
     * @return array
     */
    private function gatherReviewData(ResearchRequest $request): array
    {
        $reviews = [];

        foreach ($request->competitors as $competitor) {
            foreach ($competitor->reviews as $review) {
                $reviews[] = [
                    'competitor' => $competitor->name,
                    'rating' => $review->rating,
                    'text' => $review->review_text,
                    'pros' => $review->pros,
                    'cons' => $review->cons,
                    'pain_points' => $review->pain_points,
                    'praise_points' => $review->praise_points,
                ];
            }
        }

        return $reviews;
    }

    /**
     * Gather forum discussion data
     *
     * @param ResearchRequest $request
     * @return array
     */
    private function gatherForumData(ResearchRequest $request): array
    {
        $discussions = [];

        foreach ($request->forumDiscussions as $discussion) {
            $discussions[] = [
                'source' => $discussion->source,
                'title' => $discussion->title,
                'content' => $discussion->content,
                'pain_points' => $discussion->pain_points,
                'feature_requests' => $discussion->feature_requests,
            ];
        }

        return $discussions;
    }

    /**
     * Analyze customer data with GPT-4
     *
     * @param ResearchRequest $request
     * @param array $reviewData
     * @param array $forumData
     * @return array
     */
    private function analyzeCustomerData(ResearchRequest $request, array $reviewData, array $forumData): array
    {
        // Summarize data for GPT-4 (limit size)
        $reviewSummary = $this->summarizeReviews($reviewData);
        $forumSummary = $this->summarizeForums($forumData);

        $prompt = "
Analyze customer insights for this business:

Business Idea: {$request->business_idea}
Location: {$request->location}

Review Data Summary:
{$reviewSummary}

Forum Discussion Summary:
{$forumSummary}

Based on this data, provide comprehensive customer insights:

1. Customer Personas (3-4 distinct personas with demographics, psychographics, goals, pain points)
2. Top Pain Points (ranked by frequency/severity)
3. Customer Needs (what they're looking for)
4. Feature Requests (what they want in a solution)
5. Buying Factors (what influences their purchase decision)
6. Satisfaction Drivers (what makes them happy)
7. Common Complaints (recurring issues)
8. Purchase Decision Process (who discovers, evaluates, approves, typical timeframe)
9. Marketing Channels (where they search, communities they join)
10. Sentiment Summary (overall sentiment and themes)

Return JSON:
{
  \"customer_personas\": [
    {
      \"name\": \"Persona name\",
      \"demographics\": \"Age, income, location, role\",
      \"psychographics\": \"Interests, values, lifestyle\",
      \"goals\": [\"goal 1\", \"goal 2\"],
      \"pain_points\": [\"pain 1\", \"pain 2\"],
      \"preferred_channels\": [\"channel 1\", \"channel 2\"]
    }
  ],
  \"pain_points\": [
    {
      \"pain_point\": \"Description\",
      \"frequency\": \"High/Medium/Low\",
      \"impact\": \"Description of impact\"
    }
  ],
  \"needs\": [\"need 1\", \"need 2\"],
  \"feature_requests\": [
    {
      \"feature\": \"Feature name\",
      \"demand\": \"High/Medium/Low\",
      \"description\": \"Details\"
    }
  ],
  \"buying_factors\": [
    {
      \"factor\": \"Factor name\",
      \"importance\": \"High/Medium/Low\",
      \"description\": \"Why it matters\"
    }
  ],
  \"satisfaction_drivers\": [\"driver 1\", \"driver 2\"],
  \"common_complaints\": [
    {
      \"complaint\": \"Issue\",
      \"frequency\": \"High/Medium/Low\"
    }
  ],
  \"purchase_decision_process\": {
    \"discovery\": \"How they find solutions\",
    \"evaluation\": \"How they compare options\",
    \"decision_makers\": \"Who approves purchase\",
    \"timeframe\": \"Typical buying cycle\"
  },
  \"marketing_channels\": [
    {
      \"channel\": \"Channel name\",
      \"effectiveness\": \"High/Medium/Low\",
      \"usage\": \"How they use it\"
    }
  ],
  \"sentiment_summary\": \"2-3 paragraph summary of overall customer sentiment and key themes\"
}
";

        $response = $this->callGPTJson($prompt, 'gpt-4o', 4000);

        return $response ?? [];
    }

    /**
     * Summarize reviews for GPT-4
     *
     * @param array $reviews
     * @return string
     */
    private function summarizeReviews(array $reviews): string
    {
        if (empty($reviews)) {
            return "No review data available.";
        }

        $summary = "Total Reviews: " . count($reviews) . "\n\n";

        // Sample reviews
        $sampleSize = min(10, count($reviews));
        $sample = array_slice($reviews, 0, $sampleSize);

        foreach ($sample as $index => $review) {
            $summary .= "Review " . ($index + 1) . ":\n";
            $summary .= "Competitor: {$review['competitor']}\n";
            $summary .= "Rating: {$review['rating']}/5\n";
            $summary .= "Pros: {$review['pros']}\n";
            $summary .= "Cons: {$review['cons']}\n";
            $summary .= "Pain Points: " . json_encode($review['pain_points']) . "\n\n";
        }

        return substr($summary, 0, 3000);
    }

    /**
     * Summarize forum discussions for GPT-4
     *
     * @param array $discussions
     * @return string
     */
    private function summarizeForums(array $discussions): string
    {
        if (empty($discussions)) {
            return "No forum data available.";
        }

        $summary = "Total Discussions: " . count($discussions) . "\n\n";

        // Sample discussions
        $sampleSize = min(5, count($discussions));
        $sample = array_slice($discussions, 0, $sampleSize);

        foreach ($sample as $index => $disc) {
            $summary .= "Discussion " . ($index + 1) . ":\n";
            $summary .= "Source: {$disc['source']}\n";
            $summary .= "Title: {$disc['title']}\n";
            $summary .= "Pain Points: " . json_encode($disc['pain_points']) . "\n";
            $summary .= "Feature Requests: " . json_encode($disc['feature_requests']) . "\n\n";
        }

        return substr($summary, 0, 2000);
    }
}
