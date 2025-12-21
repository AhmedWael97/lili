@extends('dashboard.layout')

@section('title', 'AI Studio')
@section('page-title', 'AI Studio')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">🤖 AI Marketing Studio</h1>
        <p class="text-xl text-gray-600">Create strategies, content, and visuals with AI - No Facebook connection required!</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Strategy Generator -->
        <a href="{{ route('ai-studio.strategy') }}" class="group bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all transform hover:scale-105">
            <div class="flex items-center mb-4">
                <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Strategy Generator</h2>
            </div>
            <p class="text-white text-opacity-90 mb-6">Create data-driven marketing strategies and content calendars for 7-30 days</p>
            <div class="flex items-center text-white">
                <span class="font-medium">Get Started</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </a>

        <!-- Content Creator -->
        <a href="{{ route('ai-studio.content') }}" class="group bg-gradient-to-br from-pink-500 to-orange-500 rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all transform hover:scale-105">
            <div class="flex items-center mb-4">
                <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Content Creator</h2>
            </div>
            <p class="text-white text-opacity-90 mb-6">Generate engaging posts with AI-written captions and DALL-E 3 images</p>
            <div class="flex items-center text-white">
                <span class="font-medium">Create Now</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </a>
    </div>

    <!-- Features Grid -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">What You Can Do</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">AI-Powered Ideas</h4>
                <p class="text-sm text-gray-600">Get strategic recommendations based on your brand and goals</p>
            </div>

            <div class="text-center">
                <div class="bg-pink-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Image Generation</h4>
                <p class="text-sm text-gray-600">Create unique visuals with DALL-E 3 in seconds</p>
            </div>

            <div class="text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Save as Drafts</h4>
                <p class="text-sm text-gray-600">Save your content and publish later when Facebook is connected</p>
            </div>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-semibold text-blue-900 mb-1">Try AI Features Now!</h4>
                <p class="text-blue-800 text-sm">You can test all AI capabilities right away. Connect Facebook later to publish directly to your pages.</p>
            </div>
        </div>
    </div>
</div>
@endsection
