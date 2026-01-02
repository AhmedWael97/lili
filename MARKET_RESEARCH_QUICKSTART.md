# Market Research Beta - Quick Start

## What's Been Built

Core market research system with:
- ✅ Database migrations (7 tables)
- ✅ Eloquent models (ResearchRequest, Competitor, etc.)
- ✅ Google Search Service (finds competitors via search)
- ✅ Web Scraper Service (extracts social media links)
- ✅ Competitor Finder Agent (AI-powered competitor discovery)
- ✅ API endpoints (submit research, check status, get report)
- ✅ Background job processing (async research)

## Setup Instructions

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Configuration

Add to your `.env` file:

```env
# Google Custom Search API (required)
GOOGLE_API_KEY=your-google-api-key-here
GOOGLE_SEARCH_ENGINE_ID=your-search-engine-id-here

# OpenAI API (already configured)
OPENAI_API_KEY=your-openai-key

# Queue Configuration
QUEUE_CONNECTION=database
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Start Queue Worker

In a separate terminal:

```bash
php artisan queue:work --tries=3
```

### 5. Start Development Server

```bash
php artisan serve
```

## Getting Google Search API

### Step 1: Create Google Cloud Project
1. Go to https://console.cloud.google.com/
2. Create a new project
3. Enable "Custom Search API"
4. Create an API key

### Step 2: Create Custom Search Engine
1. Go to https://programmablesearchengine.google.com/
2. Click "Add"
3. Choose "Search the entire web"
4. Get your Search Engine ID

## API Endpoints

### Submit Research Request

```bash
POST /api/market-research
Content-Type: application/json

{
  "business_idea": "Organic bakery specializing in gluten-free products",
  "location": "Austin, TX"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Research request submitted successfully",
  "data": {
    "request_id": 1,
    "status": "pending",
    "estimated_time": "2-3 minutes"
  }
}
```

### Check Status

```bash
GET /api/market-research/{id}/status
```

**Response:**
```json
{
  "success": true,
  "data": {
    "request_id": 1,
    "status": "processing", // or "completed", "failed"
    "business_idea": "Organic bakery...",
    "location": "Austin, TX",
    "created_at": "2026-01-02T10:30:00Z",
    "completed_at": null
  }
}
```

### Get Report

```bash
GET /api/market-research/{id}/report
```

**Response:**
```json
{
  "success": true,
  "data": {
    "request_id": 1,
    "business_idea": "Organic bakery...",
    "location": "Austin, TX",
    "completed_at": "2026-01-02T10:33:00Z",
    "competitors": [
      {
        "id": 1,
        "business_name": "Wild flour Bakery",
        "website": "https://example.com",
        "social_media": {
          "facebook": "wildflourbakery",
          "instagram": "wildflourbakery",
          "twitter": "wildflourbake",
          "linkedin": null
        },
        "relevance_score": 95
      }
    ]
  }
}
```

## Testing the API

### Using cURL:

```bash
# Submit research
curl -X POST http://localhost:8000/api/market-research \
  -H "Content-Type: application/json" \
  -d '{
    "business_idea": "Coffee shop with coworking space",
    "location": "San Francisco, CA"
  }'

# Check status (replace {id} with actual ID)
curl http://localhost:8000/api/market-research/1/status

# Get report
curl http://localhost:8000/api/market-research/1/report
```

### Using Postman:

1. Import collection from `/docs/postman_collection.json` (TODO)
2. Set base URL: `http://localhost:8000`
3. Test endpoints

## What Happens When You Submit?

1. **Request Created** - ResearchRequest saved to database
2. **Job Queued** - ProcessMarketResearch job dispatched
3. **Competitor Search** - Google Search API finds relevant businesses
4. **Web Scraping** - Extracts social media links from websites
5. **AI Ranking** - GPT-4 ranks competitors by relevance
6. **Save Results** - Competitors saved to database
7. **Status Updated** - Request marked as "completed"

## Current Limitations (Beta v0.1)

- ✅ Finds competitors via Google Search
- ✅ Extracts social media profiles
- ✅ AI-powered relevance ranking
- ⏳ Social media analysis (TODO)
- ⏳ Market analysis (TODO)
- ⏳ Report generation (TODO)
- ⏳ PDF export (TODO)

## Next Steps

1. Test competitor finding with real business ideas
2. Implement Social Intelligence Agent
3. Implement Market Analysis Agent
4. Implement Report Generator
5. Build simple frontend UI

## Troubleshooting

### "Class GoogleSearchService not found"
```bash
composer dump-autoload
```

### "Queue jobs not processing"
```bash
# Make sure queue worker is running
php artisan queue:work

# Or use sync for testing
# In .env: QUEUE_CONNECTION=sync
```

### "Google API error"
- Check your API key is correct
- Verify Custom Search API is enabled
- Check quota limits (100 free queries/day)

### "No competitors found"
- Try broader search terms
- Check Google Search results manually
- Review logs: `tail -f storage/logs/laravel.log`

## Database Tables

- `research_requests` - User research submissions
- `competitors` - Discovered competitors
- `competitor_social_metrics` - Social media follower counts, engagement
- `competitor_posts` - Individual social media posts
- `market_analysis` - Market insights and trends
- `social_intelligence` - Social media strategy analysis
- `reports` - Final generated reports

## Logs

Monitor processing:

```bash
tail -f storage/logs/laravel.log
```

## What's Next?

See `docs/MARKET_RESEARCH_BETA_PLAN.md` for full roadmap.
