<!-- Test Generator Section -->
<div id="test-generator-section" class="section-content hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">⚡ Automated Test Generator</h3>
            <button onclick="hideSection()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        
        <form id="test-generator-form" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Framework *</label>
                    <select name="framework" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Select Framework...</option>
                        <option value="PHPUnit">PHPUnit (PHP)</option>
                        <option value="Jest">Jest (JavaScript)</option>
                        <option value="Cypress">Cypress (E2E)</option>
                        <option value="Playwright">Playwright (E2E)</option>
                        <option value="PyTest">PyTest (Python)</option>
                        <option value="JUnit">JUnit (Java)</option>
                        <option value="NUnit">NUnit (C#)</option>
                        <option value="RSpec">RSpec (Ruby)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Type *</label>
                    <select name="test_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Select Type...</option>
                        <option value="Unit">Unit Tests</option>
                        <option value="Integration">Integration Tests</option>
                        <option value="E2E">End-to-End Tests</option>
                        <option value="API">API Tests</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Component/Function Name *</label>
                <input type="text" name="component_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="UserAuthentication">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Component Description *</label>
                <textarea name="description" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="What does this component do?"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Source Code (Optional)</label>
                <textarea name="code" rows="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 font-mono text-sm" placeholder="Paste the code to test..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Test Requirements (Optional)</label>
                <textarea name="requirements" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="What scenarios should be tested?"></textarea>
            </div>
            
            <button type="submit" id="generate-tests-btn" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 font-medium">
                Generate Tests
            </button>
        </form>
        
        <div id="test-generator-result" class="hidden mt-8"></div>
    </div>
</div>

<!-- Test Cases Section -->
<div id="test-cases-section" class="section-content hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">📝 Test Case Designer</h3>
            <button onclick="hideSection()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        
        <form id="test-cases-form" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Feature Name *</label>
                <input type="text" name="feature_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500" placeholder="User Login">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Feature Description *</label>
                <textarea name="feature_description" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500" placeholder="Describe the feature..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User Story *</label>
                <textarea name="user_story" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500" placeholder="As a [user], I want to [action], so that [benefit]"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Acceptance Criteria (Optional)</label>
                <textarea name="acceptance_criteria" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500" placeholder="List acceptance criteria..."></textarea>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Type</label>
                    <select name="test_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="Functional">Functional</option>
                        <option value="Regression">Regression</option>
                        <option value="Smoke">Smoke</option>
                        <option value="Integration">Integration</option>
                        <option value="UAT">User Acceptance</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" id="generate-test-cases-btn" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-lg hover:from-yellow-700 hover:to-orange-700 font-medium">
                Generate Test Cases
            </button>
        </form>
        
        <div id="test-cases-result" class="hidden mt-8"></div>
    </div>
</div>

<!-- Security Scanner Section -->
<div id="security-scanner-section" class="section-content hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">🔒 Security Vulnerability Scanner</h3>
            <button onclick="hideSection()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        
        <form id="security-scanner-form" class="space-y-6">
            @csrf
            
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6">
                <div class="flex">
                    <div class="text-2xl mr-3">⚠️</div>
                    <div>
                        <h4 class="font-semibold text-orange-900 mb-1">OWASP Top 10 Analysis</h4>
                        <p class="text-sm text-orange-700">This scanner checks for common vulnerabilities including SQL injection, XSS, CSRF, and authentication issues.</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application Type *</label>
                    <select name="app_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Select Type...</option>
                        <option value="Web Application">Web Application</option>
                        <option value="API">API/Backend</option>
                        <option value="Mobile App">Mobile App Backend</option>
                        <option value="SPA">Single Page App</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Programming Language *</label>
                    <select name="language" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Select Language...</option>
                        <option value="PHP">PHP</option>
                        <option value="JavaScript">JavaScript/Node.js</option>
                        <option value="Python">Python</option>
                        <option value="Java">Java</option>
                        <option value="C#">C#</option>
                        <option value="Ruby">Ruby</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Component/Module to Scan *</label>
                <input type="text" name="component" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Authentication Module">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Code to Scan *</label>
                <textarea name="code" rows="12" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 font-mono text-sm" placeholder="Paste the code to scan for vulnerabilities..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Context (Optional)</label>
                <textarea name="context" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Framework, dependencies, authentication method..."></textarea>
            </div>
            
            <button type="submit" id="scan-security-btn" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg hover:from-orange-700 hover:to-red-700 font-medium">
                Scan for Vulnerabilities
            </button>
        </form>
        
        <div id="security-scanner-result" class="hidden mt-8"></div>
    </div>
</div>

<!-- Bug Report Section -->
<div id="bug-report-section" class="section-content hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">📄 Professional Bug Report Writer</h3>
            <button onclick="hideSection()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        
        <form id="bug-report-form" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bug Title *</label>
                <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="Login button not responding on mobile">
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Severity *</label>
                    <select name="severity" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                        <option value="">Select Severity...</option>
                        <option value="Critical">Critical</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                    <select name="priority" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                        <option value="">Select Priority...</option>
                        <option value="Urgent">Urgent</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Environment *</label>
                <input type="text" name="environment" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="Production, iOS 16.2, iPhone 13">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Steps to Reproduce *</label>
                <textarea name="steps" rows="6" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="1. Navigate to login page&#10;2. Enter credentials&#10;3. Tap login button&#10;4. Observe..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expected Behavior *</label>
                <textarea name="expected" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="What should happen?"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Actual Behavior *</label>
                <textarea name="actual" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="What actually happens?"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Information (Optional)</label>
                <textarea name="additional_info" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="Error messages, logs, screenshots description..."></textarea>
            </div>
            
            <button type="submit" id="generate-bug-report-btn" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-pink-600 to-rose-600 text-white rounded-lg hover:from-pink-700 hover:to-rose-700 font-medium">
                Generate Bug Report
            </button>
        </form>
        
        <div id="bug-report-result" class="hidden mt-8"></div>
    </div>
</div>

<!-- GitHub PR Review Section -->
<div id="github-review-section" class="section-content hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">🔗 GitHub Pull Request Review</h3>
            <button onclick="hideSection()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        
        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6">
            <div class="flex">
                <div class="text-2xl mr-3">💡</div>
                <div>
                    <h4 class="font-semibold text-indigo-900 mb-1">Automated Code Review</h4>
                    <p class="text-sm text-indigo-700">AI analyzes your pull requests for bugs, security issues, code quality, and best practices. Optionally posts review comments directly to GitHub!</p>
                </div>
            </div>
        </div>
        
        <form id="github-review-form" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">GitHub Personal Access Token *</label>
                <input type="password" name="github_token" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="ghp_xxxxxxxxxxxx">
                <p class="text-xs text-gray-500 mt-1">Create token at: github.com/settings/tokens (needs repo scope)</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Repository Owner *</label>
                    <input type="text" name="owner" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="octocat">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Repository Name *</label>
                    <input type="text" name="repo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="my-project">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pull Request Number *</label>
                <input type="number" name="pr_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="123">
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="post_comments" id="post_comments" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="post_comments" class="ml-2 text-sm text-gray-700">Post review comments to GitHub automatically</label>
            </div>
            
            <button type="submit" id="review-pr-btn" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-medium">
                Review Pull Request
            </button>
        </form>
        
        <div id="github-review-result" class="hidden mt-8"></div>
    </div>
</div>

<!-- Live Automation Testing Section -->
<div id="live-testing-section" class="section-content hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">🤖 Live Automation Testing</h3>
            <button onclick="hideSection()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6">
            <div class="flex">
                <div class="text-2xl mr-3">🚀</div>
                <div>
                    <h4 class="font-semibold text-emerald-900 mb-1">Real-Time Website Testing</h4>
                    <p class="text-sm text-emerald-700">Watch as AI performs automated tests on any website in real-time. Choose simulation mode (AI predicts) or real execution (requires Playwright/Puppeteer).</p>
                </div>
            </div>
        </div>
        
        <form id="live-testing-form" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Website URL *</label>
                <input type="url" name="url" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="https://example.com">
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website Type *</label>
                    <select name="website_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select Type...</option>
                        <option value="E-commerce">E-commerce</option>
                        <option value="SaaS">SaaS Application</option>
                        <option value="Blog">Blog/Content Site</option>
                        <option value="Social Media">Social Media</option>
                        <option value="Dashboard">Dashboard/Admin</option>
                        <option value="Landing Page">Landing Page</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Framework *</label>
                    <select name="framework" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="simulate">AI Simulation (Always Works)</option>
                        <option value="playwright">Playwright (Requires Installation)</option>
                        <option value="puppeteer">Puppeteer (Requires Installation)</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Test Scenario *</label>
                <textarea name="test_scenario" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="User logs in, navigates to dashboard, creates a new item..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Test Steps *</label>
                <div id="test-steps-container" class="space-y-3">
                    <div class="test-step flex gap-2">
                        <input type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="Step description" data-field="description">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg" data-field="action">
                            <option value="navigate">Navigate</option>
                            <option value="click">Click</option>
                            <option value="type">Type</option>
                            <option value="wait">Wait</option>
                            <option value="assert">Assert</option>
                        </select>
                        <input type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="Selector (optional)" data-field="selector">
                        <input type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="Value (optional)" data-field="value">
                    </div>
                </div>
                <button type="button" onclick="addTestStep()" class="mt-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 text-sm">+ Add Step</button>
            </div>
            
            <button type="submit" id="execute-live-test-btn" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 font-medium">
                Execute Live Test
            </button>
        </form>
        
        <div id="live-testing-result" class="hidden mt-8"></div>
    </div>
</div>

<script>
// GitHub PR Review Form Handler
document.getElementById('github-review-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('review-pr-btn');
    const resultDiv = document.getElementById('github-review-result');
    
    btn.disabled = true;
    btn.innerHTML = 'Reviewing PR...';
    
    const formData = new FormData(e.target);
    const data = {
        github_token: formData.get('github_token'),
        owner: formData.get('owner'),
        repo: formData.get('repo'),
        pr_number: parseInt(formData.get('pr_number')),
        post_comments: formData.get('post_comments') === 'on'
    };
    
    try {
        const response = await fetch('{{ route("qa-agent.review-pr") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayGitHubReview(result);
            resultDiv.classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to review pull request'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Review Pull Request';
    }
});

// Live Testing Form Handler
document.getElementById('live-testing-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('execute-live-test-btn');
    const resultDiv = document.getElementById('live-testing-result');
    
    btn.disabled = true;
    btn.innerHTML = 'Executing Test...';
    
    const formData = new FormData(e.target);
    const testSteps = collectTestSteps();
    
    const data = {
        url: formData.get('url'),
        website_type: formData.get('website_type'),
        test_scenario: formData.get('test_scenario'),
        test_steps: testSteps,
        framework: formData.get('framework')
    };
    
    try {
        const response = await fetch('{{ route("qa-agent.execute-live-test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayLiveTestResults(result);
            resultDiv.classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to execute test'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Execute Live Test';
    }
});

function addTestStep() {
    const container = document.getElementById('test-steps-container');
    const stepHtml = `
        <div class="test-step flex gap-2">
            <input type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="Step description" data-field="description">
            <select class="px-4 py-2 border border-gray-300 rounded-lg" data-field="action">
                <option value="navigate">Navigate</option>
                <option value="click">Click</option>
                <option value="type">Type</option>
                <option value="wait">Wait</option>
                <option value="assert">Assert</option>
            </select>
            <input type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="Selector (optional)" data-field="selector">
            <input type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="Value (optional)" data-field="value">
            <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">✕</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', stepHtml);
}

function collectTestSteps() {
    const steps = [];
    document.querySelectorAll('.test-step').forEach(stepEl => {
        const description = stepEl.querySelector('[data-field="description"]').value;
        const action = stepEl.querySelector('[data-field="action"]').value;
        const selector = stepEl.querySelector('[data-field="selector"]').value;
        const value = stepEl.querySelector('[data-field="value"]').value;
        
        if (description) {
            steps.push({ description, action, selector, value });
        }
    });
    return steps;
}

function displayGitHubReview(result) {
    const div = document.getElementById('github-review-result');
    const review = result.review.review;
    const prData = result.pr_data;
    
    let html = '<div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-6 border border-indigo-200">';
    html += `<h4 class="text-xl font-bold text-gray-900 mb-4">🔗 Code Review Results</h4>`;
    
    // PR Info
    html += '<div class="bg-white rounded-lg p-4 mb-4 border">';
    html += `<h5 class="font-semibold text-gray-900 mb-2">${prData.title}</h5>`;
    html += `<p class="text-sm text-gray-600">by ${prData.author} • ${prData.additions} additions, ${prData.deletions} deletions</p>`;
    if (result.comments_posted) {
        html += `<p class="text-sm text-green-600 mt-2">✅ Review comments posted to GitHub!</p>`;
    }
    html += '</div>';
    
    // Overall Assessment
    if (review.overall_assessment) {
        html += '<div class="bg-white rounded-lg p-4 mb-4">';
        html += '<h5 class="font-semibold text-gray-900 mb-2">Overall Assessment</h5>';
        html += `<p class="text-gray-700">${review.overall_assessment}</p>`;
        html += `<p class="mt-2"><span class="px-3 py-1 rounded-full text-sm font-medium ${getApprovalClass(review.approval_status)}">${review.approval_status}</span></p>`;
        html += '</div>';
    }
    
    // Issues
    if (review.issues && review.issues.length > 0) {
        html += '<div class="bg-white rounded-lg p-4 mb-4">';
        html += '<h5 class="font-semibold text-gray-900 mb-3">Issues Found</h5>';
        html += '<div class="space-y-3">';
        review.issues.forEach((issue, index) => {
            const severityColor = getSeverityColor(issue.severity);
            html += `<div class="border-l-4 border-${severityColor}-500 pl-4 py-2">`;
            html += `<div class="flex items-start justify-between">`;
            html += `<h6 class="font-medium text-gray-900">${issue.type}</h6>`;
            html += `<span class="px-2 py-1 bg-${severityColor}-100 text-${severityColor}-800 text-xs rounded">${issue.severity}</span>`;
            html += `</div>`;
            html += `<p class="text-sm text-gray-700 mt-1">${issue.description}</p>`;
            if (issue.file) html += `<p class="text-xs text-gray-500 mt-1">📁 ${issue.file}${issue.line ? ':' + issue.line : ''}</p>`;
            if (issue.suggestion) html += `<p class="text-sm text-green-700 mt-2">💡 ${issue.suggestion}</p>`;
            html += `</div>`;
        });
        html += '</div></div>';
    }
    
    // Positive Points
    if (review.positive_points && review.positive_points.length > 0) {
        html += '<div class="bg-green-50 rounded-lg p-4 mb-4">';
        html += '<h5 class="font-semibold text-gray-900 mb-2">✅ Positive Points</h5>';
        html += '<ul class="list-disc list-inside space-y-1">';
        review.positive_points.forEach(point => {
            html += `<li class="text-sm text-gray-700">${point}</li>`;
        });
        html += '</ul></div>';
    }
    
    html += `<a href="${prData.pr_url}" target="_blank" class="inline-block mt-4 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">View PR on GitHub →</a>`;
    html += '</div>';
    
    div.innerHTML = html;
}

function displayLiveTestResults(result) {
    const div = document.getElementById('live-testing-result');
    const results = result.results;
    
    let html = '<div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-6 border border-emerald-200">';
    html += '<h4 class="text-xl font-bold text-gray-900 mb-4">🤖 Live Test Execution Results</h4>';
    
    if (results.note) {
        html += `<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">`;
        html += `<p class="text-sm text-blue-700">${results.note}</p>`;
        html += `</div>`;
    }
    
    if (results.requires_setup) {
        html += `<div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-4">`;
        html += `<p class="text-sm text-orange-700">${results.error}</p>`;
        html += `</div>`;
    }
    
    // Display simulation or real results
    if (results.simulation) {
        const sim = results.simulation.simulation;
        html += '<div class="bg-white rounded-lg p-4 mb-4">';
        html += `<h5 class="font-semibold text-gray-900 mb-3">${sim.test_name || 'Test Execution'}</h5>`;
        
        if (sim.steps_executed) {
            html += '<div class="space-y-2">';
            sim.steps_executed.forEach((step, i) => {
                const statusColor = step.status === 'Passed' ? 'green' : step.status === 'Failed' ? 'red' : 'gray';
                html += `<div class="flex items-start gap-3 p-3 bg-gray-50 rounded">`;
                html += `<span class="text-2xl">${step.status === 'Passed' ? '✅' : step.status === 'Failed' ? '❌' : '⏭️'}</span>`;
                html += `<div class="flex-1">`;
                html += `<p class="font-medium text-gray-900">Step ${step.step_number}: ${step.description}</p>`;
                html += `<p class="text-sm text-gray-600 mt-1">${step.screenshot || ''}</p>`;
                if (step.error) html += `<p class="text-sm text-red-600 mt-1">Error: ${step.error}</p>`;
                html += `<p class="text-xs text-gray-500 mt-1">${step.execution_time_ms}ms</p>`;
                html += `</div></div>`;
            });
            html += '</div>';
        }
        
        if (sim.summary) {
            html += `<div class="mt-4 grid grid-cols-4 gap-4 text-center">`;
            html += `<div class="bg-green-100 p-3 rounded"><p class="text-2xl font-bold text-green-700">${sim.summary.passed}</p><p class="text-xs text-green-600">Passed</p></div>`;
            html += `<div class="bg-red-100 p-3 rounded"><p class="text-2xl font-bold text-red-700">${sim.summary.failed}</p><p class="text-xs text-red-600">Failed</p></div>`;
            html += `<div class="bg-gray-100 p-3 rounded"><p class="text-2xl font-bold text-gray-700">${sim.summary.skipped}</p><p class="text-xs text-gray-600">Skipped</p></div>`;
            html += `<div class="bg-blue-100 p-3 rounded"><p class="text-2xl font-bold text-blue-700">${sim.summary.total_time_ms}ms</p><p class="text-xs text-blue-600">Total Time</p></div>`;
            html += `</div>`;
        }
        
        html += '</div>';
    } else if (results.results) {
        html += '<div class="bg-white rounded-lg p-4">';
        html += '<pre class="text-sm overflow-x-auto">' + JSON.stringify(results.results, null, 2) + '</pre>';
        html += '</div>';
    }
    
    html += '</div>';
    div.innerHTML = html;
}

