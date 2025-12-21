@extends('layouts.app')

@section('title', 'Market Analysis')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Market Analysis</h1>
                    <p class="text-gray-600">Analyze competitors, discover trends, and find content opportunities</p>
                </div>
                <a href="{{ route('marketing.studio.index') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 border border-gray-300 transition">
                    ← Back to Studio
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <button onclick="showCompetitorModal()" class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition text-left border-2 border-transparent hover:border-purple-500">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Analyze Competitor</h3>
                <p class="text-gray-600 text-sm">Add a competitor's Facebook page to analyze their strategy</p>
            </button>

            <button onclick="generateSWOT()" class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition text-left border-2 border-transparent hover:border-blue-500">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Generate SWOT</h3>
                <p class="text-gray-600 text-sm">AI-powered SWOT analysis for your brand</p>
            </button>

            <button onclick="getOpportunities()" class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition text-left border-2 border-transparent hover:border-green-500">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Find Opportunities</h3>
                <p class="text-gray-600 text-sm">Discover content gaps and growth opportunities</p>
            </button>
        </div>

        <!-- Industry Benchmarks -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Industry Benchmarks - {{ ucfirst($industry) }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $benchmarks['avg_engagement_rate'] }}%</div>
                    <div class="text-sm text-gray-600 mt-1">Avg Engagement</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $benchmarks['avg_posts_per_week'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Posts/Week</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $benchmarks['avg_followers_growth'] }}%</div>
                    <div class="text-sm text-gray-600 mt-1">Growth Rate</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600">{{ $benchmarks['avg_response_time'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Response Time</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-semibold text-gray-700">Best Types:</div>
                    <div class="text-xs text-gray-600 mt-1">{{ implode(', ', $benchmarks['best_content_types']) }}</div>
                </div>
            </div>
        </div>

        <!-- Competitors Section -->
        @if($competitors->count() > 0)
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Competitor Analysis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($competitors as $competitor)
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900">{{ $competitor->competitor_name }}</h3>
                            <p class="text-sm text-gray-500">Analyzed {{ $competitor->last_analyzed_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="refreshCompetitor({{ $competitor->id }})" class="text-blue-600 hover:text-blue-800" title="Refresh">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                            <button onclick="deleteCompetitor({{ $competitor->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Page Data -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="text-center p-3 bg-gray-50 rounded">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($competitor->page_data['followers_count'] ?? 0) }}</div>
                            <div class="text-xs text-gray-600">Followers</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($competitor->engagement_metrics['avg_engagement_rate'] ?? 0, 2) }}%</div>
                            <div class="text-xs text-gray-600">Engagement</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded">
                            <div class="text-2xl font-bold text-blue-600">{{ $competitor->engagement_metrics['posts_analyzed'] ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Posts</div>
                        </div>
                    </div>

                    <!-- Engagement Metrics -->
                    <div class="mb-4">
                        <h4 class="font-semibold text-sm text-gray-700 mb-2">Engagement Breakdown</h4>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div>
                                <span class="text-gray-600">Likes:</span>
                                <span class="font-semibold">{{ number_format($competitor->engagement_metrics['avg_likes'] ?? 0) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Comments:</span>
                                <span class="font-semibold">{{ number_format($competitor->engagement_metrics['avg_comments'] ?? 0) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Shares:</span>
                                <span class="font-semibold">{{ number_format($competitor->engagement_metrics['avg_shares'] ?? 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Posting Patterns -->
                    @if(isset($competitor->posting_patterns['best_posting_hours']))
                    <div class="mb-4">
                        <h4 class="font-semibold text-sm text-gray-700 mb-2">Best Posting Times</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_slice($competitor->posting_patterns['best_posting_hours'], 0, 3) as $hour => $count)
                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs">{{ $hour }}:00 ({{ $count }} posts)</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Content Strategy -->
                    @if(isset($competitor->content_strategy['content_types']))
                    <div>
                        <h4 class="font-semibold text-sm text-gray-700 mb-2">Content Mix</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($competitor->content_strategy['content_types'] as $type => $count)
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">{{ ucfirst($type) }}: {{ $count }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- SWOT Analysis -->
        @if($swot)
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">SWOT Analysis</h2>
                <span class="text-sm text-gray-500">Generated {{ $swot->created_at->diffForHumans() }}</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Strengths -->
                <div class="border-2 border-green-200 rounded-lg p-4 bg-green-50">
                    <h3 class="font-semibold text-lg text-green-800 mb-3">Strengths</h3>
                    <ul class="space-y-2">
                        @foreach($swot->ai_analysis['strengths'] ?? [] as $strength)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $strength }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Weaknesses -->
                <div class="border-2 border-red-200 rounded-lg p-4 bg-red-50">
                    <h3 class="font-semibold text-lg text-red-800 mb-3">Weaknesses</h3>
                    <ul class="space-y-2">
                        @foreach($swot->ai_analysis['weaknesses'] ?? [] as $weakness)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $weakness }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Opportunities -->
                <div class="border-2 border-blue-200 rounded-lg p-4 bg-blue-50">
                    <h3 class="font-semibold text-lg text-blue-800 mb-3">Opportunities</h3>
                    <ul class="space-y-2">
                        @foreach($swot->ai_analysis['opportunities'] ?? [] as $opportunity)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $opportunity }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Threats -->
                <div class="border-2 border-orange-200 rounded-lg p-4 bg-orange-50">
                    <h3 class="font-semibold text-lg text-orange-800 mb-3">Threats</h3>
                    <ul class="space-y-2">
                        @foreach($swot->ai_analysis['threats'] ?? [] as $threat)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $threat }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Key Insights & Actions -->
            @if(isset($swot->ai_analysis['key_insights']) || isset($swot->ai_analysis['action_items']))
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($swot->ai_analysis['key_insights']))
                <div class="border border-purple-200 rounded-lg p-4 bg-purple-50">
                    <h3 class="font-semibold text-lg text-purple-800 mb-3">Key Insights</h3>
                    <ul class="space-y-2">
                        @foreach($swot->ai_analysis['key_insights'] as $insight)
                        <li class="text-sm text-gray-700">• {{ $insight }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(isset($swot->ai_analysis['action_items']))
                <div class="border border-indigo-200 rounded-lg p-4 bg-indigo-50">
                    <h3 class="font-semibold text-lg text-indigo-800 mb-3">Action Items</h3>
                    <ul class="space-y-2">
                        @foreach($swot->ai_analysis['action_items'] as $action)
                        <li class="text-sm text-gray-700">• {{ $action }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        <!-- Opportunities Section (Dynamic) -->
        <div id="opportunities-section" class="hidden bg-white rounded-xl shadow-md p-6"></div>
    </div>
</div>

<!-- Add Competitor Modal -->
<div id="competitorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Add Competitor</h3>
        <form id="competitorForm" onsubmit="analyzeCompetitor(event)">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Competitor Name</label>
                <input type="text" id="competitor_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., Nike">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Facebook Page URL or Username</label>
                <input type="text" id="facebook_url" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="https://facebook.com/noon or just 'noon'">
                <p class="text-xs text-gray-500 mt-1">Enter full URL or just the page username</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Follower Count (Optional)</label>
                <input type="text" id="follower_count" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., 1.1M or 1100000">
                <p class="text-xs text-gray-500 mt-1">If automatic fetch fails, enter manually (supports 1.1M, 500K, etc.)</p>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Industry (Optional)</label>
                <input type="text" id="industry" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="e.g., Fashion">
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <span id="analyzeBtn">Analyze</span>
                    <span id="analyzingBtn" class="hidden">Analyzing...</span>
                </button>
                <button type="button" onclick="hideCompetitorModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCompetitorModal() {
    document.getElementById('competitorModal').classList.remove('hidden');
}

function hideCompetitorModal() {
    document.getElementById('competitorModal').classList.add('hidden');
    document.getElementById('competitorForm').reset();
}

async function analyzeCompetitor(event) {
    event.preventDefault();
    
    const analyzeBtn = document.getElementById('analyzeBtn');
    const analyzingBtn = document.getElementById('analyzingBtn');
    analyzeBtn.classList.add('hidden');
    analyzingBtn.classList.remove('hidden');

    try {
        const payload = {
            competitor_name: document.getElementById('competitor_name').value,
            facebook_url: document.getElementById('facebook_url').value,
            industry: document.getElementById('industry').value
        };
        
        // Add manual follower count if provided
        const followerCount = document.getElementById('follower_count').value;
        if (followerCount) {
            payload.manual_follower_count = followerCount;
        }
        
        const response = await fetch('{{ route("market.analysis.competitor.analyze") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success) {
            alert('Competitor analyzed successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error analyzing competitor: ' + error.message);
    } finally {
        analyzeBtn.classList.remove('hidden');
        analyzingBtn.classList.add('hidden');
    }
}

async function generateSWOT() {
    if (!confirm('Generate AI-powered SWOT analysis? This may take a moment.')) return;

    try {
        const response = await fetch('{{ route("market.analysis.swot.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success) {
            alert('SWOT analysis generated successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error generating SWOT: ' + error.message);
    }
}

async function getOpportunities() {
    try {
        const response = await fetch('{{ route("market.analysis.opportunities") }}');
        const data = await response.json();

        if (data.success && data.opportunities && data.opportunities.length > 0) {
            displayOpportunities(data.opportunities);
        } else {
            alert(data.message || 'No opportunities found. Please set up your brand profile in Settings.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error fetching opportunities. Please check your brand settings.');
    }
}

function displayOpportunities(opportunities) {
    const section = document.getElementById('opportunities-section');
    
    let html = `
        <h2 class="text-xl font-bold text-gray-900 mb-4">Content Opportunities</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    `;

    opportunities.forEach(opp => {
        const priorityColor = opp.priority === 'high' ? 'red' : opp.priority === 'medium' ? 'yellow' : 'green';
        html += `
            <div class="border border-${priorityColor}-200 rounded-lg p-4 bg-${priorityColor}-50">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900">${opp.title}</h3>
                    <span class="px-2 py-1 bg-${priorityColor}-200 text-${priorityColor}-800 rounded text-xs uppercase">${opp.priority}</span>
                </div>
                <p class="text-sm text-gray-700 mb-2">${opp.description}</p>
                <p class="text-sm font-medium text-gray-900">→ ${opp.action}</p>
            </div>
        `;
    });

    html += '</div>';
    section.innerHTML = html;
    section.classList.remove('hidden');
}

async function deleteCompetitor(id) {
    if (!confirm('Delete this competitor analysis?')) return;

    try {
        const response = await fetch(`/marketing/studio/market-analysis/competitor/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error deleting competitor: ' + error.message);
    }
}

async function refreshCompetitor(id) {
    if (!confirm('Refresh competitor analysis? This will fetch latest data.')) return;

    try {
        const response = await fetch(`/marketing/studio/market-analysis/competitor/${id}/refresh`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success) {
            alert('Competitor analysis refreshed!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error refreshing competitor: ' + error.message);
    }
}
</script>
@endsection
