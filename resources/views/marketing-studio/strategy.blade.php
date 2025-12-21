@extends('dashboard.layout')

@section('title', 'Marketing Strategy Generator')
@section('page-title', 'AI Marketing Strategy Generator')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Usage Limit Warning -->
    @if(isset($usageSummary) && $usageSummary['has_subscription'])
        @php
            $postsUsed = $usageSummary['posts']['used'];
            $postsLimit = $usageSummary['posts']['limit'];
            $percentage = $usageSummary['posts']['percentage'];
            $isUnlimited = $usageSummary['posts']['unlimited'];
        @endphp
        
        @if(!$isUnlimited && $percentage >= 80)
            <div class="mb-6 p-4 {{ $percentage >= 100 ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }} border rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 {{ $percentage >= 100 ? 'text-red-600' : 'text-yellow-600' }} mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold {{ $percentage >= 100 ? 'text-red-900' : 'text-yellow-900' }} mb-1">
                            {{ $percentage >= 100 ? 'Monthly Limit Reached' : 'Approaching Monthly Limit' }}
                        </h4>
                        <p class="text-sm {{ $percentage >= 100 ? 'text-red-700' : 'text-yellow-700' }}">
                            You've used <strong>{{ $postsUsed }} of {{ $postsLimit }}</strong> content generations this month ({{ $percentage }}%). 
                            @if($percentage >= 100)
                                Please <a href="{{ route('dashboard.billing') }}" class="underline font-semibold">upgrade your plan</a> to continue.
                            @else
                                Consider <a href="{{ route('dashboard.billing') }}" class="underline">upgrading</a> if you need more.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif
    
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Generate Your Marketing Strategy</h2>
            <p class="text-gray-600">Let AI create a comprehensive content strategy tailored to your brand</p>
        </div>

        <form id="strategy-form" class="space-y-6">
            @csrf
            
            <!-- Hidden fields for agent configuration -->
            @if($agentConfig)
                <input type="hidden" name="agent_config[business_name]" value="{{ $agentConfig->business_name }}">
                <input type="hidden" name="agent_config[industry]" value="{{ $agentConfig->industry }}">
                <input type="hidden" name="agent_config[products_services]" value="{{ $agentConfig->products_services }}">
                <input type="hidden" name="agent_config[unique_value_proposition]" value="{{ $agentConfig->unique_value_proposition }}">
                <input type="hidden" name="agent_config[brand_tone]" value="{{ $agentConfig->brand_tone }}">
                <input type="hidden" name="agent_config[target_audience]" value="{{ json_encode($agentConfig->target_audience) }}">
                <input type="hidden" name="agent_config[pain_points]" value="{{ $agentConfig->pain_points }}">
                <input type="hidden" name="agent_config[marketing_goals]" value="{{ json_encode($agentConfig->marketing_goals) }}">
                <input type="hidden" name="agent_config[focus_keywords]" value="{{ json_encode($agentConfig->focus_keywords) }}">
                <input type="hidden" name="agent_config[topics_to_avoid]" value="{{ $agentConfig->topics_to_avoid }}">
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand Name</label>
                    <input type="text" name="brand_name" 
                           value="{{ $agentConfig->business_name ?? $brandSettings->brand_name ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Your Brand">
                    @if(!$agentConfig && (!$brandSettings || !$brandSettings->brand_name))
                        <p class="text-xs text-blue-600 mt-1">💡 Set this in <a href="{{ route('dashboard.settings') }}" class="underline">Settings</a> to auto-fill</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                    <input type="text" name="industry" 
                           value="{{ $agentConfig->industry ?? $brandSettings->industry ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Fashion, Tech, Food">
                    @if(!$agentConfig && (!$brandSettings || !$brandSettings->industry))
                        <p class="text-xs text-blue-600 mt-1">💡 Set this in <a href="{{ route('dashboard.settings') }}" class="underline">Settings</a> to auto-fill</p>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                <textarea name="target_audience" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Describe your target audience (age, interests, demographics...)">{{ $agentConfig ? ($agentConfig->target_audience['age'] ?? '') . ', ' . ($agentConfig->target_audience['location'] ?? '') . ', ' . ($agentConfig->target_audience['interests'] ?? '') : ($brandSettings->target_audience ?? '') }}</textarea>
                @if(!$agentConfig && (!$brandSettings || !$brandSettings->target_audience))
                    <p class="text-xs text-blue-600 mt-1">💡 Set this in <a href="{{ route('dashboard.settings') }}" class="underline">Settings</a> to auto-fill</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Business Goals</label>
                <textarea name="business_goals" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                          placeholder="What do you want to achieve? (e.g., increase engagement, drive sales, build awareness)">{{ $agentConfig && $agentConfig->marketing_goals ? implode(', ', $agentConfig->marketing_goals) : ($brandSettings->business_goals ?? '') }}</textarea>
                @if(!$agentConfig && (!$brandSettings || !$brandSettings->business_goals))
                    <p class="text-xs text-blue-600 mt-1">💡 Set this in <a href="{{ route('dashboard.settings') }}" class="underline">Settings</a> to auto-fill</p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand Tone</label>
                    <select name="brand_tone" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="professional" {{ ($agentConfig->brand_tone ?? $brandSettings->brand_tone ?? 'professional') == 'professional' ? 'selected' : '' }}>Professional</option>
                        <option value="casual" {{ ($agentConfig->brand_tone ?? $brandSettings->brand_tone ?? '') == 'casual' ? 'selected' : '' }}>Casual</option>
                        <option value="friendly" {{ ($agentConfig->brand_tone ?? $brandSettings->brand_tone ?? '') == 'friendly' ? 'selected' : '' }}>Friendly</option>
                        <option value="authoritative" {{ ($agentConfig->brand_tone ?? $brandSettings->brand_tone ?? '') == 'authoritative' ? 'selected' : '' }}>Authoritative</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Strategy Duration *</label>
                    <select name="days" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="7" selected>7 Days</option>
                        <option value="14">14 Days</option>
                        <option value="21">21 Days</option>
                        <option value="30">30 Days</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Words to Avoid (Optional)</label>
                <input type="text" name="forbidden_words" 
                       value="{{ $agentConfig->topics_to_avoid ?? $brandSettings->forbidden_words ?? '' }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Comma-separated words to avoid">
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('marketing.studio.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" id="generate-btn" 
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Generate Strategy
                </button>
            </div>
        </form>

        <!-- Results Area -->
        <div id="results-area" class="hidden mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Your Marketing Strategy</h3>
                <button id="generate-all-btn" class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-blue-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Generate All Content
                </button>
            </div>
            <div id="strategy-content"></div>
            <div id="generation-progress" class="hidden mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center mb-2">
                    <svg class="animate-spin h-5 w-5 mr-2 text-blue-600" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-semibold text-blue-900">Generating content...</span>
                </div>
                <p id="generation-status" class="text-sm text-blue-700">This may take a few minutes...</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('strategy-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('generate-btn');
    const resultsArea = document.getElementById('results-area');
    const resultsContent = document.getElementById('strategy-content');
    
    // Show loading
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-2 inline" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Generating...
    `;
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route('marketing.studio.generate-strategy') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayStrategy(result.strategy);
            resultsArea.classList.remove('hidden');
            resultsArea.scrollIntoView({ behavior: 'smooth' });
            
            // Store strategy data for bulk generation
            window.strategyData = {
                calendar: result.strategy.content_calendar,
                brandContext: result.brand_context,
                strategyId: result.strategy_id
            };
            
            // Show generate all button
            document.getElementById('generate-all-btn').classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to generate strategy'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please check your OpenAI API key configuration.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            Generate Strategy
        `;
    }
});

