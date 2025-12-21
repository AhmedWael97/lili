# Project Restructuring Documentation
## Multi-Agent AI Platform Architecture

**Date:** December 21, 2025  
**Version:** 2.0  
**Status:** In Progress (30% Complete)

---

## 🎯 Project Vision

This platform has evolved from a marketing automation tool into a **comprehensive multi-agent AI platform** that provides expert-level assistance across multiple business domains. Each agent acts as a **20-year experienced professional**, providing full company-like services with data collection for continuous learning.

### Supported Agent Types
1. **Marketing Agent** 📊 - Market research, content strategy, campaign management
2. **QA Specialist** 🔍 - Test planning, automated testing, quality assurance
3. **Senior Developer** 💻 - Code architecture, bug fixes, performance optimization
4. **Senior Accountant** 💰 - Financial analysis, tax planning, budget management
5. **Customer Service Expert** 🎧 - Support strategies, complaint resolution, satisfaction improvement

---

## 📁 New Architecture Structure

### Directory Organization

```
app/
├── Agents/                          # NEW: Agent-based architecture
│   ├── Base/
│   │   ├── BaseAgent.php           # ✅ Abstract base for all agents
│   │   └── BaseMarketingAgent.php  # ✅ Marketing-specific base class
│   ├── Marketing/
│   │   ├── StrategistAgent.php     # ✅ MIGRATED from Services/AI/
│   │   ├── CopywriterAgent.php     # ⏳ TODO: Migrate & refactor
│   │   └── CreativeAgent.php       # ⏳ TODO: Migrate & refactor
│   ├── QA/                          # ⏳ TODO: Create QA agents
│   ├── Developer/                   # ⏳ TODO: Create developer agents
│   ├── Accountant/                  # ⏳ TODO: Create accountant agents
│   └── CustomerService/             # ⏳ TODO: Create CS agents
├── Services/
│   ├── AgentService.php             # ✅ NEW: Agent activation/management
│   ├── AgentInteractionService.php  # ✅ NEW: Data collection for ML
│   ├── AI/
│   │   └── OpenAIService.php        # Existing OpenAI integration
│   └── ...existing services
├── Models/
│   ├── AgentType.php                # ✅ NEW: Agent type definitions
│   ├── UserAgent.php                # ✅ NEW: User's activated agents
│   ├── AgentInteraction.php         # ✅ NEW: Interaction logging for ML
│   └── ...existing models
└── Http/Controllers/
    ├── AgentController.php          # ✅ NEW: Agent management
    └── ...existing controllers

resources/
└── views/
    ├── agents/
    │   ├── index.blade.php           # ✅ NEW: Agent marketplace
    │   ├── dashboard.blade.php       # ⏳ TODO: Agent dashboard
    │   └── analytics.blade.php       # ⏳ TODO: Agent analytics
    └── ai-studio/                     # ⏳ TODO: Migrate to agents/marketing/
        └── ...existing views

database/
└── migrations/
    ├── 2025_12_21_020010_create_agent_types_table.php        # ✅ DONE
    ├── 2025_12_21_020011_create_user_agents_table.php        # ✅ DONE
    ├── 2025_12_21_020012_create_agent_interactions_table.php # ✅ DONE
    └── 2025_12_21_020013_add_agent_slots_to_packages_table.php # ✅ DONE
```

---

## 🗄️ Database Schema

### New Tables

#### `agent_types`
Stores available agent types (Marketing, QA, Developer, etc.)

```sql
- id (bigint, PK)
- code (string, unique) - 'marketing', 'qa', 'developer', etc.
- name (string) - Display name
- category (string) - 'business', 'technical', 'financial', 'support'
- description (text) - Agent description
- icon (string) - Emoji icon
- color (string) - Hex color code
- is_active (boolean) - Availability status
- features (json) - Array of features
- sort_order (integer) - Display order
- timestamps
```

**Seeded Data:**
- ✅ Marketing Expert (code: marketing)
- ✅ QA Specialist (code: qa)
- ✅ Senior Developer (code: developer)
- ✅ Senior Accountant (code: accountant)
- ✅ Customer Service Expert (code: customer_service)

#### `user_agents`
Tracks which agents each user has activated

```sql
- id (bigint, PK)
- user_id (FK → users)
- agent_type_id (FK → agent_types)
- status (enum: active, inactive, paused)
- activated_at (timestamp)
- last_used_at (timestamp, nullable)
- interaction_count (integer, default 0)
- settings (json, nullable) - Custom agent settings
- timestamps
- UNIQUE(user_id, agent_type_id)
```

#### `agent_interactions`
**DATA COLLECTION FOR ML LEARNING** - Stores every agent interaction

