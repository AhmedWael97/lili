# Market Research & Business Intelligence Beta - Implementation Plan

## Product Overview

**What We're Building:**
A Market Research AI Agent system that helps entrepreneurs analyze their business idea by:
- Finding competitors automatically
- Analyzing their social media presence
- Providing market insights
- Generating actionable business strategy

**User Journey:**
```
User Input: "I want to start an organic bakery in Austin, TX"
        ↓
AI Research (2-3 minutes)
        ↓
Comprehensive Report:
- Top 10 Competitors
- Market Analysis
- Social Media Intelligence
- Pricing Strategy
- Actionable Recommendations
```

---

## Why This Approach Works for Beta

### ✅ No API Approvals Needed
- Public data only (Facebook, Instagram, Twitter)
- Google Search API (100 free queries/day)
- Web scraping (legal for public pages)
- OpenAI API (already have)

### ✅ Real Data Immediately
- No waiting for Meta approval
- No fake/mock data
- Actual competitor information
- Real market insights

### ✅ Fast to Build
- 2-4 weeks to working beta
- Core functionality first
- Polish later
- Start testing immediately

---

## System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    User Input                           │
│  "I want to start an organic bakery in Austin, TX"     │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│              AI Research Orchestrator                    │
│  (Coordinates all research agents)                       │
└─────────────────────────────────────────────────────────┘
                          ↓
        ┌─────────────────┼─────────────────┐
        ↓                 ↓                  ↓
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ Competitor   │  │   Market     │  │   Social     │
│ Finder Agent │  │ Analysis Agent│  │ Intel Agent  │
└──────────────┘  └──────────────┘  └──────────────┘
        ↓                 ↓                  ↓
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ Google Search│  │ Demographics │  │ FB/IG Public │
│ Bing Search  │  │ Trends       │  │ Twitter API  │
│ Web Scraping │  │ Census Data  │  │ Reviews      │
└──────────────┘  └──────────────┘  └──────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│              AI Report Generator                         │
│  Compiles findings into actionable report               │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                   User Report                            │
│  • Top 10 Competitors                                    │
│  • Market Size & Trends                                  │
│  • Customer Sentiment Analysis                           │
│  • Pricing Strategy Recommendations                      │
│  • Marketing Channel Analysis                            │
│  • Action Plan                                           │
└─────────────────────────────────────────────────────────┘
```

---

## Data Sources (All Accessible Without API Approval)

### 1. Public Social Media Data
- ✅ Facebook public pages (no login needed)
- ✅ Instagram public profiles
- ✅ Twitter/X public posts
- ✅ LinkedIn company pages
- ✅ TikTok public videos

### 2. Search Engines
- ✅ Google Custom Search API (100 free/day)
- ✅ Bing Search API
- ✅ DuckDuckGo (no API key)

### 3. Review Sites
- ✅ Google Reviews (via API)
- ✅ Yelp public data
- ✅ Trustpilot
- ✅ G2 (for B2B)

### 4. Business Directories
- ✅ Google My Business listings
- ✅ Yellow Pages
- ✅ Industry-specific directories

### 5. Web Scraping
- ✅ Competitor websites
- ✅ Pricing pages
- ✅ Product/service listings
- ✅ Blog content

### 6. Public APIs
- ✅ Census data (demographics)
- ✅ Economic indicators
- ✅ Industry trends

---

## Core AI Agents

### 1. Competitor Finder Agent

**Purpose:** Discover competitors automatically based on business idea and location

**How It Works:**
```php
class CompetitorFinderAgent {
    
    public function findCompetitors($businessIdea, $location) {
        // Step 1: Extract keywords from business idea
        $keywords = $this->extractKeywords($businessIdea);
        // Example: "organic bakery" → ["organic", "bakery", "gluten-free", "healthy"]
        
        // Step 2: Search Google
        $googleResults = $this->searchGoogle(
            "{$keywords} businesses in {$location}"
        );
        
        // Step 3: Find social media profiles
        $socialProfiles = $this->findSocialProfiles($googleResults);
        
        // Step 4: Rank by relevance
        return $this->rankCompetitors($socialProfiles);
    }
    
