@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($request->status === 'pending' || $request->status === 'processing')
            <!-- Processing State -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="text-center mb-8">
                        <div class="mb-6">
                            <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-4 border-lili-600"></div>
                        </div>
                        
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">
                            Analyzing Your Market...
                        </h1>
                        
                        <p class="text-lg text-gray-600 mb-2">
                            <strong>{{ $request->business_idea }}</strong>
                        </p>
                        <p class="text-sm text-gray-500">📍 {{ $request->location }}</p>
                    </div>

                    <!-- Live Progress Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 text-center border border-blue-200">
                            <div class="text-3xl font-bold text-blue-600" id="competitors-count">{{ $initialProgress['competitors'] }}</div>
                            <div class="text-xs text-blue-700 mt-1 font-medium">Competitors</div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 text-center border border-purple-200">
                            <div class="text-3xl font-bold text-purple-600" id="reviews-count">{{ $initialProgress['reviews'] }}</div>
                            <div class="text-xs text-purple-700 mt-1 font-medium">Reviews</div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 text-center border border-green-200">
                            <div class="text-3xl font-bold text-green-600" id="pricing-count">{{ $initialProgress['pricing_tiers'] }}</div>
                            <div class="text-xs text-green-700 mt-1 font-medium">Pricing Tiers</div>
                        </div>
                        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg p-4 text-center border border-amber-200">
                            <div class="text-3xl font-bold text-amber-600" id="forums-count">{{ $initialProgress['forum_discussions'] }}</div>
                            <div class="text-xs text-amber-700 mt-1 font-medium">Forum Posts</div>
                        </div>
                    </div>

                    <!-- Processing Steps -->
                    <div class="space-y-3 mb-8">
                        <div class="flex items-center p-3 rounded-lg transition" id="step-competitors">
                            <span class="text-2xl mr-3" id="icon-competitors">🔍</span>
                            <div class="flex-1">
                                <span class="text-gray-700 font-medium" id="text-competitors">Searching for competitors...</span>
                                <div class="text-xs text-gray-500 mt-1" id="detail-competitors"></div>
                            </div>
                            <span class="text-xs text-gray-400" id="status-competitors">In Progress</span>
                        </div>
                        <div class="flex items-center p-3 rounded-lg transition" id="step-reviews">
                            <span class="text-2xl mr-3" id="icon-reviews">⭐</span>
                            <div class="flex-1">
                                <span class="text-gray-500" id="text-reviews">Analyzing customer reviews...</span>
                                <div class="text-xs text-gray-400 mt-1" id="detail-reviews"></div>
                            </div>
                            <span class="text-xs text-gray-400" id="status-reviews">Waiting</span>
                        </div>
                        <div class="flex items-center p-3 rounded-lg transition" id="step-pricing">
                            <span class="text-2xl mr-3" id="icon-pricing">💰</span>
                            <div class="flex-1">
                                <span class="text-gray-500" id="text-pricing">Researching pricing strategies...</span>
                                <div class="text-xs text-gray-400 mt-1" id="detail-pricing"></div>
                            </div>
                            <span class="text-xs text-gray-400" id="status-pricing">Waiting</span>
                        </div>
                        <div class="flex items-center p-3 rounded-lg transition" id="step-forums">
                            <span class="text-2xl mr-3" id="icon-forums">💬</span>
                            <div class="flex-1">
                                <span class="text-gray-500" id="text-forums">Scanning forums and discussions...</span>
                                <div class="text-xs text-gray-400 mt-1" id="detail-forums"></div>
                            </div>
                            <span class="text-xs text-gray-400" id="status-forums">Waiting</span>
                        </div>
                        <div class="flex items-center p-3 rounded-lg transition" id="step-market">
                            <span class="text-2xl mr-3" id="icon-market">📊</span>
                            <div class="flex-1">
                                <span class="text-gray-500" id="text-market">Analyzing market dynamics...</span>
                                <div class="text-xs text-gray-400 mt-1" id="detail-market"></div>
                            </div>
                            <span class="text-xs text-gray-400" id="status-market">Waiting</span>
                        </div>
                        <div class="flex items-center p-3 rounded-lg transition" id="step-insights">
                            <span class="text-2xl mr-3" id="icon-insights">🎯</span>
                            <div class="flex-1">
                                <span class="text-gray-500" id="text-insights">Generating customer insights...</span>
                                <div class="text-xs text-gray-400 mt-1" id="detail-insights"></div>
                            </div>
                            <span class="text-xs text-gray-400" id="status-insights">Waiting</span>
                        </div>
                        <div class="flex items-center p-3 rounded-lg transition" id="step-report">
                            <span class="text-2xl mr-3" id="icon-report">📝</span>
                            <div class="flex-1">
                                <span class="text-gray-500" id="text-report">Compiling final report...</span>
                                <div class="text-xs text-gray-400 mt-1" id="detail-report"></div>
                            </div>
                            <span class="text-xs text-gray-400" id="status-report">Waiting</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-500">
                            ⏱️ This usually takes 10-15 minutes. You can close this page and come back later.
                        </p>
                        <div class="mt-2 text-xs text-gray-400">
                            Last updated: <span id="last-updated">just now</span>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                let lastUpdate = Date.now();
                
                function updateLastUpdated() {
                    const seconds = Math.floor((Date.now() - lastUpdate) / 1000);
                    const elem = document.getElementById('last-updated');
                    if (seconds < 5) elem.textContent = 'just now';
                    else if (seconds < 60) elem.textContent = seconds + ' seconds ago';
                    else elem.textContent = Math.floor(seconds / 60) + ' minutes ago';
                }
                
                function updateStep(stepId, isComplete, isActive, count) {
                    const step = document.getElementById('step-' + stepId);
                    const icon = document.getElementById('icon-' + stepId);
                    const text = document.getElementById('text-' + stepId);
                    const status = document.getElementById('status-' + stepId);
                    const detail = document.getElementById('detail-' + stepId);
                    
                    if (isComplete) {
                        step.classList.add('bg-green-50');
                        icon.textContent = '✅';
                        text.classList.remove('text-gray-500');
                        text.classList.add('text-green-700', 'font-medium');
                        status.textContent = 'Complete';
                        status.classList.add('text-green-600', 'font-medium');
                        if (count) detail.textContent = 'Found ' + count + ' items';
                    } else if (isActive) {
                        step.classList.add('bg-blue-50', 'border', 'border-blue-200');
                        text.classList.remove('text-gray-500');
                        text.classList.add('text-blue-700', 'font-medium');
                        status.textContent = 'In Progress';
                        status.classList.add('text-blue-600', 'font-medium');
                        if (count) detail.textContent = 'Processing... ' + count + ' so far';
                    }
                }
                
                function animateCount(elementId, newValue) {
                    const elem = document.getElementById(elementId);
                    const currentValue = parseInt(elem.textContent) || 0;
                    if (newValue > currentValue) {
                        let current = currentValue;
                        const increment = Math.ceil((newValue - currentValue) / 10);
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= newValue) {
                                current = newValue;
                                clearInterval(timer);
                            }
                            elem.textContent = current;
                        }, 50);
                    }
                }
                
                function updateProgress(data) {
                    lastUpdate = Date.now();
                    
                    // Update counts with animation
                    animateCount('competitors-count', data.progress.competitors);
                    animateCount('reviews-count', data.progress.reviews);
                    animateCount('pricing-count', data.progress.pricing_tiers);
                    animateCount('forums-count', data.progress.forum_discussions);
                    
                    // Update steps
                    updateStep('competitors', data.progress.competitors > 0, true, data.progress.competitors);
                    updateStep('reviews', data.progress.reviews > 0, data.progress.competitors > 0, data.progress.reviews);
                    updateStep('pricing', data.progress.pricing_tiers > 0, data.progress.reviews > 0, data.progress.pricing_tiers);
                    updateStep('forums', data.progress.forum_discussions > 0, data.progress.pricing_tiers > 0, data.progress.forum_discussions);
                    updateStep('market', data.progress.has_market_data, data.progress.forum_discussions > 0);
                    updateStep('insights', data.progress.has_insights, data.progress.has_market_data);
                    updateStep('report', data.progress.has_report, data.progress.has_insights);
                }
                
                // Poll for status updates every 3 seconds
                setInterval(function() {
                    fetch('{{ route('market-research.status', $request->id) }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'completed' || data.status === 'failed') {
                                location.reload();
                            } else {
                                updateProgress(data);
                            }
                        })
                        .catch(err => console.error('Status check failed:', err));
                }, 3000);
                
                // Update "last updated" time every second
                setInterval(updateLastUpdated, 1000);
                
                // Initial poll
                fetch('{{ route('market-research.status', $request->id) }}')
                    .then(response => response.json())
                    .then(data => updateProgress(data));
            </script>

        @elseif($request->status === 'failed')
            <!-- Failed State -->
            <div class="max-w-3xl mx-auto">
                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-8">
                    <div class="text-center mb-6">
                        <div class="text-6xl mb-4">⚠️</div>
                        <h1 class="text-2xl font-bold text-red-900 mb-2">
                            Analysis Failed
                        </h1>
                        <p class="text-red-700 mb-4">
                            We encountered an error while analyzing your market research request.
                        </p>
                    </div>
                    
                    @if($request->error_message)
                        <div class="bg-red-100 border border-red-300 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-red-800 mb-1">Error Details</h3>
                                    <p class="text-sm text-red-700">{{ $request->error_message }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Progress Summary -->
                    @if($initialProgress['competitors'] > 0 || $initialProgress['reviews'] > 0)
                        <div class="bg-white border border-red-200 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Progress Before Failure:</h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="flex items-center">
                                    <span class="text-blue-600 mr-2">✓</span>
                                    <span class="text-gray-600">{{ $initialProgress['competitors'] }} Competitors found</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-purple-600 mr-2">✓</span>
                                    <span class="text-gray-600">{{ $initialProgress['reviews'] }} Reviews analyzed</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span class="text-gray-600">{{ $initialProgress['pricing_tiers'] }} Pricing tiers</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-amber-600 mr-2">✓</span>
                                    <span class="text-gray-600">{{ $initialProgress['forum_discussions'] }} Forum posts</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-3 justify-center">
                        <form action="{{ route('market-research.retry', $request->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-lili-600 text-white px-6 py-3 rounded-lg hover:bg-lili-700 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Retry Analysis
                            </button>
                        </form>
                        <a href="{{ route('market-research.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition">
                            Start New Research
                        </a>
                    </div>
                    
                    <p class="text-center text-sm text-gray-500 mt-6">
                        💡 The retry will continue from where it left off, preserving any data already collected.
                    </p>
                    </div>
                </div>
            </div>

        @elseif($request->status === 'completed' && $request->report)
            <!-- Completed State - Show Report -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Market Research Report</h1>
                    <p class="text-gray-600 mt-1">{{ $request->business_idea }} • {{ $request->location }}</p>
                </div>
                <div>
                    <a href="{{ route('market-research.pdf', $request->id) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                        Download PDF
                    </a>
                </div>
            </div>

            @php
                $report = $request->report;
                $sections = $report->report_sections ?? [];
            @endphp

            <!-- Executive Summary -->
            <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Executive Summary</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($report->executive_summary)) !!}
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-3xl font-bold text-indigo-600">{{ $report->competitor_count }}</div>
                    <div class="text-sm text-gray-600 mt-1">Competitors Found</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-3xl font-bold text-indigo-600">{{ $report->review_count }}</div>
                    <div class="text-sm text-gray-600 mt-1">Reviews Analyzed</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-3xl font-bold text-indigo-600">{{ count($report->opportunities ?? []) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Opportunities</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-3xl font-bold text-indigo-600">{{ count($report->risks ?? []) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Risks Identified</div>
                </div>
            </div>
            <!-- Market Size & Growth Metrics -->
            @if($request->marketData)
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md p-8 mb-6 border border-indigo-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span>📊</span> Market Size & Growth Metrics
                </h2>
                <div class="grid md:grid-cols-3 gap-6">
                    @if($request->marketData->market_size_estimate)
                        <div class="bg-white rounded-lg p-6 shadow">
                            <div class="text-xs text-gray-600 mb-1">Market Size</div>
                            <div class="text-3xl font-bold text-indigo-600">{{ $request->marketData->market_size_estimate }}</div>
                        </div>
                    @endif
                    @if($request->marketData->growth_rate)
                        <div class="bg-white rounded-lg p-6 shadow">
                            <div class="text-xs text-gray-600 mb-1">Growth Rate</div>
                            <div class="text-3xl font-bold text-green-600">{{ $request->marketData->growth_rate }}</div>
                        </div>
                    @endif
                    @if($request->marketData->competition_level)
                        <div class="bg-white rounded-lg p-6 shadow">
                            <div class="text-xs text-gray-600 mb-1">Competition Level</div>
                            <div class="text-2xl font-bold text-amber-600">{{ $request->marketData->competition_level }}</div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Pricing Recommendation -->
            @php
                $allPrices = [];
                foreach($request->competitors as $comp) {
                    foreach($comp->pricing as $p) {
                        if($p->price) {
                            $allPrices[] = $p->price;
                        }
                    }
                }
                $avgPrice = count($allPrices) > 0 ? array_sum($allPrices) / count($allPrices) : null;
                $minPrice = count($allPrices) > 0 ? min($allPrices) : null;
                $maxPrice = count($allPrices) > 0 ? max($allPrices) : null;
            @endphp
            @if($avgPrice)
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-md p-8 mb-6 border border-green-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>💸</span> Recommended Competitive Pricing
                </h2>
                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="text-xs text-gray-600 mb-1">Market Low</div>
                        <div class="text-2xl font-bold text-gray-700">${{ number_format($minPrice, 0) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow border-2 border-green-500">
                        <div class="text-xs text-green-700 mb-1 font-semibold">Sweet Spot (Avg)</div>
                        <div class="text-3xl font-bold text-green-600">${{ number_format($avgPrice, 0) }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="text-xs text-gray-600 mb-1">Market High</div>
                        <div class="text-2xl font-bold text-gray-700">${{ number_format($maxPrice, 0) }}</div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">🎯 Strategy:</span> Based on {{ count($allPrices) }} competitor pricing points analyzed, 
                        we recommend positioning around <span class="font-bold text-green-600">${{ number_format($avgPrice, 0) }}</span> to remain competitive.
                        Consider pricing slightly below average (${{ number_format($avgPrice * 0.9, 0) }}) for market penetration, 
                        or match the average if you offer superior features.
                    </p>
                </div>
            </div>
            @endif
            <!-- Report Sections -->
            <div class="space-y-6">
                
                <!-- Competitors with Detailed Data -->
                @if($request->competitors->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">🏪 Competitor Analysis</h2>
                    <div class="space-y-6">
                        @foreach($request->competitors as $competitor)
                            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-lili-300 transition">
                                <!-- Competitor Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-xl text-gray-900 mb-1">{{ $competitor->name }}</h3>
                                        @if($competitor->website)
                                            <a href="{{ $competitor->website }}" target="_blank" class="text-sm text-lili-600 hover:underline flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                {{ $competitor->website }}
                                            </a>
                                        @endif
                                        @if($competitor->description)
                                            <p class="text-sm text-gray-600 mt-2">{{ $competitor->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right ml-4">
                                        @if($competitor->overall_rating)
                                            <div class="flex items-center gap-1 justify-end">
                                                <span class="text-2xl font-bold text-amber-500">{{ number_format($competitor->overall_rating, 1) }}</span>
                                                <span class="text-yellow-400">⭐</span>
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $competitor->review_count }} reviews</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Pricing Section -->
                                @if($competitor->pricing->count() > 0)
                                    <div class="mb-4">
                                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                            <span>💰</span> Pricing Plans ({{ $competitor->pricing->count() }})
                                        </h4>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700">Plan</th>
                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700">Price</th>
                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700">Key Features</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($competitor->pricing as $pricing)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                                {{ $pricing->tier_name }}
                                                                @if($pricing->is_popular)
                                                                    <span class="ml-2 inline-block bg-lili-500 text-white text-xs px-2 py-0.5 rounded">Popular</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3 text-sm">
                                                                @if($pricing->price)
                                                                    <span class="font-bold text-lili-600">${{ number_format($pricing->price, 0) }}</span>
                                                                    <span class="text-gray-500 text-xs">/{{ $pricing->billing_period }}</span>
                                                                @else
                                                                    <span class="text-gray-600">Custom</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3 text-xs text-gray-700">
                                                                @if($pricing->features && count($pricing->features) > 0)
                                                                    {{ implode(', ', array_slice($pricing->features, 0, 3)) }}
                                                                    @if(count($pricing->features) > 3)
                                                                        <span class="text-gray-500">+{{ count($pricing->features) - 3 }} more</span>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <!-- Reviews Section -->
                                @if($competitor->reviews->count() > 0)
                                    <div>
                                        <button onclick="showReviews({{ $competitor->id }})" class="text-sm text-lili-600 hover:text-lili-700 font-medium flex items-center gap-2">
                                            <span>💬</span>
                                            View {{ $competitor->reviews->count() }} Customer Reviews
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- Reviews Modal -->
                                        <div id="reviews-modal-{{ $competitor->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                                                <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-center">
                                                    <h3 class="text-xl font-bold text-gray-900">{{ $competitor->name }} - Customer Reviews</h3>
                                                    <button onclick="closeReviews({{ $competitor->id }})" class="text-gray-400 hover:text-gray-600">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="p-6 space-y-4">
                                                    @foreach($competitor->reviews as $review)
                                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                            <div class="flex justify-between items-start mb-2">
                                                                <div>
                                                                    <span class="font-medium text-gray-900">{{ $review->reviewer_name }}</span>
                                                                    @if($review->reviewer_role)
                                                                        <span class="text-xs text-gray-500 ml-2">{{ $review->reviewer_role }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="flex items-center gap-1">
                                                                    <span class="font-semibold text-amber-600">{{ $review->rating }}</span>
                                                                    <span class="text-yellow-400">⭐</span>
                                                                </div>
                                                            </div>
                                                            @if($review->title)
                                                                <h5 class="font-semibold text-sm text-gray-800 mb-1">{{ $review->title }}</h5>
                                                            @endif
                                                            @if($review->review_text)
                                                                <p class="text-sm text-gray-700 mb-2">{{ $review->review_text }}</p>
                                                            @endif
                                                            <div class="grid md:grid-cols-2 gap-2 mt-2">
                                                                @if($review->pros)
                                                                    <div class="text-xs">
                                                                        <span class="font-semibold text-green-700">👍 Pros:</span>
                                                                        <p class="text-gray-600">{{ $review->pros }}</p>
                                                                    </div>
                                                                @endif
                                                                @if($review->cons)
                                                                    <div class="text-xs">
                                                                        <span class="font-semibold text-red-700">👎 Cons:</span>
                                                                        <p class="text-gray-600">{{ $review->cons }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            @if($review->platform)
                                                                <div class="text-xs text-gray-500 mt-2">Source: {{ $review->platform }}</div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Forum Discussions -->
                @if($request->forumDiscussions->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">💬 Community Insights from Forums</h2>
                    <div class="space-y-3">
                        @foreach($request->forumDiscussions->take(15) as $discussion)
                            <div class="border-l-4 border-lili-500 bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 text-sm mb-2">{{ $discussion->title }}</h3>
                                        @if($discussion->pain_points && count($discussion->pain_points) > 0)
                                            <div class="mb-2">
                                                <span class="text-xs font-semibold text-red-700">🔥 Key Insights:</span>
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach(array_slice($discussion->pain_points, 0, 3) as $pain)
                                                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">{{ $pain }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-3 text-xs text-gray-500">
                                            <span class="bg-lili-100 text-lili-700 px-2 py-0.5 rounded">{{ $discussion->source }}</span>
                                            @if($discussion->upvotes)
                                                <span>👍 {{ $discussion->upvotes }}</span>
                                            @endif
                                            @if($discussion->comments_count)
                                                <span>💬 {{ $discussion->comments_count }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($discussion->url)
                                        <a href="{{ $discussion->url }}" target="_blank" class="ml-4 text-lili-600 hover:text-lili-700 text-xs font-medium whitespace-nowrap">
                                            Read Full →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Old Competitors Section from Report -->
                @if(isset($sections['competitors']))
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">🏪 Top Competitors</h2>
                    <div class="space-y-4">
                        @foreach($sections['competitors']['direct_competitors'] ?? [] as $competitor)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $competitor['name'] }}</h3>
                                        @if($competitor['website'])
                                            <a href="{{ $competitor['website'] }}" target="_blank" class="text-sm text-indigo-600 hover:underline">
                                                {{ $competitor['website'] }}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-yellow-500 font-semibold">⭐ {{ $competitor['rating'] ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $competitor['reviews'] ?? 0 }} reviews</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Market Problem & Customer Pain -->
                @if(isset($sections['market_problem']))
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">😣 Customer Pain Points</h2>
                    <div class="space-y-2">
                        @foreach($sections['market_problem']['problems_identified'] ?? [] as $problem)
                            <div class="flex items-start">
                                <span class="text-red-500 mr-2">•</span>
                                <span class="text-gray-700">{{ is_array($problem) ? $problem['pain_point'] ?? '' : $problem }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Opportunities -->
                @if(count($report->opportunities ?? []) > 0)
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">💡 Market Opportunities</h2>
                    <div class="space-y-4">
                        @foreach($report->opportunities as $opportunity)
                            <div class="border-l-4 border-green-500 pl-4">
                                <h3 class="font-semibold text-gray-900">{{ $opportunity['opportunity'] ?? '' }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $opportunity['description'] ?? '' }}</p>
                                <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                    {{ $opportunity['potential'] ?? 'Medium' }} Potential
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Action Plan -->
                @if(!empty($report->action_plan))
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">✅ 30-Day Action Plan</h2>
                    <div class="space-y-6">
                        @foreach($report->action_plan as $weekKey => $week)
                            <div>
                                <h3 class="font-bold text-lg text-indigo-600 mb-3">{{ $week['title'] ?? ucfirst(str_replace('_', ' ', $weekKey)) }}</h3>
                                <div class="space-y-2">
                                    @foreach($week['tasks'] ?? [] as $task)
                                        <div class="flex items-start bg-gray-50 p-3 rounded">
                                            <span class="text-indigo-600 mr-2">□</span>
                                            <div class="flex-1">
                                                <div class="text-gray-900">{{ $task['task'] ?? '' }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Priority: {{ $task['priority'] ?? 'Medium' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Competitive Advantage Analysis -->
                @if($request->competitors->count() > 0 && $request->customerInsights)
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">⚔️ Competitive Advantage Analysis</h2>
                    
                    <!-- Competitor Strengths & Weaknesses -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Competitor Positioning</h3>
                        <div class="space-y-4">
                            @foreach($request->competitors->take(5) as $competitor)
                                @php
                                    $strengths = [];
                                    $weaknesses = [];
                                    
                                    // Analyze pricing
                                    if($competitor->pricing->count() > 0) {
                                        $avgCompPrice = $competitor->pricing->where('price', '>', 0)->avg('price');
                                        if($avgCompPrice > $avgPrice) $weaknesses[] = 'Premium pricing may limit market penetration';
                                        elseif($avgCompPrice < $avgPrice * 0.7) $strengths[] = 'Competitive pricing advantage';
                                    }
                                    
                                    // Analyze reviews
                                    if($competitor->overall_rating && $competitor->overall_rating >= 4.5) {
                                        $strengths[] = 'High customer satisfaction (' . $competitor->overall_rating . '★)';
                                    } elseif($competitor->overall_rating && $competitor->overall_rating < 3.5) {
                                        $weaknesses[] = 'Low customer satisfaction (' . $competitor->overall_rating . '★)';
                                    }
                                    
                                    // Extract from reviews
                                    foreach($competitor->reviews->take(10) as $review) {
                                        if($review->pros) {
                                            $prosArr = explode(',', $review->pros);
                                            if(count($prosArr) > 0) $strengths[] = trim($prosArr[0]);
                                        }
                                        if($review->cons) {
                                            $consArr = explode(',', $review->cons);
                                            if(count($consArr) > 0) $weaknesses[] = trim($consArr[0]);
                                        }
                                    }
                                    
                                    $strengths = array_unique(array_slice($strengths, 0, 3));
                                    $weaknesses = array_unique(array_slice($weaknesses, 0, 3));
                                @endphp
                                
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-bold text-gray-900 mb-3">{{ $competitor->name }}</h4>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <div class="text-xs font-semibold text-green-700 mb-2">💪 Strengths:</div>
                                            <ul class="space-y-1">
                                                @forelse($strengths as $strength)
                                                    <li class="text-xs text-gray-700 flex items-start">
                                                        <span class="text-green-500 mr-1">✓</span>
                                                        <span>{{ $strength }}</span>
                                                    </li>
                                                @empty
                                                    <li class="text-xs text-gray-500">No significant strengths identified</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-red-700 mb-2">⚠️ Weaknesses:</div>
                                            <ul class="space-y-1">
                                                @forelse($weaknesses as $weakness)
                                                    <li class="text-xs text-gray-700 flex items-start">
                                                        <span class="text-red-500 mr-1">×</span>
                                                        <span>{{ $weakness }}</span>
                                                    </li>
                                                @empty
                                                    <li class="text-xs text-gray-500">No significant weaknesses identified</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Your Unique Positioning Suggestions -->
                    <div class="bg-gradient-to-r from-lili-50 to-purple-50 rounded-lg p-6 border-2 border-lili-300">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">💡 Your Unique Positioning Strategy</h3>
                        @php
                            $allWeaknesses = [];
                            foreach($request->competitors as $comp) {
                                foreach($comp->reviews as $review) {
                                    if($review->cons) {
                                        $allWeaknesses[] = $review->cons;
                                    }
                                    if($review->pain_points && is_array($review->pain_points)) {
                                        $allWeaknesses = array_merge($allWeaknesses, $review->pain_points);
                                    }
                                }
                            }
                            $suggestions = array_unique(array_slice($allWeaknesses, 0, 5));
                        @endphp
                        
                        <div class="space-y-3">
                            @forelse($suggestions as $index => $suggestion)
                                <div class="bg-white rounded-lg p-4 border border-lili-200">
                                    <div class="flex items-start gap-3">
                                        <div class="bg-lili-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-sm mb-1">Address: {{ Str::limit($suggestion, 60) }}</h4>
                                            <p class="text-xs text-gray-600">
                                                This is a common pain point among competitors. Make this a core feature of your offering to differentiate.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600">Analyze competitor reviews to identify gaps you can fill.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- Reviews Modal JavaScript -->
            <script>
                function showReviews(competitorId) {
                    document.getElementById('reviews-modal-' + competitorId).classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
                
                function closeReviews(competitorId) {
                    document.getElementById('reviews-modal-' + competitorId).classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
                
                // Close modal on outside click
                document.addEventListener('click', function(event) {
                    if (event.target.id && event.target.id.startsWith('reviews-modal-')) {
                        closeReviews(event.target.id.replace('reviews-modal-', ''));
                    }
                });
            </script>

        @endif

    </div>
</div>
@endsection
