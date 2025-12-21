<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Lili - Multi-Agent AI Platform

> **A scalable SaaS platform providing expert-level AI agents across multiple business domains.**

Transform your business with AI agents that act like 20-year experienced professionals. From marketing strategies to code reviews, from financial analysis to customer support - all in one platform.

---

## 🌟 Overview

Lili is a **multi-agent AI platform** that gives you access to specialized AI agents, each trained to act as a senior professional with 20 years of experience. Activate the agents you need, use them when you need them, and scale as you grow.

### 🎯 Vision
- **Multi-Domain Expertise**: Marketing, QA, Development, Accounting, Customer Service
- **Package-Based Access**: Activate agents based on your subscription tier
- **Data-Driven Learning**: Every interaction is logged to improve AI models
- **Professional Quality**: Each agent provides company-grade expertise

---

## 🤖 Available AI Agents

### 📊 Marketing Expert (Ready)
- Market research & competitor analysis
- Content strategy & calendar planning
- Budget allocation & ROI optimization
- Social media management
- Ad campaign creation

### 🔍 QA Specialist (Coming Soon)
- Test plan development
- Automated test creation
- Bug detection & reporting
- Quality metrics analysis

### 💻 Senior Developer (Coming Soon)
- Code architecture & review
- Bug fixing & debugging
- Performance optimization
- Technical documentation

### 💰 Senior Accountant (Coming Soon)
- Financial analysis & reporting
- Tax planning & compliance
- Budget management
- Investment analysis

### 🎧 Customer Service Expert (Coming Soon)
- Support strategy development
- Complaint resolution scripts
- Customer satisfaction analysis
- FAQ & knowledge base creation

---

## 🎁 Features

### Core Platform
- ✅ **Agent Marketplace** - Beautiful UI to browse and activate agents
- ✅ **Slot-Based Activation** - Activate agents within your package limits
- ✅ **Usage Analytics** - Track performance and interactions per agent
- ✅ **Feedback System** - Rate agent responses to improve AI
- ✅ **Data Collection** - Every interaction logged for ML training

### Marketing Features (Active)
- ✅ AI-powered content strategy generation
- ✅ Automated content calendar creation
- ✅ Bulk content generation with brand consistency
- ✅ Facebook OAuth integration
- ✅ Facebook page management
- ✅ Content scheduling & auto-publishing
- ✅ Logo overlay on generated images
- ✅ Multi-language support (10 languages)
- ✅ Budget-aware recommendations

### Security & Permissions
- ✅ Spatie Laravel Permission (RBAC)
- ✅ 4 Roles: Admin, Manager, User, Viewer
- ✅ 41 Granular Permissions
- ✅ Agent-specific access control

### Subscription System
- ✅ 4 Package Tiers (Free, Starter, Pro, Agency)
- ✅ Usage tracking & limits
- ✅ Agent slot enforcement
- ✅ Stripe integration ready

---

## 📦 Subscription Tiers

| Feature | Free | Starter | Professional | Agency |
|---------|------|---------|--------------|--------|
| **Price** | $0/mo | $29/mo | $99/mo | $299/mo |
| **Agent Slots** | 1 | 2 | 5 | Unlimited |
| **Marketing Agent** | ✅ | ✅ | ✅ | ✅ |
| **QA Agent** | ❌ | ✅ | ✅ | ✅ |
| **Developer Agent** | ❌ | ✅ | ✅ | ✅ |
| **Accountant Agent** | ❌ | ❌ | ✅ | ✅ |
| **Customer Service** | ❌ | ❌ | ✅ | ✅ |
| **Agent Analytics** | ❌ | ✅ | ✅ | ✅ |
| **Priority Support** | ❌ | ❌ | ✅ | ✅ |

---

## 🚀 Quick Start

### Visit Agent Marketplace
```
1. Login to your account
2. Go to /agents
3. Browse available agents
4. Click "Activate Agent" to start using
```

### Use an Agent
```
1. Navigate to agent's interface (e.g., AI Studio for Marketing)
2. Provide your requirements
3. Get professional-grade results
4. Rate the response to help improve AI
```

### View Analytics
```
1. Go to /agents/{agent-code}/analytics
2. See usage stats, success rates, token consumption
3. Review recent interactions
4. Export data for analysis
```

---

## 💻 Tech Stack

- **Backend**: Laravel 12 (PHP 8.2)
- **Database**: MySQL
- **Cache/Queue**: Redis
- **AI**: OpenAI API (GPT-4o, DALL-E 3)
- **Social**: Facebook Graph API
- **Payments**: Stripe Cashier

## Installation

### Prerequisites

```bash
- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Redis >= 6.0
- Node.js >= 18
```

### Setup Steps

1. **Clone & Install Dependencies**

```bash
git clone <repository>
cd Lili
composer install
npm install
```

