# ✅ Facebook API Permission Fix

## The Issue

Your User Access Token is working, but it **lacks the required permissions** to read page data.

### Error:
```
(#100) This endpoint requires the 'pages_read_engagement' permission 
or the 'Page Public Content Access' feature
```

## Quick Fix (2 minutes)

### Option 1: Use Graph API Explorer (Easiest)

1. **Go to Graph API Explorer**:
   https://developers.facebook.com/tools/explorer/

2. **Select Your App** in the top-right dropdown:
   - Meta App: **Lili** (1361511915457122)

3. **Add Permissions**:
   - Click **"Get User Access Token"**
   - In the permissions popup, check these boxes:
     - ✅ `pages_show_list`
     - ✅ `pages_read_engagement`
     - ✅ `pages_read_user_content`
     - ✅ `public_profile`
   
4. **Generate Token**:
   - Click "Generate Access Token"
   - Grant permissions when asked
   - **Copy the new token** (in the "Access Token" field)

5. **Update .env**:
   ```env
   FACEBOOK_APP_ACCESS_TOKEN=paste_new_token_here
   ```

### Option 2: Direct Permission URL

Visit this URL to grant permissions:

```
https://www.facebook.com/v18.0/dialog/oauth?client_id=1361511915457122&redirect_uri=https://developers.facebook.com/tools/explorer/callback&scope=pages_show_list,pages_read_engagement,pages_read_user_content,public_profile&response_type=token
```

Then copy the token from the URL after authorization.

## Test It

```bash
php test_facebook_scraper.php
```

**Expected Result:**
```
✅ Success: YES
📊 Source: facebook_graph_api  ← Should change from "public_scraping"
👥 Followers: 38,245,123  ← Real number
📝 Posts Count: 20  ← Actual posts
```

## What Permissions Do

- `pages_show_list`: See list of pages you manage
- `pages_read_engagement`: Read engagement metrics (likes, comments)  
- `pages_read_user_content`: Read posts from pages
- `public_profile`: Basic profile info

## Alternative: Use Page Access Token

If the above doesn't work for public pages like Nike, you can use the **Pages API** differently:

```bash
# Test with Graph API Explorer
curl "https://graph.facebook.com/v18.0/nike?fields=id,name,fan_count,category&access_token=YOUR_TOKEN"
```

For **public pages** you don't own, Facebook restricts access. You may need to:
1. Test with pages you own/manage
2. Apply for "Page Public Content Access" feature (for public pages)
3. Use the public scraping fallback (current method)

---

**Status**: Need to add permissions to your token
**Time**: 2 minutes
**Solution**: Use Graph API Explorer to generate token with permissions
