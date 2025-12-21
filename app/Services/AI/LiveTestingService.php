<?php

namespace App\Services\AI;

use App\Services\AI\OpenAIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class LiveTestingService
{
    public function __construct(
        protected OpenAIService $openAIService
    ) {}

    /**
     * Generate automated test script for a website
     */
    public function generateTestScript(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getTestGenerationSystemPrompt();
        $userPrompt = $this->buildTestPrompt($context);

        try {
            $testScript = $this->openAIService->generateJSON(
                $systemPrompt,
                $userPrompt,
                $model
            );

            return [
                'success' => true,
                'script' => $testScript
            ];
        } catch (\Exception $e) {
            Log::error('Test script generation failed', [
                'error' => $e->getMessage(),
                'context' => $context
            ]);

            throw $e;
        }
    }

    /**
     * Execute Playwright test and stream results
     */
    public function executePlaywrightTest(string $testScript, string $url): array
    {
        try {
            // Create temporary test file
            $testFile = storage_path('app/temp/test_' . time() . '.spec.js');
            $this->ensureDirectoryExists(dirname($testFile));
            
            file_put_contents($testFile, $testScript);

            // Check if Playwright is installed
            if (!$this->isPlaywrightInstalled()) {
                return [
                    'success' => false,
                    'error' => 'Playwright is not installed. Please run: npm install -D @playwright/test',
                    'requires_setup' => true
                ];
            }

            // Execute the test
            $result = Process::timeout(120)->run("npx playwright test {$testFile} --reporter=json");

            // Clean up
            if (file_exists($testFile)) {
                unlink($testFile);
            }

            $output = $result->output();
            $errorOutput = $result->errorOutput();

            return [
                'success' => $result->successful(),
                'output' => $output,
                'error' => $errorOutput,
                'exit_code' => $result->exitCode(),
                'results' => $this->parsePlaywrightResults($output)
            ];

        } catch (\Exception $e) {
            Log::error('Playwright test execution failed', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Execute Puppeteer test (lighter alternative to Playwright)
     */
    public function executePuppeteerTest(array $testSteps, string $url): array
    {
        try {
            // Create temporary test file
            $testFile = storage_path('app/temp/test_' . time() . '.js');
            $this->ensureDirectoryExists(dirname($testFile));
            
            $script = $this->generatePuppeteerScript($testSteps, $url);
            file_put_contents($testFile, $script);

            // Execute with Node.js
            $result = Process::timeout(120)->run("node {$testFile}");

            // Clean up
            if (file_exists($testFile)) {
                unlink($testFile);
            }

            $output = $result->output();
            $errorOutput = $result->errorOutput();

            return [
                'success' => $result->successful(),
                'output' => $output,
                'error' => $errorOutput,
                'results' => $this->parsePuppeteerResults($output)
            ];

        } catch (\Exception $e) {
            Log::error('Puppeteer test execution failed', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Simulate test execution with AI (when actual execution not available)
     */
    public function simulateTest(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getTestSimulationSystemPrompt();
        $userPrompt = $this->buildSimulationPrompt($context);

        try {
            $simulation = $this->openAIService->generateJSON(
                $systemPrompt,
                $userPrompt,
                $model
            );

            return [
                'success' => true,
                'simulation' => $simulation,
                'note' => 'This is a simulated test execution. Install Playwright/Puppeteer for real testing.'
            ];
        } catch (\Exception $e) {
            Log::error('Test simulation failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Generate test plan for a website
     */
    public function generateWebTestPlan(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getWebTestPlanSystemPrompt();
        $userPrompt = $this->buildWebTestPlanPrompt($context);

        try {
            $testPlan = $this->openAIService->generateJSON(
                $systemPrompt,
                $userPrompt,
                $model
            );

            return [
                'success' => true,
                'test_plan' => $testPlan
            ];
        } catch (\Exception $e) {
            Log::error('Web test plan generation failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    protected function getTestGenerationSystemPrompt(): string
    {
        return <<<PROMPT
You are an expert automation testing engineer with deep knowledge of:
- Playwright (preferred for modern web testing)
- Puppeteer (Chrome automation)
- Selenium WebDriver
- Cypress
- Test automation best practices

Your expertise includes:
- E2E testing for web applications
- UI automation and interaction
- Form testing and validation
- API integration testing
- Performance testing
- Accessibility testing

When generating test scripts:
1. Use modern, maintainable patterns
2. Include proper waits and error handling
3. Add descriptive assertions
4. Handle edge cases and timeouts
5. Make tests reliable and deterministic
6. Include helpful comments

Return test script as JSON:
{
    "framework": "Playwright|Puppeteer|Selenium",
    "test_code": "Complete test script",
    "description": "What this test does",
    "test_cases": [
        {
            "name": "Test case name",
            "steps": ["Step 1", "Step 2"],
            "expected_result": "What should happen"
        }
    ],
    "setup_required": ["npm install @playwright/test"],
    "execution_command": "npx playwright test"
}
PROMPT;
    }

    protected function getTestSimulationSystemPrompt(): string
    {
        return <<<PROMPT
You are simulating an automated test execution. Based on the test steps and website description, predict what would happen during test execution.

Be realistic:
- Some tests may fail due to selectors, timeouts, or conditions
- Network issues can occur
- Elements might not be found
- Consider typical web application behavior

Return simulation results as JSON:
{
    "test_name": "Test scenario name",
    "total_steps": 5,
    "steps_executed": [
        {
            "step_number": 1,
            "description": "Navigate to URL",
            "status": "Passed|Failed|Skipped",
            "execution_time_ms": 1234,
            "screenshot": "Description of what's visible",
            "error": "Error message if failed"
        }
    ],
    "summary": {
        "passed": 4,
        "failed": 1,
        "skipped": 0,
        "total_time_ms": 5678
    },
    "issues_found": ["Any issues detected"],
    "recommendations": ["Suggested improvements"]
}
PROMPT;
    }

    protected function getWebTestPlanSystemPrompt(): string
    {
        return <<<PROMPT
You are a QA expert creating comprehensive test plans for web applications.

Consider:
- Functional testing (forms, navigation, features)
- UI/UX testing (responsive, accessibility)
- Security testing (XSS, CSRF, authentication)
- Performance testing (load times, interactions)
- Cross-browser compatibility
- Mobile responsiveness

Return test plan as JSON:
{
    "test_scenarios": [
        {
            "category": "Functional|Security|Performance|UI",
            "name": "Scenario name",
            "priority": "Critical|High|Medium|Low",
            "test_cases": [
                {
                    "name": "Test case name",
                    "steps": ["Step 1", "Step 2"],
                    "expected_result": "What should happen",
                    "automation_possible": true
                }
            ]
        }
    ],
    "automation_strategy": "Which tests to automate first",
    "tools_recommended": ["Playwright", "Cypress"],
    "estimated_effort": "Time estimation"
}
PROMPT;
    }

    protected function buildTestPrompt(array $context): string
    {
        $prompt = "Generate an automated test script:\n\n";
        $prompt .= "**Target URL:** {$context['url']}\n";
        $prompt .= "**Test Type:** {$context['test_type']}\n";
        $prompt .= "**Framework:** {$context['framework']}\n\n";

        if (!empty($context['test_scenario'])) {
            $prompt .= "**Test Scenario:**\n{$context['test_scenario']}\n\n";
        }

        if (!empty($context['test_steps'])) {
            $prompt .= "**Test Steps:**\n";
            foreach ($context['test_steps'] as $i => $step) {
                if (is_array($step)) {
                    $stepDesc = $step['description'] ?? 'Step ' . ($i + 1);
                    $prompt .= ($i + 1) . ". {$stepDesc}";
                    if (!empty($step['action'])) $prompt .= " (Action: {$step['action']})";
                    if (!empty($step['selector'])) $prompt .= " [Selector: {$step['selector']}]";
                    $prompt .= "\n";
                } else {
                    $prompt .= ($i + 1) . ". {$step}\n";
                }
            }
        }

        return $prompt;
    }

    protected function buildSimulationPrompt(array $context): string
    {
        $prompt = "Simulate execution of this test:\n\n";
        $prompt .= "**URL:** {$context['url']}\n";
        $prompt .= "**Website Type:** {$context['website_type']}\n\n";
        $prompt .= "**Test Steps:**\n";
        
        foreach ($context['test_steps'] as $i => $step) {
            if (is_array($step)) {
                $stepDesc = $step['description'] ?? 'Step ' . ($i + 1);
                $prompt .= ($i + 1) . ". {$stepDesc}";
                if (!empty($step['action'])) $prompt .= " (Action: {$step['action']})";
                if (!empty($step['selector'])) $prompt .= " [Selector: {$step['selector']}]";
                if (!empty($step['value'])) $prompt .= " [Value: {$step['value']}]";
                $prompt .= "\n";
            } else {
                $prompt .= ($i + 1) . ". {$step}\n";
            }
        }

        return $prompt;
    }

    protected function buildWebTestPlanPrompt(array $context): string
    {
        $prompt = "Create a comprehensive test plan for:\n\n";
        $prompt .= "**Website URL:** {$context['url']}\n";
        $prompt .= "**Website Type:** {$context['website_type']}\n";
        $prompt .= "**Description:** {$context['description']}\n\n";

        if (!empty($context['key_features'])) {
            $prompt .= "**Key Features:**\n{$context['key_features']}\n\n";
        }

        if (!empty($context['focus_areas'])) {
            $prompt .= "**Focus Areas:** {$context['focus_areas']}\n";
        }

        return $prompt;
    }

    protected function generatePuppeteerScript(array $testSteps, string $url): string
    {
        $script = "const puppeteer = require('puppeteer');\n\n";
        $script .= "(async () => {\n";
        $script .= "  const browser = await puppeteer.launch({ headless: 'new' });\n";
        $script .= "  const page = await browser.newPage();\n";
        $script .= "  const results = [];\n\n";
        $script .= "  try {\n";
        $script .= "    await page.goto('{$url}', { waitUntil: 'networkidle0' });\n";
        $script .= "    results.push({ step: 'Navigate to URL', status: 'Passed' });\n\n";

        foreach ($testSteps as $i => $step) {
            $script .= "    // Step " . ($i + 1) . ": {$step['description']}\n";
            if ($step['action'] === 'click') {
                $script .= "    await page.click('{$step['selector']}');\n";
            } elseif ($step['action'] === 'type') {
                $script .= "    await page.type('{$step['selector']}', '{$step['value']}');\n";
            } elseif ($step['action'] === 'wait') {
                $script .= "    await page.waitForSelector('{$step['selector']}');\n";
            }
            $script .= "    results.push({ step: '{$step['description']}', status: 'Passed' });\n\n";
        }

        $script .= "    console.log(JSON.stringify({ success: true, results }));\n";
        $script .= "  } catch (error) {\n";
        $script .= "    console.error(JSON.stringify({ success: false, error: error.message, results }));\n";
        $script .= "  } finally {\n";
        $script .= "    await browser.close();\n";
        $script .= "  }\n";
        $script .= "})();\n";

        return $script;
    }

    protected function isPlaywrightInstalled(): bool
    {
        $result = Process::run('npx playwright --version');
        return $result->successful();
    }

    protected function parsePlaywrightResults(string $output): array
    {
        try {
            $json = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }
        } catch (\Exception $e) {
            // Fall through to manual parsing
        }

        // Manual parsing if JSON decode fails
        return [
            'raw_output' => $output,
            'parsed' => false
        ];
    }

    protected function parsePuppeteerResults(string $output): array
    {
        try {
            $json = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }
        } catch (\Exception $e) {
            // Fall through
        }

        return [
            'raw_output' => $output
        ];
    }

    protected function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