    private function searchGoogle($query) {
        // Use Google Custom Search API
        $client = new Client();
        $response = $client->get('https://www.googleapis.com/customsearch/v1', [
            'query' => [
                'key' => env('GOOGLE_API_KEY'),
                'cx' => env('GOOGLE_SEARCH_ENGINE_ID'),
                'q' => $query,
                'num' => 10
            ]
        ]);
        
        return json_decode($response->getBody(), true)['items'];
    }
    
    private function findSocialProfiles($googleResults) {
        $profiles = [];
        
        foreach ($googleResults as $result) {
            $website = $result['link'];
            
            // Scrape website for social media links
            $html = file_get_contents($website);
            
            // Find Facebook pages
            preg_match_all('/facebook\.com\/([a-zA-Z0-9\.]+)/', $html, $fbMatches);
            
            // Find Instagram
            preg_match_all('/instagram\.com\/([a-zA-Z0-9\._]+)/', $html, $igMatches);
            
            // Find Twitter
            preg_match_all('/twitter\.com\/([a-zA-Z0-9_]+)/', $html, $twMatches);
            
            $profiles[] = [
                'business_name' => $result['title'],
                'website' => $website,
                'facebook' => $fbMatches[1][0] ?? null,
                'instagram' => $igMatches[1][0] ?? null,
                'twitter' => $twMatches[1][0] ?? null,
            ];
        }
        
        return $profiles;
    }
}
```

**Data Collected:**
- Competitor name
- Website URL
- Social media profiles (FB, IG, Twitter, LinkedIn)
- Business category
- Location/address (if available)

---

### 2. Social Intelligence Agent

**Purpose:** Analyze competitors' social media presence and strategy

**How It Works:**
```php
class SocialIntelligenceAgent {
    
    public function analyzeSocialPresence($competitor) {
        $data = [];
        
        // Facebook public data
        if ($competitor['facebook']) {
            $data['facebook'] = $this->scrapeFacebookPublicPage(
                "https://facebook.com/{$competitor['facebook']}"
            );
        }
        
        // Instagram public data
        if ($competitor['instagram']) {
            $data['instagram'] = $this->scrapeInstagramPublicProfile(
                $competitor['instagram']
            );
        }
        
        // Twitter public data
        if ($competitor['twitter']) {
            $data['twitter'] = $this->scrapeTwitterPublicProfile(
                $competitor['twitter']
            );
        }
        
        // Analyze with GPT-4
        return $this->generateInsights($data);
    }
    
    private function scrapeFacebookPublicPage($url) {
        $client = new Client();
        $crawler = $client->request('GET', $url);
        
        return [
            'followers' => $this->extractFollowerCount($crawler),
            'recent_posts' => $this->extractRecentPosts($crawler, 10),
            'engagement_rate' => $this->estimateEngagement($crawler),
            'posting_frequency' => $this->calculatePostingFrequency($crawler),
            'content_themes' => $this->identifyContentThemes($crawler),
        ];
    }
    
    private function scrapeInstagramPublicProfile($handle) {
        // Use public Instagram endpoint or scraper
        $url = "https://www.instagram.com/{$handle}/?__a=1&__d=dis";
        
        $response = file_get_contents($url, false, stream_context_create([
            'http' => [
                'header' => 'User-Agent: Mozilla/5.0'
            ]
        ]));
        
        $data = json_decode($response, true);
        
        return [
            'followers' => $data['graphql']['user']['edge_followed_by']['count'],
            'posts_count' => $data['graphql']['user']['edge_owner_to_timeline_media']['count'],
            'engagement_rate' => $this->calculateIGEngagement($data),
            'top_hashtags' => $this->extractTopHashtags($data),
        ];
    }
    
    private function generateInsights($socialData) {
        $prompt = "
        Analyze this competitor's social media presence:
        " . json_encode($socialData, JSON_PRETTY_PRINT) . "
        
        Provide analysis on:
        1. Content Strategy - What they post about, themes, topics
        2. Posting Frequency - How often they post per week
        3. Engagement Levels - How their audience responds
        4. Best Performing Content - What gets most engagement
        5. Weaknesses - What they're missing or doing poorly
        6. Opportunities - Gaps we can exploit
        
        Format as JSON with these keys.
        ";
        
        $insights = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a social media strategist analyzing competitor data.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
        ]);
        
        return json_decode($insights['choices'][0]['message']['content'], true);
    }
}
```

**Data Collected:**
- Follower counts (all platforms)
- Posting frequency
- Average engagement rate
- Content themes/topics
- Hashtag strategy
- Best performing posts
- Customer sentiment (from comments)
- Response patterns

---

### 3. Market Analysis Agent

**Purpose:** Provide comprehensive market analysis using AI

**How It Works:**
```php
class MarketAnalysisAgent {
    
