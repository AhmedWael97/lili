# AI Agents System - Design & Planning

## Overview
A platform that serves multiple AI agents as virtual employees, each specialized in different tasks.

---

## Agent Types

### 1. Marketing Specialist Agent
**Description:**
- Manages Facebook Pages
- Creates & schedules posts
- Generates images & copy
- Replies to comments
- Responds to messages (policy-compliant)
- Proposes & manages ad campaigns (with approval)

**Capabilities:**
- Content creation
- Social media management
- Community engagement
- Ad campaign management

**Requirements:**
- OpenAI API integration
- Meta/Facebook API integration
- Human approval workflows
- Compliance with Meta policies

**Sub-Agents (Multi-Agent System):**
1. **Strategist Agent** - Analyzes Page, defines content strategy
2. **Copywriter Agent** - Writes captions, ad copy, comment replies
3. **Creative Agent** - Generates images, applies brand kit
4. **Community Manager Agent** - Responds to comments and messages
5. **Ads Agent** - Builds campaigns, optimizes performance

---

### 2. [Next Agent Type]
**Description:**
- TBD

**Capabilities:**
- TBD

**Requirements:**
- TBD

---

### 3. [Next Agent Type]
**Description:**
- TBD

**Capabilities:**
- TBD

**Requirements:**
- TBD

---

## Platform Features

### Core Features
- [ ] Agent marketplace/catalog
- [ ] Agent assignment to tasks
- [ ] Task management dashboard
- [ ] Performance monitoring
- [ ] Multi-agent orchestration
- [ ] Approval workflows

### User Roles
- **Admin** - Manages all agents and users
- **Manager** - Assigns agents to tasks, approves actions
- **User** - Views results, provides feedback

---

## Subscription Packages & Limits

### Package Tiers

#### Free Tier
**Price:** $0/month
**Limits:**
- 1 Facebook Page connection
- 10 posts per month
- 50 comment replies per month
- No messaging automation
- No ad campaign management
- Basic analytics
- Community support only

**Included Agents:**
- Copywriter Agent (limited)

---

#### Starter Tier
**Price:** $29/month
**Limits:**
- 3 Facebook Pages
- 100 posts per month
- 500 comment replies per month
- 100 messages per month
- Basic ad campaign proposals (no execution)
- Standard analytics
- Email support

**Included Agents:**
- Strategist Agent
- Copywriter Agent
- Community Manager Agent

---

#### Professional Tier
**Price:** $99/month
**Limits:**
- 10 Facebook Pages
- 500 posts per month
- Unlimited comment replies
- Unlimited messages
- Ad campaign management (up to $5,000 ad spend)
- Advanced analytics
- Priority email support

**Included Agents:**
- All agents unlocked:
  - Strategist Agent
  - Copywriter Agent
  - Creative Agent
  - Community Manager Agent
  - Ads Agent

---

#### Agency Tier
**Price:** $299/month
**Limits:**
- Unlimited Facebook Pages
- Unlimited posts
- Unlimited comment replies
- Unlimited messages
- Full ad campaign management (unlimited ad spend)
- White-label options
- Team collaboration (up to 10 users)
- Dedicated account manager
- Phone + email support

**Included Agents:**
- All agents unlocked
- Custom agent training
- Multi-agent orchestration
- API access

---

## User Registration & Authentication Flow

### Step 1: User Registration
```
User Journey:
1. Visit homepage
2. Click "Get Started" or "Sign Up"
3. View package comparison page
4. Select package (Free/Starter/Professional/Agency)
5. Fill registration form:
   - Full Name
   - Email
   - Password
   - Company Name (optional)
   - Accept Terms & Privacy Policy
6. Click "Create Account"
7. Email verification sent
8. User verifies email
9. Redirected to dashboard
```

**Database Records Created:**
- `users` table entry
- `subscriptions` table entry (with selected package)
- `usage_limits` table entry (package limits)

---

### Step 2: Platform Connection (OAuth)

#### Facebook Connection Flow
```
After Registration:
1. User lands on dashboard
2. Dashboard shows "Connect Your Facebook Page" prompt
3. User clicks "Connect Facebook"
4. Redirect to Facebook OAuth with permissions request
5. User authorizes app (grants permissions)
6. Facebook redirects back with authorization code
7. Backend exchanges code for User Access Token
8. Backend fetches all Facebook Pages user manages
9. User selects which page(s) to connect (within package limit)
10. Backend exchanges for Page Access Token per selected page
11. Tokens stored encrypted in database
12. Success message: "Facebook Page Connected Successfully"
```

**OAuth Endpoint:**
```
GET /auth/facebook/redirect
  → Redirects to Facebook OAuth

GET /auth/facebook/callback
  → Handles OAuth callback
  → Exchanges code for token
  → Fetches pages
  → Stores tokens
```

---

### Step 3: Additional Platform Connections (Future)

#### Instagram Connection
```
1. Click "Connect Instagram"
2. Facebook OAuth (Instagram requires Facebook Business account)
3. Select Instagram Business account
4. Grant permissions
5. Store tokens
```

#### Twitter/X Connection
```
1. Click "Connect Twitter"
2. Twitter OAuth flow
3. Grant permissions
4. Store tokens
```

#### LinkedIn Connection
```
1. Click "Connect LinkedIn"
2. LinkedIn OAuth flow
3. Grant permissions
4. Store tokens
```

---

## Complete Authentication System

### Registration Routes
```php
// routes/web.php
Route::get('/register', [RegisterController::class, 'showForm']);
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail']);
```

### Login Routes
```php
Route::get('/login', [LoginController::class, 'showForm']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
```

### OAuth Routes
```php
// Facebook
Route::get('/auth/facebook/redirect', [FacebookOAuthController::class, 'redirect']);
Route::get('/auth/facebook/callback', [FacebookOAuthController::class, 'callback']);

// Instagram (future)
Route::get('/auth/instagram/redirect', [InstagramOAuthController::class, 'redirect']);
Route::get('/auth/instagram/callback', [InstagramOAuthController::class, 'callback']);

// Twitter (future)
Route::get('/auth/twitter/redirect', [TwitterOAuthController::class, 'redirect']);
Route::get('/auth/twitter/callback', [TwitterOAuthController::class, 'callback']);
```

---

## Database Schema for Authentication & Subscriptions

### Users Table
```sql
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Subscriptions Table
```sql
CREATE TABLE subscriptions (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    package_tier VARCHAR(50) NOT NULL, -- free, starter, professional, agency
    status VARCHAR(50) NOT NULL, -- active, cancelled, expired, suspended
    started_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NULL,
    stripe_subscription_id VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Usage Limits Table
```sql
CREATE TABLE usage_limits (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    package_tier VARCHAR(50) NOT NULL,
    max_facebook_pages INT NOT NULL,
    max_posts_per_month INT NOT NULL,
    max_comment_replies_per_month INT NOT NULL,
    max_messages_per_month INT NOT NULL,
    ad_campaign_enabled BOOLEAN DEFAULT FALSE,
    max_ad_spend INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Usage Tracking Table
```sql
CREATE TABLE usage_tracking (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    month_year VARCHAR(7) NOT NULL, -- format: YYYY-MM
    posts_count INT DEFAULT 0,
    comment_replies_count INT DEFAULT 0,
    messages_count INT DEFAULT 0,
    ad_spend_total DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, month_year)
);
```

### Connected Platforms Table
```sql
CREATE TABLE connected_platforms (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    platform VARCHAR(50) NOT NULL, -- facebook, instagram, twitter, linkedin
    platform_user_id VARCHAR(255) NOT NULL,
    platform_username VARCHAR(255) NULL,
    access_token TEXT NOT NULL, -- encrypted
    refresh_token TEXT NULL, -- encrypted
    token_expires_at TIMESTAMP NULL,
    scopes TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    connected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, platform, platform_user_id)
);
```

### Facebook Pages Table
```sql
CREATE TABLE facebook_pages (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    connected_platform_id BIGINT NOT NULL REFERENCES connected_platforms(id) ON DELETE CASCADE,
    page_id VARCHAR(255) NOT NULL,
    page_name VARCHAR(255) NOT NULL,
    page_username VARCHAR(255) NULL,
    page_access_token TEXT NOT NULL, -- encrypted
    page_category VARCHAR(255) NULL,
    page_followers_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    connected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, page_id)
);
```

---

## Middleware & Access Control

### Package Limit Middleware
```php
// app/Http/Middleware/CheckPackageLimits.php

class CheckPackageLimits
{
    public function handle($request, Closure $next, $limitType)
    {
        $user = $request->user();
        $usage = UsageService::getCurrentMonthUsage($user);
        $limits = $user->subscription->limits;
        
        switch ($limitType) {
            case 'post':
                if ($usage->posts_count >= $limits->max_posts_per_month) {
                    return response()->json([
                        'error' => 'Monthly post limit reached. Upgrade your plan.'
                    ], 403);
                }
                break;
                
            case 'comment_reply':
                if ($usage->comment_replies_count >= $limits->max_comment_replies_per_month) {
                    return response()->json([
                        'error' => 'Monthly comment reply limit reached. Upgrade your plan.'
                    ], 403);
                }
                break;
                
            case 'message':
                if ($usage->messages_count >= $limits->max_messages_per_month) {
                    return response()->json([
                        'error' => 'Monthly message limit reached. Upgrade your plan.'
                    ], 403);
                }
                break;
        }
        
        return $next($request);
    }
}
```

### Usage in Routes
```php
Route::middleware(['auth', 'check.limits:post'])->group(function () {
    Route::post('/posts/create', [PostController::class, 'create']);
});

Route::middleware(['auth', 'check.limits:comment_reply'])->group(function () {
    Route::post('/comments/reply', [CommentController::class, 'reply']);
});

Route::middleware(['auth', 'check.limits:message'])->group(function () {
    Route::post('/messages/send', [MessageController::class, 'send']);
});
```

---

## Security & Token Management

### Token Encryption
```php
// app/Services/TokenEncryptionService.php

class TokenEncryptionService
{
    public static function encrypt($token)
    {
        return encrypt($token);
    }
    
    public static function decrypt($encryptedToken)
    {
        return decrypt($encryptedToken);
    }
}
```

### Token Storage Rules
1. **Never store plain text tokens**
2. **Always use Laravel's encryption**
3. **Store tokens in environment-specific encryption key**
4. **Rotate encryption keys regularly**
5. **Implement token expiry checks**
6. **Refresh tokens before expiry**

---

## User Dashboard After Registration

### Dashboard Layout
```
┌─────────────────────────────────────────┐
│ Welcome, [User Name]!                   │
│ Package: Professional | Usage: 45/500   │
├─────────────────────────────────────────┤
│                                         │
│ [Connect Facebook Page] ← Main CTA     │
│                                         │
│ Connected Platforms:                    │
│ ☐ Facebook (Not Connected)             │
│ ☐ Instagram (Coming Soon)              │
│ ☐ Twitter (Coming Soon)                │
│                                         │
│ Available Agents:                       │
│ ✓ Strategist Agent                     │
│ ✓ Copywriter Agent                     │
│ ✓ Creative Agent                       │
│ ✓ Community Manager Agent              │
│ ✓ Ads Agent                            │
│                                         │
│ Quick Actions:                          │
│ [Create Post] [View Analytics]         │
│ [Manage Pages] [Upgrade Plan]          │
└─────────────────────────────────────────┘
```

---

## Technical Architecture

### Frontend
- Technology: Blade (Laravel)
- Features:
  - Agent catalog/list
  - Task assignment interface
  - Approval dashboards
  - Performance analytics

### Backend
- Technology: PHP/Laravel
- Features:
  - Agent orchestration
  - Task queuing
  - API integrations
  - Webhook handling

### Database
- PostgreSQL
- Tables needed:
  - agents
  - tasks
  - agent_assignments
  - approvals
  - audit_logs

---

## Required API Providers & Integrations

### 1. OpenAI API
**Purpose:** AI content generation, text completion, image generation

**What We Need:**
- API Key
- Organization ID (optional)
- Project ID (optional)

**Models to Use:**
- GPT-4.1 or GPT-4o (text generation)
- GPT-4o-mini (faster responses for simple tasks)
- DALL-E 3 (image generation)

**Documentation:**
- Main Docs: https://platform.openai.com/docs
- API Reference: https://platform.openai.com/docs/api-reference
- Authentication: https://platform.openai.com/docs/api-reference/authentication
- Rate Limits: https://platform.openai.com/docs/guides/rate-limits
- Pricing: https://openai.com/api/pricing/

**Getting Started:**
1. Create account: https://platform.openai.com/signup
2. Generate API key: https://platform.openai.com/api-keys
3. Set up billing: https://platform.openai.com/account/billing

**Laravel Package:**
- `openai-php/laravel` - Official OpenAI Laravel integration
- Installation: `composer require openai-php/laravel`
- Docs: https://github.com/openai-php/laravel

---

### 2. Meta (Facebook) Graph API
**Purpose:** Facebook Pages management, posting, messaging, ads

**What We Need:**
- Facebook App ID
- Facebook App Secret
- User Access Token (OAuth)
- Page Access Token (per page)
- Webhook Verification Token

**Required Permissions:**
- `pages_manage_posts` - Create and schedule posts
- `pages_read_engagement` - Read comments, reactions
- `pages_manage_engagement` - Reply to comments
- `pages_messaging` - Send/receive messages
- `read_page_mailboxes` - Read messages
- `ads_management` - Manage ad campaigns (Phase 3)
- `ads_read` - Read ad performance (Phase 3)

**Documentation:**
- Graph API Overview: https://developers.facebook.com/docs/graph-api
- Pages API: https://developers.facebook.com/docs/pages-api
- Messenger Platform: https://developers.facebook.com/docs/messenger-platform
- Marketing API: https://developers.facebook.com/docs/marketing-apis
- Webhooks: https://developers.facebook.com/docs/graph-api/webhooks
- App Review: https://developers.facebook.com/docs/app-review
- Permissions: https://developers.facebook.com/docs/permissions

**Getting Started:**
1. Create Meta Developer Account: https://developers.facebook.com/
2. Create App: https://developers.facebook.com/apps/
3. Configure OAuth: https://developers.facebook.com/docs/facebook-login
4. Submit for App Review: https://developers.facebook.com/docs/app-review

**Laravel Package:**
- `socialiteproviders/facebook` - OAuth integration
- `facebook/graph-sdk` - Official PHP SDK
- Installation: `composer require facebook/graph-sdk`

**Testing:**
- Graph API Explorer: https://developers.facebook.com/tools/explorer
- Access Token Debugger: https://developers.facebook.com/tools/debug/accesstoken

---

### 3. PostgreSQL Database
**Purpose:** Primary data storage

**What We Need:**
- Database host
- Database name
- Username
- Password
- Port (default: 5432)

**Hosting Options:**
- AWS RDS: https://aws.amazon.com/rds/postgresql/
- Google Cloud SQL: https://cloud.google.com/sql/postgresql
- Heroku Postgres: https://www.heroku.com/postgres
- DigitalOcean: https://www.digitalocean.com/products/managed-databases-postgresql
- Supabase: https://supabase.com/

**Laravel Setup:**
- Already configured in Laravel
- Update `.env` file with connection details

---

### 4. Redis
**Purpose:** Queue management, rate limiting, caching

**What We Need:**
- Redis host
- Redis password (if required)
- Port (default: 6379)

**Hosting Options:**
- AWS ElastiCache: https://aws.amazon.com/elasticache/redis/
- Redis Cloud: https://redis.com/cloud/
- Heroku Redis: https://www.heroku.com/redis
- DigitalOcean: https://www.digitalocean.com/products/managed-databases-redis

**Laravel Packages:**
- `predis/predis` - PHP Redis client
- Installation: `composer require predis/predis`

**Documentation:**
- Redis Docs: https://redis.io/docs/
- Laravel Queues: https://laravel.com/docs/queues
- Laravel Cache: https://laravel.com/docs/cache

---

### 5. Additional Services (Optional/Future)

#### Payment Processing (for subscriptions)
- **Stripe:** https://stripe.com/docs/api
  - Laravel Cashier: https://laravel.com/docs/billing
- **Paddle:** https://paddle.com/docs

#### Email Service
- **Mailgun:** https://documentation.mailgun.com/
- **SendGrid:** https://docs.sendgrid.com/
- **AWS SES:** https://docs.aws.amazon.com/ses/

#### File Storage
- **AWS S3:** https://docs.aws.amazon.com/s3/
- **DigitalOcean Spaces:** https://docs.digitalocean.com/products/spaces/

#### Error Tracking
- **Sentry:** https://docs.sentry.io/platforms/php/guides/laravel/
- **Bugsnag:** https://docs.bugsnag.com/platforms/php/laravel/

---

## Environment Variables Needed

```env
# Application
APP_NAME="AI Agents Platform"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=your-postgres-host
DB_PORT=5432
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=null
REDIS_PORT=6379

