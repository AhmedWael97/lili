# 🚀 Quick Setup: Facebook API for Accurate Competitor Data

## Problem You're Solving

Currently getting customer posts that TAG businesses (wrong) instead of posts FROM official business pages (correct).

## Solution: 5-Minute Facebook API Setup

### Step 1: Create Facebook App (2 minutes)

1. Go to: https://developers.facebook.com/apps/
2. Click **"Create App"**
3. Choose **"Business"** type
4. Fill in:
   - App Name: `Lili Market Research`
   - Contact Email: Your email
   - Business Account: (optional - can skip)
5. Click **"Create App"**

### Step 2: Get Your Credentials (1 minute)

After creating app, you'll see:

```
App ID: 123456789012345
App Secret: abc123def456ghi789 (click "Show")
```

### Step 3: Generate Access Token (1 minute)

**Method A: Simple Token (Recommended for testing)**
```
Your App Access Token:
123456789012345|abc123def456ghi789
(This is just: app_id|app_secret)
```

**Method B: API Call (For production)**
```bash
curl "https://graph.facebook.com/oauth/access_token?client_id=YOUR_APP_ID&client_secret=YOUR_APP_SECRET&grant_type=client_credentials"
```

Response:
```json
{
  "access_token": "123456789012345|abc123def456ghi789",
  "token_type": "bearer"
}
```

### Step 4: Add to .env File (1 minute)

Open `.env` in your project root and add:

```env
FACEBOOK_APP_ID=123456789012345
FACEBOOK_APP_SECRET=abc123def456ghi789
FACEBOOK_APP_ACCESS_TOKEN=123456789012345|abc123def456ghi789
```

**Save the file!**

### Step 5: Test It (30 seconds)

```bash
php artisan tinker
```

```php
$scraper = app(\App\Services\MarketResearch\SocialMediaScraperService::class);
$result = $scraper->scrapeFacebookPage('nike'); // Test with Nike
dd($result);
```

You should see:
```php
[
  "success" => true
  "source" => "facebook_graph_api"  // ✅ Using API!
  "page_name" => "Nike"
  "followers" => 38245123  // Accurate count
  "posts" => [  // Posts FROM Nike's page
    [
      "text" => "Just Do It. New campaign..."
      "likes" => 25340
      "comments" => 1234
      "shares" => 567
    ]
  ]
]
```

---

## ✅ Verification Checklist

- [ ] Created Facebook app
- [ ] Got App ID and App Secret
- [ ] Added to `.env` file
- [ ] Tested in tinker - sees `"source" => "facebook_graph_api"`
- [ ] Run market research - gets real follower counts
- [ ] Check `competitor_posts` table - has actual posts from official pages

---

## 🔍 What Changes After Setup

### Before (Without API):
```
Scraping: galambo
Source: estimated
Followers: 8,450 (random guess)
Posts: 0
Warning: Configure FACEBOOK_APP_ACCESS_TOKEN
Quality: ⭐ POOR
```

### After (With API):
```
Scraping: galambo
Source: facebook_graph_api
Followers: 38,245 (accurate)
Posts: 20 (FROM official page)
Engagement: 3.2% (calculated from real data)
Quality: ⭐⭐⭐⭐⭐ EXCELLENT
```

---

## 🎯 What You Get With API

### Official Page Data Only:
- ✅ Posts published BY the business
- ✅ Accurate follower counts
- ✅ Real engagement metrics (likes/comments/shares)
- ✅ Posting frequency (calculated from timestamps)
- ✅ Page category and description
- ❌ NO customer posts that tag the business
- ❌ NO random search results

### Example - Galambo Restaurant:

**Without API (Wrong):**
```
Post: "In the company of good friends..."
Author: random_customer
Type: Customer post tagging @galambo ❌
```

**With API (Correct):**
```
Post: "New seasonal menu this weekend! Come try..."
Author: Galambo Restaurant (official page)
Type: Business post from official page ✅
```

---

## 💰 Cost

**FREE** for basic usage:
- 100,000 API calls per day
- Access to public page data
- No credit card required

---

## 🛠️ Troubleshooting

### "Invalid OAuth access token"
→ Check that `FACEBOOK_APP_ACCESS_TOKEN` is correct in `.env`
→ Format: `app_id|app_secret` (with the pipe symbol)

### "Page not found"
→ Check the Facebook handle is correct
→ Try: `facebook.com/nike` (handle is "nike")

### Still seeing "source: estimated"
→ Check logs: `tail -f storage/logs/laravel.log`
→ Look for "Facebook API error" messages
→ Verify `.env` file was saved

### "Provide valid app ID"
→ Your app might be in Development Mode
→ Go to Settings > Basic > App Mode → Switch to "Live"

---

## 📊 API Limits

- **100,000 calls/day** (more than enough)
- **200 calls/hour** per user/IP
- **Rate limiting**: System has built-in 1-second delays

For typical usage (10-50 competitors per research):
- Scrapes needed: 50
- API calls used: ~150 (including posts)
- Daily capacity: 100,000
- **You can run 666+ research requests per day!**

---

## 🎓 Next Steps After Setup

1. **Run a Test Research**:
   ```
   Visit: http://localhost:8000/market-research
   Enter: "Restaurant in Miami"
   Click: "Start Research"
   ```

2. **Check Results**:
   ```sql
   -- View scraped posts
   SELECT * FROM competitor_posts WHERE platform = 'facebook';
   
   -- Check data sources
   SELECT business_name, (SELECT source FROM competitor_social_metrics 
                          WHERE competitor_id = competitors.id LIMIT 1)
   FROM competitors;
   ```

3. **Monitor Quality**:
   - Check logs for "facebook_graph_api" (good) vs "estimated" (needs API)
   - Verify posts are from official pages, not customers
   - Confirm follower counts are accurate

---

## 📝 Summary

**Time Required**: 5 minutes
**Cost**: FREE
**Result**: Accurate official page data instead of customer tags
**Impact**: 10x better competitor intelligence

**Setup Now → Get Accurate Data → Make Better Business Decisions**

---

## 🔗 Resources

- Facebook Developer Docs: https://developers.facebook.com/docs/graph-api
- Get Started: https://developers.facebook.com/apps/
- Graph API Explorer: https://developers.facebook.com/tools/explorer/
- Rate Limits: https://developers.facebook.com/docs/graph-api/overview/rate-limiting

---

**Status**: Ready to implement
**Priority**: HIGH
**Difficulty**: Easy (5 minutes)
**Impact**: Critical (accurate data vs random guesses)