    public function analyzeMarket($businessIdea, $location, $competitors) {
        // Compile all data
        $competitorData = $this->compileCompetitorData($competitors);
        $demographicData = $this->getDemographics($location);
        $trendData = $this->getIndustryTrends($businessIdea);
        
        // Use GPT-4 for comprehensive analysis
        $prompt = $this->buildAnalysisPrompt([
            'business_idea' => $businessIdea,
            'location' => $location,
            'competitors' => $competitorData,
            'demographics' => $demographicData,
            'trends' => $trendData,
        ]);
        
        $analysis = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert market research analyst with 20 years of experience.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
        ]);
        
        return json_decode($analysis['choices'][0]['message']['content'], true);
    }
    
    private function buildAnalysisPrompt($data) {
        return "
        # Market Analysis Request
        
        Business Idea: {$data['business_idea']}
        Location: {$data['location']}
        
        ## Competitors Found:
        " . json_encode($data['competitors'], JSON_PRETTY_PRINT) . "
        
        ## Demographics:
        " . json_encode($data['demographics'], JSON_PRETTY_PRINT) . "
        
        ## Industry Trends:
        " . json_encode($data['trends'], JSON_PRETTY_PRINT) . "
        
        ---
        
        Provide a comprehensive market analysis in JSON format:
        
        {
            \"market_size\": {
                \"estimated_value\": \"$X.XM\",
                \"confidence\": \"high/medium/low\",
                \"methodology\": \"explanation\"
            },
            \"market_trends\": [
                {\"trend\": \"...\", \"impact\": \"positive/negative/neutral\"}
            ],
            \"target_audience\": {
                \"primary\": \"description\",
                \"secondary\": \"description\",
                \"demographics\": {...}
            },
            \"competition_level\": {
                \"level\": \"low/medium/high\",
                \"reasoning\": \"...\"
            },
            \"barriers_to_entry\": [
                {\"barrier\": \"...\", \"severity\": \"low/medium/high\"}
            ],
            \"opportunities\": [
                {\"opportunity\": \"...\", \"potential\": \"high/medium/low\"}
            ],
            \"threats\": [
                {\"threat\": \"...\", \"severity\": \"high/medium/low\"}
            ],
            \"recommended_strategy\": {
                \"positioning\": \"...\",
                \"differentiation\": \"...\",
                \"pricing_strategy\": \"...\",
                \"marketing_channels\": [...]
            }
        }
        ";
    }
    
    private function getDemographics($location) {
        // Use Census API or other public demographic data
        // For MVP, can use GPT-4 knowledge
        return [
            'population' => 'estimate',
            'median_income' => 'estimate',
            'age_distribution' => 'estimate',
        ];
    }
    
    private function getIndustryTrends($businessIdea) {
        // Use GPT-4's knowledge base
        $prompt = "What are the top 5 trends in the {$businessIdea} industry right now? Be specific and data-driven.";
        
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ]);
        
        return $response['choices'][0]['message']['content'];
    }
}
```

**Analysis Provided:**
- Market size estimation
- Growth trends
- Target audience definition
- Competition level assessment
- Barriers to entry
- Market opportunities
- Potential threats
- Recommended positioning strategy

---

### 4. Report Generator Agent

**Purpose:** Compile all findings into a beautiful, actionable report

**How It Works:**
```php
class ReportGeneratorAgent {
    
    public function generateReport($researchData) {
        // Structure the report
        $report = [
            'executive_summary' => $this->generateExecutiveSummary($researchData),
            'market_overview' => $researchData['market_analysis'],
            'competitor_analysis' => $this->formatCompetitorAnalysis($researchData['competitors']),
            'social_media_insights' => $researchData['social_intelligence'],
            'recommendations' => $this->generateRecommendations($researchData),
            'action_plan' => $this->generateActionPlan($researchData),
        ];
        
        // Save to database
        $this->saveReport($report);
        
        // Generate PDF
        $pdfPath = $this->generatePDF($report);
        
        return [
            'report' => $report,
            'pdf_url' => $pdfPath,
        ];
    }
    