function getApprovalClass(status) {
    if (status === 'APPROVE') return 'bg-green-100 text-green-800';
    if (status === 'REQUEST_CHANGES') return 'bg-red-100 text-red-800';
    return 'bg-gray-100 text-gray-800';
}

function getSeverityColor(severity) {
    if (severity === 'Critical') return 'red';
    if (severity === 'High') return 'orange';
    if (severity === 'Medium') return 'yellow';
    return 'green';
}
</script>
<script>
// Test Generator Form Handler
document.getElementById('test-generator-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('generate-tests-btn');
    const resultDiv = document.getElementById('test-generator-result');
    
    btn.disabled = true;
    btn.innerHTML = 'Generating Tests...';
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("qa-agent.generate-tests") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayGeneratedTests(result.tests);
            resultDiv.classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to generate tests'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Generate Tests';
    }
});

// Test Cases Form Handler
document.getElementById('test-cases-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('generate-test-cases-btn');
    const resultDiv = document.getElementById('test-cases-result');
    
    btn.disabled = true;
    btn.innerHTML = 'Generating Test Cases...';
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("qa-agent.test-cases") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayTestCases(result.test_cases);
            resultDiv.classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to generate test cases'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Generate Test Cases';
    }
});

// Security Scanner Form Handler
document.getElementById('security-scanner-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('scan-security-btn');
    const resultDiv = document.getElementById('security-scanner-result');
    
    btn.disabled = true;
    btn.innerHTML = 'Scanning...';
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("qa-agent.security-analysis") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displaySecurityAnalysis(result.analysis);
            resultDiv.classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to scan for vulnerabilities'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Scan for Vulnerabilities';
    }
});

