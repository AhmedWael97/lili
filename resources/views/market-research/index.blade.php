@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-gray-900 mb-4">
                AI-Powered Market Research
            </h1>
            <p class="text-xl text-gray-600 mb-2">
                Get instant market insights for your business idea
            </p>
            <p class="text-gray-500">
                Analyze competitors, customers, pricing, and opportunities in 10-15 minutes
            </p>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
            <form action="{{ route('market-research.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Business Idea Input -->
                <div>
                    <label for="business_idea" class="block text-lg font-semibold text-gray-900 mb-3">
                        What business do you want to start?
                    </label>
                    <textarea 
                        name="business_idea" 
                        id="business_idea" 
                        rows="3"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                        placeholder="e.g., SaaS platform for restaurant inventory management, Organic bakery specializing in gluten-free products, AI-powered marketing analytics tool..."
                        required
                    >{{ old('business_idea') }}</textarea>
                    @error('business_idea')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        Be specific! Include your niche, target market, or unique angle.
                    </p>
                </div>

                <!-- Location Input -->
                <div>
                    <label for="location" class="block text-lg font-semibold text-gray-900 mb-3">
                        Where will you launch?
                    </label>
                    <input 
                        type="text" 
                        name="location" 
                        id="location" 
                        value="{{ old('location') }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                        placeholder="e.g., Austin, Texas or USA or United Kingdom"
                        required
                    >
                    @error('location')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        City, state, or country
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button 
                        type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-lg font-semibold py-4 px-8 rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 duration-200"
                    >
                        Start Free Analysis
                        <span class="ml-2">→</span>
                    </button>
                    <p class="text-center mt-4 text-sm text-gray-500">
                        ⏱️ Takes 10-15 minutes • No credit card required
                    </p>
                </div>
            </form>
        </div>

        <!-- What You'll Get Section -->
        <div class="mt-16 grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg p-6 shadow-md">
                <div class="text-indigo-600 text-3xl mb-3">🎯</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Competitor Analysis</h3>
                <p class="text-gray-600 text-sm">
                    Find 5-10 competitors automatically with ratings, reviews, and positioning analysis
                </p>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-md">
                <div class="text-indigo-600 text-3xl mb-3">💰</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pricing Intelligence</h3>
                <p class="text-gray-600 text-sm">
                    See competitor pricing models, tiers, and identify pricing opportunities
                </p>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-md">
                <div class="text-indigo-600 text-3xl mb-3">👥</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Customer Insights</h3>
                <p class="text-gray-600 text-sm">
                    Understand customer pain points, needs, and buying factors from real reviews
                </p>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-md">
                <div class="text-indigo-600 text-3xl mb-3">💡</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Market Opportunities</h3>
                <p class="text-gray-600 text-sm">
                    Discover gaps in the market and strategic recommendations for success
                </p>
            </div>
        </div>

        <!-- 12 Sections Preview -->
        <div class="mt-12 bg-white rounded-lg p-8 shadow-md">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Your Complete Research Report Includes:</h2>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Market Problem & Customer Pain</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Target Customer Profile</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Market Demand Analysis</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Competitor Landscape</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Pricing Reality & Models</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Customer Sentiment Analysis</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Market Size & Growth</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Purchase Decision Process</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Marketing Channels</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Market Trends & Timing</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Risks & Barriers</span>
                </div>
                <div class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    <span class="text-gray-700">Opportunities & Gaps</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