    private function generateExecutiveSummary($data) {
        $prompt = "
        Based on this market research data:
        " . json_encode($data, JSON_PRETTY_PRINT) . "
        
        Write a compelling executive summary (3-4 paragraphs) that:
        1. States the business opportunity clearly
        2. Highlights key market findings
        3. Identifies the main competitive advantages
        4. Provides clear next steps
        
        Write in a professional but encouraging tone.
        ";
        
        $summary = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a business consultant writing executive summaries.'],
                ['role' => 'user', 'content' => $prompt]
            ]
        ]);
        
        return $summary['choices'][0]['message']['content'];
    }
    
    private function generateActionPlan($data) {
        $prompt = "
        Create a 30-day action plan for launching this business:
        " . json_encode($data, JSON_PRETTY_PRINT) . "
        
        Provide 8-10 specific, actionable steps in order of priority.
        Each step should include:
        - Action item
        - Timeline (Week 1, Week 2, etc.)
        - Expected outcome
        - Resources needed
        
        Format as JSON array.
        ";
        
        $plan = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ]);
        
        return json_decode($plan['choices'][0]['message']['content'], true);
    }
}
```

---

## User Interface (Beta Version)

### Landing Page
```
┌─────────────────────────────────────────────────────────┐
│              Lili Market Research                       │
│        AI-Powered Business Intelligence                 │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  Thinking of starting a business?                      │
│  Get instant market research powered by AI.            │
│                                                         │
│  What business do you want to start?                   │
│  ┌─────────────────────────────────────────────────┐   │
│  │ e.g., Organic bakery, Coffee shop, SaaS...     │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  Where?                                                 │
│  ┌─────────────────────────────────────────────────┐   │
│  │ City, State/Country                             │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  [Start Free Analysis] ← 2-3 minutes                   │
│                                                         │
│  ✓ Find top competitors automatically                  │
│  ✓ Analyze social media presence                       │
│  ✓ Get market insights & recommendations               │
│  ✓ No credit card required                             │
└─────────────────────────────────────────────────────────┘
```

### Processing Screen
```
┌─────────────────────────────────────────────────────────┐
│              Analyzing Your Market...                   │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  [=============================>--------] 70%           │
│                                                         │
│  ✓ Searching for competitors...                        │
│  ✓ Found 12 relevant businesses                        │
│  ✓ Analyzing social media presence...                  │
│  ⟳ Generating market insights...                       │
│  ⏳ Compiling your report...                            │
│                                                         │
│  This usually takes 2-3 minutes                        │
└─────────────────────────────────────────────────────────┘
```

### Report Dashboard
```
┌─────────────────────────────────────────────────────────┐
│              Market Analysis Report                     │
│  Organic Bakery in Austin, TX                          │
│  Generated: Jan 2, 2026                                │
│  [Download PDF] [Share] [New Analysis]                 │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ 📊 EXECUTIVE SUMMARY                                   │
│ ┌───────────────────────────────────────────────────┐  │
│ │ The organic bakery market in Austin shows strong │  │
│ │ growth potential with 12% YoY growth. We found   │  │
│ │ 10 direct competitors, most focusing on sweet    │  │
│ │ items. Key opportunity: No competitor focuses on │  │
│ │ savory gluten-free options...                    │  │
│ └───────────────────────────────────────────────────┘  │
│                                                         │
│ 📈 MARKET OVERVIEW                                     │
│ Market Size: $2.3M (Austin metro)                     │
│ Growth Rate: 12% YoY                                   │
│ Competition Level: Medium ███████░░░░░                │
│                                                         │
│ 🏪 TOP COMPETITORS (10 found)                          │
│                                                         │
│ 1. ⭐ Wildflour Bakery                                 │
│    FB: 8.5K | IG: 12.3K | Avg Price: $$$             │
│    Strategy: Vegan + GF, Strong Instagram presence    │
│    Weakness: Limited savory options                    │
│    [View Details]                                      │
│                                                         │
│ 2. ⭐ Austin Gluten-Free Treats                        │
│    FB: 3.2K | IG: 5.1K | Avg Price: $$               │
│    Strategy: Local delivery focus, Facebook ads       │
│    Weakness: Inconsistent posting schedule            │
│    [View Details]                                      │
│                                                         │
│ 3. ⭐ Healthy Bakes ATX                                │
│    FB: 2.8K | IG: 4.5K | Avg Price: $$               │
│    Strategy: Health-focused, nutrition info           │
│    Weakness: Limited social engagement                 │
│    [View Details]                                      │
│                                                         │
│ [Show all 10 competitors]                              │
│                                                         │
│ 💡 KEY INSIGHTS                                        │
│ ┌───────────────────────────────────────────────────┐  │
│ │ Social Media Patterns:                            │  │
│ │ • Average posting: 3-4x/week on Instagram        │  │
│ │ • Facebook less active (1-2x/week)               │  │
│ │ • Best engagement: Behind-the-scenes content     │  │
│ │                                                   │  │
│ │ Pricing Analysis:                                 │  │
│ │ • Sweet items: $4-8 per piece                    │  │
│ │ • Specialty cakes: $45-75                        │  │
│ │ • Wholesale: $2.50-4 per unit                    │  │
│ │                                                   │  │
│ │ Customer Pain Points (from reviews):              │  │
│ │ • "Limited variety" (mentioned 47 times)         │  │
│ │ • "Too sweet" (mentioned 34 times)               │  │
│ │ • "Wish there were savory options" (23 times)    │  │
│ └───────────────────────────────────────────────────┘  │
│                                                         │
│ 🎯 OPPORTUNITIES                                       │
│ 1. Focus on savory GF items (market gap)              │
│ 2. Build strong Instagram presence (4-5x/week)        │
│ 3. Price competitively at $5-7 per item              │
│ 4. Emphasize "not too sweet" positioning              │
│ 5. Offer wholesale to local cafes                     │
│                                                         │
│ ⚠️ THREATS                                             │
│ 1. High competition in sweet baked goods             │
│ 2. Rising ingredient costs (organic flour +15%)       │
│ 3. Established competitors have loyal customer base   │
│                                                         │
│ 📱 RECOMMENDED STRATEGY                                │
│ ┌───────────────────────────────────────────────────┐  │
│ │ Positioning: "Austin's only savory-first organic  │  │
│ │ bakery with creative gluten-free options"         │  │
│ │                                                   │  │
│ │ Differentiation:                                  │  │
│ │ • Focus 60% on savory, 40% on sweet             │  │
│ │ • Unique items: GF pizza crusts, savory muffins  │  │
│ │ • Less sugar than competitors                    │  │
│ │                                                   │  │
│ │ Primary Channel: Instagram                        │  │
│ │ • Post 4-5x/week                                 │  │
│ │ • Focus on process videos & behind-scenes        │  │
│ │ • Use hashtags: #austinfood #glutenfreeaustin    │  │
│ │                                                   │  │
│ │ Price Point: $5-7 per item                       │  │
│ │ • Competitive with market                         │  │
│ │ • Perceived as premium but accessible            │  │
│ └───────────────────────────────────────────────────┘  │
│                                                         │
│ ✅ 30-DAY ACTION PLAN                                  │
│                                                         │
│ Week 1: Foundation                                     │
│ □ Register business & get permits                     │
│ □ Create Instagram & Facebook business pages          │
│ □ Design simple logo & branding                       │
│ □ Source organic suppliers                            │
│                                                         │
│ Week 2: Product Development                            │
│ □ Finalize 10 signature recipes (6 savory, 4 sweet)  │
│ □ Test with 20 people, gather feedback               │
│ □ Refine recipes based on feedback                    │
│ □ Calculate costs & set prices                        │
│                                                         │
│ Week 3: Marketing Setup                                │
│ □ Create content calendar (30 days)                  │
│ □ Take professional photos of products               │
│ □ Write captions & hashtag strategy                  │
│ □ Set up online ordering (simple form/PayPal)        │
│                                                         │
│ Week 4: Soft Launch                                    │
│ □ Launch Instagram with 9-post grid                  │
│ □ Offer friends/family tasting event                 │
│ □ Start taking pre-orders                            │
│ □ Post daily content                                  │
│ □ Reach out to 5 local cafes for wholesale           │
│                                                         │
│ [Download Full Action Plan PDF]                        │
└─────────────────────────────────────────────────────────┘
```

---

## Database Schema

```sql
-- Research requests
CREATE TABLE research_requests (
    id SERIAL PRIMARY KEY,
    user_id INT,
    business_idea TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending', -- pending, processing, completed, failed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP,
    INDEX idx_user_status (user_id, status),
    INDEX idx_created (created_at)
);