// Bug Report Form Handler
document.getElementById('bug-report-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('generate-bug-report-btn');
    const resultDiv = document.getElementById('bug-report-result');
    
    btn.disabled = true;
    btn.innerHTML = 'Generating Report...';
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("qa-agent.bug-report") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayBugReport(result.report);
            resultDiv.classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to generate bug report'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Generate Bug Report';
    }
});

function displayGeneratedTests(tests) {
    currentTests = tests; // Store for download/copy
    const div = document.getElementById('test-generator-result');
    
    let html = '<div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-6 border border-purple-200">';
    html += '<h4 class="text-xl font-bold text-gray-900 mb-4">⚡ Generated Tests</h4>';
    
    if (tests.code) {
        html += '<div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">';
        html += `<pre class="text-green-400 text-sm font-mono">${escapeHtml(tests.code)}</pre>`;
        html += '</div>';
    }
    
    html += '<div class="mt-4 flex gap-2">';
    html += '<button onclick="copyTests()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Copy Code</button>';
    html += '<button onclick="downloadTests()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Download File</button>';
    html += '</div>';
    html += '</div>';
    
    div.innerHTML = html;
}

function displayTestCases(testCases) {
    currentTestCases = testCases; // Store for export
    const div = document.getElementById('test-cases-result');
    
    let html = '<div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-6 border border-yellow-200">';
    html += '<h4 class="text-xl font-bold text-gray-900 mb-4">📝 Test Cases</h4>';
    
    if (testCases.cases && testCases.cases.length > 0) {
        html += '<div class="space-y-4">';
        testCases.cases.forEach((tc, index) => {
            html += `<div class="bg-white rounded-lg p-4 border">`;
            html += `<h5 class="font-semibold text-gray-900 mb-2">Test Case ${index + 1}: ${tc.title || 'Test'}</h5>`;
            if (tc.steps) html += `<div class="text-sm text-gray-700 mb-2"><strong>Steps:</strong><br>${tc.steps.replace(/\n/g, '<br>')}</div>`;
            if (tc.expected) html += `<div class="text-sm text-gray-700"><strong>Expected:</strong> ${tc.expected}</div>`;
            html += `</div>`;
        });
        html += '</div>';
    }
    
    html += '<button onclick="exportTestCases()" class="mt-4 px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Export Test Cases</button>';
    html += '</div>';
    
    div.innerHTML = html;
}