function displayStrategy(strategy) {
    const content = document.getElementById('strategy-content');
    
    let html = '<div class="space-y-6">';
    
    // Content Calendar
    if (strategy.content_calendar) {
        html += '<div class="bg-white rounded-lg p-6 shadow">';
        html += '<h4 class="font-bold text-lg mb-4 text-purple-600">📅 Content Calendar</h4>';
        html += '<div class="space-y-3">';
        
        strategy.content_calendar.forEach(item => {
            html += `
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-gray-900">${item.day || 'Day'} - ${item.time || ''}</span>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">${item.content_type || 'Post'}</span>
                    </div>
                    <p class="text-sm text-gray-700 mb-1"><strong>Topic:</strong> ${item.topic || ''}</p>
                    <p class="text-xs text-gray-600"><strong>Objective:</strong> ${item.objective || ''}</p>
                </div>
            `;
        });
        
        html += '</div></div>';
    }
    
    // Strategic Recommendations
    if (strategy.strategic_recommendations) {
        html += '<div class="bg-white rounded-lg p-6 shadow">';
        html += '<h4 class="font-bold text-lg mb-4 text-blue-600">💡 Strategic Recommendations</h4>';
        html += '<ul class="space-y-2">';
        
        // Handle if it's an array
        if (Array.isArray(strategy.strategic_recommendations)) {
            strategy.strategic_recommendations.forEach(rec => {
                // Check if rec is an object with suggestion/rationale
                if (typeof rec === 'object' && rec.suggestion) {
                    html += `
                        <li class="flex items-start text-gray-700 mb-4">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">${rec.suggestion}</p>
                                ${rec.rationale ? `<p class="text-sm text-gray-600 mt-1">${rec.rationale}</p>` : ''}
                            </div>
                        </li>
                    `;
                }
                // Check if rec is an object with recommendation only
                else if (typeof rec === 'object' && rec.recommendation) {
                    html += `
                        <li class="flex items-start text-gray-700 mb-3">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>${rec.recommendation}</span>
                        </li>
                    `;
                } 
                else if (typeof rec === 'string') {
                    html += `<li class="flex items-start text-gray-700"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span>${rec}</span></li>`;
                }
            });
        } 
        // Handle if it's a string
        else if (typeof strategy.strategic_recommendations === 'string') {
            html += `<li class="flex items-start text-gray-700"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span>${strategy.strategic_recommendations}</span></li>`;
        }
        // Handle if it's an object
        else if (typeof strategy.strategic_recommendations === 'object') {
            Object.values(strategy.strategic_recommendations).forEach(rec => {
                if (typeof rec === 'object' && rec.suggestion) {
                    html += `
                        <li class="flex items-start text-gray-700 mb-4">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">${rec.suggestion}</p>
                                ${rec.rationale ? `<p class="text-sm text-gray-600 mt-1">${rec.rationale}</p>` : ''}
                            </div>
                        </li>
                    `;
                } else if (typeof rec === 'object' && rec.recommendation) {
                    html += `
                        <li class="flex items-start text-gray-700 mb-3">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>${rec.recommendation}</span>
                        </li>
                    `;
                } else {
                    html += `<li class="flex items-start text-gray-700"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span>${rec}</span></li>`;
                }
            });
        }
        
        html += '</ul></div>';
    }
    
    html += '</div>';
    
    content.innerHTML = html;
}