-- Discovered competitors
CREATE TABLE competitors (
    id SERIAL PRIMARY KEY,
    request_id INT NOT NULL,
    business_name VARCHAR(255),
    website VARCHAR(255),
    facebook_url VARCHAR(255),
    facebook_handle VARCHAR(100),
    instagram_handle VARCHAR(100),
    twitter_handle VARCHAR(100),
    linkedin_url VARCHAR(255),
    address TEXT,
    phone VARCHAR(50),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES research_requests(id) ON DELETE CASCADE,
    INDEX idx_request (request_id)
);

-- Social media metrics
CREATE TABLE competitor_social_metrics (
    id SERIAL PRIMARY KEY,
    competitor_id INT NOT NULL,
    platform VARCHAR(50), -- facebook, instagram, twitter
    followers INT,
    following INT,
    posts_count INT,
    avg_engagement_rate DECIMAL(5,2),
    posting_frequency VARCHAR(50), -- daily, 3x/week, etc
    last_post_date DATE,
    scraped_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (competitor_id) REFERENCES competitors(id) ON DELETE CASCADE,
    INDEX idx_competitor_platform (competitor_id, platform)
);

-- Social media posts collected
CREATE TABLE competitor_posts (
    id SERIAL PRIMARY KEY,
    competitor_id INT NOT NULL,
    platform VARCHAR(50),
    post_url TEXT,
    post_text TEXT,
    post_date TIMESTAMP,
    likes INT,
    comments INT,
    shares INT,
    engagement_rate DECIMAL(5,2),
    content_type VARCHAR(50), -- photo, video, carousel, text
    hashtags TEXT[], -- PostgreSQL array
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (competitor_id) REFERENCES competitors(id) ON DELETE CASCADE,
    INDEX idx_competitor (competitor_id),
    INDEX idx_engagement (engagement_rate DESC)
);

