# API Configuration Guide

## Real Competitor Analysis Data

Currently, the system returns **mock/fake data** for competitor analysis because the required API keys are not configured. To get **REAL data**, you need to configure the following APIs:

---

## Required API Keys

### 1. SEMrush API (Keywords & SEO Data)
**Purpose:** Get real organic/paid keywords, search volumes, rankings, and traffic data

**How to get:**
1. Sign up at: https://www.semrush.com/
2. Go to: https://www.semrush.com/api-analytics/
3. Purchase API credits (starts at $200/month for 10,000 API units)
4. Copy your API key

**Add to `.env`:**
```env
SEMRUSH_API_KEY=your_actual_api_key_here
```

**What you'll get:**
- ✅ Real organic keywords with search volumes
- ✅ Real paid keywords with CPC data
- ✅ Actual keyword positions
- ✅ Traffic estimates
- ✅ Keyword difficulty scores

---

### 2. Ahrefs API (Backlinks Data)
**Purpose:** Get real backlink data, domain ratings, and referring domains

**How to get:**
1. Sign up at: https://ahrefs.com/
2. Go to: https://ahrefs.com/api
3. Purchase API access (Custom pricing, contact sales)
4. Get your API token

**Add to `.env`:**
```env
AHREFS_API_KEY=your_actual_api_key_here
```

**What you'll get:**
- ✅ Real backlinks with domain ratings
- ✅ Anchor text data
- ✅ Link types (dofollow/nofollow)
- ✅ First/last seen dates
- ✅ Referring domains count

---

### 3. OpenAI API (AI Analysis - Already Required)
**Purpose:** Power all AI agents (competitor analysis, strategy generation, etc.)

**How to get:**
1. Sign up at: https://platform.openai.com/
2. Add payment method
3. Get API key from: https://platform.openai.com/api-keys

**Add to `.env`:**
```env
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx
OPENAI_ORGANIZATION=org-xxxxxxxxxxxxx
```

**What you'll get:**
- ✅ Real AI-powered competitor analysis
- ✅ Intelligent market positioning insights
- ✅ Strategy recommendations
- ✅ All AI agent functionality

---

## Current Behavior (Without API Keys)

When API keys are **not configured**, the system returns:

### Mock SEMrush Data:
```json
{
  "organic_keywords": "Random number between 500-5000",
  "organic_traffic": "Random number between 1000-50000",
  "adwords_keywords": "Random number between 50-500",
  "keywords": [
    {"keyword": "online shopping", "position": "Random 1-100", "search_volume": "Random 1000-100000"}
  ]
}
```

### Mock Ahrefs Data:
```json
{
  "domain_rating": "Random number 0-100",
  "backlinks": "Random number 1000-500000",
  "referring_domains": "Random number 100-10000",
  "backlinks": [
    {"source_url": "example.com", "anchor_text": "generic text", "dr": "Random 0-100"}
  ]
}
```

### Mock Social Data:
```json
{
  "platform": "facebook/instagram/twitter/linkedin",
  "followers": "Random 10000-500000",
  "engagement_rate": "Random 2-8%",
  "posts_count": "Random 500-5000"
}
```

---

## Cost Estimates

| API | Monthly Cost | Notes |
|-----|--------------|-------|
| **SEMrush** | $200 - $800 | Based on API units needed |
| **Ahrefs** | $500 - $2000 | Custom pricing, contact sales |
| **OpenAI** | $20 - $200 | Pay per token, depends on usage |
| **Total** | **$720 - $3000/mo** | For full real data |

---

## Alternative: Keep Using Mock Data

If you want to **test the system** without paying for APIs:

### Option 1: Continue with Mock Data
- The system will work with fake but realistic-looking data
- Good for demos and UI testing
- No additional cost

### Option 2: Partial Real Data
- Configure only OpenAI for real AI analysis
- Keep SEMrush/Ahrefs mock data
- Cost: ~$50-200/month

### Option 3: Use Free Tier APIs
Some alternatives with free tiers:
- **SerpApi** (100 free searches/month) - Keyword data
- **Dataforseo** (Free trial) - SEO metrics
- **Social Blade** (Limited free API) - Social stats

---

## Setup Instructions

### Step 1: Update `.env` File

Copy `.env.example` to `.env` if not already done:
```bash
cp .env.example .env
```

### Step 2: Add Your API Keys

Edit `.env` and add your keys:
```env
# Required for AI functionality
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxx

# Optional: Add for real competitor data
SEMRUSH_API_KEY=your_semrush_key_here
AHREFS_API_KEY=your_ahrefs_key_here
```

### Step 3: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Test

Add a new competitor - the system will automatically use real APIs if keys are configured, otherwise it falls back to mock data.

---

## How to Verify if Real Data is Being Used

Check the API response - real data includes:
- ✅ `"mock": false` or no mock flag
- ✅ Realistic, non-rounded numbers
- ✅ Actual competitor keywords (not "online shopping", "buy online")
- ✅ Real URLs in backlinks
- ✅ Consistent data across refreshes

Mock data includes:
- ⚠️ `"mock": true` flag in response
- ⚠️ Generic keywords like "online shopping"
- ⚠️ Random numbers that change on refresh
- ⚠️ No real competitor-specific insights

---

## Questions?

The current implementation is **working correctly** - it's designed to gracefully fall back to mock data when APIs aren't configured. This allows you to:
- ✅ Test the UI/UX
- ✅ Demo the system
- ✅ Develop features without API costs
- ✅ Upgrade to real data when ready

To get real data, simply add the API keys to your `.env` file as shown above.