function displaySecurityAnalysis(analysis) {
    const div = document.getElementById('security-scanner-result');
    
    let html = '<div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-6 border border-orange-200">';
    html += '<h4 class="text-xl font-bold text-gray-900 mb-4">🔒 Security Analysis Results</h4>';
    
    if (analysis.vulnerabilities && analysis.vulnerabilities.length > 0) {
        html += '<div class="space-y-4">';
        analysis.vulnerabilities.forEach((vuln, index) => {
            const severityColor = vuln.severity === 'Critical' ? 'red' : vuln.severity === 'High' ? 'orange' : 'yellow';
            html += `<div class="bg-white rounded-lg p-4 border-l-4 border-${severityColor}-500">`;
            html += `<div class="flex items-start justify-between mb-2">`;
            html += `<h6 class="font-semibold text-gray-900">${index + 1}. ${vuln.type || 'Vulnerability'}</h6>`;
            html += `<span class="px-2 py-1 bg-${severityColor}-100 text-${severityColor}-800 text-xs font-medium rounded">${vuln.severity}</span>`;
            html += `</div>`;
            if (vuln.description) html += `<p class="text-sm text-gray-700 mb-2">${vuln.description}</p>`;
            if (vuln.remediation) html += `<div class="text-sm text-green-700 bg-green-50 p-2 rounded"><strong>Fix:</strong> ${vuln.remediation}</div>`;
            html += `</div>`;
        });
        html += '</div>';
    } else {
        html += '<p class="text-green-600 font-medium">✓ No major vulnerabilities detected!</p>';
    }
    
    html += '</div>';
    div.innerHTML = html;
}