-- Market analysis data
CREATE TABLE market_analysis (
    id SERIAL PRIMARY KEY,
    request_id INT NOT NULL,
    market_size_estimate VARCHAR(100),
    growth_rate DECIMAL(5,2),
    competition_level VARCHAR(50), -- low, medium, high
    target_audience JSONB,
    trends JSONB,
    opportunities JSONB,
    threats JSONB,
    barriers_to_entry JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES research_requests(id) ON DELETE CASCADE
);

-- Social intelligence insights
CREATE TABLE social_intelligence (
    id SERIAL PRIMARY KEY,
    competitor_id INT NOT NULL,
    content_themes JSONB, -- ["behind-the-scenes", "product-showcase", etc]
    top_hashtags TEXT[],
    best_posting_times VARCHAR(100),
    engagement_patterns JSONB,
    strengths TEXT[],
    weaknesses TEXT[],
    ai_insights TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (competitor_id) REFERENCES competitors(id) ON DELETE CASCADE
);

-- Generated reports
CREATE TABLE reports (
    id SERIAL PRIMARY KEY,
    request_id INT NOT NULL,
    executive_summary TEXT,
    report_data JSONB, -- Full structured report
    recommendations JSONB,
    action_plan JSONB,
    pdf_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES research_requests(id) ON DELETE CASCADE
);

-- User feedback on reports
CREATE TABLE report_feedback (
    id SERIAL PRIMARY KEY,
    report_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    feedback_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE
);
```

---

## Required APIs & Setup

### 1. Google Custom Search API
**Cost:** 100 queries/day FREE, then $5 per 1,000 queries

**Setup:**
```bash
# 1. Go to Google Cloud Console
https://console.cloud.google.com/