2. **Environment Configuration**

```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure .env**

```env
APP_NAME="AI Marketing Agent"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_DATABASE=ai_agents
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Facebook OAuth
FACEBOOK_APP_ID=your_app_id
FACEBOOK_APP_SECRET=your_app_secret
FACEBOOK_REDIRECT_URI=http://localhost/auth/facebook/callback

# OpenAI
OPENAI_API_KEY=sk-your_key_here
OPENAI_ORGANIZATION=org-your_org

# Stripe (for subscriptions)
STRIPE_KEY=pk_test_your_key
STRIPE_SECRET=sk_test_your_key
```

4. **Database Migration**

```bash
php artisan migrate
```

5. **Build Frontend Assets**

```bash
npm run build
# Or for development:
npm run dev
```

6. **Start Queue Worker**

```bash
php artisan queue:work --tries=3
```

7. **Schedule Cron Job**

Add to crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

8. **Run Development Server**

```bash
php artisan serve
```

Visit: http://localhost:8000

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/              # Authentication
│   │   ├── Admin/             # Admin panel
│   │   ├── API/               # REST API
│   │   └── ContentGenerationController.php
│   ├── Middleware/            # Custom middleware
│   └── Requests/              # Form validation
├── Models/                    # Eloquent models
├── Repositories/              # Data access layer
├── Services/
│   ├── AI/                    # 5 AI agent services
│   ├── ContentService.php
│   ├── FacebookService.php
│   ├── SubscriptionService.php
│   └── UsageService.php
├── Jobs/                      # Queue jobs
└── Console/Commands/          # Artisan commands

database/
├── migrations/                # Database schema
├── seeders/                   # Test data
└── factories/                 # Model factories

resources/
├── views/
│   ├── dashboard/             # User dashboard
│   ├── admin/                 # Admin panel
│   └── errors/                # Error pages
└── js/                        # Frontend modules

routes/
├── web.php                    # Web routes
└── api.php                    # API routes
```

## API Documentation

### Authentication
All API requests require authentication via Sanctum token.

### Endpoints

#### Content Management
```
GET    /api/content              # List content
POST   /api/content              # Create content
GET    /api/content/{id}         # View content
PUT    /api/content/{id}         # Update content
DELETE /api/content/{id}         # Delete content
POST   /api/content/{id}/schedule # Schedule content
POST   /api/content/{id}/publish  # Publish immediately
```

#### AI Agents
```
POST   /api/agents/strategy      # Generate strategy
POST   /api/agents/caption       # Generate caption
POST   /api/agents/image         # Generate image
POST   /api/agents/reply         # Generate reply
POST   /api/agents/campaign      # Generate ad campaign
```

#### Facebook Pages
```
GET    /api/pages                # List pages
GET    /api/pages/{id}           # View page
POST   /api/pages/{id}/sync      # Sync from Facebook
```

#### Usage Stats
```
GET    /api/usage                # Current usage
```

## Subscription Packages

| Feature | Free | Starter | Professional | Agency |
|---------|------|---------|--------------|--------|
| **Price** | $0 | $29/mo | $99/mo | $299/mo |
| **Facebook Pages** | 1 | 3 | 10 | Unlimited |
| **Posts/Month** | 10 | 100 | 500 | Unlimited |
| **Comment Replies** | 50 | 500 | Unlimited | Unlimited |
| **DM Responses** | 0 | 100 | Unlimited | Unlimited |
| **Ad Spend** | $0 | $0 | $5K | Unlimited |

## AI Agents

### 1. Strategist Agent
- Content calendar generation
- Audience analysis
- Trend research
- Posting time optimization

### 2. Copywriter Agent
- Post caption generation
- Brand voice matching
- Hashtag optimization
- CTA creation

### 3. Creative Agent
- DALL-E 3 image generation
- Visual prompt crafting
- Brand consistency
- Multiple variations

### 4. Community Manager Agent
- Comment reply generation
- Sentiment analysis
- Escalation detection
- Empathetic responses

### 5. Ads Agent
- Campaign structure
- Audience targeting
- Budget allocation
- A/B test variations

## Queue Jobs

### PublishContentToFacebookJob
Publishes content to Facebook pages via Graph API.

### GenerateContentJob
Asynchronously generates content with AI agents.

## Artisan Commands

```bash
# Publish scheduled content
php artisan content:publish-scheduled

# Clear expired tokens
php artisan tokens:cleanup
```

## Troubleshooting

### Queue not processing
```bash
php artisan queue:restart
php artisan queue:work --verbose
```

### Facebook OAuth fails
- Check callback URL matches exactly
- Verify app is in Live mode
- Check required permissions

### AI generation fails
- Verify OpenAI API key
- Check API quota/limits
- Review error logs

## License