function displayBugReport(report) {
    currentBugReport = report; // Store for copy/export
    const div = document.getElementById('bug-report-result');
    
    let html = '<div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg p-6 border border-pink-200">';
    html += '<h4 class="text-xl font-bold text-gray-900 mb-4">📄 Professional Bug Report</h4>';
    html += '<div class="bg-white rounded-lg p-6 border">';
    
    if (report.formatted_report) {
        html += `<div class="prose max-w-none">${report.formatted_report.replace(/\n/g, '<br>')}</div>`;
    }
    
    html += '</div>';
    html += '<div class="mt-4 flex gap-2">';
    html += '<button onclick="copyBugReport()" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">Copy Report</button>';
    html += '<button onclick="exportBugReport()" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">Export as Markdown</button>';
    html += '</div>';
    html += '</div>';
    
    div.innerHTML = html;
}

function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, m => map[m]);
}

let currentTestPlan = null;
let currentTests = null;
let currentTestCases = null;
let currentBugReport = null;

function copyTests() {
    if (!currentTests) return;
    const code = currentTests.code || JSON.stringify(currentTests, null, 2);
    navigator.clipboard.writeText(code).then(() => {
        alert('✅ Tests copied to clipboard!');
    }).catch(() => {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = code;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('✅ Tests copied to clipboard!');
    });
}

