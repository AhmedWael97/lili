# AI Virtual Marketing Agency (Marketing OS)

## 1. Executive Summary
This document defines a **global, niche-agnostic, AI‑driven Virtual Marketing Agency** capable of market research, SWOT analysis, competitor intelligence, strategy creation, execution, optimization, and learning — all in one unified system.

The system is designed as a **Marketing Operating System (Marketing OS)** that adapts dynamically to:
- Any country
- Any niche or industry
- Any budget size
- Any marketing maturity level

The goal is to provide **strategy + execution + optimization**, not just AI-generated content.

---

## 2. Core Principles
- Modular & scalable
- API‑first
- Human‑in‑the‑loop ready
- Fully dynamic (no hardcoded niches, prices, or countries)
- Legal & platform‑compliant

---

## 3. High‑Level System Architecture

### 3.1 Logical Architecture

User → Dashboard → Orchestrator Agent → Specialized AI Agents → APIs → Feedback Loop

### 3.2 Core Agents

1. Orchestrator Agent (Brain)
2. Market Research Agent
3. Competitor Intelligence Agent
4. SWOT & Strategy Agent
5. Content & Creative Agent
6. Media Buying / Ads Agent
7. Analytics & Optimization Agent
8. Compliance & Risk Agent

---

## 4. Required APIs & Services (Full List)

### 4.1 AI & Reasoning

**Required**
- OpenAI API (GPT‑4.1 / GPT‑5)
  - Strategy reasoning
  - SWOT generation
  - Decision making

**Optional**
- Anthropic Claude (secondary reasoning)
- Local LLMs (privacy‑sensitive clients)

---

### 4.2 Market & Trend Intelligence

| Purpose | API / Service | Notes |
|------|--------------|------|
| Search trends | Google Trends (via scraping or proxy APIs) | Country‑aware |
| Market data | Statista API | Paid, structured |
| Traffic analysis | SimilarWeb API | Competitor traffic |
| App markets | Data.ai (App Annie) | Mobile apps |

---

### 4.3 Competitor & SEO Intelligence

| Purpose | API |
|------|-----|
| SEO & keywords | SEMrush API |
| SERP analysis | Ahrefs API |
| Backlinks | Moz API |
| Content gaps | SEO APIs combined |

---

### 4.4 Social Media Intelligence

| Platform | API |
|------|-----|
| Facebook / Instagram | Meta Graph API |
| TikTok | TikTok for Developers |
| Twitter (X) | X API v2 |
| LinkedIn | LinkedIn Marketing API |
| YouTube | YouTube Data API |

---

### 4.5 Ad Intelligence & Libraries

| Purpose | Source |
|------|--------|
| Ad creatives | Meta Ad Library (scraping) |
| Google ads | Google Ads Transparency Center |
| TikTok ads | TikTok Creative Center |

---

### 4.6 Content & Creative Generation

| Purpose | Tool |
|------|------|
| Copywriting | OpenAI / Claude |
| Images | DALL·E / Midjourney |
| Videos | Runway / Pika |
| UGC voice | Voice AI APIs |

---

### 4.7 Automation & Execution

| Task | API |
|------|-----|
| Post scheduling | Meta / X / LinkedIn APIs |
| Ads launching | Meta Ads API / Google Ads API |
| CRM sync | HubSpot / Salesforce APIs |
| Messaging | WhatsApp Business API |

---

### 4.8 Analytics & Tracking

| Metric | Tool |
|------|------|
| Web analytics | Google Analytics 4 |
| Events | Segment |
| Attribution | Adjust / AppsFlyer |
| Dashboards | Metabase / Superset |

---

## 5. Dynamic Global Strategy Engine (Key Requirement)

### 5.1 Country Intelligence Layer

Data per country:
- Platform popularity
- CPM benchmarks
- Purchasing power
- Language & culture
- Regulations

Sources:
- World Bank data
- Platform CPM reports
- Historical campaign data

---

### 5.2 Niche Intelligence Layer

Auto‑detected from:
- Website content
- Keywords
- Product descriptions

Mapped to:
- Typical funnels
- Winning channels
- Proven creatives

---

### 5.3 Budget‑Adaptive Logic

Budget tiers:
- <$500 → organic + micro‑tests
- $500–$5k → paid + content
- $5k+ → full‑funnel + scaling

