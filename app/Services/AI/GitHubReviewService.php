<?php

namespace App\Services\AI;

use App\Services\AI\OpenAIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubReviewService
{
    public function __construct(
        protected OpenAIService $openAIService
    ) {}

    /**
     * Analyze a GitHub Pull Request and provide code review
     */
    public function reviewPullRequest(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getReviewSystemPrompt();
        $userPrompt = $this->buildReviewPrompt($context);

        try {
            $review = $this->openAIService->generateJSON(
                $systemPrompt,
                $userPrompt,
                $model
            );

            return [
                'success' => true,
                'review' => $review,
                'pr_url' => $context['pr_url'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('GitHub PR Review failed', [
                'error' => $e->getMessage(),
                'context' => $context
            ]);

            throw $e;
        }
    }

    /**
     * Fetch PR details from GitHub API
     */
    public function fetchPullRequest(string $owner, string $repo, int $prNumber, string $token): array
    {
        try {
            // Fetch PR details
            $prResponse = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/vnd.github.v3+json',
            ])->get("https://api.github.com/repos/{$owner}/{$repo}/pulls/{$prNumber}");

            if (!$prResponse->successful()) {
                throw new \Exception('Failed to fetch PR: ' . $prResponse->body());
            }

            $pr = $prResponse->json();

            // Fetch PR diff
            $diffResponse = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/vnd.github.v3.diff',
            ])->get("https://api.github.com/repos/{$owner}/{$repo}/pulls/{$prNumber}");

            $diff = $diffResponse->body();

            // Fetch PR files
            $filesResponse = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/vnd.github.v3+json',
            ])->get("https://api.github.com/repos/{$owner}/{$repo}/pulls/{$prNumber}/files");

            $files = $filesResponse->json();

            return [
                'title' => $pr['title'],
                'description' => $pr['body'] ?? '',
                'author' => $pr['user']['login'],
                'base_branch' => $pr['base']['ref'],
                'head_branch' => $pr['head']['ref'],
                'additions' => $pr['additions'],
                'deletions' => $pr['deletions'],
                'changed_files' => $pr['changed_files'],
                'diff' => $diff,
                'files' => $files,
                'pr_url' => $pr['html_url'],
                'state' => $pr['state']
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch GitHub PR', [
                'owner' => $owner,
                'repo' => $repo,
                'pr_number' => $prNumber,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Post review comments to GitHub PR
     */
    public function postReviewComments(
        string $owner,
        string $repo,
        int $prNumber,
        array $comments,
        string $token
    ): bool {
        try {
            // Format review body
            $body = $this->formatReviewForGitHub($comments);

            // Post review
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/vnd.github.v3+json',
            ])->post("https://api.github.com/repos/{$owner}/{$repo}/pulls/{$prNumber}/reviews", [
                'body' => $body,
                'event' => 'COMMENT' // Options: APPROVE, REQUEST_CHANGES, COMMENT
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to post review: ' . $response->body());
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to post GitHub review', [
                'owner' => $owner,
                'repo' => $repo,
                'pr_number' => $prNumber,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Analyze code diff for issues
     */
    public function analyzeDiff(array $context, string $model = 'gpt-4o'): array
    {
        $systemPrompt = $this->getDiffAnalysisSystemPrompt();
        $userPrompt = $this->buildDiffAnalysisPrompt($context);

        try {
            $analysis = $this->openAIService->generateJSON(
                $systemPrompt,
                $userPrompt,
                $model
            );

            return [
                'success' => true,
                'analysis' => $analysis
            ];
        } catch (\Exception $e) {
            Log::error('GitHub diff analysis failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    protected function getReviewSystemPrompt(): string
    {
        return <<<PROMPT
You are an expert code reviewer with 15+ years of experience in software engineering, architecture, and quality assurance.

Your expertise includes:
- Code quality and best practices (SOLID, DRY, KISS)
- Security vulnerabilities (OWASP Top 10)
- Performance optimization
- Testing coverage and quality
- Design patterns and architecture
- Language-specific idioms and conventions
- Code maintainability and readability
- Error handling and edge cases

When reviewing code, you:
1. Focus on critical issues first (security, bugs, performance)
2. Provide specific, actionable feedback
3. Explain WHY something is a problem, not just WHAT
4. Suggest concrete improvements with examples when possible
5. Acknowledge good practices and improvements
6. Balance criticism with encouragement
7. Consider the context and intent of the change

Return your review as JSON with this structure:
{
    "overall_assessment": "Brief summary of the PR",
    "approval_status": "APPROVE|REQUEST_CHANGES|COMMENT",
    "issues": [
        {
            "severity": "Critical|High|Medium|Low",
            "type": "Security|Bug|Performance|Style|Best Practice",
            "file": "filename.php",
            "line": 123,
            "description": "What's the issue?",
            "suggestion": "How to fix it",
            "code_example": "Suggested code if applicable"
        }
    ],
    "positive_points": ["What was done well"],
    "suggestions": ["General improvements"],
    "testing_recommendations": ["What should be tested"]
}
PROMPT;
    }

    protected function getDiffAnalysisSystemPrompt(): string
    {
        return <<<PROMPT
You are a code analysis expert specializing in identifying potential issues in code changes.

Focus on:
- Logic errors and bugs
- Security vulnerabilities
- Performance bottlenecks
- Missing error handling
- Edge cases not covered
- Breaking changes
- Code smells

Return analysis as JSON:
{
    "risk_level": "High|Medium|Low",
    "issues_found": [
        {
            "type": "Bug|Security|Performance|Breaking Change",
            "description": "Issue description",
            "location": "File and line number",
            "impact": "What could go wrong",
            "recommendation": "How to fix"
        }
    ],
    "tests_needed": ["What tests should be added"],
    "breaking_changes": ["Any breaking changes detected"]
}
PROMPT;
    }

    protected function buildReviewPrompt(array $context): string
    {
        $prompt = "Please review this Pull Request:\n\n";
        $prompt .= "**Title:** {$context['title']}\n";
        $prompt .= "**Description:** {$context['description']}\n";
        $prompt .= "**Author:** {$context['author']}\n";
        $prompt .= "**Changes:** {$context['additions']} additions, {$context['deletions']} deletions\n";
        $prompt .= "**Files Changed:** {$context['changed_files']}\n\n";

        $prompt .= "**Diff:**\n```\n{$context['diff']}\n```\n\n";

        if (!empty($context['files'])) {
            $prompt .= "**Changed Files:**\n";
            foreach (array_slice($context['files'], 0, 20) as $file) {
                $prompt .= "- {$file['filename']} (+{$file['additions']} -{$file['deletions']})\n";
            }
        }

        return $prompt;
    }

    protected function buildDiffAnalysisPrompt(array $context): string
    {
        $prompt = "Analyze this code diff for potential issues:\n\n";
        $prompt .= "**File:** {$context['file']}\n";
        $prompt .= "**Language:** {$context['language']}\n\n";
        $prompt .= "**Diff:**\n```\n{$context['diff']}\n```\n";

        return $prompt;
    }

    protected function formatReviewForGitHub(array $comments): string
    {
        $body = "## 🤖 AI Code Review\n\n";

        if (!empty($comments['overall_assessment'])) {
            $body .= "### Overall Assessment\n";
            $body .= $comments['overall_assessment'] . "\n\n";
        }

        if (!empty($comments['issues'])) {
            $body .= "### Issues Found\n\n";
            foreach ($comments['issues'] as $issue) {
                $emoji = match($issue['severity']) {
                    'Critical' => '🔴',
                    'High' => '🟠',
                    'Medium' => '🟡',
                    'Low' => '🟢',
                    default => '⚪'
                };

                $body .= "{$emoji} **{$issue['severity']} - {$issue['type']}**\n";
                if (!empty($issue['file'])) {
                    $body .= "📁 File: `{$issue['file']}`";
                    if (!empty($issue['line'])) {
                        $body .= " (Line {$issue['line']})";
                    }
                    $body .= "\n";
                }
                $body .= "\n{$issue['description']}\n";
                if (!empty($issue['suggestion'])) {
                    $body .= "\n**Suggestion:** {$issue['suggestion']}\n";
                }
                if (!empty($issue['code_example'])) {
                    $body .= "\n```\n{$issue['code_example']}\n```\n";
                }
                $body .= "\n---\n\n";
            }
        }

        if (!empty($comments['positive_points'])) {
            $body .= "### ✅ Positive Points\n\n";
            foreach ($comments['positive_points'] as $point) {
                $body .= "- {$point}\n";
            }
            $body .= "\n";
        }

        if (!empty($comments['suggestions'])) {
            $body .= "### 💡 Suggestions\n\n";
            foreach ($comments['suggestions'] as $suggestion) {
                $body .= "- {$suggestion}\n";
            }
            $body .= "\n";
        }

        if (!empty($comments['testing_recommendations'])) {
            $body .= "### 🧪 Testing Recommendations\n\n";
            foreach ($comments['testing_recommendations'] as $rec) {
                $body .= "- {$rec}\n";
            }
            $body .= "\n";
        }

        $body .= "\n---\n";
        $body .= "*Generated by Lili QA Agent - Your AI Quality Assurance Assistant*";

        return $body;
    }
}
