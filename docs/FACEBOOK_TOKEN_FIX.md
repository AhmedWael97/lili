# 🔧 Facebook API Token Issue - Quick Fix

## Current Status

✅ Facebook App ID & Secret are configured  
⚠️ App Access Token not working for Pages API  
📊 Currently using public scraping fallback (limited data)

## The Problem

The token format `app_id|app_secret` works for some APIs but **NOT for Pages API** which we need to get official page posts.

### Error in Logs:
```
Facebook API: Page not found or access denied
Falling back to: public_page_scraping
```

## Solution: Use Access Token Tool

### Option 1: Quick Test Token (60 days - Recommended for Testing)

1. **Go to Access Token Tool**:
   https://developers.facebook.com/tools/accesstoken/

2. **You'll see your app** (`1361511915457122`):
   ```
   App Name: [Your App Name]
   User Token: [Generate Token button]
   ```

3. **Click "Generate Token"**
   - Login with Facebook if needed
   - Grant permissions

4. **Copy the User Access Token**:
   ```
   EAATZA...very long string...ZD
   ```

5. **Update .env**:
   ```env
   FACEBOOK_APP_ACCESS_TOKEN=EAATZA...your_user_token...ZD
   ```

6. **Test again**:
   ```bash
   php test_facebook_scraper.php
   ```

### Option 2: Long-Lived Token (60 days, renewable)

After getting User Token from Option 1, exchange it for long-lived:

```bash
curl "https://graph.facebook.com/v18.0/oauth/access_token?grant_type=fb_exchange_token&client_id=1361511915457122&client_secret=c11b95fe02dd461f9f3c2874e70e0662&fb_exchange_token=YOUR_SHORT_TOKEN"
```

Response:
```json
{
  "access_token": "EAATZA...long_lived_token...ZD",
  "token_type": "bearer",
  "expires_in": 5184000
}
```

Use this long-lived token in `.env`.

### Option 3: Never-Expiring Page Token (Best for Production)

1. Get User Token from Option 1
2. Get your page ID:
   ```bash
   curl "https://graph.facebook.com/v18.0/me/accounts?access_token=YOUR_USER_TOKEN"
   ```
3. Use the `access_token` from the page you want (never expires)

## Testing

After updating token in `.env`:

```bash
php test_facebook_scraper.php
```

**Expected Output:**
```
✅ Success: YES
📊 Source: facebook_graph_api  ← Should say "facebook_graph_api"
👥 Followers: 38,245,123
📝 Posts Count: 20
📱 Sample Post: Just Do It...
```

## What Changes

### Before (Current - Public Scraping):
```
Source: facebook_public_scraping
Followers: 0
Posts: 0
Note: Can't access official posts
```

### After (With User Token):
```
Source: facebook_graph_api ✅
Followers: 38,245,123
Posts: 20 official posts FROM Nike's page
Engagement: Real metrics
```

## Quick Commands

```bash
# Test current setup
php test_facebook_scraper.php

# Check logs
Get-Content storage/logs/laravel.log -Tail 20 | Select-String "Facebook"

# Verify token works
curl "https://graph.facebook.com/v18.0/me?access_token=YOUR_TOKEN"
```

## Need Help?

1. **App Dashboard**: https://developers.facebook.com/apps/1361511915457122
2. **Access Token Tool**: https://developers.facebook.com/tools/accesstoken/
3. **Graph Explorer**: https://developers.facebook.com/tools/explorer/

---

**Next Step**: Get User Access Token from developers.facebook.com/tools/accesstoken and update `.env`
