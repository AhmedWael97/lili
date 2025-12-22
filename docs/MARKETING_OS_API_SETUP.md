# Marketing OS - API Configuration Guide

## Required API Keys (Phase 1)

### 1. OpenAI API (REQUIRED)
- **Purpose**: Powers all AI agents (Market Research, SWOT, Strategy, etc.)
- **Cost**: ~$20-$200/month depending on usage
- **Registration**: https://platform.openai.com/api-keys
- **Setup**:
  ```
  OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx
  OPENAI_MODEL=gpt-4-turbo-preview
  ```

### 2. SimilarWeb API (OPTIONAL - has mock data fallback)
- **Purpose**: Website traffic & competitor analysis
- **Cost**: $100-$300/month
- **Registration**: https://account.similar web.com/api-management
- **Setup**:
  ```
  SIMILARWEB_API_KEY=your_api_key_here
  ```
- **Note**: System will use mock data if not configured

### 3. SEMrush API (OPTIONAL - has mock data fallback)
- **Purpose**: SEO keywords & organic traffic analysis
- **Cost**: $120-$450/month
- **Registration**: https://www.semrush.com/api-analytics/
- **Setup**:
  ```
  SEMRUSH_API_KEY=your_api_key_here
  ```
- **Note**: System will use mock data if not configured

### 4. Ahrefs API (OPTIONAL - has mock data fallback)
- **Purpose**: Backlinks & domain rating analysis
- **Cost**: $200-$500/month
- **Registration**: https://ahrefs.com/api
- **Setup**:
  ```
  AHREFS_API_KEY=your_api_key_here
  ```
- **Note**: System will use mock data if not configured

### 5. Google Trends (FREE - uses mock data in Phase 1)
- **Purpose**: Search trends & regional interest
- **Cost**: FREE
- **Note**: Currently uses mock data. For production, consider using SerpApi or DataForSEO

## Minimum Setup (Development/Testing)

For testing, you only need:
```
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx
```

All other APIs will use mock data automatically.

## Production Setup

For full production features, configure all APIs:
```
# AI Engine
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx
OPENAI_MODEL=gpt-4-turbo-preview

# Competitor Intelligence
SIMILARWEB_API_KEY=your_key
SEMRUSH_API_KEY=your_key
AHREFS_API_KEY=your_key
```

## Testing APIs

Run the unit tests to verify your API configuration:
```bash
php artisan test --testsuite=Marketing
```

## Cost Estimation

**Lean MVP** (OpenAI only): $20-$50/month
**Development** (OpenAI + 1-2 APIs): $150-$300/month
**Production** (All APIs): $400-$800/month

## Support

- OpenAI docs: https://platform.openai.com/docs
- SimilarWeb docs: https://developer.similarweb.com
- SEMrush docs: https://www.semrush.com/api-documentation/
- Ahrefs docs: https://ahrefs.com/api/documentation