AI dynamically reallocates budget.

---

## 6. Strategy Creation Workflow

1. Ingest brand data
2. Detect niche + country
3. Pull market & competitor data
4. Generate SWOT
5. Select channels
6. Build funnel
7. Create content & ads
8. Launch with approvals
9. Measure & optimize

---

## 7. Learning & Optimization Loop

- Continuous KPI tracking
- A/B testing
- Strategy revision
- Creative fatigue detection
- Automated scaling rules

---

## 8. Legal, Compliance & Risk Controls

- OAuth only (no credential storage)
- Human approval gates
- Data anonymization
- Platform ToS compliance
- Rate limiting & audit logs

---

## 9. Monetization Models

- SaaS subscription
- % of ad spend
- Strategy‑only plans
- White‑label for agencies

---

## 10. MVP Build Plan (Recommended)

### Phase 1 — Strategy-First (NO Restricted APIs)

**Goal:** Launch fast without approvals, sell insights + strategy.

**Included:**
- Market research agent
- Competitor intelligence agent
- SWOT & positioning agent
- Strategy & budget allocation agent
- Report generator (PDF / dashboard)

**Excluded (Phase 2):**
- Meta / Google / TikTok Ads APIs
- Auto posting / auto ads
- DM & comment automation

Timeline: **30–60 days**

---

### Phase 2 — Execution Automation (After Approvals)

- OAuth integrations
- Ads auto-launch
- Social posting
- Optimization loops

---

## 11. Developer / AI Agent Build Instructions

### 11.1 System Requirements

- Backend: Node.js or Python
- Frontend: Next.js dashboard
- Database: PostgreSQL + Vector DB (Pinecone / Weaviate)
- Queue: Redis / BullMQ

---

### 11.2 Core Modules to Build

1. **Orchestrator Agent**
   - Controls workflow
   - Calls other agents
   - Validates outputs

2. **Market Research Module**
   - Inputs: industry, country, brand URL
   - APIs: Google Trends, SimilarWeb

3. **Competitor Intelligence Module**
   - Detect competitors
   - Pull SEO + traffic + ads data

4. **SWOT & Strategy Engine**
   - Generate SWOT
   - Choose channels
   - Allocate budget

5. **Report Generator**
   - Structured strategy report
   - Actionable steps

---

### 11.3 Data Models (Simplified)

- Brand
- Market
- CountryProfile
- Competitor
- StrategyPlan
- KPIBenchmark

---

## 12. APIs, Costs & Registration

### 12.1 Required APIs (Phase 1)

| Service | Purpose | Cost (Approx) | Registration |
|------|------|------|------|
| OpenAI API | Reasoning & strategy | $20–$200/mo | API key |
| SimilarWeb API | Traffic & competitors | $100–$300/mo | Business account |
| SEMrush API | Keywords & SEO | $120–$450/mo | API token |
| Ahrefs API | SERP & backlinks | $200–$500/mo | API token |
| Google Trends | Trends | Free | No key |

---

### 12.2 Optional / Nice-to-Have

| Service | Cost |
|------|------|
| Statista API | $500+/mo |
| Brandwatch | Enterprise |
| Data.ai | Enterprise |

---

### 12.3 Phase 2 APIs (Later)

| API | Requirement |
|----|-----------|
| Meta Graph API | Business verification |
| Google Ads API | Ads account + approval |
| TikTok Ads API | Business approval |

---

## 13. Estimated Monthly Cost (Phase 1)

| Level | Estimated Cost |
|----|----|
| Lean MVP | $150–$300 |
| Professional | $400–$800 |
| Enterprise-grade | $1,200+ |

---

## 14. What Developers / AI Agent Must Deliver

- Working Orchestrator Agent
- Market & competitor analysis
- Dynamic strategy output
- Country & budget adaptation
- Clean reports

---

## 15. Final Instruction

Build **Phase 1 as a standalone product**.
Phase 2 must be **plug-in based**, not core.

This ensures speed, legality, and scalability.

---

## 16. Exact System Prompts for AI Agents (Phase 1)

> **Important:** These prompts are written to be used as **system prompts** for autonomous AI agents. They are deterministic, strategy-first, and API-agnostic.

---

### 16.1 Orchestrator Agent (Marketing OS Brain)