function downloadTests() {
    if (!currentTests) return;
    const code = currentTests.code || JSON.stringify(currentTests, null, 2);
    const framework = currentTests.framework || 'test';
    const extension = framework.toLowerCase().includes('php') ? '.php' : '.js';
    const blob = new Blob([code], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `generated_test_${Date.now()}${extension}`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

function exportTestCases() {
    if (!currentTestCases) return;
    let markdown = '# Test Cases\n\n';
    if (currentTestCases.cases) {
        currentTestCases.cases.forEach((tc, i) => {
            markdown += `## Test Case ${i + 1}: ${tc.title || 'Test'}\n\n`;
            if (tc.steps) markdown += `**Steps:**\n${tc.steps}\n\n`;
            if (tc.expected) markdown += `**Expected Result:**\n${tc.expected}\n\n`;
            markdown += '---\n\n';
        });
    } else {
        markdown = JSON.stringify(currentTestCases, null, 2);
    }
    const blob = new Blob([markdown], { type: 'text/markdown' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `test_cases_${Date.now()}.md`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

function copyBugReport() {
    if (!currentBugReport) return;
    const report = currentBugReport.formatted_report || JSON.stringify(currentBugReport, null, 2);
    navigator.clipboard.writeText(report).then(() => {
        alert('✅ Bug report copied to clipboard!');
    }).catch(() => {
        const textarea = document.createElement('textarea');
        textarea.value = report;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('✅ Bug report copied to clipboard!');
    });
}

function exportBugReport() {
    if (!currentBugReport) return;
    const report = currentBugReport.formatted_report || JSON.stringify(currentBugReport, null, 2);
    const blob = new Blob([report], { type: 'text/markdown' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `bug_report_${Date.now()}.md`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}
</script>