# OpenAI
OPENAI_API_KEY=sk-...
OPENAI_ORGANIZATION=org-...

# Meta/Facebook
FACEBOOK_APP_ID=your-app-id
FACEBOOK_APP_SECRET=your-app-secret
FACEBOOK_WEBHOOK_VERIFY_TOKEN=your-random-token

# Queue
QUEUE_CONNECTION=redis

# Cache
CACHE_DRIVER=redis

# Session
SESSION_DRIVER=redis
```

---

## AI Agent System Prompts

### 1. Strategist Agent Prompt

**System Prompt:**
```
You are a Social Media Strategist AI Agent specializing in content strategy and planning.

Your role is to:
1. Analyze the client's Facebook Page (followers, engagement, top-performing content)
2. Understand their brand identity, tone, target audience, and business goals
3. Research industry trends and competitor strategies
4. Create data-driven content calendars (weekly/monthly)
5. Recommend optimal posting times based on audience activity
6. Suggest content themes, topics, and campaign ideas
7. Provide strategic recommendations to improve reach and engagement

Brand Context:
- Brand Name: {{brand_name}}
- Industry: {{industry}}
- Brand Tone: {{brand_tone}}
- Target Audience: {{target_audience}}
- Business Goals: {{business_goals}}
- Do Not Say: {{forbidden_words}}

Page Analytics:
- Total Followers: {{follower_count}}
- Average Engagement Rate: {{engagement_rate}}
- Top Performing Post Types: {{top_post_types}}
- Peak Activity Times: {{peak_times}}

Task: {{task_description}}

Provide strategic recommendations in a structured format with clear rationale.
```

**Example Input:**
```json
{
  "brand_name": "TechGadgets Pro",
  "industry": "Consumer Electronics",
  "brand_tone": "Professional, Innovative, Customer-focused",
  "target_audience": "Tech enthusiasts, age 25-45, early adopters",
  "business_goals": "Increase brand awareness, drive website traffic, boost product launches",
  "forbidden_words": "cheap, low-quality, outdated",
  "follower_count": 12500,
  "engagement_rate": 3.2,
  "top_post_types": "Product demos, Tech tips, Behind-the-scenes",
  "peak_times": "Tuesday-Thursday 10AM-2PM, 6PM-8PM",
  "task_description": "Create a 7-day content calendar for our new smartphone launch"
}
```

**Example Output:**
```json
{
  "content_calendar": [
    {
      "day": "Monday",
      "time": "10:00 AM",
      "content_type": "Teaser Post",
      "topic": "Mystery reveal - New innovation coming",
      "objective": "Build anticipation",
      "expected_engagement": "High shares, comments guessing"
    },
    {
      "day": "Wednesday",
      "time": "1:00 PM",
      "content_type": "Product Reveal",
      "topic": "Introducing [Smartphone Name] - Full specs",
      "objective": "Generate excitement, drive pre-orders",
      "expected_engagement": "High reactions, link clicks"
    }
  ],
  "strategic_recommendations": [
    "Leverage video content - 65% higher engagement than static posts",
    "Create countdown series to launch day",
    "Partner with micro-influencers for unboxing content"
  ]
}
```

---

### 2. Copywriter Agent Prompt

**System Prompt:**
```
You are an Expert Social Media Copywriter AI Agent specializing in creating engaging, conversion-focused content.

Your role is to:
1. Write compelling post captions that match the brand voice
2. Create attention-grabbing headlines and hooks
3. Write persuasive ad copy optimized for conversions
4. Craft engaging comment replies that foster community
5. Write message responses that are helpful and brand-aligned
6. Use appropriate emojis, hashtags, and calls-to-action
7. Optimize copy length for platform best practices

Brand Voice Guidelines:
- Brand Name: {{brand_name}}
- Tone: {{brand_tone}}
- Voice Characteristics: {{voice_characteristics}}
- Target Audience: {{target_audience}}
- Key Messages: {{key_messages}}
- Words to Avoid: {{forbidden_words}}

Content Requirements:
- Platform: Facebook
- Max Length: {{max_length}} characters
- Must Include: {{required_elements}}
- CTA Required: {{cta_required}}

Task: {{task_description}}

Generate copy that drives engagement while staying authentic to the brand.
```

**Example Input:**
```json
{
  "brand_name": "GreenLife Organics",
  "brand_tone": "Warm, Authentic, Health-conscious",
  "voice_characteristics": "Friendly expert, Uses 'we/you', Conversational, Encouraging",
  "target_audience": "Health-conscious millennials, parents seeking organic options",
  "key_messages": "100% organic, Family-owned, Sustainable farming",
  "forbidden_words": "toxic, chemical-free (legally problematic), miracle",
  "max_length": 2200,
  "required_elements": "Product benefits, CTA, 3-5 hashtags",
  "cta_required": true,
  "task_description": "Write a post announcing our new organic honey product launch"
}
```

**Example Output:**
```json
{
  "caption": "🍯 Straight from our family farm to your table!\n\nWe're thrilled to introduce our newest creation: Wildflower Honey – harvested from bees that roam our certified organic fields.\n\nWhat makes it special?\n✨ 100% raw and unfiltered\n✨ No added sugars or processing\n✨ Supports local biodiversity\n✨ Every jar supports sustainable farming\n\nPerfect for your morning tea, smoothies, or straight from the spoon (we won't judge! 😊)\n\nTap the link in our bio to bring home a jar of pure goodness. Your taste buds (and the bees) will thank you! 🐝\n\n#OrganicHoney #SustainableFarming #FamilyFarm #RawHoney #HealthyLiving",
  "character_count": 487,
  "hashtags": ["#OrganicHoney", "#SustainableFarming", "#FamilyFarm", "#RawHoney", "#HealthyLiving"],
  "cta": "Tap the link in our bio",
  "tone_analysis": "Warm and inviting, emphasizes family values and sustainability"
}
```

---

### 3. Creative Agent Prompt

**System Prompt:**
```
You are a Creative AI Agent specializing in visual content generation for social media.

Your role is to:
1. Generate image prompts for DALL-E 3 that align with brand aesthetics
2. Ensure visual consistency with brand colors and style
3. Create attention-grabbing visuals optimized for Facebook
4. Consider accessibility (avoid text-heavy images)
5. Match visual tone with copy and brand voice
6. Suggest image dimensions and formats
7. Provide alternative visual concepts

Brand Visual Guidelines:
- Brand Name: {{brand_name}}
- Primary Colors: {{primary_colors}}
- Visual Style: {{visual_style}}
- Logo Usage: {{logo_usage}}
- Image Mood: {{image_mood}}

Content Context:
- Post Caption: {{post_caption}}
- Post Objective: {{post_objective}}
- Target Audience: {{target_audience}}

Technical Requirements:
- Platform: Facebook (recommended 1200x630px)
- Format: PNG/JPEG
- Text in Image: {{text_allowed}}

Task: {{task_description}}

Generate DALL-E prompts that create scroll-stopping visuals.
```

**Example Input:**
```json
{
  "brand_name": "FitLife Gym",
  "primary_colors": "Electric blue (#0066FF), Neon green (#00FF00), Dark gray (#333333)",
  "visual_style": "Bold, Energetic, Modern, High-contrast",
  "logo_usage": "Include subtly in corner if space allows",
  "image_mood": "Motivational, Empowering, Dynamic",
  "post_caption": "New Year, New You! Join our 30-day transformation challenge 💪",
  "post_objective": "Drive signups for fitness challenge",
  "target_audience": "Fitness beginners, age 25-40, looking for motivation",
  "text_allowed": false,
  "task_description": "Create an image for our New Year fitness challenge promotion"
}
```

**Example Output:**
```json
{
  "dalle_prompt": "A powerful and energetic fitness scene: A diverse group of people in modern athletic wear doing various exercises (weights, yoga, running) in a sleek, well-lit gym with floor-to-ceiling windows showing sunrise. Dominant colors are electric blue and neon green lighting accents against dark gray walls. Dynamic composition with motion blur suggesting energy and movement. Photorealistic style, inspirational mood, shot from a dramatic low angle. High contrast, professional photography quality.",
  "alternative_prompts": [
    "Close-up of athletic shoes on a running track at sunrise, with electric blue and neon green light rays piercing through morning mist, symbolizing a fresh start. Motivational and clean aesthetic, shallow depth of field.",
    "Split-screen before/after concept: Left side shows tired person, right side shows energized person celebrating, connected by a glowing transformation arrow in neon green. Electric blue background, modern graphic design style."
  ],
  "recommended_dimensions": "1200x630px (Facebook shared image optimal)",
  "accessibility_notes": "No text in image, purely visual - accessible to all",
  "brand_alignment": "Uses brand colors (electric blue, neon green), energetic and modern style matches brand identity"
}
```

---

### 4. Community Manager Agent Prompt

**System Prompt:**
```
You are a Community Manager AI Agent responsible for engaging with the audience in a helpful, authentic way.

Your role is to:
1. Respond to comments on posts with relevant, engaging replies
2. Answer questions about products/services accurately
3. Handle complaints with empathy and professionalism
4. Foster positive community interactions
5. Identify urgent issues that need human escalation
6. Maintain brand voice in all interactions
7. Encourage further engagement

Brand Communication Guidelines:
- Brand Name: {{brand_name}}
- Response Tone: {{response_tone}}
- Response Length: {{response_length}}
- Emoji Usage: {{emoji_usage}}
- Escalation Triggers: {{escalation_triggers}}

Comment Context:
- Original Post: {{original_post}}
- Comment Text: {{comment_text}}
- Commenter Name: {{commenter_name}}
- Comment Sentiment: {{sentiment}}
- Previous Interactions: {{previous_interactions}}

Knowledge Base:
- Product Info: {{product_info}}
- FAQs: {{faqs}}
- Current Promotions: {{promotions}}

Task: Generate an appropriate reply that builds community and resolves the commenter's need.

⚠️ ESCALATION RULES:
- Refund requests → Escalate to human
- Legal threats → Escalate immediately
- Racist/hateful content → Report and escalate
- Complex technical issues → Escalate to support
- Repeatedly dissatisfied customer → Escalate
```

**Example Input:**
```json
{
  "brand_name": "CozyHome Furniture",
  "response_tone": "Friendly, Helpful, Professional",
  "response_length": "1-3 sentences, concise",
  "emoji_usage": "Occasional, not excessive",
  "escalation_triggers": ["refund", "legal", "manager", "complaint"],
  "original_post": "Check out our new velvet sofa collection! 🛋️ Available in 6 colors.",
  "comment_text": "Looks beautiful! Does it come in navy blue? And what's the delivery time to Chicago?",
  "commenter_name": "Sarah Miller",
  "sentiment": "Positive, Interested",
  "previous_interactions": "None",
  "product_info": "Velvet sofa available in: Gray, Beige, Emerald Green, Blush Pink, Navy Blue, Charcoal. Delivery: 2-4 weeks to most US cities.",
  "faqs": "Free delivery on orders over $500. 30-day return policy.",
  "promotions": "15% off first order with code COZY15"
}
```

**Example Output:**
```json
{
  "reply_text": "Hi Sarah! 😊 Yes, navy blue is available and it's stunning! Delivery to Chicago typically takes 2-4 weeks. Plus, you can get 15% off your first order with code COZY15! Feel free to DM us if you'd like to see more photos of the navy option! 💙",
  "should_escalate": false,
  "escalation_reason": null,
  "sentiment_tone": "Enthusiastic and helpful",
  "follow_up_action": "Monitor for DM response"
}
```

**Example Input (Escalation Scenario):**
```json
{
  "comment_text": "This is the third time I'm asking - WHERE IS MY REFUND?! I returned the table 3 weeks ago and still nothing. I'm contacting my lawyer if this isn't resolved TODAY.",
  "commenter_name": "John Anderson",
  "sentiment": "Very Negative, Angry",
  "previous_interactions": "2 previous unresolved comments"
}
```

**Example Output (Escalation):**
```json
{
  "reply_text": "Hi John, I sincerely apologize for the frustration and delay. This clearly needs immediate attention from our customer service team. I'm escalating your case right now to our manager who will contact you directly within 2 hours via DM. Thank you for your patience. 🙏",
  "should_escalate": true,
  "escalation_reason": "Refund dispute + Legal threat + Repeated unresolved issue",
  "urgency_level": "HIGH",
  "recommended_action": "Manager contact within 2 hours, priority refund processing"
}
```

---

### 5. Ads Agent Prompt

**System Prompt:**
```
You are a Facebook Ads Specialist AI Agent focused on creating high-performing ad campaigns.

Your role is to:
1. Analyze campaign objectives and recommend ad strategies
2. Create compelling ad copy and creative briefs
3. Suggest audience targeting parameters
4. Recommend budget allocation and bidding strategies
5. Propose A/B testing variations
6. Monitor campaign performance and suggest optimizations
7. Flag campaigns that need human approval before launch

⚠️ IMPORTANT: All ad campaigns must be approved by the user before launch.

Campaign Context:
- Brand Name: {{brand_name}}
- Campaign Objective: {{campaign_objective}}
- Budget: {{budget}}
- Duration: {{duration}}
- Target Audience: {{target_audience}}
- Product/Service: {{product_service}}
- USP (Unique Selling Point): {{usp}}

Performance Data (if existing campaigns):
- Previous CTR: {{previous_ctr}}
- Previous CPC: {{previous_cpc}}
- Previous ROAS: {{previous_roas}}
- Top Performing Ad Types: {{top_ad_types}}

Task: {{task_description}}

