@extends('dashboard.layout')

@section('title', 'QA Agent - Quality Assurance Assistant')
@section('page-title', '🔍 QA Agent - Quality Assurance Assistant')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-lg shadow-xl p-6 sm:p-8 mb-8 text-white">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold mb-3">Your AI-Powered QA Expert</h2>
                <p class="text-green-100 text-sm sm:text-base mb-4">20 years of testing experience at your fingertips</p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium">Test Plans</span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium">Bug Detection</span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium">Automated Tests</span>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium">Security Analysis</span>
                </div>
            </div>
            <div class="hidden sm:block text-6xl opacity-80">🔍</div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Test Plan Generator -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" onclick="showSection('test-plan')">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl">📋</div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Test Plan Generator</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Create comprehensive test strategies and plans</p>
            <span class="text-xs text-blue-600 font-medium">Start Planning →</span>
        </div>

        <!-- Bug Analyzer -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" onclick="showSection('bug-analyzer')">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-2xl">🐛</div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Bug Analyzer</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Detect bugs and code issues automatically</p>
            <span class="text-xs text-red-600 font-medium">Analyze Code →</span>
        </div>

        <!-- Test Generator -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" onclick="showSection('test-generator')">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-2xl">⚡</div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Test Generator</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Generate automated unit & integration tests</p>
            <span class="text-xs text-purple-600 font-medium">Generate Tests →</span>
        </div>

        <!-- Test Case Designer -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" onclick="showSection('test-cases')">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-2xl">📝</div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Test Case Designer</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Create detailed manual test scenarios</p>
            <span class="text-xs text-yellow-600 font-medium">Design Cases →</span>
        </div>

        <!-- Security Scanner -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" onclick="showSection('security-scanner')">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center text-2xl">🔒</div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Security Scanner</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Find vulnerabilities & security issues</p>
            <span class="text-xs text-orange-600 font-medium">Scan Now →</span>
        </div>

        <!-- Bug Report Writer -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" onclick="showSection('bug-report')">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center text-2xl">📄</div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Bug Report Writer</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Generate professional bug reports</p>
            <span class="text-xs text-pink-600 font-medium">Write Report →</span>
        </div>

        <!-- GitHub PR Review -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" onclick="showSection('github-review')">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-2xl">🔗</div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">GitHub PR Review</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">AI code review for pull requests</p>
            <span class="text-xs text-indigo-600 font-medium">Review PR →</span>
        </div>

        <!-- Live Automation Testing -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" onclick="showSection('live-testing')">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center text-2xl">🤖</div>
                <h3 class="ml-4 text-lg font-semibold text-gray-900">Live Testing</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Run automation tests live on websites</p>
            <span class="text-xs text-emerald-600 font-medium">Start Testing →</span>
        </div>
    </div>

    <!-- Working Sections (Initially Hidden) -->
    <div id="working-area" class="hidden">
        <!-- Test Plan Section -->
        <div id="test-plan-section" class="section-content hidden">
            <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">📋 Test Plan Generator</h3>
                    <button onclick="hideSection()" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                
                <form id="test-plan-form" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Name *</label>
                            <input type="text" name="project_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="E-commerce Website">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Type *</label>
                            <select name="project_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Type...</option>
                                <option value="Web Application">Web Application</option>
                                <option value="Mobile App">Mobile App</option>
                                <option value="API">API/Backend</option>
                                <option value="Desktop Application">Desktop Application</option>
                                <option value="Microservices">Microservices</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Platform *</label>
                            <input type="text" name="platform" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Web, iOS, Android">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Timeline *</label>
                            <input type="text" name="timeline" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="4 weeks">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project Description *</label>
                        <textarea name="description" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Describe what the project does..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Requirements & Features *</label>
                        <textarea name="requirements" rows="6" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="List key features, user stories, and requirements..."></textarea>
                    </div>
                    
                    <button type="submit" id="generate-test-plan-btn" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 font-medium">
                        Generate Test Plan
                    </button>
                </form>
                
                <div id="test-plan-result" class="hidden mt-8"></div>
            </div>
        </div>

        <!-- Bug Analyzer Section -->
        <div id="bug-analyzer-section" class="section-content hidden">
            <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">🐛 Bug Analyzer</h3>
                    <button onclick="hideSection()" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                
                <form id="bug-analyzer-form" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Programming Language *</label>
                            <select name="language" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                <option value="">Select Language...</option>
                                <option value="PHP">PHP</option>
                                <option value="JavaScript">JavaScript</option>
                                <option value="Python">Python</option>
                                <option value="Java">Java</option>
                                <option value="C#">C#</option>
                                <option value="TypeScript">TypeScript</option>
                                <option value="Go">Go</option>
                                <option value="Ruby">Ruby</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Component/Module *</label>
                            <input type="text" name="component" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="User Authentication">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Component Description *</label>
                        <textarea name="description" rows="2" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="What does this code do?"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Code to Analyze *</label>
                        <textarea name="code" rows="12" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 font-mono text-sm" placeholder="Paste your code here..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Requirements/Context (Optional)</label>
                        <textarea name="requirements" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="Add any context that helps with analysis..."></textarea>
                    </div>
                    
                    <button type="submit" id="analyze-bugs-btn" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:from-red-700 hover:to-pink-700 font-medium">
                        Analyze for Bugs
                    </button>
                </form>
                
                <div id="bug-analyzer-result" class="hidden mt-8"></div>
            </div>
        </div>

        <!-- Other sections will be similar... -->
        <!-- I'll create the remaining sections in the next file -->
    </div>
    
    @include('qa-agent.partials.sections')