```sql
- id (bigint, PK)
- user_id (FK → users)
- agent_type_id (FK → agent_types)
- action (string) - Action type (e.g., 'generate_strategy')
- input_data (json) - User input + context
- output_data (longtext) - Agent response (full data)
- tokens_used (integer) - OpenAI tokens consumed
- execution_time_ms (integer) - Performance metric
- feedback (enum: positive, negative, neutral, nullable)
- feedback_comment (text, nullable)
- success (boolean) - Execution success
- error_message (text, nullable)
- metadata (json, nullable) - Additional context
- timestamps
```

**Indexes:**
- `(user_id, agent_type_id)` - Fast user queries
- `created_at` - Time-series analysis

#### `packages` (Updated)
Added agent slot limits

```sql
... existing columns ...
+ agent_slots (integer, default 1)
  // -1 = unlimited
  // 0 = no agents
  // N = max N agents
```

**Package Limits:**
- Free: 1 agent slot
- Starter: 2 agent slots
- Professional: 5 agent slots
- Agency: -1 (unlimited)

---

## 🧠 Agent Architecture

### Base Agent Class (`BaseAgent.php`)

All agents extend this abstract class:

```php
abstract class BaseAgent
{
    protected string $agentType;      // e.g., 'marketing', 'qa'
    protected string $agentName;      // Display name
    protected int $experienceYears = 20; // Expert level
    
    abstract protected function getSystemPrompt(): string;
    abstract protected function buildPrompt(array $context): string;
    
    public function execute(array $context): array
    {
        // 1. Start timing
        // 2. Generate system + user prompts
        // 3. Call OpenAI
        // 4. Log interaction (success/failure) via AgentInteractionService
        // 5. Return response
    }
}
```

**Key Features:**
- ✅ Automatic interaction logging
- ✅ Performance tracking (execution time, tokens)
- ✅ Error handling with detailed logging
- ✅ Standardized prompt structure

### Specialized Base Classes

#### `BaseMarketingAgent` (extends `BaseAgent`)
Adds marketing-specific helpers:

```php
protected function buildBrandContext(): string
{
    // Fetches user's brand settings (name, tone, colors, logo)
}

protected function buildAnalyticsContext(): string
{
    // Fetches performance metrics
}
```

---

## 🔐 Permissions (Spatie)

### New Permissions

#### Agent-Specific Permissions
```
- use-marketing-agent
- use-qa-agent
- use-developer-agent
- use-accountant-agent
- use-customer-service-agent
```

#### Agent Management Permissions
```
- activate-agents      # Can activate new agents
- deactivate-agents    # Can deactivate agents
- configure-agents     # Can modify agent settings
- view-agent-analytics # Can view usage analytics
```

### Role Assignments

| Permission | Admin | Manager | User | Viewer |
|------------|-------|---------|------|--------|
| use-marketing-agent | ✅ | ✅ | ✅ | ❌ |
| use-qa-agent | ✅ | ✅ | ✅ | ❌ |
| use-developer-agent | ✅ | ✅ | ✅ | ❌ |
| use-accountant-agent | ✅ | ✅ | ✅ | ❌ |
| use-customer-service-agent | ✅ | ✅ | ✅ | ❌ |
| activate-agents | ✅ | ✅ | ✅ | ❌ |
| deactivate-agents | ✅ | ✅ | ✅ | ❌ |
| configure-agents | ✅ | ✅ | ❌ | ❌ |
| view-agent-analytics | ✅ | ✅ | ❌ | ❌ |

---

## 🛣️ Routes

### Agent Management Routes

```php
Route::middleware(['auth', 'subscription.active'])
    ->prefix('agents')
    ->name('agents.')
    ->group(function () {
        Route::get('/', [AgentController::class, 'index'])
            ->name('index'); // Agent marketplace
        
        Route::get('/dashboard', [DashboardController::class, 'agentDashboard'])
            ->name('dashboard'); // View active agents
        
        Route::post('/{agentCode}/activate', [AgentController::class, 'activate'])
            ->name('activate');
        
        Route::delete('/{agentCode}/deactivate', [AgentController::class, 'deactivate'])
            ->name('deactivate');
        
        Route::get('/{agentCode}/analytics', [AgentController::class, 'analytics'])
            ->name('analytics');
        
        Route::post('/interaction/{interactionId}/feedback', [AgentController::class, 'feedback'])
            ->name('feedback'); // Record user feedback
        
        Route::get('/interaction/{interactionId}', [AgentController::class, 'interaction'])
            ->name('interaction'); // View interaction details
        
        Route::get('/{agentCode}/export', [AgentController::class, 'exportTrainingData'])
            ->name('export'); // Admin: export ML training data
    });
```

