# Agent Migration Checklist

## Overview
This document tracks the migration of agents from `app/Services/AI/` to the new `app/Agents/` architecture.

---

## Migration Status

### ✅ Completed
- [x] **StrategistAgent** (`StrategistAgentService.php` → `app/Agents/Marketing/StrategistAgent.php`)
  - Extended `BaseMarketingAgent`
  - Enhanced system prompt with "20 years experience" context
  - Added `AgentInteractionService` dependency
  - Tested successfully

### ⏳ Pending

#### Marketing Agents
- [ ] **CopywriterAgent** (`CopywriterAgentService.php` → `app/Agents/Marketing/CopywriterAgent.php`)
- [ ] **CreativeAgent** (`CreativeAgentService.php` → `app/Agents/Marketing/CreativeAgent.php`)
- [ ] **CommunityManagerAgent** (`CommunityManagerAgentService.php` → `app/Agents/Marketing/CommunityManagerAgent.php`)
- [ ] **AdsAgent** (`AdsAgentService.php` → `app/Agents/Marketing/AdsAgent.php`)

---

## Migration Steps (Template)

Use these steps for each agent migration:

### 1. Create New Agent File
```bash
# Example for CopywriterAgent
touch app/Agents/Marketing/CopywriterAgent.php
```

### 2. Basic Structure
```php
<?php

namespace App\Agents\Marketing;

use App\Agents\Base\BaseMarketingAgent;

class CopywriterAgent extends BaseMarketingAgent
{
    protected string $agentType = 'marketing';
    protected string $agentName = 'Senior Copywriter';
    
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are a SENIOR COPYWRITING EXPERT with 20 years of professional experience.

You act as a FULL COPYWRITING AGENCY, providing:
1. Compelling headlines and CTAs
2. Persuasive body copy
3. Brand voice consistency
4. SEO-optimized content
5. A/B testing suggestions
6. Conversion optimization

Always provide PROFESSIONAL, STRATEGIC copywriting that drives results.
PROMPT;
    }
    
    protected function buildPrompt(array $context): string
    {
        // Build prompt with brand context
        $prompt = $this->buildBrandContext();
        
        // Add specific copywriting context
        // ... copy logic from old service
        
        return $prompt;
    }
    
    // Public methods (keep same interface)
    public function generateCopy(...): array
    {
        return $this->execute([
            'action' => 'generate_copy',
            // ... context
        ]);
    }
}
```

### 3. Update Constructor
```php
public function __construct(
    protected OpenAIService $openAI,
    protected AgentInteractionService $interactionService
) {
    // BaseAgent constructor will handle this
}
```

### 4. Migrate Methods
- Copy public methods from old service
- Keep the same method signatures (compatibility)
- Change internal logic to use `$this->execute()` instead of direct OpenAI calls
- Remove manual logging (BaseAgent handles it)

### 5. Update System Prompt
Enhance with "20 years experience" context:
```
You are a [ROLE] with 20 years of professional experience.

You act as a FULL [DEPARTMENT] COMPANY, providing:
1. [Service 1]
2. [Service 2]
...

Always provide PROFESSIONAL, STRATEGIC [type] that [outcome].
```

### 6. Update Controller Imports
```php
// OLD
use App\Services\AI\CopywriterAgentService;

// NEW
use App\Agents\Marketing\CopywriterAgent;
```

### 7. Test
- Run the agent
- Check database for `agent_interactions` logs
- Verify output quality
- Confirm no errors

### 8. Delete Old File (After Testing)
```bash
rm app/Services/AI/CopywriterAgentService.php
```

---

## Example: CopywriterAgent Migration

### Old Code (`app/Services/AI/CopywriterAgentService.php`)
```php
<?php

namespace App\Services\AI;

class CopywriterAgentService
{
    public function __construct(
        protected OpenAIService $openAI
    ) {}
    
    public function generateCopy(string $topic, string $tone, array $keywords): array
    {
        $systemPrompt = "You are an expert copywriter...";
        
        $userPrompt = "Write copy for: {$topic}...";
        
        $response = $this->openAI->generateJSON($userPrompt, $systemPrompt);
        
        return $response;
    }
}
```

### New Code (`app/Agents/Marketing/CopywriterAgent.php`)
```php
<?php

namespace App\Agents\Marketing;

use App\Agents\Base\BaseMarketingAgent;

class CopywriterAgent extends BaseMarketingAgent
{
    protected string $agentType = 'marketing';
    protected string $agentName = 'Senior Copywriter';
    
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are a SENIOR COPYWRITING EXPERT with 20 years of professional experience.

You act as a FULL COPYWRITING AGENCY, providing:
1. Compelling headlines and CTAs
2. Persuasive body copy
3. Brand voice consistency
4. SEO-optimized content
5. A/B testing suggestions
6. Conversion optimization

Always provide PROFESSIONAL, STRATEGIC copywriting that drives results.
PROMPT;
    }
    
    protected function buildPrompt(array $context): string
    {
        $brandContext = $this->buildBrandContext();
        
        $topic = $context['topic'] ?? '';
        $tone = $context['tone'] ?? 'professional';
        $keywords = $context['keywords'] ?? [];
        
        $keywordsText = implode(', ', $keywords);
        
        return <<<PROMPT
{$brandContext}

Write compelling copy for the following:

Topic: {$topic}
Tone: {$tone}
Keywords to include: {$keywordsText}

Provide:
1. 3 headline options
2. Main body copy (3-4 paragraphs)
3. Call-to-action suggestions
4. SEO recommendations

Return as JSON with this structure:
{
    "headlines": ["..."],
    "body": "...",
    "ctas": ["..."],
    "seo_recommendations": ["..."]
}
PROMPT;
    }
    
    public function generateCopy(string $topic, string $tone, array $keywords): array
    {
        return $this->execute([
            'action' => 'generate_copy',
            'topic' => $topic,
            'tone' => $tone,
            'keywords' => $keywords,
        ]);
    }
}
```

