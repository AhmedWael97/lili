<?php

namespace App\Services\AI;

class QAAgentService
{
    public function __construct(
        protected OpenAIService $openAI
    ) {}

    /**
     * Generate comprehensive test plan
     */
    public function generateTestPlan(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getTestPlanSystemPrompt();
        $userPrompt = $this->buildTestPlanPrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt, $model);
    }

    /**
     * Analyze code for bugs and issues
     */
    public function analyzeBugs(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getBugAnalysisSystemPrompt();
        $userPrompt = $this->buildBugAnalysisPrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt, $model);
    }

    /**
     * Generate automated test code
     */
    public function generateAutomatedTests(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getAutomatedTestSystemPrompt();
        $userPrompt = $this->buildAutomatedTestPrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt, $model);
    }

    /**
     * Generate test cases
     */
    public function generateTestCases(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getTestCaseSystemPrompt();
        $userPrompt = $this->buildTestCasePrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt, $model);
    }

    /**
     * Analyze security vulnerabilities
     */
    public function analyzeSecurityVulnerabilities(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getSecurityAnalysisSystemPrompt();
        $userPrompt = $this->buildSecurityAnalysisPrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt, $model);
    }

    /**
     * Generate bug report
     */
    public function generateBugReport(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getBugReportSystemPrompt();
        $userPrompt = $this->buildBugReportPrompt($context);

        return $this->openAI->generateJSON($userPrompt, $systemPrompt, $model);
    }

    /**
     * System prompt for test plan generation
     */
    protected function getTestPlanSystemPrompt(): string
    {
        return <<<PROMPT
You are a Senior QA Engineer with 20 years of experience in software quality assurance.

Your expertise includes:
- Test strategy development
- Risk-based testing
- Test automation frameworks (Selenium, Cypress, PHPUnit, Jest, Playwright)
- Performance and load testing
- Security testing (OWASP Top 10)
- Accessibility testing (WCAG 2.1)
- API testing (REST, GraphQL)
- Mobile testing (iOS, Android)
- CI/CD integration
- Test metrics and reporting

You create comprehensive, practical test plans that:
1. Identify all testing requirements
2. Define test scope and approach
3. Specify test environments and tools
4. Outline test deliverables
5. Estimate effort and timeline
6. Address risks and dependencies
7. Include acceptance criteria
8. Cover functional and non-functional testing

Always respond in JSON format with structured test plans.
PROMPT;
    }

    /**
     * System prompt for bug analysis
     */
    protected function getBugAnalysisSystemPrompt(): string
    {
        return <<<PROMPT
You are an expert Bug Hunter and Code Analyzer with deep knowledge of common software defects.

Your analysis covers:
- Logic errors and edge cases
- Memory leaks and performance issues
- Security vulnerabilities (SQL injection, XSS, CSRF)
- Race conditions and concurrency bugs
- Null pointer exceptions
- Off-by-one errors
- Input validation failures
- Error handling gaps
- API misuse
- Configuration issues

You provide:
- Clear bug descriptions
- Severity assessment (Critical, High, Medium, Low)
- Reproduction steps
- Root cause analysis
- Fix recommendations
- Prevention strategies

Always respond in JSON format with actionable bug reports.
PROMPT;
    }

    /**
     * System prompt for automated test generation
     */
    protected function getAutomatedTestSystemPrompt(): string
    {
        return <<<PROMPT
You are a Test Automation Expert specializing in writing high-quality automated tests.

You generate tests for:
- Unit tests (PHPUnit, Jest, PyTest, JUnit)
- Integration tests
- E2E tests (Cypress, Playwright, Selenium)
- API tests (Postman, REST Assured)

Your tests follow best practices:
- AAA pattern (Arrange, Act, Assert)
- Clear test names describing what is tested
- Proper setup and teardown
- Mocking and stubbing where appropriate
- Edge case coverage
- Meaningful assertions
- Independent and isolated tests
- Fast execution

You provide complete, runnable test code with explanations.

Always respond in JSON format with test code and documentation.
PROMPT;
    }

    /**
     * System prompt for test case generation
     */
    protected function getTestCaseSystemPrompt(): string
    {
        return <<<PROMPT
You are a Test Case Design Specialist with expertise in creating thorough test scenarios.

Your test cases include:
- Test case ID and name
- Description and objective
- Preconditions
- Test steps (clear and detailed)
- Test data requirements
- Expected results
- Actual results section
- Pass/Fail criteria
- Priority and severity
- Test type (functional, regression, smoke, etc.)

You apply testing techniques:
- Boundary value analysis
- Equivalence partitioning
- Decision table testing
- State transition testing
- Exploratory testing scenarios
- Negative testing

Always respond in JSON format with structured test cases.
PROMPT;
    }

    /**
     * System prompt for security analysis
     */
    protected function getSecurityAnalysisSystemPrompt(): string
    {
        return <<<PROMPT
You are a Security Testing Expert with deep knowledge of OWASP Top 10 and secure coding practices.

You analyze for:
- SQL Injection vulnerabilities
- Cross-Site Scripting (XSS)
- Cross-Site Request Forgery (CSRF)
- Authentication and session management flaws
- Sensitive data exposure
- XML External Entities (XXE)
- Broken access control
- Security misconfiguration
- Insecure deserialization
- Using components with known vulnerabilities
- Insufficient logging and monitoring

You provide:
- Vulnerability description
- Risk level (Critical, High, Medium, Low)
- Affected components
- Attack scenarios
- Impact assessment
- Remediation steps
- Code examples for fixes
- Prevention recommendations

Always respond in JSON format with detailed security findings.
PROMPT;
    }

    /**
     * System prompt for bug report generation
     */
    protected function getBugReportSystemPrompt(): string
    {
        return <<<PROMPT
You are a QA Lead expert at writing clear, actionable bug reports that developers love.

Your bug reports include:
- Clear, descriptive title
- Environment details (OS, browser, version)
- Steps to reproduce (numbered, detailed)
- Expected behavior
- Actual behavior
- Screenshots/logs (if provided)
- Severity and priority
- Affected users/features
- Workaround (if available)
- Related tickets

You write reports that are:
- Professional and objective
- Technical but understandable
- Complete with all necessary details
- Easy to reproduce
- Prioritized correctly

Always respond in JSON format with complete bug report structure.
PROMPT;
    }

    /**
     * Build test plan prompt
     */
    protected function buildTestPlanPrompt(array $context): string
    {
        return <<<PROMPT
Create a comprehensive test plan for the following:

Project Information:
- Name: {$context['project_name']}
- Type: {$context['project_type']}
- Description: {$context['description']}
- Target Platform: {$context['platform']}
- Timeline: {$context['timeline']}

Requirements:
{$context['requirements']}

Generate a detailed test plan in JSON format with:
- executive_summary (overview of testing approach)
- test_objectives (what we want to achieve)
- scope (in_scope and out_of_scope arrays)
- test_strategy (approach, types of testing, coverage goals)
- test_schedule (phases with timeline)
- test_deliverables (reports, documentation, metrics)
- test_environment (hardware, software, tools needed)
- test_cases_summary (categories and estimated count)
- entry_exit_criteria (when to start/stop testing)
- risks_and_mitigation (potential issues and solutions)
- resources_needed (team size, roles, skills)
- success_metrics (KPIs for test quality)
PROMPT;
    }

    /**
     * Build bug analysis prompt
     */
    protected function buildBugAnalysisPrompt(array $context): string
    {
        return <<<PROMPT
Analyze the following code/feature for bugs and potential issues:

Code/Feature Details:
- Language: {$context['language']}
- Component: {$context['component']}
- Description: {$context['description']}

Code Snippet:
```
{$context['code']}
```

Context/Requirements:
{$context['requirements']}

Analyze and return JSON with:
- bugs_found (array of detected bugs with severity, description, location, impact)
- security_concerns (potential vulnerabilities)
- performance_issues (efficiency problems)
- code_quality_issues (maintainability, readability)
- edge_cases_missing (unhandled scenarios)
- recommendations (prioritized fixes)
- test_suggestions (tests to catch these issues)
PROMPT;
    }

    /**
     * Build automated test prompt
     */
    protected function buildAutomatedTestPrompt(array $context): string
    {
        return <<<PROMPT
Generate automated tests for the following:

Testing Framework: {$context['framework']}
Language: {$context['language']}
Component Type: {$context['component_type']}

Code to Test:
```
{$context['code']}
```

Requirements:
{$context['requirements']}

Generate JSON with:
- test_file_name (suggested file name)
- imports (required imports/dependencies)
- setup_code (beforeEach, setUp, etc.)
- test_cases (array of test objects with name, code, description)
- teardown_code (afterEach, tearDown, etc.)
- coverage_notes (what is tested, what needs manual testing)
- run_instructions (how to execute the tests)

Include tests for:
- Happy path scenarios
- Edge cases
- Error conditions
- Boundary values
- Invalid inputs
PROMPT;
    }

    /**
     * Build test case prompt
     */
    protected function buildTestCasePrompt(array $context): string
    {
        return <<<PROMPT
Create detailed test cases for the following feature:

Feature: {$context['feature_name']}
Description: {$context['description']}
User Story: {$context['user_story']}
Acceptance Criteria: {$context['acceptance_criteria']}

Generate JSON with:
- test_cases (array of test case objects)

Each test case should include:
- test_id (unique identifier)
- test_name (clear, descriptive)
- objective (what is being tested)
- priority (Critical/High/Medium/Low)
- type (Functional/Regression/Smoke/Integration/etc.)
- preconditions (setup required)
- test_steps (numbered array of actions)
- test_data (inputs needed)
- expected_result (what should happen)
- postconditions (state after test)
- automation_candidate (boolean - can this be automated?)

Include test cases for:
- Positive scenarios
- Negative scenarios
- Boundary conditions
- Error handling
- Performance (if applicable)
- Security (if applicable)
- Accessibility (if applicable)
PROMPT;
    }

    /**
     * Build security analysis prompt
     */
    protected function buildSecurityAnalysisPrompt(array $context): string
    {
        return <<<PROMPT
Perform security analysis on:

Application Type: {$context['app_type']}
Language/Framework: {$context['tech_stack']}
Component: {$context['component']}

Code/Configuration:
```
{$context['code']}
```

Focus Areas: {$context['focus_areas']}

Analyze and return JSON with:
- vulnerabilities (array of security issues found)
  - vulnerability_id
  - title
  - severity (Critical/High/Medium/Low)
  - owasp_category (which OWASP Top 10)
  - description
  - affected_component
  - proof_of_concept (how to exploit)
  - impact (what could happen)
  - remediation (how to fix with code examples)
  - references (CWE, CVE if applicable)

- security_score (0-100)
- compliance_issues (GDPR, PCI-DSS, etc.)
- best_practices_violations
- recommendations (prioritized action items)
PROMPT;
    }

    /**
     * Build bug report prompt
     */
    protected function buildBugReportPrompt(array $context): string
    {
        return <<<PROMPT
Generate a professional bug report for:

Issue Summary: {$context['summary']}
Observed Behavior: {$context['observed_behavior']}
Expected Behavior: {$context['expected_behavior']}
Environment: {$context['environment']}
Steps to Reproduce: {$context['steps']}

Additional Context:
{$context['additional_info']}

Generate JSON with complete bug report:
- title (clear, actionable)
- severity (Critical/High/Medium/Low with justification)
- priority (P0/P1/P2/P3 with reasoning)
- environment (OS, browser, version, etc.)
- description (detailed explanation)
- steps_to_reproduce (clear, numbered steps)
- expected_result (what should happen)
- actual_result (what actually happens)
- frequency (Always/Sometimes/Rare)
- impact (who/what is affected)
- workaround (if any)
- logs_errors (relevant error messages)
- screenshots_needed (what to capture)
- root_cause_hypothesis (possible reasons)
- suggested_fix (potential solution)
- related_tickets (similar issues)
- labels (tags for categorization)
PROMPT;
    }
}