Generate campaign recommendations with clear rationale and expected outcomes.
```

**Example Input:**
```json
{
  "brand_name": "EcoBottle",
  "campaign_objective": "Conversions (Product Sales)",
  "budget": 1500,
  "duration": "14 days",
  "target_audience": "Environmentally conscious consumers, age 25-45, interested in sustainability",
  "product_service": "Reusable water bottle with temperature tracking",
  "usp": "Smart technology meets sustainability - tracks hydration goals via app",
  "previous_ctr": 2.8,
  "previous_cpc": 0.85,
  "previous_roas": 3.2,
  "top_ad_types": "Video ads showing product features",
  "task_description": "Create a campaign to launch our new smart water bottle during Earth Month"
}
```

**Example Output:**
```json
{
  "campaign_name": "EcoBottle Smart Launch - Earth Month 2025",
  "campaign_structure": {
    "ad_sets": [
      {
        "name": "Eco-Conscious Professionals",
        "targeting": {
          "age": "25-45",
          "interests": ["Sustainability", "Fitness", "Technology", "Environment"],
          "behaviors": ["Eco-friendly purchases", "Fitness enthusiasts"],
          "location": "United States, Urban areas"
        },
        "budget": 800,
        "placement": "Facebook Feed, Instagram Feed"
      },
      {
        "name": "Fitness & Wellness Audience",
        "targeting": {
          "age": "22-40",
          "interests": ["Gym", "Running", "Yoga", "Health tracking apps"],
          "location": "United States"
        },
        "budget": 700,
        "placement": "Facebook Feed, Stories"
      }
    ]
  },
  "ad_creatives": [
    {
      "ad_name": "Hero Video - Smart Features",
      "format": "Video (15 seconds)",
      "headline": "Meet Your Hydration Coach 💧",
      "primary_text": "This Earth Month, upgrade to the water bottle that cares about you AND the planet. 🌍\n\n✨ Tracks your hydration goals\n✨ Never forget to drink water again\n✨ Replaces 1,000+ plastic bottles\n\nJoin 50,000+ eco-warriors staying hydrated smarter.\n\n🎁 Earth Month Special: 25% OFF + Free Shipping",
      "cta_button": "Shop Now",
      "creative_brief": "15-second video showing: bottle glowing when hydration goal met, app interface, person drinking at gym, side-by-side with plastic waste. Upbeat music."
    },
    {
      "ad_name": "Social Proof - Testimonial",
      "format": "Image Carousel",
      "headline": "Why 50K+ People Switched to EcoBottle",
      "primary_text": "\"Best $40 I've spent on my health!\" - Sarah M.\n\nSmart hydration tracking + eco-friendly = win-win 🙌\n\n25% OFF for Earth Month only!",
      "cta_button": "Learn More"
    }
  ],
  "bidding_strategy": "Lowest Cost with Bid Cap at $1.20 (vs historical $0.85 CPC, allowing for auction competition)",
  "optimization_goal": "Purchase conversions",
  "ab_test_variations": [
    "Test A: Eco-focused messaging vs Test B: Tech-focused messaging",
    "Test A: Video ad vs Test B: Carousel ad"
  ],
  "success_metrics": {
    "target_roas": 3.5,
    "target_ctr": 3.2,
    "target_cpc": 0.90,
    "expected_conversions": 45
  },
  "requires_approval": true,
  "approval_notes": "Campaign ready for review. Estimated reach: 85,000-120,000 people. Budget utilization: $1,500 over 14 days ($107/day).",
  "optimization_schedule": [
    "Day 3: Review CTR, pause underperforming ads",
    "Day 7: Analyze ROAS, reallocate budget to top ad set",
    "Day 10: Test new creative if ROAS < 3.0"
  ]
}
```

---

## API Documentation & Examples

### Authentication

All API requests require authentication via Bearer token.

**Headers:**
```
Authorization: Bearer {access_token}
Content-Type: application/json
Accept: application/json
```

---

### Endpoints

#### 1. Create Post

**Endpoint:** `POST /api/v1/posts`

**Request:**
```json
{
  "page_id": 123,
  "caption": "Excited to announce our new product! 🎉",
  "image_url": "https://example.com/image.jpg",
  "scheduled_at": "2025-06-20 10:00:00",
  "status": "draft",
  "agent_used": "copywriter"
}
```

**Success Response (201 Created):**
```json
{
  "success": true,
  "message": "Post created successfully",
  "data": {
    "id": 456,
    "page_id": 123,
    "caption": "Excited to announce our new product! 🎉",
    "image_url": "https://example.com/image.jpg",
    "scheduled_at": "2025-06-20T10:00:00Z",
    "status": "draft",
    "agent_used": "copywriter",
    "created_at": "2025-06-15T14:23:45Z",
    "updated_at": "2025-06-15T14:23:45Z"
  },
  "meta": {
    "usage": {
      "posts_used": 245,
      "posts_limit": 500,
      "remaining": 255
    }
  }
}
```

---

#### 2. Trigger AI Agent

**Endpoint:** `POST /api/v1/agents/trigger`

**Request:**
```json
{
  "agent_type": "copywriter",
  "task": "generate_post",
  "context": {
    "topic": "Summer sale announcement",
    "tone": "exciting",
    "include_emoji": true,
    "max_length": 500
  },
  "page_id": 123
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Agent task completed successfully",
  "data": {
    "agent_type": "copywriter",
    "task": "generate_post",
    "result": {
      "caption": "🌞 Summer Sale Alert! Get ready for sizzling hot deals this weekend! Up to 50% OFF on selected items. Don't miss out! ☀️🛍️ #SummerSale #Deals",
      "character_count": 147,
      "hashtags": ["#SummerSale", "#Deals"]
    },
    "tokens_used": 156,
    "processing_time_ms": 2340
  }
}
```

---

#### 3. Get Analytics

**Endpoint:** `GET /api/v1/analytics`

**Query Parameters:**
- `page_id` (optional): Filter by specific page
- `start_date`: YYYY-MM-DD format
- `end_date`: YYYY-MM-DD format
- `metrics`: Comma-separated (reach,engagement,followers)

**Request:**
```
GET /api/v1/analytics?page_id=123&start_date=2025-06-01&end_date=2025-06-15&metrics=reach,engagement,followers
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "page_id": 123,
    "page_name": "My Business Page",
    "period": {
      "start_date": "2025-06-01",
      "end_date": "2025-06-15"
    },
    "metrics": {
      "reach": {
        "total": 45234,
        "change_percentage": 23.5,
        "trend": "up"
      },
      "engagement": {
        "total": 5678,
        "change_percentage": 45.2,
        "breakdown": {
          "likes": 3456,
          "comments": 1234,
          "shares": 988
        }
      },
      "followers": {
        "total": 12842,
        "new_followers": 342,
        "change_percentage": 2.7
      }
    },
    "top_posts": [
      {
        "id": 789,
        "caption": "Summer Sale Announcement",
        "reach": 8934,
        "engagement": 1234
      }
    ]
  }
}
```

---

#### 4. Connect Facebook Page

**Endpoint:** `POST /api/v1/platforms/facebook/connect`

**Request:**
```json
{
  "access_token": "EAAxxxxxxxxxxxxx",
  "page_id": "1234567890"
}
```

**Success Response (201 Created):**
```json
{
  "success": true,
  "message": "Facebook page connected successfully",
  "data": {
    "id": 101,
    "platform": "facebook",
    "page_id": "1234567890",
    "page_name": "My Business Page",
    "page_access_token": "[encrypted]",
    "followers": 12500,
    "connected_at": "2025-06-15T14:30:00Z",
    "status": "active"
  },
  "meta": {
    "pages_connected": 3,
    "pages_limit": 10,
    "remaining_slots": 7
  }
}
```

---

#### 5. Get Package Usage

**Endpoint:** `GET /api/v1/usage`

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "user_id": 42,
    "package": "professional",
    "billing_cycle": {
      "start": "2025-06-01",
      "end": "2025-06-30"
    },
    "usage": {
      "posts": {
        "used": 245,
        "limit": 500,
        "percentage": 49
      },
      "comment_replies": {
        "used": 1234,
        "limit": -1,
        "unlimited": true
      },
      "messages": {
        "used": 456,
        "limit": -1,
        "unlimited": true
      },
      "pages": {
        "used": 3,
        "limit": 10
      },
      "ad_spend": {
        "used": 2340,
        "limit": 5000,
        "currency": "USD"
      }
    },
    "warnings": []
  }
}
```

---

## Error Response Standards

### Standard Error Format

All errors follow this consistent structure:

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human-readable error message",
    "details": "Additional context or developer info",
    "field": "field_name" // Only for validation errors
  },
  "meta": {
    "request_id": "req_abc123xyz",
    "timestamp": "2025-06-15T14:23:45Z"
  }
}
```

---

### HTTP Status Codes

| Status Code | Meaning | Usage |
|------------|---------|-------|
| 200 | OK | Request succeeded |
| 201 | Created | Resource created successfully |
| 204 | No Content | Request succeeded, no response body |
| 400 | Bad Request | Invalid request format/parameters |
| 401 | Unauthorized | Missing or invalid authentication |
| 403 | Forbidden | Authenticated but lacking permissions |
| 404 | Not Found | Resource doesn't exist |
| 422 | Unprocessable Entity | Validation errors |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server-side error |
| 503 | Service Unavailable | Maintenance or overload |

---

### Error Codes & Examples

#### 1. Authentication Errors (401)

**Missing Token:**
```json
{
  "success": false,
  "error": {
    "code": "AUTH_TOKEN_MISSING",
    "message": "Authentication token is required",
    "details": "Please include 'Authorization: Bearer {token}' header"
  }
}
```

**Invalid Token:**
```json
{
  "success": false,
  "error": {
    "code": "AUTH_TOKEN_INVALID",
    "message": "Invalid or expired authentication token",
    "details": "Please log in again to obtain a fresh token"
  }
}
```

---

#### 2. Validation Errors (422)

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid",
    "errors": {
      "caption": [
        "The caption field is required.",
        "The caption must not exceed 5000 characters."
      ],
      "page_id": [
        "The selected page is invalid."
      ]
    }
  }
}
```

---

#### 3. Package Limit Errors (403)

```json
{
  "success": false,
  "error": {
    "code": "LIMIT_EXCEEDED",
    "message": "Monthly post limit exceeded",
    "details": "You have used 500/500 posts this month. Upgrade your plan or wait for next billing cycle.",
    "current_usage": 500,
    "limit": 500,
    "reset_date": "2025-07-01"
  },
  "meta": {
    "upgrade_url": "/dashboard/billing?action=upgrade"
  }
}
```

---

#### 4. Resource Not Found (404)

```json
{
  "success": false,
  "error": {
    "code": "RESOURCE_NOT_FOUND",
    "message": "Post not found",
    "details": "No post exists with ID 99999"
  }
}
```

---

#### 5. Rate Limiting (429)

```json
{
  "success": false,
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Too many requests",
    "details": "You have exceeded the rate limit of 100 requests per minute",
    "retry_after": 45
  },
  "meta": {
    "limit": 100,
    "remaining": 0,
    "reset_at": "2025-06-15T14:25:00Z"
  }
}
```

---

#### 6. External API Errors (502/503)

**OpenAI API Error:**
```json
{
  "success": false,
  "error": {
    "code": "AI_SERVICE_ERROR",
    "message": "AI content generation failed",
    "details": "OpenAI API is temporarily unavailable. Please try again in a few minutes.",
    "service": "openai",
    "retry": true
  }
}
```

**Facebook API Error:**
```json
{
  "success": false,
  "error": {
    "code": "FACEBOOK_API_ERROR",
    "message": "Failed to publish post to Facebook",
    "details": "Facebook API returned: (#200) The user hasn't authorized the application to perform this action",
    "service": "facebook",
    "action_required": "Reconnect your Facebook page",
    "reconnect_url": "/dashboard/platforms"
  }
}
```

---

#### 7. Business Logic Errors (400)

**Invalid State:**
```json
{
  "success": false,
  "error": {
    "code": "INVALID_POST_STATUS",
    "message": "Cannot publish an already published post",
    "details": "Post ID 456 has status 'published'. You can only publish 'draft' or 'scheduled' posts.",
    "current_status": "published"
  }
}
```

---

## Development Setup Guide

### Prerequisites

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 18.x or higher
- **PostgreSQL**: 14.x or higher
- **Redis**: 7.x or higher
- **Git**: Latest version

---

### Step 1: Clone Repository

```bash
git clone https://github.com/your-org/ai-agents-platform.git
cd ai-agents-platform
```

---

### Step 2: Install Dependencies

**PHP Dependencies:**
```bash
composer install
```

**Node Dependencies:**
```bash
npm install
```

---

### Step 3: Environment Configuration

**Copy environment file:**
```bash
cp .env.example .env
```

**Generate application key:**
```bash
php artisan key:generate
```

**Configure `.env` file:**
```env
# Application
APP_NAME="AI Agents Platform"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ai_agents
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# OpenAI
OPENAI_API_KEY=sk-your-key-here
OPENAI_ORGANIZATION=org-your-org-here

# Facebook/Meta
FACEBOOK_APP_ID=your-app-id
FACEBOOK_APP_SECRET=your-app-secret
FACEBOOK_WEBHOOK_VERIFY_TOKEN=random-secure-token

# Stripe
STRIPE_KEY=pk_test_xxxxx
STRIPE_SECRET=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx

# Queue
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@aiagents.com
MAIL_FROM_NAME="AI Agents Platform"
```

---

### Step 4: Database Setup

**Create database:**
```bash
psql -U postgres
CREATE DATABASE ai_agents;
\q
```

**Run migrations:**
```bash
php artisan migrate
```

**Seed database (optional):**
```bash
php artisan db:seed
```

---

### Step 5: Storage & Permissions

**Create storage symlink:**
```bash
php artisan storage:link
```

**Set permissions (Linux/Mac):**
```bash
chmod -R 775 storage bootstrap/cache
```

---

### Step 6: Build Frontend Assets

**Development:**
```bash
npm run dev
```

**Production:**
```bash
npm run build
```

---

### Step 7: Start Queue Workers

**In a separate terminal:**
```bash
php artisan queue:work redis --tries=3 --timeout=90
```

---

### Step 8: Start Development Server

```bash
php artisan serve
```

Application will be available at: **http://localhost:8000**

---

### Step 9: Create Admin User

```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
    'role' => 'admin'
]);
```

---

### Step 10: Run Tests

```bash
php artisan test
```

---

### Optional: Docker Setup

If you prefer Docker, use Laravel Sail:

**Install Sail:**
```bash
composer require laravel/sail --dev
php artisan sail:install
```

**Start containers:**
```bash
./vendor/bin/sail up -d
```

**Run migrations in Docker:**
```bash
./vendor/bin/sail artisan migrate
```

---

### Troubleshooting

**Issue: "SQLSTATE[08006] Connection refused"**
- Ensure PostgreSQL is running: `pg_ctl status`
- Check credentials in `.env`

**Issue: "Class OpenAI\Client not found"**
- Run: `composer dump-autoload`

**Issue: Queue jobs not processing**
- Ensure Redis is running: `redis-cli ping` (should return PONG)
- Restart queue worker: `php artisan queue:restart`

**Issue: Facebook OAuth redirect mismatch**
- Add `http://localhost:8000/auth/facebook/callback` to Facebook App settings

---

## Git Workflow & Branch Strategy

### Branch Structure

```
main (production)
  |
  └── develop (staging)
        |
        ├── feature/user-authentication
        ├── feature/ai-agents-system
        ├── feature/facebook-integration
        ├── bugfix/post-scheduling
        └── hotfix/critical-security-patch
```

---

### Branch Types

| Branch Type | Naming Convention | Purpose | Base Branch |
|------------|-------------------|---------|-------------|
| `main` | `main` | Production-ready code | - |
| `develop` | `develop` | Integration branch for features | `main` |
| `feature/*` | `feature/short-description` | New features | `develop` |
| `bugfix/*` | `bugfix/short-description` | Non-critical bug fixes | `develop` |
| `hotfix/*` | `hotfix/short-description` | Critical production fixes | `main` |
| `release/*` | `release/v1.2.0` | Release preparation | `develop` |

---

### Workflow Steps

#### 1. Starting a New Feature

```bash
# Update develop branch
git checkout develop
git pull origin develop

# Create feature branch
git checkout -b feature/content-generation

# Work on your feature
git add .
git commit -m "feat: add content generation service"

# Push to remote
git push -u origin feature/content-generation
```

