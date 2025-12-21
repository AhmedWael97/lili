@extends('dashboard.layout')

@section('title', 'AI Studio')
@section('page-title', 'AI Marketing Studio')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Usage Stats Banner -->
    @if(isset($usageSummary) && $usageSummary['has_subscription'])
    <div class="mb-6 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Your Usage This Month</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-white text-opacity-80 text-sm">Content Generated</p>
                        <div class="flex items-baseline">
                            <p class="text-3xl font-bold">{{ $usageSummary['posts']['used'] }}</p>
                            @if(!$usageSummary['posts']['unlimited'])
                            <span class="ml-2 text-white text-opacity-80">/ {{ $usageSummary['posts']['limit'] }}</span>
                            @else
                            <span class="ml-2 text-white text-opacity-80">/ Unlimited</span>
                            @endif
                        </div>
                        @if(!$usageSummary['posts']['unlimited'])
                        <div class="mt-2 w-full bg-white bg-opacity-20 rounded-full h-2">
                            <div class="bg-white h-2 rounded-full transition-all" style="width: {{ min($usageSummary['posts']['percentage'], 100) }}%"></div>
                        </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-white text-opacity-80 text-sm">Strategies Created</p>
                        <div class="flex items-baseline">
                            <p class="text-3xl font-bold">{{ $strategies->count() }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-white text-opacity-80 text-sm">Plan</p>
                        <p class="text-2xl font-bold">{{ $usageSummary['package_name'] ?? 'Basic' }}</p>
                        @if(!$usageSummary['posts']['unlimited'] && $usageSummary['posts']['percentage'] >= 80)
                        <a href="{{ route('dashboard.billing') }}" class="inline-block mt-2 text-sm bg-white text-purple-600 px-4 py-1 rounded-full hover:bg-opacity-90">
                            Upgrade Plan
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button onclick="switchTab('strategies')" id="tab-strategies" class="tab-button border-purple-500 text-purple-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Strategies
                </button>
                <button onclick="switchTab('content')" id="tab-content" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Content Creator
                </button>
                <button onclick="switchTab('market')" id="tab-market" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Market Analysis
                </button>
                <button onclick="switchTab('platforms')" id="tab-platforms" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Connected Platforms
                </button>
            </nav>
        </div>
    </div>

    <!-- Strategies Tab Content -->
    <div id="content-strategies" class="tab-content">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Your Marketing Strategies</h2>
            <a href="{{ route('marketing.studio.strategy') }}" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-blue-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Generate New Strategy
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($strategies as $strategy)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-purple-500 to-blue-600 p-4">
                    <h3 class="text-white font-bold text-lg">{{ $strategy->title }}</h3>
                    <p class="text-white text-opacity-80 text-sm">{{ $strategy->days }}-Day Strategy</p>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-medium 
                            @if($strategy->status === 'draft') bg-yellow-100 text-yellow-800
                            @elseif($strategy->status === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($strategy->status === 'completed') bg-green-100 text-green-800
                            @endif
                            px-2 py-1 rounded capitalize">
                            {{ str_replace('_', ' ', $strategy->status) }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $strategy->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="mb-3">
                        <div class="flex items-center text-sm text-gray-600 mb-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Content: {{ $strategy->content_generated }} / {{ $strategy->content_total }}
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $strategy->content_total > 0 ? ($strategy->content_generated / $strategy->content_total * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <a href="#" onclick="viewStrategy({{ $strategy->id }}); return false;" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View Details</a>
                        <button onclick="deleteStrategy({{ $strategy->id }})" class="text-red-600 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No strategies yet</h3>
                <p class="text-gray-600 mb-6">Create your first data-driven marketing strategy</p>
                <a href="{{ route('marketing.studio.strategy') }}" class="inline-block bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-3 rounded-lg hover:from-purple-700 hover:to-blue-700">
                    Generate Strategy Now
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Content Creator Tab Content -->
    <div id="content-content" class="tab-content hidden">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">🎨 AI Content Creator</h2>
            <p class="text-lg text-gray-600">Generate engaging posts with AI-written captions and DALL-E 3 images</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <a href="{{ route('marketing.studio.content') }}" class="group bg-gradient-to-br from-pink-500 to-orange-500 rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all transform hover:scale-105">
                <div class="flex items-center mb-4">
                    <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Create Single Post</h2>
                </div>
                <p class="text-white text-opacity-90 mb-6">Generate one engaging post with caption and image</p>
                <div class="flex items-center text-white">
                    <span class="font-medium">Create Now</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>

            <a href="{{ route('dashboard.content') }}" class="group bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all transform hover:scale-105">
                <div class="flex items-center mb-4">
                    <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">View All Content</h2>
                </div>
                <p class="text-white text-opacity-90 mb-6">Manage your drafts and published content</p>
                <div class="flex items-center text-white">
                    <span class="font-medium">View Content</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>
        </div>

        <div class="mt-8 bg-white rounded-xl shadow-lg p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Features</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-pink-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">AI Images</h4>
                    <p class="text-sm text-gray-600">DALL-E 3 powered image generation</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Smart Captions</h4>
                    <p class="text-sm text-gray-600">Brand-aware copywriting with hashtags</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Save Drafts</h4>
                    <p class="text-sm text-gray-600">Save and publish later</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Analysis Tab Content -->
    <div id="content-market" class="tab-content hidden">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">📊 Market Analysis</h2>
            <p class="text-lg text-gray-600">Analyze competitors, discover trends, and find growth opportunities</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('market.analysis.index') }}" class="group bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all transform hover:scale-105">
                <div class="flex items-center mb-4">
                    <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-white">Competitor Analysis</h2>
                </div>
                <p class="text-white text-opacity-90 mb-4">Track and analyze competitor Facebook pages</p>
                <div class="flex items-center text-white">
                    <span class="font-medium">View Dashboard</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>

            <a href="{{ route('market.analysis.index') }}" class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all transform hover:scale-105">
                <div class="flex items-center mb-4">
                    <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-white">SWOT Analysis</h2>
                </div>
                <p class="text-white text-opacity-90 mb-4">AI-powered strengths, weaknesses, opportunities & threats</p>
                <div class="flex items-center text-white">
                    <span class="font-medium">Generate SWOT</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>

            <a href="{{ route('market.analysis.index') }}" class="group bg-gradient-to-br from-green-500 to-teal-600 rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all transform hover:scale-105">
                <div class="flex items-center mb-4">
                    <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-white">Opportunities</h2>
                </div>
                <p class="text-white text-opacity-90 mb-4">Discover content gaps and growth opportunities</p>
                <div class="flex items-center text-white">
                    <span class="font-medium">Find Opportunities</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">What You Get</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Engagement Metrics</h4>
                    <p class="text-sm text-gray-600">Compare likes, comments, shares with competitors</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Posting Patterns</h4>
                    <p class="text-sm text-gray-600">Best times and days to post</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Content Strategy</h4>
                    <p class="text-sm text-gray-600">Analyze content types and formats</p>
                </div>
                <div class="text-center">
                    <div class="bg-orange-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Recommendations</h4>
                    <p class="text-sm text-gray-600">AI-powered actionable insights</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Platforms Tab Content -->
    <div id="content-platforms" class="tab-content hidden">
        @include('dashboard.partials.platforms-section')
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-purple-500', 'text-purple-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active state to selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-purple-500', 'text-purple-600');
}

function viewStrategy(id) {
    // TODO: Implement strategy detail view
    alert('Strategy details view coming soon!');
}

function deleteStrategy(id) {
    if (confirm('Are you sure you want to delete this strategy?')) {
        fetch(`/ai-studio/strategy/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to delete strategy');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>
</div>
@endsection