**Key Changes:**
1. ✅ Extended `BaseMarketingAgent`
2. ✅ Set `$agentType` and `$agentName`
3. ✅ Moved prompt to `getSystemPrompt()` with enhanced context
4. ✅ Moved logic to `buildPrompt()` with brand context
5. ✅ Public method now calls `$this->execute()` (auto-logs)
6. ✅ No manual logging needed

---

## Controller Update Example

### Before
```php
use App\Services\AI\CopywriterAgentService;

class AIStudioController extends Controller
{
    public function __construct(
        protected CopywriterAgentService $copywriter
    ) {}
    
    public function generateContent(Request $request)
    {
        $result = $this->copywriter->generateCopy(
            $request->topic,
            $request->tone,
            $request->keywords
        );
        
        return view('ai-studio.result', compact('result'));
    }
}
```

### After
```php
use App\Agents\Marketing\CopywriterAgent;

class AIStudioController extends Controller
{
    public function __construct(
        protected CopywriterAgent $copywriter
    ) {}
    
    public function generateContent(Request $request)
    {
        // Same interface - no changes needed!
        $result = $this->copywriter->generateCopy(
            $request->topic,
            $request->tone,
            $request->keywords
        );
        
        return view('ai-studio.result', compact('result'));
    }
}
```

**Benefits:**
- Same method signatures = minimal controller changes
- Auto-logging of all interactions
- Consistent error handling
- Performance tracking
- Data collection for ML

---

## Testing Checklist

After migrating each agent:

- [ ] Agent file exists in correct location
- [ ] Extends correct base class (`BaseMarketingAgent`)
- [ ] Has `$agentType` and `$agentName` properties
- [ ] `getSystemPrompt()` includes "20 years experience"
- [ ] `buildPrompt()` uses `buildBrandContext()`
- [ ] Public methods call `$this->execute()`
- [ ] Controller imports updated
- [ ] No PHP errors
- [ ] Database logs interactions to `agent_interactions`
- [ ] Output quality maintained
- [ ] All tests pass

---

## Priority Order

1. **CopywriterAgent** (High Priority)
   - Used in content generation
   - Core marketing functionality
   
2. **CreativeAgent** (High Priority)
   - Used for image generation and creative concepts
   - Part of content workflow
   
3. **CommunityManagerAgent** (Medium Priority)
   - Used for social media management
   - Important but can work with old service temporarily
   
4. **AdsAgent** (Lower Priority)
   - Used for ad campaign creation
   - Less frequently used

---

## Files to Update

### Agent Files to Create
- [ ] `app/Agents/Marketing/CopywriterAgent.php`
- [ ] `app/Agents/Marketing/CreativeAgent.php`
- [ ] `app/Agents/Marketing/CommunityManagerAgent.php`
- [ ] `app/Agents/Marketing/AdsAgent.php`

### Controller Files to Update
- [ ] `app/Http/Controllers/AIStudioController.php`
- [ ] `app/Http/Controllers/ContentGenerationController.php`

### Files to Delete (After Testing)
- [ ] `app/Services/AI/StrategistAgentService.php` (can delete now - replaced by StrategistAgent)
- [ ] `app/Services/AI/CopywriterAgentService.php`
- [ ] `app/Services/AI/CreativeAgentService.php`
- [ ] `app/Services/AI/CommunityManagerAgentService.php`
- [ ] `app/Services/AI/AdsAgentService.php`

**Keep:** `app/Services/AI/OpenAIService.php` (core service, used by all agents)

---

## Quick Commands

```bash
# Create new agent files
touch app/Agents/Marketing/CopywriterAgent.php
touch app/Agents/Marketing/CreativeAgent.php
touch app/Agents/Marketing/CommunityManagerAgent.php
touch app/Agents/Marketing/AdsAgent.php

# Check for controller usage
grep -r "CopywriterAgentService" app/Http/Controllers/
grep -r "CreativeAgentService" app/Http/Controllers/
grep -r "CommunityManagerAgentService" app/Http/Controllers/
grep -r "AdsAgentService" app/Http/Controllers/

# Test database logging
php artisan tinker
>>> $agent = app(App\Agents\Marketing\StrategistAgent::class);
>>> $agent->execute(['action' => 'test']);
>>> DB::table('agent_interactions')->latest()->first();

# Clear cache after updates
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## Rollback Plan

If migration causes issues:

1. **Keep old service files** until fully tested
2. **Use Git branches** for each agent migration
3. **Test in dev environment** before production
4. **Update `.env`** to toggle between old/new architecture (if needed)

Example rollback:
```bash
git checkout feature/migrate-copywriter-agent -- app/Agents/Marketing/CopywriterAgent.php
git checkout main -- app/Http/Controllers/AIStudioController.php
```

---

**Status:** 1 of 5 marketing agents migrated (20% complete)  
**Next:** Migrate CopywriterAgent  
**ETA:** 4-6 hours to complete all marketing agents