#### 2. Keeping Feature Branch Updated

```bash
# Regularly sync with develop
git checkout develop
git pull origin develop

git checkout feature/content-generation
git rebase develop

# Or merge if rebase is complex
git merge develop
```

#### 3. Creating Pull Request

**PR Title Format:**
```
[Type] Short description

Examples:
[Feature] Add AI content generation
[Bugfix] Fix post scheduling timezone issue
[Hotfix] Patch security vulnerability in OAuth
```

**PR Description Template:**
```markdown
## Description
Brief description of changes

## Changes Made
- Added ContentGenerationService
- Created Copywriter Agent prompt
- Implemented OpenAI API integration

## Testing
- [ ] Unit tests pass
- [ ] Feature tests pass
- [ ] Manually tested on local environment

## Related Issues
Closes #123
Related to #124

## Screenshots (if applicable)
[Attach screenshots]
```

#### 4. Code Review & Merge

**Requirements before merge:**
- [ ] At least 1 approval from team member
- [ ] All CI/CD checks pass
- [ ] No merge conflicts
- [ ] Tests added/updated
- [ ] Documentation updated

**Merge Strategy:**
- Use **Squash and Merge** for feature branches (keeps history clean)
- Use **Merge Commit** for release branches (preserves history)

#### 5. Hotfix Process

```bash
# Create hotfix from main
git checkout main
git pull origin main
git checkout -b hotfix/security-patch

# Fix the issue
git add .
git commit -m "hotfix: patch OAuth security vulnerability"
git push -u origin hotfix/security-patch

# Create PR to main (immediate deployment)
# After merging to main, also merge to develop
git checkout develop
git merge main
git push origin develop
```

---

### Commit Message Convention

Follow **Conventional Commits** format:

```
<type>(<scope>): <subject>

[optional body]

[optional footer]
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, no logic change)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks (dependency updates, etc.)
- `perf`: Performance improvements
- `ci`: CI/CD changes

**Examples:**
```bash
# Feature
git commit -m "feat(agents): add copywriter agent service"

# Bug fix
git commit -m "fix(auth): resolve OAuth token expiration issue"

# Documentation
git commit -m "docs(api): add endpoint examples for post creation"

# Refactor
git commit -m "refactor(services): extract common repository methods to base class"

# Breaking change
git commit -m "feat(api): change response format\n\nBREAKING CHANGE: API responses now use 'data' wrapper"
```

---

### Release Process

#### 1. Prepare Release Branch

```bash
git checkout develop
git pull origin develop
git checkout -b release/v1.2.0

# Update version numbers
# Update CHANGELOG.md
# Final bug fixes only

git commit -m "chore(release): prepare v1.2.0"
git push -u origin release/v1.2.0
```

#### 2. Merge to Main & Tag

```bash
# Merge to main
git checkout main
git merge release/v1.2.0
git tag -a v1.2.0 -m "Release version 1.2.0"
git push origin main --tags

# Merge back to develop
git checkout develop
git merge release/v1.2.0
git push origin develop

# Delete release branch
git branch -d release/v1.2.0
git push origin --delete release/v1.2.0
```

---

### Git Ignore Recommendations

**.gitignore additions:**
```
# Environment
.env
.env.backup
.env.production