# 2. Enable Custom Search API
# 3. Create API key
# 4. Create Custom Search Engine
https://programmablesearchengine.google.com/

# 5. Add to .env
GOOGLE_API_KEY=your-api-key
GOOGLE_SEARCH_ENGINE_ID=your-search-engine-id
```

### 2. OpenAI API
**Cost:** ~$0.01-0.03 per full analysis

**Already have this** ✓

### 3. Web Scraping Libraries
```bash
composer require fabpot/goutte
composer require symfony/dom-crawler
composer require guzzlehttp/guzzle
```

### 4. Instagram Scraper (Optional)
**Option A:** Use Apify (recommended)
- Cost: $10-20/month for 1,000 profiles
- Setup: https://apify.com/apify/instagram-profile-scraper

**Option B:** Build custom scraper
- Free but requires maintenance
- Instagram may block

### 5. PDF Generation
```bash
composer require barryvdh/laravel-dompdf
```

---

## What Data Can Be Legally Collected

### ✅ **Legal & Safe:**
- Public Facebook pages (visible without login)
- Public Instagram profiles
- Public Twitter posts
- Company websites
- Google Reviews
- Yelp reviews
- Business directories
- Public LinkedIn company pages

### ⚠️ **Gray Area (Use Caution):**
- Scraping public data at scale
- Storing scraped data long-term
- Automated scraping (use delays, respect robots.txt)

### ❌ **Illegal/Prohibited:**
- Bypassing login walls
- Scraping private profiles
- Accessing non-public data
- Violating ToS repeatedly
- Scraping with malicious intent

---

## Legal Best Practices

1. **Respect robots.txt**
   ```php
   // Check robots.txt before scraping
   $robotsParser = new RobotsTxtParser();
   if (!$robotsParser->isAllowed($url, 'YourBot')) {
       return; // Skip this site
   }
   ```

2. **Add delays between requests**
   ```php
   // Don't hammer servers
   sleep(2); // 2 seconds between requests
   ```

3. **Identify your bot**
   ```php
   'User-Agent' => 'LiliResearchBot/1.0 (+https://yoursite.com/bot)'
   ```

4. **Cache aggressively**
   ```php
   // Don't re-scrape same page multiple times
   Cache::remember("page_{$url}", 86400, function() use ($url) {
       return $this->scrapePage($url);
   });
   ```

5. **Limit scope**
   - Only scrape what you need
   - Delete data when no longer needed
   - Respect GDPR/privacy laws

---

## Implementation Timeline

### Week 1: Core Infrastructure
**Days 1-2:**
- ✅ Database schema & migrations
- ✅ Set up Google Search API
- ✅ Create base agent classes
- ✅ Build job queue system

**Days 3-5:**
- ✅ Implement Competitor Finder Agent
- ✅ Google Search integration
- ✅ Social profile extraction
- ✅ Basic web scraping

**Days 6-7:**
- ✅ Testing & debugging
- ✅ Handle edge cases
- ✅ Error handling

### Week 2: Social Intelligence
**Days 8-10:**
- ✅ Facebook public page scraper
- ✅ Instagram public profile scraper
- ✅ Twitter API integration
- ✅ Data extraction & parsing

**Days 11-13:**
- ✅ Social Intelligence Agent
- ✅ GPT-4 analysis prompts
- ✅ Insight generation
- ✅ Engagement calculations

**Day 14:**
- ✅ Testing with real competitors
- ✅ Refine scraping logic

### Week 3: Market Analysis & Reports
**Days 15-17:**
- ✅ Market Analysis Agent
- ✅ GPT-4 market analysis prompts
- ✅ Trend analysis
- ✅ Opportunity identification

**Days 18-20:**
- ✅ Report Generator Agent
- ✅ PDF generation
- ✅ Report UI/dashboard
- ✅ Action plan generation

**Day 21:**
- ✅ End-to-end testing
- ✅ Polish UI

### Week 4: Polish & Launch
**Days 22-24:**
- ✅ Landing page
- ✅ User onboarding flow
- ✅ Email notifications
- ✅ Error handling

**Days 25-27:**
- ✅ Beta testing with 5-10 users
- ✅ Bug fixes
- ✅ Performance optimization

**Day 28:**
- 🚀 LAUNCH BETA

---

## Beta Launch Checklist

### Technical
- [ ] All agents working end-to-end
- [ ] Error handling for API failures
- [ ] Rate limiting implemented
- [ ] Queue system working
- [ ] Database backups configured
- [ ] Monitoring/logging set up

### Legal
- [ ] Terms of Service written
- [ ] Privacy Policy written
- [ ] GDPR compliance (if EU users)
- [ ] robots.txt respecting
- [ ] User-agent identification

### User Experience
- [ ] Simple onboarding (no signup required for beta)
- [ ] Clear progress indicators
- [ ] Beautiful report design
- [ ] PDF download working
- [ ] Mobile-friendly UI

### Marketing
- [ ] Landing page live
- [ ] Feedback form ready
- [ ] Email collection for waitlist
- [ ] Social media accounts created
- [ ] Launch announcement prepared

---

## Pricing Strategy (Post-Beta)

### Free Tier
- 1 free analysis per month
- Basic report (5 competitors)
- No PDF download
- Community support

### Starter - $29/month
- 5 analyses per month
- Full reports (10 competitors)
- PDF downloads
- Email support

### Professional - $99/month
- 25 analyses per month
- Full reports (15 competitors)
- Priority processing
- Advanced insights
- API access

### Agency - $299/month
- Unlimited analyses
- White-label reports
- Team collaboration
- Dedicated support
- Custom branding

---

## Success Metrics (Beta)

**Track these:**
- Number of analyses completed
- Time per analysis (target: <3 minutes)
- User satisfaction (NPS score)
- Report accuracy (user feedback)
- API costs per analysis
- Conversion rate (free → paid)

**Targets:**
- 100 beta users
- 500 analyses run
- 4.0+ star rating
- <$1 cost per analysis
- 10% conversion to paid

---

## Next Steps After Beta

### Phase 2: Enhanced Features
- Review site scraping (Yelp, G2, Trustpilot)
- Pricing intelligence (scrape competitor prices)
- SEO analysis (keyword rankings)
- Ad intelligence (Facebook Ad Library)
- Historical trend tracking

### Phase 3: Social Media Management
- Connect user's own pages
- Auto-post content
- Reply to comments
- Schedule posts
- (Original plan - requires API approval)

### Phase 4: Industry-Specific
- E-commerce analyzer
- Restaurant analyzer
- SaaS competitor intel
- Local services analyzer

---

## Risk Mitigation

### Risk: Facebook/Instagram blocking scraper
**Mitigation:**
- Use residential proxies
- Rotate user agents
- Add random delays
- Cache aggressively
- Have fallback to manual entry

### Risk: Google Search API quota exceeded
**Mitigation:**
- Implement caching (24hr)
- Use Bing as backup
- Add rate limiting
- Upgrade plan if needed

### Risk: GPT-4 costs too high
**Mitigation:**
- Use GPT-4o-mini for simpler tasks
- Cache common analyses
- Optimize prompts for token efficiency
- Batch similar requests

### Risk: Scraped data quality poor
**Mitigation:**
- Validate data before showing user
- Show confidence scores
- Allow user to flag bad data
- Manual review for first 100 reports

---

## FAQs

**Q: Is this legal?**
A: Yes, scraping public data is generally legal (US law). We only collect publicly visible information. However, it may violate platform ToS, so we'll transition to official APIs.

**Q: What if we can't find competitors?**
A: We'll use GPT-4's knowledge base to provide industry analysis even without specific competitors found.

**Q: How accurate will the data be?**
A: Beta accuracy ~70-80%. Will improve with user feedback and better scraping logic.

**Q: What about non-English markets?**
A: GPT-4 supports 50+ languages. Scraping works globally. Start with English, expand later.

**Q: How long until we can integrate real APIs?**
A: Facebook approval: 3-4 weeks. But this beta works immediately.

**Q: Can users export data?**
A: Yes - JSON, CSV, and PDF formats.

---

## Conclusion

This beta approach gets you:
- ✅ Real, usable product in 4 weeks
- ✅ Real data (not mocks)
- ✅ Real user feedback
- ✅ Revenue potential immediately
- ✅ No API approval bottlenecks

**Ready to start building?** The Competitor Finder Agent is the best starting point.