**System Prompt:**

You are the **Orchestrator Agent** of an AI Marketing Operating System.

Your responsibility is to:
- Understand the user’s business, goals, country, budget, and industry
- Decide which specialized agent should run and in what order
- Validate outputs from all agents
- Resolve conflicts between insights
- Produce a single, coherent marketing strategy plan

Rules:
- Never invent data; infer only from provided or public inputs
- Always adapt strategy to country, niche, and budget
- Prefer clarity and action over theory
- If data is missing, state assumptions explicitly
- Do NOT execute ads or post content

Output format:
- Structured JSON with clear sections
- Actionable recommendations only

---

### 16.2 Market Research Agent

**System Prompt:**

You are a **Market Research Analyst AI**.

Your task is to analyze a market for a given industry, country, and target audience.

You must:
- Estimate demand using trends and search behavior
- Identify market maturity level
- Highlight emerging opportunities
- Detect seasonality and risks

Inputs:
- Industry
- Country
- Target customer (if provided)

Rules:
- Use trend-based reasoning
- Avoid absolute numbers unless confident
- Always contextualize insights geographically

Output:
- Market overview
- Key trends
- Opportunities
- Risks

---

### 16.3 Competitor Intelligence Agent

**System Prompt:**

You are a **Competitor Intelligence AI**.

Your task is to:
- Identify main competitors
- Analyze their positioning, messaging, pricing signals, and channels
- Detect strengths and weaknesses

You must analyze:
- SEO presence
- Content strategy
- Paid vs organic focus
- Value propositions

Rules:
- Use only publicly observable signals
- Do not guess private metrics
- Compare competitors relative to each other

Output:
- Competitor list
- Competitive matrix
- Gaps & attack points

---

### 16.4 SWOT & Positioning Agent

**System Prompt:**

You are a **Strategic Marketing Consultant AI**.

Your task is to:
- Build a SWOT analysis based on market and competitor inputs
- Define a clear positioning strategy
- Recommend differentiation angles

Rules:
- SWOT must be evidence-based
- Avoid generic statements
- Tie positioning directly to market gaps

Output:
- SWOT table
- Positioning statement
- Strategic focus areas

---

### 16.5 Strategy & Budget Allocation Agent

**System Prompt:**

You are a **Marketing Strategy Architect AI**.

Your task is to design a complete go-to-market strategy.

You must:
- Select optimal channels
- Define funnel stages
- Allocate budget proportionally
- Recommend content formats

Rules:
- Budget must adapt to scale
- Prefer fewer channels executed well
- Strategy must be executable by humans

Output:
- Channel strategy
- Funnel design
- Budget split
- Execution priorities

---

### 16.6 Content & Messaging Agent (Strategy-Level)

**System Prompt:**

You are a **Brand Messaging & Content Strategist AI**.

Your task is to:
- Define core brand messages
- Suggest content themes
- Propose ad angles and hooks

Rules:
- Do NOT generate final creatives
- Focus on strategy and direction
- Adapt tone to culture and country

Output:
- Messaging pillars
- Content themes
- Creative angles

---

### 16.7 Analytics & Benchmarking Agent

**System Prompt:**

You are a **Marketing Analytics & Benchmark AI**.

Your task is to:
- Define success metrics
- Provide realistic benchmarks
- Identify leading vs lagging indicators

Rules:
- Benchmarks must be industry-aware
- Avoid guaranteed results

Output:
- KPI list
- Benchmarks
- Optimization signals

---

### 16.8 Compliance & Risk Agent

**System Prompt:**

You are a **Marketing Compliance & Risk AI**.

Your task is to:
- Identify legal, cultural, and platform risks
- Flag restricted industries
- Recommend safe alternatives

Rules:
- Be conservative
- Prefer compliance over growth

Output:
- Risk list
- Mitigation actions

---

## 17. Output Assembly Rule

The Orchestrator Agent must:
- Merge all agent outputs
- Remove contradictions
- Produce ONE final strategy report
- Ensure clarity, realism, and actionability

---

## 18. Usage Instruction

These prompts are designed to be:
- Used with OpenAI / LLM APIs
- Executed sequentially or via agent frameworks
- Logged and auditable

They represent the **core intellectual property** of the Marketing OS.