---

## 🎨 User Interface

### Agent Marketplace (`/agents`)

Features:
- ✅ Grid display of all available agents
- ✅ Visual indicators (icon, color, category)
- ✅ Feature lists
- ✅ Slot usage display ("2/5 slots used")
- ✅ Activate/Deactivate buttons
- ✅ "View Analytics" link for active agents
- ✅ Disabled state when no slots available

### Agent Dashboard (`/agents/dashboard`) ⏳ TODO
Will display:
- All active agents
- Quick access to each agent's interface
- Usage statistics per agent
- Recent interactions

### Agent Analytics (`/agents/{code}/analytics`) ⏳ TODO
Will display:
- Total interactions
- Success/failure rates
- Token usage
- Average execution time
- Positive/negative feedback ratio
- Recent interactions with feedback options

---

## 🔄 Data Collection System

### Purpose
Collect **every user interaction** with agents to:
1. Train future AI models
2. Improve agent prompts
3. Identify common patterns
4. Build domain-specific datasets
5. Enable personalized agent behavior

### What's Logged
```php
AgentInteraction::create([
    'user_id' => $user->id,
    'agent_type_id' => $agentType->id,
    'action' => 'generate_strategy',
    'input_data' => [
        'context' => [...],
        'system_prompt' => '...',
        'user_prompt' => '...',
    ],
    'output_data' => [...], // Full AI response
    'tokens_used' => 1250,
    'execution_time_ms' => 3450,
    'success' => true,
    'metadata' => [
        'agent_version' => '2.0',
        'model' => 'gpt-4',
    ],
]);
```

### Feedback Loop
Users can provide feedback on any interaction:
- 👍 Positive
- 👎 Negative
- 😐 Neutral
- Optional comment

This creates a labeled dataset for supervised learning.

### Export for Training
Admins can export training data with filters:
```
GET /agents/{code}/export?success=true&feedback=positive&date_from=2025-01-01
```

Returns JSON dataset ready for ML training.

---

## ✅ Completed Tasks

### Infrastructure
- ✅ Created `agent_types` table and migration
- ✅ Created `user_agents` table and migration
- ✅ Created `agent_interactions` table and migration
- ✅ Added `agent_slots` to packages table
- ✅ Seeded 5 agent types with full details
- ✅ Updated packages with slot limits (1, 2, 5, -1)

### Models
- ✅ AgentType model with relationships and scopes
- ✅ UserAgent model with helper methods
- ✅ AgentInteraction model with feedback methods
- ✅ User model updated with agent relationships

### Services
- ✅ AgentService - Agent activation/deactivation logic
- ✅ AgentInteractionService - Data logging and analytics

### Base Classes
- ✅ BaseAgent - Abstract base with automatic logging
- ✅ BaseMarketingAgent - Marketing-specific helpers

### Agents
- ✅ StrategistAgent - Refactored with 20-year expert prompt

### Controllers
- ✅ AgentController - Complete agent management

### Views
- ✅ Agent marketplace (`agents/index.blade.php`)

### Routes
- ✅ Complete agent management route group

### Permissions
- ✅ Updated RolePermissionSeeder with 10 new permissions
- ✅ Assigned permissions to all roles

---

## ⏳ Pending Tasks (Priority Order)

### 1. Refactor Remaining Marketing Agents (2 hours)
- [ ] Migrate CopywriterAgent to `app/Agents/Marketing/`
- [ ] Migrate CreativeAgent to `app/Agents/Marketing/`
- [ ] Extend BaseMarketingAgent
- [ ] Add AgentInteractionService to constructor
- [ ] Update AIStudioController imports

