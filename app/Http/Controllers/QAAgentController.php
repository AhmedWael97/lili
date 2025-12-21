<?php

namespace App\Http\Controllers;

use App\Services\AI\QAAgentService;
use App\Services\AI\GitHubReviewService;
use App\Services\AI\LiveTestingService;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QAAgentController extends Controller
{
    public function __construct(
        protected QAAgentService $qaAgent,
        protected GitHubReviewService $githubReview,
        protected LiveTestingService $liveTesting,
        protected UsageService $usageService
    ) {}

    /**
     * Show QA Agent dashboard
     */
    public function index()
    {
        $usageSummary = $this->usageService->getUsageSummary(Auth::id());
        
        return view('qa-agent.index', compact('usageSummary'));
    }

    /**
     * Generate test plan
     */
    public function generateTestPlan(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_type' => 'required|string|max:100',
            'description' => 'required|string',
            'platform' => 'required|string|max:100',
            'timeline' => 'required|string|max:100',
            'requirements' => 'required|string',
        ]);

        try {
            // Get agent model from database
            $agentType = \App\Models\AgentType::where('code', 'qa')->first();
            $model = $agentType ? $agentType->ai_model : 'gpt-4o';

            $testPlan = $this->qaAgent->generateTestPlan($validated, $model);

            return response()->json([
                'success' => true,
                'test_plan' => $testPlan,
            ]);

        } catch (\Exception $e) {
            Log::error('Test plan generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate test plan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze code for bugs
     */
    public function analyzeBugs(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|string|max:50',
            'component' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|string',
            'requirements' => 'nullable|string',
        ]);

        try {
            $agentType = \App\Models\AgentType::where('code', 'qa')->first();
            $model = $agentType ? $agentType->ai_model : 'gpt-4o';

            $analysis = $this->qaAgent->analyzeBugs($validated, $model);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);

        } catch (\Exception $e) {
            Log::error('Bug analysis failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze code: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate automated tests
     */
    public function generateAutomatedTests(Request $request)
    {
        $validated = $request->validate([
            'framework' => 'required|string|max:100',
            'language' => 'required|string|max:50',
            'component_type' => 'required|string|max:100',
            'code' => 'required|string',
            'requirements' => 'nullable|string',
        ]);

        try {
            $agentType = \App\Models\AgentType::where('code', 'qa')->first();
            $model = $agentType ? $agentType->ai_model : 'gpt-4o';

            $tests = $this->qaAgent->generateAutomatedTests($validated, $model);

            return response()->json([
                'success' => true,
                'tests' => $tests,
            ]);

        } catch (\Exception $e) {
            Log::error('Automated test generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate tests: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate test cases
     */
    public function generateTestCases(Request $request)
    {
        $validated = $request->validate([
            'feature_name' => 'required|string|max:255',
            'description' => 'required|string',
            'user_story' => 'required|string',
            'acceptance_criteria' => 'required|string',
        ]);

        try {
            $agentType = \App\Models\AgentType::where('code', 'qa')->first();
            $model = $agentType ? $agentType->ai_model : 'gpt-4o';

            $testCases = $this->qaAgent->generateTestCases($validated, $model);

            return response()->json([
                'success' => true,
                'test_cases' => $testCases,
            ]);

        } catch (\Exception $e) {
            Log::error('Test case generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate test cases: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze security vulnerabilities
     */
    public function analyzeSecurityVulnerabilities(Request $request)
    {
        $validated = $request->validate([
            'app_type' => 'required|string|max:100',
            'tech_stack' => 'required|string|max:255',
            'component' => 'required|string|max:255',
            'code' => 'required|string',
            'focus_areas' => 'nullable|string',
        ]);

        try {
            $agentType = \App\Models\AgentType::where('code', 'qa')->first();
            $model = $agentType ? $agentType->ai_model : 'gpt-4o';

            $analysis = $this->qaAgent->analyzeSecurityVulnerabilities($validated, $model);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);

        } catch (\Exception $e) {
            Log::error('Security analysis failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze security: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate bug report
     */
    public function generateBugReport(Request $request)
    {
        $validated = $request->validate([
            'summary' => 'required|string|max:500',
            'observed_behavior' => 'required|string',
            'expected_behavior' => 'required|string',
            'environment' => 'required|string|max:500',
            'steps' => 'required|string',
            'additional_info' => 'nullable|string',
        ]);

        try {
            $agentType = \App\Models\AgentType::where('code', 'qa')->first();
            $model = $agentType ? $agentType->ai_model : 'gpt-4o';

            $bugReport = $this->qaAgent->generateBugReport($validated, $model);

            return response()->json([
                'success' => true,
                'bug_report' => $bugReport,
            ]);

        } catch (\Exception $e) {
            Log::error('Bug report generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate bug report: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Review GitHub Pull Request
     */
    public function reviewPullRequest(Request $request)
    {
        $validated = $request->validate([
            'github_token' => 'required|string',
            'owner' => 'required|string',
            'repo' => 'required|string',
            'pr_number' => 'required|integer',
            'post_comments' => 'boolean',
        ]);

        try {
            // Get agent model
            $agentType = \App\Models\AgentType::where('code', 'qa')->first();
            $model = $agentType ? $agentType->ai_model : 'gpt-4o';

            // Fetch PR from GitHub
            $prData = $this->githubReview->fetchPullRequest(
                $validated['owner'],
                $validated['repo'],
                $validated['pr_number'],
                $validated['github_token']
            );

            // Review the PR
            $review = $this->githubReview->reviewPullRequest($prData, $model);

            // Optionally post comments to GitHub
            if (!empty($validated['post_comments']) && $validated['post_comments']) {
                $this->githubReview->postReviewComments(
                    $validated['owner'],
                    $validated['repo'],
                    $validated['pr_number'],
                    $review['review'],
                    $validated['github_token']
                );
            }

            return response()->json([
                'success' => true,
                'review' => $review,
                'pr_data' => $prData,
                'comments_posted' => !empty($validated['post_comments'])
            ]);

        } catch (\Exception $e) {
            Log::error('GitHub PR review failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to review pull request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Execute live automation test
     */
    public function executeLiveTest(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'website_type' => 'required|string',
            'test_scenario' => 'required|string',
            'test_steps' => 'required|array',
            'test_steps.*.description' => 'required|string',
            'test_steps.*.action' => 'required|string',
            'test_steps.*.selector' => 'nullable|string',
            'test_steps.*.value' => 'nullable|string',
            'framework' => 'required|in:playwright,puppeteer,simulate',
        ]);

        try {
            // Get agent model
            $agentType = \App\Models\AgentType::where('code', 'qa')->first();
            $model = $agentType ? $agentType->ai_model : 'gpt-4o';

            $result = null;

            if ($validated['framework'] === 'simulate') {
                // AI-powered simulation (always works)
                $result = $this->liveTesting->simulateTest([
                    'url' => $validated['url'],
                    'website_type' => $validated['website_type'],
                    'test_steps' => $validated['test_steps']
                ], $model);
            } elseif ($validated['framework'] === 'playwright') {
                // Try actual Playwright execution
                $testScript = $this->liveTesting->generateTestScript([
                    'url' => $validated['url'],
                    'test_type' => 'E2E',
                    'framework' => 'Playwright',
                    'test_scenario' => $validated['test_scenario'],
                    'test_steps' => array_column($validated['test_steps'], 'description')
                ], $model);

                $result = $this->liveTesting->executePlaywrightTest(
                    $testScript['script']['test_code'],
                    $validated['url']
                );
            } elseif ($validated['framework'] === 'puppeteer') {
                // Puppeteer execution
                $result = $this->liveTesting->executePuppeteerTest(
                    $validated['test_steps'],
                    $validated['url']
                );
            }

            return response()->json([
                'success' => true,
                'results' => $result,
                'framework' => $validated['framework']
            ]);

        } catch (\Exception $e) {
            Log::error('Live test execution failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to execute test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate web test plan
     */
    public function generateWebTestPlan(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'website_type' => 'required|string',
            'description' => 'required|string',
            'key_features' => 'nullable|string',
            'focus_areas' => 'nullable|string',
        ]);

        try {
            // Get agent model
            $agentType = \App\Models\AgentType::where('code', 'qa')->first();
            $model = $agentType ? $agentType->ai_model : 'gpt-4o';

            $testPlan = $this->liveTesting->generateWebTestPlan($validated, $model);

            return response()->json([
                'success' => true,
                'test_plan' => $testPlan,
            ]);

        } catch (\Exception $e) {
            Log::error('Web test plan generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate test plan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