Proprietary - All rights reserved

---

Built with ❤️ using Laravel 12 & OpenAI

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## 📚 Documentation

### For Users
- **[Quick Start Guide](QUICK_START.md)** - Get started in 5 minutes
- **[Agent Marketplace](http://localhost:8000/agents)** - Browse and activate agents

### For Developers
- **[Restructuring Overview](docs/RESTRUCTURING.md)** - Complete technical documentation
- **[Architecture Diagrams](docs/ARCHITECTURE_DIAGRAMS.md)** - Visual system architecture
- **[Migration Checklist](docs/AGENT_MIGRATION_CHECKLIST.md)** - Step-by-step agent migration
- **[Restructuring Summary](docs/RESTRUCTURING_SUMMARY.md)** - What we've built

### Key Concepts
- **Agent Type**: Definition of an agent stored in database (Marketing, QA, etc.)
- **User Agent**: User's activated instance of an agent
- **Agent Interaction**: One usage of an agent, logged for ML training
- **Agent Slot**: Package-based limit on how many agents can be active

---

## 🏗️ Architecture

### Agent System
```
BaseAgent (Abstract)
├─ BaseMarketingAgent
│  ├─ StrategistAgent ✅
│  ├─ CopywriterAgent (migrating)
│  └─ CreativeAgent (migrating)
├─ BaseQAAgent (planned)
├─ BaseDeveloperAgent (planned)
├─ BaseAccountantAgent (planned)
└─ BaseCustomerServiceAgent (planned)
```

### Data Collection
Every agent interaction is logged to `agent_interactions` table:
- Input prompt & context
- Output response (full data)
- Tokens used & execution time
- Success/failure status
- User feedback (positive/negative/neutral)

This creates a **comprehensive dataset for training future AI models**.

---

## 🔮 Roadmap

### Phase 1: Foundation (Current - 30% Complete)
- ✅ Multi-agent architecture
- ✅ Agent marketplace UI
- ✅ Data collection pipeline
- ✅ Marketing agent (partially migrated)
- ⏳ Complete marketing agent migration
- ⏳ Agent analytics dashboard

### Phase 2: Expansion (Next 1-2 months)
- 🔜 QA Specialist agent
- 🔜 Senior Developer agent
- 🔜 Senior Accountant agent
- 🔜 Customer Service agent
- 🔜 Advanced analytics & reporting

### Phase 3: Intelligence (3-6 months)
- 🔮 Fine-tune agents with collected data
- 🔮 Personalized agent behavior
- 🔮 Predictive recommendations
- 🔮 Agent-to-agent collaboration

### Phase 4: Marketplace (6-12 months)
- 🔮 Custom agent builder
- 🔮 3rd-party agent marketplace
- 🔮 Voice interaction
- 🔮 Multi-modal agents (text + image + voice)

---

## 🤝 Contributing

We welcome contributions! Areas where you can help:

1. **Agent Development**: Create new specialized agents
2. **UI/UX**: Improve agent marketplace and dashboards
3. **Documentation**: Enhance guides and tutorials
4. **Testing**: Write tests for agent functionality
5. **Performance**: Optimize agent execution speed

See [docs/AGENT_MIGRATION_CHECKLIST.md](docs/AGENT_MIGRATION_CHECKLIST.md) for contribution guidelines.

---

## 📞 Support

### Common Issues

**"No available agent slots"**
- Check your package tier
- Deactivate an unused agent
- Upgrade for more slots

**"Agent not showing in marketplace"**
- Ensure `agent_types.is_active = true`
- Check user permissions
- Clear cache: `php artisan cache:clear`

**"Interactions not logging"**
- Verify user is authenticated
- Check AgentInteractionService dependency
- Inspect database connection

### Get Help
- 📖 Read the [documentation](docs/RESTRUCTURING.md)
- 🐛 Report bugs via GitHub Issues
- 💬 Join our community (Discord coming soon)

---

## 📜 License

This project is proprietary software. All rights reserved.

---

## 🙏 Acknowledgments

- **Laravel Framework** - Foundation of the platform
- **OpenAI** - AI models powering agents
- **Spatie** - Laravel Permission package
- **Facebook** - Graph API integration

---

## 📈 Project Status

**Current Version:** 2.0 (Multi-Agent Platform)  
**Status:** Active Development (30% Complete)  
**Last Updated:** December 21, 2025

### What's Working
✅ Agent marketplace  
✅ Agent activation/deactivation  
✅ Marketing strategist agent  
✅ Data collection pipeline  
✅ Permission system  
✅ Package-based limits  

### In Progress
⏳ Complete marketing agent migration  
⏳ Agent analytics dashboard  
⏳ QA/Developer/Accountant/CS agents  

---

**🚀 Ready to transform your business with AI agents? [Get Started](QUICK_START.md)**