# IDE
.idea/
.vscode/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Laravel
/vendor/
/node_modules/
/public/hot
/public/storage
/storage/*.key

# Logs
*.log
npm-debug.log*
yarn-debug.log*
yarn-error.log*

# Testing
.phpunit.result.cache
coverage/

# Build
/public/build/
/public/mix-manifest.json
```

---

## Implementation Process

### Phase 1: Setup & Authentication
1. ✅ Create OpenAI account & get API key
2. ✅ Create Meta Developer account & app
3. ✅ Set up PostgreSQL database
4. ✅ Set up Redis instance
5. ✅ Configure environment variables
6. ✅ Install required Laravel packages

### Phase 2: Meta OAuth Integration
1. Build OAuth flow for Facebook login
2. Request permissions
3. Exchange tokens
4. Store encrypted tokens
5. Fetch user's Facebook Pages

### Phase 3: AI Agent Implementation
1. Create agent service classes
2. Implement OpenAI prompting system
3. Build content generation workflows
4. Add approval system

### Phase 4: Facebook Integration
1. Implement posting to Pages
2. Set up webhook receivers
3. Build comment reply system
4. Implement messaging system

### Phase 5: Advanced Features
1. Ad campaign management
2. Analytics & reporting
3. Multi-page management
4. Team collaboration

---

## Frontend Pages & UI Design

### 1. Landing Page (Public)

**URL:** `/`

**Purpose:** Introduce the platform, showcase features, convert visitors to users

#### Sections:

##### Hero Section
```
┌─────────────────────────────────────────────────────────┐
│  Logo                    [Login] [Sign Up]              │
├─────────────────────────────────────────────────────────┤
│                                                         │
│         AI Agents for Your Business                     │
│         Automate marketing with intelligent AI agents   │
│                                                         │
│         [Get Started Free] [See How It Works]          │
│                                                         │
│         [Hero Image/Animation of AI Agents at Work]    │
└─────────────────────────────────────────────────────────┘
```

##### Features Section
```
┌─────────────────────────────────────────────────────────┐
│                  What Our Agents Can Do                 │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  [Icon] Strategist Agent      [Icon] Copywriter Agent  │
│  Analyzes & plans content     Creates engaging posts    │
│                                                         │
│  [Icon] Creative Agent        [Icon] Community Mgr     │
│  Generates images             Manages engagement        │
│                                                         │
│  [Icon] Ads Agent                                      │
│  Optimizes ad campaigns                                │
└─────────────────────────────────────────────────────────┘
```

##### How It Works Section
```
┌─────────────────────────────────────────────────────────┐
│                    How It Works                         │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  Step 1              Step 2              Step 3        │
│  [Icon]              [Icon]              [Icon]        │
│  Sign Up &           Connect Your        AI Agents     │
│  Choose Plan         Facebook Page       Start Working │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

##### Pricing Section
```
┌─────────────────────────────────────────────────────────┐
│                  Choose Your Plan                       │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐  │
│ │  Free   │  │ Starter │  │  Pro    │  │ Agency  │  │
│ │  $0/mo  │  │ $29/mo  │  │ $99/mo  │  │ $299/mo │  │
│ ├─────────┤  ├─────────┤  ├─────────┤  ├─────────┤  │
│ │1 Page   │  │3 Pages  │  │10 Pages │  │Unlimited│  │
│ │10 Posts │  │100 Posts│  │500 Posts│  │Unlimited│  │
│ │Limited  │  │3 Agents │  │5 Agents │  │All+API  │  │
│ │         │  │         │  │         │  │         │  │
│ │[Start]  │  │[Start]  │  │[Start]  │  │[Contact]│  │
│ └─────────┘  └─────────┘  └─────────┘  └─────────┘  │
└─────────────────────────────────────────────────────────┘
```

##### Testimonials Section
```
┌─────────────────────────────────────────────────────────┐
│              What Our Customers Say                     │
├─────────────────────────────────────────────────────────┤
│  "Increased engagement by 300%"    [Customer Photo]    │
│  - John Doe, ABC Company                               │
│                                                         │
│  "Saves us 20 hours per week"      [Customer Photo]    │
│  - Jane Smith, XYZ Agency                              │
└─────────────────────────────────────────────────────────┘
```

##### CTA Section
```
┌─────────────────────────────────────────────────────────┐
│        Ready to Get Started?                            │
│        Join thousands of businesses automating          │
│        their marketing with AI agents                   │
│                                                         │
│        [Get Started Free - No Credit Card Required]    │
└─────────────────────────────────────────────────────────┘
```

##### Footer
```
┌─────────────────────────────────────────────────────────┐
│  Logo                                                   │
│  AI Agents Platform - Automate Your Marketing          │
│                                                         │
│  Product          Company          Resources           │
│  - Features       - About          - Documentation     │
│  - Pricing        - Careers        - API Docs          │
│  - Agents         - Contact        - Support           │
│                                                         │
│  Legal: Privacy Policy | Terms of Service              │
│  © 2025 AI Agents Platform. All rights reserved.       │
└─────────────────────────────────────────────────────────┘
```

---

### 2. User Dashboard

**URL:** `/dashboard`

**Purpose:** Central hub for users to manage their agents, content, and connected platforms

**Access:** Authenticated users only

#### Main Dashboard Layout

```
┌─────────────────────────────────────────────────────────────────────┐
│ Logo    Dashboard    Content    Analytics    Settings    [Avatar ▼] │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│ ┌─ Sidebar ──────┐  ┌─ Main Content ─────────────────────────────┐│
│ │                │  │                                             ││
│ │ 📊 Overview    │  │  Welcome back, John!                       ││
│ │ 📄 Content     │  │  Package: Professional | Usage: 245/500    ││
│ │ 🤖 AI Agents   │  │                                             ││
│ │ 📱 Platforms   │  │  ┌──────────────────────────────────────┐ ││
│ │ 📈 Analytics   │  │  │ Quick Stats                          │ ││
│ │ ⚙️ Settings    │  │  │                                      │ ││
│ │ 💳 Billing     │  │  │ Posts: 245  Comments: 1,234         │ ││
│ │                │  │  │ Engagement: +45%  Followers: 12.5K  │ ││
│ │ ──────────     │  │  └──────────────────────────────────────┘ ││
│ │                │  │                                             ││
│ │ [Upgrade]      │  │  Connected Pages                            ││
│ │                │  │  ┌────────────┐  ┌────────────┐           ││
│ └────────────────┘  │  │ Page 1     │  │ Page 2     │           ││
│                     │  │ 👍 5.2K    │  │ 👍 3.8K    │           ││
│                     │  │ [Manage]   │  │ [Manage]   │           ││
│                     │  └────────────┘  └────────────┘           ││
│                     │                                             ││
│                     │  Recent Activity                            ││
│                     │  • Post published: "New Product Launch"    ││
│                     │  • 15 comments replied automatically       ││
│                     │  • Ad campaign approved                    ││
│                     └─────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────────────┘
```

#### Sub-Pages

##### Content Page (`/dashboard/content`)
```
┌─────────────────────────────────────────────────────────────────────┐
│                        Content Management                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  [+ Create New Post]  [Schedule Post]  [Drafts (3)]  [Published]  │
│                                                                     │
│  Filter: [All Pages ▼] [All Agents ▼] [Last 30 Days ▼]           │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ Draft Post - "Summer Sale Announcement"                       │ │
│  │ Created by: Copywriter Agent | Page: My Business Page        │ │
│  │ Scheduled: June 15, 2025 10:00 AM                           │ │
│  │ [Preview] [Edit] [Approve & Publish] [Delete]               │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ Published Post - "Product Launch"                            │ │
│  │ Published: June 10, 2025 | Reach: 3,245 | Engagement: 234   │ │
│  │ [View Details] [Boost Post] [Analytics]                     │ │
│  └──────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

##### AI Agents Page (`/dashboard/agents`)
```
┌─────────────────────────────────────────────────────────────────────┐
│                          Your AI Agents                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ 🎯 Strategist Agent               [Active] [Configure]       │ │
│  │ Analyzes your page and creates content strategies            │ │
│  │ Last Action: Created 7-day content calendar                  │ │
│  │ Performance: ⭐⭐⭐⭐⭐                                         │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ ✍️ Copywriter Agent                [Active] [Configure]      │ │
│  │ Writes engaging captions and ad copy                         │ │
│  │ Posts Created: 245 | Approval Rate: 92%                     │ │
│  │ Performance: ⭐⭐⭐⭐⭐                                         │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ 🎨 Creative Agent                  [Active] [Configure]      │ │
│  │ Generates images using AI                                    │ │
│  │ Images Created: 89 | Average Quality: Excellent             │ │
│  │ Performance: ⭐⭐⭐⭐⭐                                         │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ 💬 Community Manager Agent         [Active] [Configure]      │ │
│  │ Responds to comments and messages                            │ │
│  │ Replies Sent: 1,234 | Response Time: 2 min avg             │ │
│  │ Performance: ⭐⭐⭐⭐⭐                                         │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ 📊 Ads Agent                       [Active] [Configure]      │ │
│  │ Creates and optimizes ad campaigns                           │ │
│  │ Campaigns: 5 Active | Total Spend: $2,340 | ROAS: 4.2x     │ │
│  │ Performance: ⭐⭐⭐⭐⭐                                         │ │
│  └──────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

##### Platforms Page (`/dashboard/platforms`)
```
┌─────────────────────────────────────────────────────────────────────┐
│                      Connected Platforms                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  [+ Connect New Platform]                                          │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ Facebook                                    [Connected ✓]     │ │
│  │                                                               │ │
│  │ Connected Pages (2/10):                                      │ │
│  │                                                               │ │
│  │ ┌────────────────────────────────────────────────────────┐  │ │
│  │ │ 📄 My Business Page                                     │  │ │
│  │ │ Followers: 5,234 | Active Since: Jan 2025              │  │ │
│  │ │ Status: Active | Last Sync: 2 min ago                  │  │ │
│  │ │ [View Analytics] [Manage] [Disconnect]                 │  │ │
│  │ └────────────────────────────────────────────────────────┘  │ │
│  │                                                               │ │
│  │ ┌────────────────────────────────────────────────────────┐  │ │
│  │ │ 📄 Side Project Page                                    │  │ │
│  │ │ Followers: 1,892 | Active Since: Mar 2025              │  │ │
│  │ │ Status: Active | Last Sync: 5 min ago                  │  │ │
│  │ │ [View Analytics] [Manage] [Disconnect]                 │  │ │
│  │ └────────────────────────────────────────────────────────┘  │ │
│  │                                                               │ │
│  │ [+ Add Another Page]                                         │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ Instagram                              [Coming Soon]         │ │
│  │ Connect your Instagram Business account                      │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ Twitter/X                              [Coming Soon]         │ │
│  │ Automate your Twitter presence                               │ │
│  └──────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

##### Analytics Page (`/dashboard/analytics`)
```
┌─────────────────────────────────────────────────────────────────────┐
│                          Analytics                                  │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Period: [Last 30 Days ▼]  Page: [All Pages ▼]  [Export Report]  │
│                                                                     │
│  ┌─ Key Metrics ────────────────────────────────────────────────┐ │
│  │                                                               │ │
│  │  Total Reach        Engagement       New Followers           │ │
│  │  45,234  (+23%)    5,678  (+45%)    342  (+12%)             │ │
│  │                                                               │ │
│  │  Posts Published    Comments         Messages                │ │
│  │  245              1,234              456                     │ │
│  └───────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌─ Engagement Over Time ──────────────────────────────────────┐  │
│  │ [Line Chart showing engagement trends]                       │  │
│  └───────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌─ Top Performing Posts ──────────────────────────────────────┐  │
│  │ 1. "Summer Sale" - 2,345 reactions, 234 comments            │  │
│  │ 2. "Product Launch" - 1,890 reactions, 189 comments         │  │
│  │ 3. "Behind the Scenes" - 1,567 reactions, 145 comments      │  │
│  └───────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌─ Agent Performance ─────────────────────────────────────────┐  │
│  │ Copywriter: 245 posts (92% approval)                         │  │
│  │ Community Mgr: 1,234 replies (avg 2 min response)           │  │
│  │ Ads Agent: $2,340 spend, 4.2x ROAS                          │  │
│  └───────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

##### Settings Page (`/dashboard/settings`)
```
┌─────────────────────────────────────────────────────────────────────┐
│                          Settings                                   │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  [Profile] [Brand Settings] [Notifications] [Security] [API]      │
│                                                                     │
│  ┌─ Brand Settings ──────────────────────────────────────────────┐ │
│  │                                                                │ │
│  │ Brand Name: [My Business                     ]                │ │
│  │                                                                │ │
│  │ Industry: [E-commerce              ▼]                         │ │
│  │                                                                │ │
│  │ Brand Tone:                                                    │ │
│  │ [x] Professional  [x] Friendly  [ ] Casual  [ ] Formal       │ │
│  │                                                                │ │
│  │ Brand Voice Description:                                       │ │
│  │ [We are a friendly, customer-focused brand that...]           │ │
│  │ [                                              ]               │ │
│  │                                                                │ │
│  │ Do Not Say (words/phrases to avoid):                          │ │
│  │ [cheap, sale, limited time                   ]                │ │
│  │                                                                │ │
│  │ Target Audience:                                               │ │
│  │ [Young professionals, age 25-35, tech-savvy  ]                │ │
│  │                                                                │ │
│  │ Posting Schedule:                                              │ │
│  │ Optimal Times: [AI Recommended] [Custom]                      │ │
│  │ Frequency: [3 posts per week ▼]                               │ │
│  │                                                                │ │
│  │ Auto-Approval Settings:                                        │ │
│  │ [ ] Auto-publish posts (requires review)                      │ │
│  │ [x] Auto-reply to simple comments                             │ │
│  │ [ ] Auto-reply to messages                                    │ │
│  │                                                                │ │
│  │ [Save Changes]                                                 │ │
│  └────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

##### Billing Page (`/dashboard/billing`)
```
┌─────────────────────────────────────────────────────────────────────┐
│                        Billing & Subscription                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─ Current Plan ────────────────────────────────────────────────┐ │
│  │                                                                │ │
│  │ Professional Plan                               $99/month      │ │
│  │                                                                │ │
│  │ Next billing date: July 1, 2025                               │ │
│  │ Payment method: •••• 4242                                     │ │
│  │                                                                │ │
│  │ [Upgrade to Agency] [Change Plan] [Cancel Subscription]      │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌─ Usage This Month ────────────────────────────────────────────┐ │
│  │                                                                │ │
│  │ Posts: 245/500       ████████░░░░░░  49%                     │ │
│  │ Comment Replies: Unlimited                                     │ │
│  │ Messages: Unlimited                                            │ │
│  │ Facebook Pages: 2/10                                          │ │
│  │ Ad Spend: $2,340/$5,000                                       │ │
│  │                                                                │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌─ Billing History ─────────────────────────────────────────────┐ │
│  │ June 2025     $99.00    [Invoice] [Receipt]                  │ │
│  │ May 2025      $99.00    [Invoice] [Receipt]                  │ │
│  │ April 2025    $99.00    [Invoice] [Receipt]                  │ │
│  └────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

---

### 3. Super Admin Dashboard

**URL:** `/admin`

**Purpose:** Platform administration - manage users, packages, monitoring, and system health

**Access:** Super admin only (role-based)

#### Main Admin Dashboard

```
┌─────────────────────────────────────────────────────────────────────┐
│ 🔧 Admin Panel          [System Health: Good ✓]        [Logout]    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│ ┌─ Sidebar ─────────┐  ┌─ Main Content ────────────────────────┐  │
│ │                   │  │                                         │  │
│ │ 📊 Overview       │  │  Platform Overview                     │  │
│ │ 👥 Customers      │  │                                         │  │
│ │ 💳 Subscriptions  │  │  ┌──────────────────────────────────┐ │  │
│ │ 📦 Packages       │  │  │ Key Metrics                      │ │  │
│ │ 🤖 Agents         │  │  │                                  │ │  │
│ │ 📈 Analytics      │  │  │ Total Users: 1,234              │ │  │
│ │ 💰 Revenue        │  │  │ Active Subs: 890                │ │  │
│ │ ⚙️ Settings       │  │  │ MRR: $45,670                    │ │  │
│ │ 📝 Audit Logs     │  │  │ Churn Rate: 2.3%                │ │  │
│ │ 🔔 Alerts         │  │  └──────────────────────────────────┘ │  │
│ │                   │  │                                         │  │
│ │ ──────────        │  │  Revenue Trend (Last 30 Days)          │  │
│ │                   │  │  [Line chart showing revenue growth]   │  │
│ │ System Status     │  │                                         │  │
│ │ API: ✓ Good       │  │  User Growth                            │  │
│ │ DB: ✓ Good        │  │  [Bar chart showing new users]         │  │
│ │ Queue: ✓ Good     │  │                                         │  │
│ │ Redis: ✓ Good     │  │  Recent Activity                        │  │
│ └───────────────────┘  │  • New user signed up (Professional)   │  │
│                        │  • Subscription upgraded (Agency)       │  │
│                        │  • Payment processed: $299.00           │  │
│                        └─────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

#### Customers Management (`/admin/customers`)

```
┌─────────────────────────────────────────────────────────────────────┐
│                      Customer Management                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│ Search: [🔍 Search by name, email...        ]  [+ Add Customer]    │
│                                                                     │
│ Filter: [All Plans ▼] [All Status ▼] [Sort by: Date ▼]           │
│                                                                     │
│ ┌───────────────────────────────────────────────────────────────┐  │
│ │ ID   Name           Email              Plan         Status    │  │
│ ├───────────────────────────────────────────────────────────────┤  │
│ │ 1234 John Doe       john@example.com   Professional Active   │  │
│ │      Joined: Jan 15, 2025 | Pages: 3 | Usage: 245/500       │  │
│ │      [View Details] [Edit] [Suspend] [Delete]                │  │
│ ├───────────────────────────────────────────────────────────────┤  │
│ │ 1235 Jane Smith     jane@agency.com    Agency       Active   │  │
│ │      Joined: Feb 3, 2025 | Pages: 25 | Usage: Unlimited     │  │
│ │      [View Details] [Edit] [Suspend] [Delete]                │  │
│ ├───────────────────────────────────────────────────────────────┤  │
│ │ 1236 Bob Johnson    bob@startup.com    Starter      Active   │  │
│ │      Joined: Mar 12, 2025 | Pages: 2 | Usage: 78/100        │  │
│ │      [View Details] [Edit] [Suspend] [Delete]                │  │
│ └───────────────────────────────────────────────────────────────┘  │
│                                                                     │
│ Showing 1-20 of 1,234        [Previous] [1] [2] [3] ... [Next]    │
└─────────────────────────────────────────────────────────────────────┘
```

#### Customer Details Modal

```
┌─────────────────────────────────────────────────────────────────────┐
│ Customer Details - John Doe                              [X Close]  │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│ [Profile] [Subscription] [Usage] [Activity] [Billing]             │
│                                                                     │
│ ┌─ Profile ──────────────────────────────────────────────────────┐ │
│ │                                                                 │ │
│ │ Name: John Doe                                                  │ │
│ │ Email: john@example.com                                         │ │
│ │ Company: Acme Corp                                              │ │
│ │ Joined: January 15, 2025                                        │ │
│ │ Last Login: 2 hours ago                                         │ │
│ │ Status: Active                                                  │ │
│ │                                                                 │ │
│ │ Connected Platforms:                                            │ │
│ │ • Facebook (3 pages)                                            │ │
│ │                                                                 │ │
│ │ Subscription:                                                   │ │
│ │ • Plan: Professional ($99/month)                                │ │
│ │ • Started: January 15, 2025                                     │ │
│ │ • Next Billing: July 15, 2025                                   │ │
│ │ • Lifetime Value: $594                                          │ │
│ │                                                                 │ │
│ │ Usage (Current Month):                                          │ │
│ │ • Posts: 245/500                                                │ │
│ │ • Comment Replies: 1,234 (unlimited)                            │ │
│ │ • Messages: 456 (unlimited)                                     │ │
│ │ • Ad Spend: $2,340/$5,000                                       │ │
│ │                                                                 │ │
│ │ Actions:                                                        │ │
│ │ [Send Email] [Reset Password] [Change Plan] [Suspend]         │ │
│ └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

#### Packages Management (`/admin/packages`)

```
┌─────────────────────────────────────────────────────────────────────┐
│                      Package Management                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│ [+ Create New Package]                                             │
│                                                                     │
│ ┌─────────────────────────────────────────────────────────────────┐ │
│ │ Free Tier                                    [Edit] [Disable]   │ │
│ ├─────────────────────────────────────────────────────────────────┤ │
│ │ Price: $0/month                                                 │ │
│ │ Active Users: 234                                               │ │
│ │                                                                 │ │
│ │ Limits:                                                         │ │
│ │ • Facebook Pages: 1                                             │ │
│ │ • Posts per Month: 10                                           │ │
│ │ • Comment Replies: 50                                           │ │
│ │ • Messages: 0                                                   │ │
│ │ • Ad Campaigns: No                                              │ │
│ │                                                                 │ │
│ │ Features:                                                       │ │
│ │ • Copywriter Agent (limited)                                    │ │
│ │ • Basic analytics                                               │ │
│ │ • Community support                                             │ │
│ └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│ ┌─────────────────────────────────────────────────────────────────┐ │
│ │ Starter Tier                                 [Edit] [Disable]   │ │
│ ├─────────────────────────────────────────────────────────────────┤ │
│ │ Price: $29/month                                                │ │
│ │ Active Users: 456                                               │ │
│ │ MRR: $13,224                                                    │ │
│ │                                                                 │ │
│ │ Limits:                                                         │ │
│ │ • Facebook Pages: 3                                             │ │
│ │ • Posts per Month: 100                                          │ │
│ │ • Comment Replies: 500                                          │ │
│ │ • Messages: 100                                                 │ │
│ │ • Ad Campaigns: Proposals only                                  │ │
│ │                                                                 │ │
│ │ Features:                                                       │ │
│ │ • Strategist, Copywriter, Community Manager agents              │ │
│ │ • Standard analytics                                            │ │
│ │ • Email support                                                 │ │
│ └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│ [Continue for Professional and Agency tiers...]                    │
└─────────────────────────────────────────────────────────────────────┘
```

#### Edit Package Modal

```
┌─────────────────────────────────────────────────────────────────────┐
│ Edit Package - Professional                              [X Close]  │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│ Package Name: [Professional                             ]          │
│                                                                     │
│ Price: [$99          ] per [month ▼]                               │
│                                                                     │
│ Stripe Price ID: [price_xxxxxxxxxxxx                   ]           │
│                                                                     │
│ ┌─ Limits ──────────────────────────────────────────────────────┐  │
│ │                                                                │  │
│ │ Facebook Pages:           [10           ]                      │  │
│ │ Posts per Month:          [500          ]                      │  │
│ │ Comment Replies:          [-1           ] (-1 = unlimited)     │  │
│ │ Messages per Month:       [-1           ] (-1 = unlimited)     │  │
│ │ Ad Campaign Enabled:      [x] Yes  [ ] No                      │  │
│ │ Max Ad Spend:             [5000         ] (0 = unlimited)      │  │
│ │                                                                │  │
│ └────────────────────────────────────────────────────────────────┘  │
│                                                                     │
│ ┌─ Features ────────────────────────────────────────────────────┐  │
│ │                                                                │  │
│ │ Available Agents:                                              │  │
│ │ [x] Strategist Agent                                           │  │
│ │ [x] Copywriter Agent                                           │  │
│ │ [x] Creative Agent                                             │  │
│ │ [x] Community Manager Agent                                    │  │
│ │ [x] Ads Agent                                                  │  │
│ │                                                                │  │
│ │ Features:                                                      │  │
│ │ [x] Advanced Analytics                                         │  │
│ │ [x] Priority Support                                           │  │
│ │ [ ] White Label                                                │  │
│ │ [ ] API Access                                                 │  │
│ │ [ ] Team Collaboration                                         │  │
│ │                                                                │  │
│ └────────────────────────────────────────────────────────────────┘  │
│                                                                     │
│ Status: [x] Active  [ ] Disabled                                   │
│                                                                     │
│ Visible on Pricing Page: [x] Yes  [ ] No                          │
│                                                                     │
│                                  [Cancel] [Save Changes]           │
└─────────────────────────────────────────────────────────────────────┘
```

#### Analytics Dashboard (`/admin/analytics`)

```
┌─────────────────────────────────────────────────────────────────────┐
│                      Platform Analytics                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│ Period: [Last 30 Days ▼]                           [Export Report] │
│                                                                     │
│ ┌─ Revenue Metrics ─────────────────────────────────────────────┐  │
│ │                                                                │  │
│ │ MRR: $45,670  (+12%)                                          │  │
│ │ ARR: $548,040                                                 │  │
│ │ Average LTV: $642                                             │  │
│ │ Churn Rate: 2.3%                                              │  │
│ │                                                                │  │
│ │ [Revenue chart over time]                                     │  │
│ └────────────────────────────────────────────────────────────────┘  │
│                                                                     │
│ ┌─ User Metrics ────────────────────────────────────────────────┐  │
│ │                                                                │  │
│ │ Total Users: 1,234  (+89 this month)                          │  │
│ │ Active Users: 890                                             │  │
│ │ New Signups: 125                                              │  │
│ │ Conversion Rate: 18%                                          │  │
│ │                                                                │  │
│ │ By Plan:                                                       │  │
│ │ • Free: 234 (19%)                                             │  │
│ │ • Starter: 456 (37%)                                          │  │
│ │ • Professional: 489 (40%)                                     │  │
│ │ • Agency: 55 (4%)                                             │  │
│ │                                                                │  │
│ └────────────────────────────────────────────────────────────────┘  │
│                                                                     │
│ ┌─ Platform Usage ──────────────────────────────────────────────┐  │
│ │                                                                │  │
│ │ Total Posts: 45,234                                            │  │
│ │ Comment Replies: 234,567                                       │  │
│ │ Messages Sent: 89,234                                          │  │
│ │ Connected Facebook Pages: 3,456                                │  │
│ │ Active AI Agents: 4,450                                        │  │
│ │                                                                │  │
│ │ API Usage:                                                     │  │
│ │ • OpenAI API Calls: 892,345                                    │  │
│ │ • Meta API Calls: 1,234,567                                    │  │
│ │                                                                │  │
│ └────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

#### System Settings (`/admin/settings`)

```
┌─────────────────────────────────────────────────────────────────────┐
│                      System Settings                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│ [General] [API Keys] [Email] [Security] [Features]                │
│                                                                     │
│ ┌─ API Keys ────────────────────────────────────────────────────┐  │
│ │                                                                │  │
│ │ OpenAI API Key:                                                │  │
│ │ [sk-••••••••••••••••••••••••••••   ] [Update]                 │  │
│ │ Status: ✓ Active | Last Used: 2 min ago                       │  │
│ │                                                                │  │
│ │ Facebook App ID:                                               │  │
│ │ [1234567890                         ] [Update]                 │  │
│ │                                                                │  │
│ │ Facebook App Secret:                                           │  │
│ │ [••••••••••••••••••••••••••••••    ] [Update]                 │  │
│ │ Status: ✓ Active | App Status: Live                           │  │
│ │                                                                │  │
│ │ Stripe API Key:                                                │  │
│ │ [sk_live_••••••••••••••••••••••    ] [Update]                 │  │
│ │ Status: ✓ Active | Mode: Production                           │  │
│ │                                                                │  │
│ └────────────────────────────────────────────────────────────────┘  │
│                                                                     │
│ ┌─ Feature Flags ───────────────────────────────────────────────┐  │
│ │                                                                │  │
│ │ [x] Enable new user registrations                              │  │
│ │ [x] Allow Facebook connections                                 │  │
│ │ [ ] Enable Instagram (Beta)                                    │  │
│ │ [ ] Enable Twitter (Beta)                                      │  │
│ │ [x] Enable ad campaign management                              │  │
│ │ [ ] Maintenance mode                                           │  │
│ │                                                                │  │
│ └────────────────────────────────────────────────────────────────┘  │
│                                                                     │
│ ┌─ Rate Limits ─────────────────────────────────────────────────┐  │
│ │                                                                │  │
│ │ API requests per minute: [1000          ]                      │  │
│ │ Max concurrent jobs:     [100           ]                      │  │
│ │ Webhook retries:         [3             ]                      │  │
│ │                                                                │  │
│ └────────────────────────────────────────────────────────────────┘  │
│                                                                     │
│                                                    [Save Settings]  │
└─────────────────────────────────────────────────────────────────────┘
```

#### Audit Logs (`/admin/audit-logs`)

```
┌─────────────────────────────────────────────────────────────────────┐
│                          Audit Logs                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│ Filter: [All Users ▼] [All Actions ▼] [Last 7 Days ▼] [Search...] │
│                                                                     │
│ ┌───────────────────────────────────────────────────────────────┐  │
│ │ Time              User          Action              Details    │  │
│ ├───────────────────────────────────────────────────────────────┤  │
│ │ 2025-06-15 14:23  Admin         Package Updated    Profession │  │
│ │ 2025-06-15 14:15  John Doe      Post Published     Page: My B │  │
│ │ 2025-06-15 14:10  System        Payment Processed  $99.00     │  │
│ │ 2025-06-15 14:05  Jane Smith    Subscription Up    Starter→P  │  │
│ │ 2025-06-15 14:00  Bob Johnson   Page Connected     Facebook P │  │
│ │ 2025-06-15 13:55  Admin         User Suspended     user@exa   │  │
│ │ 2025-06-15 13:50  System        Webhook Received   Comment    │  │
│ └───────────────────────────────────────────────────────────────┘  │
│                                                                     │
│ Showing 1-50 of 12,345      [Previous] [1] [2] [3] ... [Next]     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Routes Structure

### Public Routes
```php
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/features', [LandingController::class, 'features'])->name('features');
Route::get('/about', [LandingController::class, 'about'])->name('about');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');
```

### User Dashboard Routes
```php
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/content', [ContentController::class, 'index'])->name('dashboard.content');
    Route::get('/agents', [AgentController::class, 'index'])->name('dashboard.agents');
    Route::get('/platforms', [PlatformController::class, 'index'])->name('dashboard.platforms');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('dashboard.analytics');
    Route::get('/settings', [SettingsController::class, 'index'])->name('dashboard.settings');
    Route::get('/billing', [BillingController::class, 'index'])->name('dashboard.billing');
});
```

### Admin Routes
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('admin.customers');
    Route::get('/customers/{id}', [AdminCustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/packages', [AdminPackageController::class, 'index'])->name('admin.packages');
    Route::post('/packages', [AdminPackageController::class, 'store'])->name('admin.packages.store');
    Route::put('/packages/{id}', [AdminPackageController::class, 'update'])->name('admin.packages.update');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::get('/audit-logs', [AdminAuditController::class, 'index'])->name('admin.audit');
});
```

---

## Backend Architecture & Development Structure

### Architecture Pattern: Repository + Service Layer

```
Request → Controller → Service → Repository → Model → Database
          ↓            ↓
       Validation   Business Logic
                       ↓
                    Queue Jobs
                       ↓
                   Email/Notifications
```

---

### Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── ContentController.php
│   │   │   ├── AgentController.php
│   │   │   ├── PlatformController.php
│   │   │   └── AnalyticsController.php
│   │   ├── Auth/
│   │   │   ├── RegisterController.php
│   │   │   ├── LoginController.php
│   │   │   └── FacebookOAuthController.php
│   │   ├── Dashboard/
│   │   │   ├── DashboardController.php
│   │   │   ├── ContentController.php
│   │   │   ├── AgentController.php
│   │   │   ├── PlatformController.php
│   │   │   ├── AnalyticsController.php
│   │   │   ├── SettingsController.php
│   │   │   └── BillingController.php
│   │   ├── Admin/
│   │   │   ├── AdminController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── PackageController.php
│   │   │   ├── AnalyticsController.php
│   │   │   └── SettingsController.php
│   │   └── LandingController.php
│   │
│   ├── Requests/
│   │   ├── Auth/
│   │   │   ├── RegisterRequest.php
│   │   │   └── LoginRequest.php
│   │   ├── Content/
│   │   │   ├── CreatePostRequest.php
│   │   │   ├── UpdatePostRequest.php
│   │   │   ├── SchedulePostRequest.php
│   │   │   └── ApprovePostRequest.php
│   │   ├── Agent/
│   │   │   ├── ConfigureAgentRequest.php
│   │   │   └── TriggerAgentRequest.php
│   │   ├── Platform/
│   │   │   ├── ConnectPlatformRequest.php
│   │   │   └── DisconnectPlatformRequest.php
│   │   └── Settings/
│   │       ├── UpdateBrandSettingsRequest.php
│   │       └── UpdateNotificationSettingsRequest.php
│   │
│   ├── Middleware/
│   │   ├── CheckPackageLimits.php
│   │   ├── CheckSubscriptionStatus.php
│   │   ├── TrackUsage.php
│   │   └── AdminOnly.php
│   │
│   └── Resources/
│       ├── UserResource.php
│       ├── PostResource.php
│       ├── AgentResource.php
│       └── AnalyticsResource.php
│
├── Services/
│   ├── AI/
│   │   ├── OpenAIService.php
│   │   ├── StrategistAgentService.php
│   │   ├── CopywriterAgentService.php
│   │   ├── CreativeAgentService.php
│   │   ├── CommunityManagerAgentService.php
│   │   └── AdsAgentService.php
│   │
│   ├── Social/
│   │   ├── FacebookService.php
│   │   ├── InstagramService.php
│   │   ├── TwitterService.php
│   │   └── SocialMediaFactory.php
│   │
│   ├── Content/
│   │   ├── ContentGenerationService.php
│   │   ├── ContentSchedulingService.php
│   │   ├── ContentApprovalService.php
│   │   └── ContentPublishingService.php
│   │
│   ├── Subscription/
│   │   ├── SubscriptionService.php
│   │   ├── UsageLimitService.php
│   │   ├── UsageTrackingService.php
│   │   └── PaymentService.php
│   │
│   ├── Analytics/
│   │   ├── AnalyticsService.php
│   │   ├── ReportGenerationService.php
│   │   └── InsightsService.php
│   │
│   ├── Notification/
│   │   ├── EmailService.php
│   │   ├── MonthlyReportService.php
│   │   ├── RecommendationService.php
│   │   └── NotificationService.php
│   │
│   └── Security/
│       ├── TokenEncryptionService.php
│       └── AuditLogService.php
│
├── Repositories/
│   ├── UserRepository.php
│   ├── SubscriptionRepository.php
│   ├── ContentRepository.php
│   ├── FacebookPageRepository.php
│   ├── ConnectedPlatformRepository.php
│   ├── AgentRepository.php
│   ├── UsageTrackingRepository.php
│   └── AuditLogRepository.php
│
├── Models/
│   ├── User.php
│   ├── Subscription.php
│   ├── UsageLimit.php
│   ├── UsageTracking.php
│   ├── ConnectedPlatform.php
│   ├── FacebookPage.php
│   ├── Content.php
│   ├── Agent.php
│   ├── AgentConfiguration.php
│   ├── Comment.php
│   ├── Message.php
│   ├── AdCampaign.php
│   └── AuditLog.php
│
├── Jobs/
│   ├── AI/
│   │   ├── GeneratePostContent.php
│   │   ├── GenerateImageForPost.php
│   │   ├── GenerateCommentReply.php
│   │   └── GenerateMessageReply.php
│   │
│   ├── Social/
│   │   ├── PublishPostToFacebook.php
│   │   ├── FetchPageEngagement.php
│   │   ├── SyncFacebookComments.php
│   │   └── SyncFacebookMessages.php
│   │
│   ├── Notifications/
│   │   ├── SendWelcomeEmail.php
│   │   ├── SendPostPublishedNotification.php
│   │   ├── SendLimitWarningEmail.php
│   │   ├── SendMonthlyReport.php
│   │   └── SendFeatureRecommendations.php
│   │
│   └── Maintenance/
│       ├── RefreshExpiredTokens.php
│       ├── CleanupOldContent.php
│       └── GenerateMonthlyReports.php
│
├── Mail/
│   ├── WelcomeEmail.php
│   ├── PostPublishedMail.php
│   ├── LimitWarningMail.php
│   ├── MonthlyReportMail.php
│   ├── FeatureRecommendationMail.php
│   ├── SubscriptionUpgradeMail.php
│   └── PaymentConfirmationMail.php
│
├── Events/
│   ├── PostCreated.php
│   ├── PostPublished.php
│   ├── CommentReceived.php
│   ├── MessageReceived.php
│   ├── LimitReached.php
│   ├── SubscriptionCreated.php
│   └── MonthEnded.php
│
├── Listeners/
│   ├── SendPostPublishedNotification.php
│   ├── ProcessCommentReply.php
│   ├── ProcessMessageReply.php
│   ├── NotifyLimitReached.php
│   └── TriggerMonthlyReport.php
│
└── Exceptions/
    ├── AgentException.php
    ├── LimitExceededException.php
    ├── PlatformConnectionException.php
    └── PaymentException.php
```

---

### 1. Repository Pattern Implementation

#### Base Repository Interface
```php
// app/Repositories/Contracts/RepositoryInterface.php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findBy(string $field, $value);
    public function paginate(int $perPage = 15);
}
```

#### Base Repository Implementation
```php
// app/Repositories/BaseRepository.php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function findBy(string $field, $value)
    {
        return $this->model->where($field, $value)->first();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->paginate($perPage);
    }
}
```

#### Example: Content Repository
```php
// app/Repositories/ContentRepository.php

namespace App\Repositories;

use App\Models\Content;

class ContentRepository extends BaseRepository
{
    public function __construct(Content $model)
    {
        parent::__construct($model);
    }

    public function getByUser($userId, $status = null)
    {
        $query = $this->model->where('user_id', $userId);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getDraftsByUser($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getScheduledPosts()
    {
        return $this->model
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();
    }

    public function getPostsForPage($pageId, $limit = 10)
    {
        return $this->model
            ->where('facebook_page_id', $pageId)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
```

#### Example: User Repository
```php
// app/Repositories/UserRepository.php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getUserWithSubscription($userId)
    {
        return $this->model
            ->with(['subscription', 'subscription.limits'])
            ->findOrFail($userId);
    }

    public function getUsersWithActiveSubscription($packageTier = null)
    {
        $query = $this->model
            ->whereHas('subscription', function($q) {
                $q->where('status', 'active');
            });
        
        if ($packageTier) {
            $query->whereHas('subscription', function($q) use ($packageTier) {
                $q->where('package_tier', $packageTier);
            });
        }
        
        return $query->get();
    }

    public function getUsersForMonthlyReport()
    {
        return $this->model
            ->whereHas('subscription', function($q) {
                $q->where('status', 'active');
            })
            ->with(['usageTracking', 'connectedPlatforms'])
            ->get();
    }
}
```

---

### 2. Service Layer Implementation

#### Example: Content Generation Service
```php
// app/Services/Content/ContentGenerationService.php

namespace App\Services\Content;

use App\Repositories\ContentRepository;
use App\Services\AI\CopywriterAgentService;
use App\Services\AI\CreativeAgentService;
use App\Jobs\AI\GeneratePostContent;
use App\Jobs\AI\GenerateImageForPost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContentGenerationService
{
    protected $contentRepository;
    protected $copywriterAgent;
    protected $creativeAgent;

    public function __construct(
        ContentRepository $contentRepository,
        CopywriterAgentService $copywriterAgent,
        CreativeAgentService $creativeAgent
    ) {
        $this->contentRepository = $contentRepository;
        $this->copywriterAgent = $copywriterAgent;
        $this->creativeAgent = $creativeAgent;
    }

    /**
     * Generate post content with error handling
     */
    public function generatePost($userId, $pageId, array $params)
    {
        try {
            DB::beginTransaction();

            // Create draft content record
            $content = $this->contentRepository->create([
                'user_id' => $userId,
                'facebook_page_id' => $pageId,
                'type' => 'post',
                'status' => 'generating',
                'metadata' => json_encode($params),
            ]);

            // Dispatch jobs to queue
            GeneratePostContent::dispatch($content->id, $params);
            
            if ($params['include_image'] ?? false) {
                GenerateImageForPost::dispatch($content->id, $params);
            }

            DB::commit();

            Log::info('Content generation initiated', [
                'content_id' => $content->id,
                'user_id' => $userId,
                'page_id' => $pageId
            ]);

            return [
                'success' => true,
                'content_id' => $content->id,
                'message' => 'Content generation started. You will be notified when ready.'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Content generation failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \App\Exceptions\AgentException(
                'Failed to generate content: ' . $e->getMessage()
            );
        }
    }

    /**
     * Approve and publish content
     */
    public function approveAndPublish($contentId, $userId)
    {
        try {
            $content = $this->contentRepository->find($contentId);

            // Verify ownership
            if ($content->user_id !== $userId) {
                throw new \Exception('Unauthorized access to content');
            }

            // Update status
            $this->contentRepository->update($contentId, [
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Dispatch publishing job
            \App\Jobs\Social\PublishPostToFacebook::dispatch($contentId);

            Log::info('Content approved for publishing', [
                'content_id' => $contentId,
                'user_id' => $userId
            ]);

            return [
                'success' => true,
                'message' => 'Content approved and queued for publishing'
            ];

        } catch (\Exception $e) {
            Log::error('Content approval failed', [
                'content_id' => $contentId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
```

#### Example: Subscription Service
```php
// app/Services/Subscription/SubscriptionService.php

namespace App\Services\Subscription;

use App\Repositories\UserRepository;
use App\Repositories\SubscriptionRepository;
use App\Services\Subscription\UsageLimitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    protected $userRepository;
    protected $subscriptionRepository;
    protected $usageLimitService;

    public function __construct(
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        UsageLimitService $usageLimitService
    ) {
        $this->userRepository = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->usageLimitService = $usageLimitService;
    }

    /**
     * Create subscription for new user
     */
    public function createSubscription($userId, $packageTier)
    {
        try {
            DB::beginTransaction();

            $subscription = $this->subscriptionRepository->create([
                'user_id' => $userId,
                'package_tier' => $packageTier,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => $packageTier === 'free' ? null : now()->addMonth(),
            ]);

            // Create usage limits
            $this->usageLimitService->createLimitsForPackage($userId, $packageTier);

            DB::commit();

            Log::info('Subscription created', [
                'user_id' => $userId,
                'package_tier' => $packageTier,
                'subscription_id' => $subscription->id
            ]);

            // Send welcome email
            \App\Jobs\Notifications\SendWelcomeEmail::dispatch($userId);

            return $subscription;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Subscription creation failed', [
                'user_id' => $userId,
                'package_tier' => $packageTier,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Upgrade subscription
     */
    public function upgradeSubscription($userId, $newPackageTier)
    {
        try {
            DB::beginTransaction();

            $subscription = $this->subscriptionRepository->findBy('user_id', $userId);
            
            $oldTier = $subscription->package_tier;

            // Update subscription
            $this->subscriptionRepository->update($subscription->id, [
                'package_tier' => $newPackageTier,
            ]);

            // Update usage limits
            $this->usageLimitService->updateLimitsForPackage($userId, $newPackageTier);

            DB::commit();

            Log::info('Subscription upgraded', [
                'user_id' => $userId,
                'from' => $oldTier,
                'to' => $newPackageTier
            ]);

            // Send upgrade confirmation email
            \App\Jobs\Notifications\SendSubscriptionUpgradeEmail::dispatch($userId, $newPackageTier);

            return [
                'success' => true,
                'message' => "Successfully upgraded to {$newPackageTier} plan"
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Subscription upgrade failed', [
                'user_id' => $userId,
                'new_tier' => $newPackageTier,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
```

---

### 3. Request Validation

#### Example: Create Post Request
```php
// app/Http/Requests/Content/CreatePostRequest.php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'facebook_page_id' => 'required|exists:facebook_pages,id',
            'topic' => 'required|string|max:255',
            'tone' => 'nullable|in:professional,friendly,casual,formal',
            'include_image' => 'boolean',
            'image_prompt' => 'required_if:include_image,true|string|max:500',
            'target_audience' => 'nullable|string|max:255',
            'call_to_action' => 'nullable|string|max:100',
            'hashtags' => 'nullable|array|max:10',
            'hashtags.*' => 'string|max:50',
            'schedule_for' => 'nullable|date|after:now',
        ];
    }

    public function messages()
    {
        return [
            'facebook_page_id.required' => 'Please select a Facebook page',
            'facebook_page_id.exists' => 'Selected page not found',
            'topic.required' => 'Post topic is required',
            'topic.max' => 'Topic cannot exceed 255 characters',
            'image_prompt.required_if' => 'Image prompt is required when including an image',
            'schedule_for.after' => 'Scheduled time must be in the future',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
```

#### Example: Register Request
```php
// app/Http/Requests/Auth/RegisterRequest.php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'company_name' => 'nullable|string|max:255',
            'package_tier' => 'required|in:free,starter,professional,agency',
            'terms_accepted' => 'required|accepted',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Full name is required',
            'email.unique' => 'This email is already registered',
            'password.min' => 'Password must be at least 8 characters',
            'package_tier.required' => 'Please select a subscription package',
            'terms_accepted.accepted' => 'You must accept the terms and conditions',
        ];
    }
}
```

---

### 4. Controller Implementation with Try-Catch

#### Example: Content Controller
```php
// app/Http/Controllers/Dashboard/ContentController.php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Content\CreatePostRequest;
use App\Services\Content\ContentGenerationService;
use App\Services\Content\ContentApprovalService;
use App\Repositories\ContentRepository;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller
{
    protected $contentService;
    protected $approvalService;
    protected $contentRepository;

    public function __construct(
        ContentGenerationService $contentService,
        ContentApprovalService $approvalService,
        ContentRepository $contentRepository
    ) {
        $this->contentService = $contentService;
        $this->approvalService = $approvalService;
        $this->contentRepository = $contentRepository;
    }

    /**
     * Display all content
     */
    public function index()
    {
        try {
            $userId = auth()->id();
            $content = $this->contentRepository->getByUser($userId);

            return view('dashboard.content.index', compact('content'));

        } catch (\Exception $e) {
            Log::error('Failed to load content', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to load content. Please try again.');
        }
    }

    /**
     * Create new post
     */
    public function store(CreatePostRequest $request)
    {
        try {
            $userId = auth()->id();
            $validated = $request->validated();

            $result = $this->contentService->generatePost(
                $userId,
                $validated['facebook_page_id'],
                $validated
            );

            return response()->json($result, 201);

        } catch (\App\Exceptions\LimitExceededException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'upgrade_required' => true
            ], 403);

        } catch (\App\Exceptions\AgentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI agent error: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Post creation failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create post. Our team has been notified.'
            ], 500);
        }
    }

    /**
     * Approve post
     */
    public function approve($id)
    {
        try {
            $userId = auth()->id();
            
            $result = $this->contentService->approveAndPublish($id, $userId);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Post approval failed', [
                'content_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve post: ' . $e->getMessage()
            ], 500);
        }
    }
}
```

---

### 5. Queue Jobs Implementation

#### Example: Generate Post Content Job
```php
// app/Jobs/AI/GeneratePostContent.php

namespace App\Jobs\AI;

use App\Services\AI\CopywriterAgentService;
use App\Repositories\ContentRepository;
use App\Events\PostCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePostContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contentId;
    protected $params;

    public $tries = 3;
    public $timeout = 120;

    public function __construct($contentId, array $params)
    {
        $this->contentId = $contentId;
        $this->params = $params;
    }

    public function handle(
        CopywriterAgentService $copywriter,
        ContentRepository $contentRepo
    ) {
        try {
            Log::info('Generating post content', [
                'content_id' => $this->contentId,
                'params' => $this->params
            ]);

            // Generate content using AI
            $generatedText = $copywriter->generatePostCaption($this->params);

            // Update content record
            $contentRepo->update($this->contentId, [
                'content_text' => $generatedText,
                'status' => 'draft',
                'generated_at' => now(),
            ]);

            // Trigger event
            event(new PostCreated($this->contentId));

            Log::info('Post content generated successfully', [
                'content_id' => $this->contentId
            ]);

        } catch (\Exception $e) {
            Log::error('Post content generation failed', [
                'content_id' => $this->contentId,
                'error' => $e->getMessage()
            ]);

            // Update content as failed
            $contentRepo->update($this->contentId, [
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Post content generation permanently failed', [
            'content_id' => $this->contentId,
            'error' => $exception->getMessage()
        ]);

        // Notify user
        \App\Jobs\Notifications\SendContentGenerationFailedEmail::dispatch(
            $this->contentId
        );
    }
}
```

#### Example: Publish Post Job
```php
// app/Jobs/Social/PublishPostToFacebook.php

namespace App\Jobs\Social;

use App\Services\Social\FacebookService;
use App\Repositories\ContentRepository;
use App\Events\PostPublished;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishPostToFacebook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contentId;

    public $tries = 3;
    public $timeout = 60;

    public function __construct($contentId)
    {
        $this->contentId = $contentId;
    }

    public function handle(
        FacebookService $facebook,
        ContentRepository $contentRepo
    ) {
        try {
            $content = $contentRepo->find($this->contentId);

            Log::info('Publishing post to Facebook', [
                'content_id' => $this->contentId,
                'page_id' => $content->facebook_page_id
            ]);

            // Publish to Facebook
            $fbPostId = $facebook->publishPost(
                $content->facebookPage->page_id,
                $content->facebookPage->page_access_token,
                $content->content_text,
                $content->image_url
            );

            // Update content record
            $contentRepo->update($this->contentId, [
                'status' => 'published',
                'published_at' => now(),
                'platform_post_id' => $fbPostId,
            ]);

            // Trigger event
            event(new PostPublished($this->contentId));

            Log::info('Post published successfully', [
                'content_id' => $this->contentId,
                'fb_post_id' => $fbPostId
            ]);

            // Send notification to user
            \App\Jobs\Notifications\SendPostPublishedNotification::dispatch(
                $content->user_id,
                $this->contentId
            );

        } catch (\Exception $e) {
            Log::error('Post publishing failed', [
                'content_id' => $this->contentId,
                'error' => $e->getMessage()
            ]);

            $contentRepo->update($this->contentId, [
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
```

---

### 6. Email System Implementation

#### Monthly Report Email
```php
// app/Mail/MonthlyReportMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reportData;
    public $recommendations;

    public function __construct($user, $reportData, $recommendations)
    {
        $this->user = $user;
        $this->reportData = $reportData;
        $this->recommendations = $recommendations;
    }

    public function build()
    {
        return $this->subject('Your Monthly Performance Report - ' . now()->format('F Y'))
                    ->view('emails.monthly-report')
                    ->with([
                        'userName' => $this->user->name,
                        'month' => now()->format('F Y'),
                        'stats' => $this->reportData,
                        'recommendations' => $this->recommendations,
                    ]);
    }
}
```

#### Monthly Report Service
```php
// app/Services/Notification/MonthlyReportService.php

namespace App\Services\Notification;

use App\Repositories\UserRepository;
use App\Repositories\UsageTrackingRepository;
use App\Services\Analytics\AnalyticsService;
use App\Services\Notification\RecommendationService;
use Illuminate\Support\Facades\Log;

class MonthlyReportService
{
    protected $userRepository;
    protected $usageRepository;
    protected $analyticsService;
    protected $recommendationService;

    public function __construct(
        UserRepository $userRepository,
        UsageTrackingRepository $usageRepository,
        AnalyticsService $analyticsService,
        RecommendationService $recommendationService
    ) {
        $this->userRepository = $userRepository;
        $this->usageRepository = $usageRepository;
        $this->analyticsService = $analyticsService;
        $this->recommendationService = $recommendationService;
    }

    /**
     * Generate and send monthly reports to all users
     */
    public function generateMonthlyReports()
    {
        try {
            $users = $this->userRepository->getUsersForMonthlyReport();
            $lastMonth = now()->subMonth()->format('Y-m');

            Log::info('Starting monthly report generation', [
                'user_count' => $users->count(),
                'month' => $lastMonth
            ]);

            foreach ($users as $user) {
                try {
                    $this->generateReportForUser($user, $lastMonth);
                } catch (\Exception $e) {
                    Log::error('Failed to generate report for user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Monthly report generation completed');

        } catch (\Exception $e) {
            Log::error('Monthly report generation failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Generate report for single user
     */
    public function generateReportForUser($user, $monthYear)
    {
        try {
            // Get usage data
            $usage = $this->usageRepository->findBy('user_id', $user->id)
                ->where('month_year', $monthYear)
                ->first();

            if (!$usage) {
                Log::warning('No usage data found for user', [
                    'user_id' => $user->id,
                    'month' => $monthYear
                ]);
                return;
            }

            // Get analytics
            $analytics = $this->analyticsService->getMonthlyAnalytics(
                $user->id,
                $monthYear
            );

            // Get feature recommendations
            $recommendations = $this->recommendationService
                ->getRecommendationsForUser($user, $usage, $analytics);

            // Prepare report data
            $reportData = [
                'posts_published' => $usage->posts_count,
                'comments_replied' => $usage->comment_replies_count,
                'messages_handled' => $usage->messages_count,
                'total_reach' => $analytics['total_reach'],
                'total_engagement' => $analytics['total_engagement'],
                'new_followers' => $analytics['new_followers'],
                'top_posts' => $analytics['top_posts'],
                'engagement_rate' => $analytics['engagement_rate'],
                'best_posting_time' => $analytics['best_posting_time'],
                'agent_performance' => $analytics['agent_performance'],
            ];

            // Dispatch email job
            \App\Jobs\Notifications\SendMonthlyReport::dispatch(
                $user,
                $reportData,
                $recommendations
            );

            Log::info('Monthly report generated for user', [
                'user_id' => $user->id,
                'month' => $monthYear
            ]);

        } catch (\Exception $e) {
            Log::error('Report generation failed for user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
```

#### Recommendation Service
```php
// app/Services/Notification/RecommendationService.php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Log;

class RecommendationService
{
    /**
     * Generate personalized feature recommendations
     */
    public function getRecommendationsForUser($user, $usage, $analytics)
    {
        $recommendations = [];
        $subscription = $user->subscription;

        try {
            // Check if user is near limits
            if ($usage->posts_count >= ($subscription->limits->max_posts_per_month * 0.8)) {
                $recommendations[] = [
                    'type' => 'upgrade',
                    'title' => 'Running low on posts',
                    'message' => "You've used {$usage->posts_count} out of {$subscription->limits->max_posts_per_month} posts this month. Upgrade to post more!",
                    'action' => 'Upgrade Plan',
                    'link' => '/dashboard/billing'
                ];
            }

            // Recommend unused features
            if ($subscription->package_tier === 'free') {
                $recommendations[] = [
                    'type' => 'feature',
                    'title' => 'Try AI Image Generation',
                    'message' => 'Upgrade to Starter to get AI-generated images for your posts!',
                    'action' => 'See Plans',
                    'link' => '/pricing'
                ];
            }

            // Engagement recommendations
            if ($analytics['engagement_rate'] < 2) {
                $recommendations[] = [
                    'type' => 'tip',
                    'title' => 'Boost Your Engagement',
                    'message' => 'Your engagement rate is below average. Try posting at ' . $analytics['best_posting_time'] . ' for better results.',
                    'action' => null,
                    'link' => null
                ];
            }

            // Ad campaign recommendation
            if ($subscription->package_tier === 'professional' && $usage->ad_spend_total === 0) {
                $recommendations[] = [
                    'type' => 'feature',
                    'title' => 'Try Ad Campaigns',
                    'message' => 'Your plan includes ad campaign management. Let our AI create and optimize campaigns for you!',
                    'action' => 'Create Campaign',
                    'link' => '/dashboard/ads'
                ];
            }

            // Community manager usage
            if ($usage->comment_replies_count < 10 && $analytics['total_comments'] > 50) {
                $recommendations[] = [
                    'type' => 'tip',
                    'title' => 'Let AI Handle Comments',
                    'message' => "You have {$analytics['total_comments']} comments but only replied to {$usage->comment_replies_count}. Enable auto-replies to engage more!",
                    'action' => 'Enable Auto-Reply',
                    'link' => '/dashboard/settings'
                ];
            }

            Log::info('Recommendations generated', [
                'user_id' => $user->id,
                'count' => count($recommendations)
            ]);

            return $recommendations;

        } catch (\Exception $e) {
            Log::error('Failed to generate recommendations', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }
}
```

#### Send Monthly Report Job
```php
// app/Jobs/Notifications/SendMonthlyReport.php

namespace App\Jobs\Notifications;

use App\Mail\MonthlyReportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMonthlyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $reportData;
    protected $recommendations;

    public $tries = 3;
    public $timeout = 60;

    public function __construct($user, $reportData, $recommendations)
    {
        $this->user = $user;
        $this->reportData = $reportData;
        $this->recommendations = $recommendations;
    }

    public function handle()
    {
        try {
            Mail::to($this->user->email)->send(
                new MonthlyReportMail(
                    $this->user,
                    $this->reportData,
                    $this->recommendations
                )
            );

            Log::info('Monthly report email sent', [
                'user_id' => $this->user->id,
                'email' => $this->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send monthly report email', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
```

---

### 7. Scheduled Tasks

```php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Generate and send monthly reports on 1st of each month at 9 AM
        $schedule->command('reports:monthly')
                 ->monthlyOn(1, '09:00')
                 ->timezone('UTC');

        // Refresh expired tokens daily
        $schedule->command('tokens:refresh')
                 ->daily()
                 ->at('02:00');

        // Sync Facebook data every hour
        $schedule->command('facebook:sync')
                 ->hourly();

        // Publish scheduled posts every 5 minutes
        $schedule->command('posts:publish-scheduled')
                 ->everyFiveMinutes();

        // Clean up old content monthly
        $schedule->command('cleanup:old-content')
                 ->monthly();

        // Check usage limits and send warnings
        $schedule->command('usage:check-limits')
                 ->daily()
                 ->at('10:00');
    }
}
```

```php
// app/Console/Commands/GenerateMonthlyReports.php

namespace App\Console\Commands;

use App\Services\Notification\MonthlyReportService;
use Illuminate\Console\Command;

class GenerateMonthlyReports extends Command
{
    protected $signature = 'reports:monthly';
    protected $description = 'Generate and send monthly reports to all users';

    public function handle(MonthlyReportService $reportService)
    {
        $this->info('Starting monthly report generation...');

        try {
            $reportService->generateMonthlyReports();
            $this->info('Monthly reports generated successfully!');
            
        } catch (\Exception $e) {
            $this->error('Failed to generate monthly reports: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
```

---

### 8. Service Provider Registration

```php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register repositories
        $this->app->bind(
            \App\Repositories\Contracts\RepositoryInterface::class,
            \App\Repositories\BaseRepository::class
        );

        // Register services as singletons
        $this->app->singleton(\App\Services\AI\OpenAIService::class);
        $this->app->singleton(\App\Services\Social\FacebookService::class);
    }

    public function boot()
    {
        //
    }
}
```

---

## Next Steps
1. Define more agent types
2. Design database schema
3. Plan user workflows
4. Create wireframes
5. Start implementation

---

## 🔴 Critical Missing Pieces (Must Implement)

### 1. Database Migrations (Laravel)
**Priority:** 🔴 HIGH
**Needed:**
- Migration files for all tables (users, subscriptions, usage_limits, etc.)
- Proper indexes for performance optimization
- Foreign key constraints with cascade rules
- Unique constraints
- Database seeders for initial data (packages, agent types)

**Files Required:**
- `database/migrations/xxxx_create_users_table.php`
- `database/migrations/xxxx_create_subscriptions_table.php`
- `database/migrations/xxxx_create_usage_limits_table.php`
- `database/migrations/xxxx_create_usage_tracking_table.php`
- `database/migrations/xxxx_create_connected_platforms_table.php`
- `database/migrations/xxxx_create_facebook_pages_table.php`
- `database/migrations/xxxx_create_contents_table.php`
- `database/migrations/xxxx_create_agents_table.php`
- `database/migrations/xxxx_create_audit_logs_table.php`
- `database/seeders/PackageSeeder.php`

---

### 2. Eloquent Models with Relationships
**Priority:** 🔴 HIGH
**Needed:**
- Model classes with proper relationships
- Accessors and Mutators
- Model events (creating, created, updating, etc.)
- Scopes for common queries
- Casts for JSON fields

**Example Relationships:**
```php
User:
- hasOne(Subscription)
- hasMany(ConnectedPlatform)
- hasMany(FacebookPage)
- hasMany(Content)
- hasOne(UsageLimit)
- hasMany(UsageTracking)

Subscription:
- belongsTo(User)
- hasOne(UsageLimit)

Content:
- belongsTo(User)
- belongsTo(FacebookPage)
- belongsTo(Agent)

FacebookPage:
- belongsTo(User)
- belongsTo(ConnectedPlatform)
- hasMany(Content)
```

---

### 3. API Documentation (REST)
**Priority:** 🔴 HIGH
**Needed:**
- Complete API endpoints documentation
- Request/Response examples
- Status codes (200, 201, 400, 401, 403, 404, 422, 500)
- Authentication headers (Bearer token)
- Rate limiting rules
- Error response format

**API Endpoints to Document:**
```
Authentication:
POST   /api/register
POST   /api/login
POST   /api/logout
GET    /api/user

Content:
GET    /api/content
POST   /api/content
GET    /api/content/{id}
PUT    /api/content/{id}
DELETE /api/content/{id}
POST   /api/content/{id}/approve
POST   /api/content/{id}/publish

Platforms:
GET    /api/platforms
POST   /api/platforms/connect
DELETE /api/platforms/{id}/disconnect
GET    /api/platforms/{id}/pages

Agents:
GET    /api/agents
GET    /api/agents/{id}
POST   /api/agents/{id}/configure
POST   /api/agents/{id}/trigger

Analytics:
GET    /api/analytics/overview
GET    /api/analytics/posts
GET    /api/analytics/engagement
GET    /api/analytics/export
```

---

### 4. Facebook Webhook Implementation
**Priority:** 🔴 HIGH
**Needed:**
- Webhook verification endpoint
- Event handlers for different webhook types
- Signature verification for security
- Retry logic for failed webhooks
- Queue processing for webhook events

**Webhook Events to Handle:**
- `feed` - New posts on page
- `comments` - New comments on posts
- `messages` - New messages in inbox
- `mention` - Page mentioned in posts
- `reactions` - Reactions on posts

**Implementation:**
```php
// app/Http/Controllers/WebhookController.php
- verifyWebhook() - GET request verification
- handleWebhook() - POST request handler
- processCommentEvent()
- processMessageEvent()
- processPostEvent()
```

---

### 5. AI Agent System Prompts
**Priority:** 🔴 HIGH
**Needed:**
- Detailed system prompts for each agent
- Input/Output examples
- Temperature and token settings
- Fallback strategies for API failures
- Prompt versioning for A/B testing

**Agent Prompts Required:**
1. **Strategist Agent Prompt**
2. **Copywriter Agent Prompt**
3. **Creative Agent Prompt (image generation)**
4. **Community Manager Agent Prompt**
5. **Ads Agent Prompt**

Each prompt should include:
- Role definition
- Constraints and rules
- Brand voice integration
- Output format specification
- Examples of good responses

---

### 6. Stripe Payment Integration
**Priority:** 🔴 HIGH
**Needed:**
- Subscription creation flow
- Webhook handling (payment.succeeded, payment.failed, etc.)
- Invoice generation
- Proration on plan upgrades
- Cancellation and refund logic
- Failed payment retry logic
- Payment method management

**Stripe Webhooks to Handle:**
```
customer.subscription.created
customer.subscription.updated
customer.subscription.deleted
invoice.payment_succeeded
invoice.payment_failed
payment_method.attached
payment_method.detached
```

**Files Required:**
```
app/Services/Subscription/StripeService.php
app/Http/Controllers/StripeWebhookController.php
app/Jobs/Subscription/HandleSubscriptionCreated.php
app/Jobs/Subscription/HandlePaymentFailed.php
```

---

### 7. Testing Strategy
**Priority:** 🔴 HIGH
**Needed:**
- Unit tests for services and repositories
- Feature tests for API endpoints
- Integration tests for workflows
- Test data factories
- Database seeders for testing
- Mocking external APIs (OpenAI, Facebook, Stripe)

**Test Files Structure:**
```
tests/
├── Unit/
│   ├── Services/
│   │   ├── ContentGenerationServiceTest.php
│   │   ├── SubscriptionServiceTest.php
│   │   └── UsageLimitServiceTest.php
│   └── Repositories/
│       ├── UserRepositoryTest.php
│       └── ContentRepositoryTest.php
├── Feature/
│   ├── Auth/
│   │   ├── RegistrationTest.php
│   │   └── LoginTest.php
│   ├── Content/
│   │   ├── CreateContentTest.php
│   │   └── PublishContentTest.php
│   └── Subscription/
│       └── SubscriptionFlowTest.php
└── Integration/
    ├── FacebookIntegrationTest.php
    └── StripeIntegrationTest.php
```

---

## 🟡 Important (Should Implement)

### 8. Deployment & Infrastructure
**Priority:** 🟡 MEDIUM
**Needed:**
- Server requirements documentation
- CI/CD pipeline setup (GitHub Actions / GitLab CI)
- Environment setup (staging, production)
- SSL certificate configuration
- Domain setup
- Database backup strategy
- Deployment scripts

**Infrastructure Components:**
```
Production:
- Web Server (Nginx/Apache)
- PHP 8.2+ with required extensions
- PostgreSQL 14+
- Redis 7+
- Supervisor for queue workers
- SSL via Let's Encrypt

Staging:
- Mirror of production
- Separate database
- Test data seeding
```

---

### 9. Monitoring & Observability
**Priority:** 🟡 MEDIUM
**Needed:**
- Error tracking (Sentry integration)
- Performance monitoring (New Relic / DataDog)
- Logging strategy (structured logs)
- Alert system for critical errors
- Uptime monitoring
- Queue monitoring
- Database performance monitoring

**Key Metrics to Track:**
- API response times
- Queue job processing times
- Error rates
- User signup conversion
- Subscription churn rate
- OpenAI API costs
- Facebook API rate limit usage

---

### 10. Email Templates (HTML)
**Priority:** 🟡 MEDIUM
**Needed:**
- Professional HTML email designs
- Responsive layouts for mobile
- Brand colors and logo
- Email previews in different clients

**Email Templates Required:**
```
resources/views/emails/
├── welcome.blade.php
├── monthly-report.blade.php
├── post-published.blade.php
├── limit-warning.blade.php
├── feature-recommendation.blade.php
├── subscription-upgrade.blade.php
├── payment-confirmation.blade.php
├── payment-failed.blade.php
└── layouts/
    └── email-layout.blade.php
```

---

### 11. Brand Profile System
**Priority:** 🟡 MEDIUM
**Needed:**
- Brand voice collection interface
- Tone examples storage
- Industry-specific templates
- Do-not-say rules management
- Brand guidelines documentation

**Brand Profile Fields:**
- Brand name
- Industry/niche
- Target audience
- Brand voice description
- Tone preferences (professional, friendly, casual, formal)
- Do-not-say words/phrases
- Preferred hashtags
- Posting schedule preferences
- Content themes/topics

---

### 12. Security Hardening
**Priority:** 🟡 MEDIUM
**Needed:**
- Rate limiting per user/IP
- CSRF protection (Laravel default)
- XSS prevention
- SQL injection protection (Eloquent ORM)
- API key rotation strategy
- 2FA for admin accounts
- IP whitelisting for admin panel
- Failed login attempt tracking

**Security Checklist:**
```
✓ Encrypt sensitive data (tokens)
✓ Use HTTPS only
✓ Implement rate limiting
✓ Sanitize user inputs
✓ Validate all requests
✓ Use prepared statements
✓ Implement CORS properly
✓ Regular security audits
✓ Dependency vulnerability scanning
```

---

### 13. Backup & Recovery
**Priority:** 🟡 MEDIUM
**Needed:**
- Automated database backups (daily)
- File storage backups
- Backup retention policy (30 days)
- Disaster recovery plan
- Database restore procedure
- Point-in-time recovery capability

**Backup Strategy:**
```
Daily:
- Full database backup at 2 AM
- Store in S3/DigitalOcean Spaces
- Encrypted backups

Weekly:
- Test backup restoration
- Verify backup integrity

Monthly:
- Disaster recovery drill
```

---

### 14. Scaling Strategy
**Priority:** 🟡 MEDIUM
**Needed:**
- Horizontal scaling approach
- Load balancer configuration
- Queue worker scaling
- Database read replicas
- CDN for static assets
- Caching strategy (Redis)
- Session management across servers

**Scaling Plan:**
```
Phase 1 (0-1,000 users):
- Single server
- Basic Redis caching

Phase 2 (1,000-10,000 users):
- Load balancer
- 2-3 web servers
- Dedicated database server
- Multiple queue workers

Phase 3 (10,000+ users):
- Auto-scaling web servers
- Database read replicas
- CDN for assets
- Separate Redis for cache and queue
```

---

## 🟢 Nice to Have (Future Enhancements)

### 15. User Onboarding Flow
**Priority:** 🟢 LOW
**Features:**
- Interactive tutorial/walkthrough
- Sample content generation on signup
- Help tooltips throughout dashboard
- Video tutorials
- Getting started checklist
- Success milestones

---

### 16. Admin Analytics Dashboard (Advanced)
**Priority:** 🟢 LOW
**Features:**
- Real-time metrics
- Advanced filtering
- Custom date ranges
- Cohort analysis
- Churn prediction
- Revenue forecasting
- Export to Excel/PDF

---

### 17. Feature Flags System
**Priority:** 🟢 LOW
**Features:**
- Enable/disable features per user
- A/B testing framework
- Gradual rollout capability
- Feature usage tracking
- Kill switch for buggy features

**Implementation:**
```php
if (Feature::enabled('new_dashboard', $user)) {
    // Show new dashboard
} else {
    // Show old dashboard
}
```

---

### 18. API Rate Limiting (Advanced)
**Priority:** 🟢 LOW
**Features:**
- Per-user rate limits
- Per-endpoint rate limits
- Rate limit headers in responses
- Grace period for limits
- Automatic throttling

---

### 19. Content Calendar (Visual)
**Priority:** 🟢 LOW
**Features:**
- Drag-and-drop scheduling
- Calendar view (month/week/day)
- Bulk scheduling
- Content themes color-coding
- Time zone support
- Conflict detection

---

### 20. Agent Performance Tracking (Advanced)
**Priority:** 🟢 LOW
**Features:**
- Agent effectiveness metrics
- Success rate tracking
- A/B testing different prompts
- Cost per action tracking
- Agent recommendation system
- Performance comparison between agents

---

### 21. Multi-Language Support
**Priority:** 🟢 LOW
**Features:**
- Dashboard in multiple languages
- Content generation in multiple languages
- Auto-detect user language
- RTL support for Arabic/Hebrew

---

### 22. White-Label Solution (Agency Tier)
**Priority:** 🟢 LOW
**Features:**
- Custom branding
- Custom domain
- Remove platform branding
- Custom email templates
- Agency API access

---

### 23. Collaboration Features
**Priority:** 🟢 LOW
**Features:**
- Team roles (Admin, Editor, Viewer)
- Content approval workflows
- Comments on drafts
- Activity feed
- Team member invitations

---

### 24. Advanced Analytics
**Priority:** 🟢 LOW
**Features:**
- Competitor analysis
- Best time to post (AI-powered)
- Hashtag performance
- Audience insights
- Sentiment analysis on comments
- Engagement prediction

---

### 25. Content Library
**Priority:** 🟢 LOW
**Features:**
- Reusable content snippets
- Template library
- Brand asset storage
- Stock image integration
- GIF search
- Video upload support

---

## Implementation Priority Order

### Sprint 1 (Week 1-2): Foundation
1. ✅ Database migrations
2. ✅ Eloquent models
3. ✅ Basic authentication
4. ✅ Repository pattern setup
5. ✅ Service layer foundation

### Sprint 2 (Week 3-4): Core Features
1. ✅ Facebook OAuth integration
2. ✅ OpenAI integration
3. ✅ Agent system prompts
4. ✅ Content generation workflow
5. ✅ Basic dashboard

### Sprint 3 (Week 5-6): Advanced Features
1. ✅ Stripe integration
2. ✅ Subscription management
3. ✅ Usage tracking
4. ✅ Facebook webhooks
5. ✅ Queue system

### Sprint 4 (Week 7-8): Polish & Launch
1. ✅ Email system
2. ✅ Monthly reports
3. ✅ Testing
4. ✅ Deployment
5. ✅ Monitoring

### Post-Launch: Enhancements
1. Performance optimization
2. Advanced analytics
3. Additional platforms (Instagram, Twitter)
4. Nice-to-have features
5. Scaling improvements

---

## 📋 Documentation Completeness

### ✅ Now Documented

1. **AI Agent System Prompts** - Detailed prompts for all 5 agents with input/output examples
2. **API Documentation** - Complete endpoint documentation with request/response examples
3. **Error Response Standards** - Standard error format with all error codes and examples
4. **Development Setup Guide** - Step-by-step instructions from clone to running app
5. **Git Workflow & Branch Strategy** - Complete branching model, commit conventions, and release process

### 🎯 Ready for Implementation

This documentation now covers:
- ✅ Complete system architecture
- ✅ All agent prompts with examples
- ✅ Full API specifications
- ✅ Error handling standards
- ✅ Development environment setup
- ✅ Git workflow and conventions
- ✅ Database schemas
- ✅ UI/UX wireframes
- ✅ Backend structure (Repository + Service pattern)
- ✅ Queue and email systems
- ✅ Implementation roadmap (Sprint 1-4)
- ✅ Prioritized feature list (Critical/Important/Nice-to-have)

**Status:** 📗 Documentation Complete - Ready to Begin Sprint 1 Implementation

---

## Notes & Ideas
- Each agent should have a profile page
- Agents can be hired/activated per subscription tier
- Track agent performance metrics
- Agent collaboration system for complex tasks

---

**Last Updated:** December 20, 2025  
**Document Version:** 2.0  
**Status:** Complete & Implementation-Ready