### 2. Update Existing Controllers (1 hour)
- [ ] AIStudioController - Import agents from `App\Agents\Marketing\`
- [ ] ContentGenerationController - Update agent imports
- [ ] Remove old service files from `App\Services\AI\`

### 3. Create Agent Dashboard View (2 hours)
- [ ] Design dashboard layout
- [ ] Display all active agents with quick actions
- [ ] Show usage stats per agent
- [ ] Add "Switch Agent" functionality

### 4. Create Agent Analytics View (2 hours)
- [ ] Chart.js integration for visualizations
- [ ] Display success/failure rates
- [ ] Token usage over time
- [ ] Recent interactions table
- [ ] Feedback interface

### 5. Migrate AI Studio Views (1.5 hours)
- [ ] Move `ai-studio/` views to `agents/marketing/`
- [ ] Update route references
- [ ] Update navigation menu
- [ ] Test all links

### 6. Create QA Agent (3 hours)
- [ ] Create `app/Agents/QA/BaseQAAgent.php`
- [ ] Create `app/Agents/QA/TestPlannerAgent.php`
- [ ] Create QA-specific views
- [ ] Add routes

### 7. Create Developer Agent (3 hours)
- [ ] Create `app/Agents/Developer/BaseDeveloperAgent.php`
- [ ] Create `app/Agents/Developer/CodeReviewAgent.php`
- [ ] Create developer-specific views
- [ ] Add routes

### 8. Create Accountant Agent (3 hours)
- [ ] Create `app/Agents/Accountant/BaseAccountantAgent.php`
- [ ] Create `app/Agents/Accountant/FinancialAnalystAgent.php`
- [ ] Create accountant-specific views
- [ ] Add routes

### 9. Create Customer Service Agent (3 hours)
- [ ] Create `app/Agents/CustomerService/BaseCustomerServiceAgent.php`
- [ ] Create `app/Agents/CustomerService/SupportAgent.php`
- [ ] Create CS-specific views
- [ ] Add routes

### 10. Testing & Documentation (3 hours)
- [ ] Test agent activation/deactivation
- [ ] Test slot limits
- [ ] Test permissions
- [ ] Test data collection
- [ ] Update README.md
- [ ] Create API documentation

**Total Estimated Time:** ~24 hours

---

## 🚀 Usage Examples

### Activating an Agent

```php
$agentService = app(AgentService::class);
$agentService->activateAgent($user, 'marketing');
// Throws exception if no slots available
```

### Using an Agent

```php
$strategist = app(StrategistAgent::class);
$response = $strategist->execute([
    'action' => 'generate_strategy',
    'business_type' => 'E-commerce',
    'target_audience' => 'Millennials',
]);
// Automatically logs interaction to agent_interactions table
```

### Checking Agent Stats

```php
$stats = $agentService->getAgentStats($user, 'marketing');
// Returns:
// [
//     'is_active' => true,
//     'interaction_count' => 45,
//     'last_used_at' => Carbon,
//     'activated_at' => Carbon,
// ]
```

### Recording Feedback

```php
$interactionService = app(AgentInteractionService::class);
$interactionService->recordFeedback(
    interactionId: 123,
    feedback: 'positive',
    comment: 'Great strategy suggestions!'
);
```

---

## 📊 Package Comparison

| Feature | Free | Starter | Professional | Agency |
|---------|------|---------|--------------|--------|
| Agent Slots | 1 | 2 | 5 | Unlimited |
| Marketing Agent | ✅ | ✅ | ✅ | ✅ |
| QA Agent | ❌ | ✅ | ✅ | ✅ |
| Developer Agent | ❌ | ✅ | ✅ | ✅ |
| Accountant Agent | ❌ | ❌ | ✅ | ✅ |
| Customer Service | ❌ | ❌ | ✅ | ✅ |
| Agent Analytics | ❌ | ✅ | ✅ | ✅ |
| Priority Support | ❌ | ❌ | ✅ | ✅ |

---

## 🔮 Future Enhancements

### Phase 1: Current Implementation
- Multi-agent activation/deactivation
- Data collection for learning
- Basic analytics
- Feedback system

### Phase 2: Intelligence Layer (Future)
- Use collected data to fine-tune agents
- Personalized agent behavior per user
- Auto-suggest next actions based on patterns
- Predictive analytics

### Phase 3: Advanced Features (Future)
- Agent-to-agent collaboration
- Custom agent creation by users
- Agent marketplace (3rd party agents)
- Multi-language support
- Voice interaction

---

## 📝 Notes

### Why This Architecture?

1. **Scalability**: Easy to add new agent types
2. **Maintainability**: Clear separation of concerns
3. **Data Collection**: Built-in ML data pipeline
4. **Flexibility**: Package-based slot limits
5. **Extensibility**: Base classes reduce code duplication

### Migration Strategy

We're taking an **incremental approach**:
1. ✅ Build new infrastructure (tables, models, services)
2. ✅ Migrate one agent (StrategistAgent) as proof of concept
3. ⏳ Migrate remaining marketing agents
4. ⏳ Create new agent types one by one
5. ⏳ Gradually sunset old architecture

This minimizes disruption while allowing continuous testing.

---

## 🆘 Troubleshooting

### "No available agent slots"
- User has reached their package limit
- Solution: Upgrade package or deactivate an existing agent

### Agent not appearing in marketplace
- Check `agent_types.is_active = true`
- Check user permissions (`use-{agent}-agent`)

### Interaction not logging
- Ensure user is authenticated
- Check `AgentInteractionService` injection in BaseAgent
- Verify database connection

---

**Last Updated:** December 21, 2025  
**Author:** AI Development Team  
**Status:** Active Development - 30% Complete