</div>

<script>
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.section-content').forEach(section => {
        section.classList.add('hidden');
    });
    
    // Show working area
    document.getElementById('working-area').classList.remove('hidden');
    
    // Show selected section
    document.getElementById(sectionName + '-section').classList.remove('hidden');
    
    // Scroll to section
    document.getElementById('working-area').scrollIntoView({ behavior: 'smooth' });
}

function hideSection() {
    document.getElementById('working-area').classList.add('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Test Plan Form Handler
document.getElementById('test-plan-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('generate-test-plan-btn');
    const resultDiv = document.getElementById('test-plan-result');
    
    btn.disabled = true;
    btn.innerHTML = 'Generating...';
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("qa-agent.generate-test-plan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayTestPlan(result.test_plan);
            resultDiv.classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to generate test plan'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Generate Test Plan';
    }
});

// Bug Analyzer Form Handler
document.getElementById('bug-analyzer-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('analyze-bugs-btn');
    const resultDiv = document.getElementById('bug-analyzer-result');
    
    btn.disabled = true;
    btn.innerHTML = 'Analyzing...';
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("qa-agent.analyze-bugs") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayBugAnalysis(result.analysis);
            resultDiv.classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to analyze code'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Analyze for Bugs';
    }
});

let currentTestPlan = null;

function displayTestPlan(plan) {
    currentTestPlan = plan; // Store for download
    const div = document.getElementById('test-plan-result');
    
    let html = '<div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">';
    html += '<h4 class="text-xl font-bold text-gray-900 mb-4">📋 Generated Test Plan</h4>';
    
    // Executive Summary
    if (plan.executive_summary) {
        html += '<div class="mb-6"><h5 class="font-semibold text-gray-900 mb-2">Executive Summary</h5>';
        html += `<p class="text-gray-700">${plan.executive_summary}</p></div>`;
    }
    
    // Test Objectives
    if (plan.test_objectives) {
        html += '<div class="mb-6"><h5 class="font-semibold text-gray-900 mb-2">Test Objectives</h5>';
        html += `<p class="text-gray-700">${plan.test_objectives}</p></div>`;
    }
    
    // Add more sections...
    html += '<button onclick="downloadTestPlan()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Download Test Plan</button>';
    html += '</div>';
    
    div.innerHTML = html;
}

function downloadTestPlan() {
    if (!currentTestPlan) return;
    const plan = JSON.stringify(currentTestPlan, null, 2);
    const blob = new Blob([plan], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `test_plan_${Date.now()}.json`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

function displayBugAnalysis(analysis) {
    const div = document.getElementById('bug-analyzer-result');
    
    let html = '<div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-lg p-6 border border-red-200">';
    html += '<h4 class="text-xl font-bold text-gray-900 mb-4">🐛 Bug Analysis Results</h4>';
    
    if (analysis.bugs_found && analysis.bugs_found.length > 0) {
        html += '<div class="space-y-4">';
        analysis.bugs_found.forEach((bug, index) => {
            const severityColor = bug.severity === 'Critical' ? 'red' : bug.severity === 'High' ? 'orange' : 'yellow';
            html += `<div class="bg-white rounded-lg p-4 border-l-4 border-${severityColor}-500">`;
            html += `<div class="flex items-start justify-between">`;
            html += `<h6 class="font-semibold text-gray-900">${index + 1}. ${bug.description || 'Bug Found'}</h6>`;
            html += `<span class="px-2 py-1 bg-${severityColor}-100 text-${severityColor}-800 text-xs font-medium rounded">${bug.severity}</span>`;
            html += `</div>`;
            if (bug.location) html += `<p class="text-sm text-gray-600 mt-2">Location: ${bug.location}</p>`;
            if (bug.impact) html += `<p class="text-sm text-gray-700 mt-2">${bug.impact}</p>`;
            html += `</div>`;
        });
        html += '</div>';
    } else {
        html += '<p class="text-green-600 font-medium">✓ No critical bugs detected!</p>';
    }
    
    html += '</div>';
    div.innerHTML = html;
}

function downloadTestPlan() {
    // Implementation for downloading test plan
    alert('Test plan download feature coming soon!');
}
</script>
@endsection