// Handle Generate All Content button
document.getElementById('generate-all-btn').addEventListener('click', async function() {
    const btn = this;
    const progressDiv = document.getElementById('generation-progress');
    const statusText = document.getElementById('generation-status');
    
    // Validation
    if (!window.strategyData || !window.strategyData.calendar || window.strategyData.calendar.length === 0) {
        alert('No strategy calendar found. Please generate a strategy first.');
        return;
    }
    
    // Disable button and show progress
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    progressDiv.classList.remove('hidden');
    statusText.textContent = 'Generating content for all days... This may take a few minutes.';
    
    try {
        const response = await fetch('{{ route('marketing.studio.generate-all-content') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                strategy_calendar: window.strategyData.calendar,
                brand_context: window.strategyData.brandContext,
                strategy_id: window.strategyData.strategyId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            statusText.textContent = `✓ Success! Generated ${result.summary.success} of ${result.summary.total} content pieces. ${result.summary.failed > 0 ? result.summary.failed + ' failed.' : ''}`;
            progressDiv.classList.remove('bg-blue-50');
            progressDiv.classList.add('bg-green-50');
            
            // Redirect to content page after 2 seconds
            setTimeout(() => {
                window.location.href = '{{ route('dashboard.content') }}';
            }, 2000);
        } else {
            statusText.textContent = '✗ Error: ' + (result.message || 'Failed to generate content');
            progressDiv.classList.remove('bg-blue-50');
            progressDiv.classList.add('bg-red-50');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    } catch (error) {
        console.error('Error generating content:', error);
        statusText.textContent = '✗ Network error. Please try again.';
        progressDiv.classList.remove('bg-blue-50');
        progressDiv.classList.add('bg-red-50');
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
});
</script>
@endsection
