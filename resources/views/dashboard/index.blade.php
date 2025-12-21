@extends('dashboard.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $activeAgents = auth()->user()->userAgents()->where('status', 'active')->with('agentType')->get();
    $activeAgentsCount = $activeAgents->count();
    $isNewUser = auth()->user()->created_at->diffInMinutes(now()) < 60;
    $hasMarketingAgent = $activeAgents->where('agentType.category', 'Marketing')->count() > 0;
    
    // Get agent interactions stats
    $totalInteractions = auth()->user()->agentInteractions()->count();
    $thisMonthInteractions = auth()->user()->agentInteractions()
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
@endphp

<!-- Welcome Banner for New Users (No Agents) -->
@if($activeAgentsCount === 0)
<div class="bg-gradient-to-br from-lili-500 via-purple-500 to-pink-500 rounded-2xl shadow-2xl p-8 mb-6 text-white relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-48 translate-y-48"></div>
    </div>
    
    <div class="relative">
        <div class="flex items-start justify-between mb-6">
            <div class="flex-1">
                <h2 class="text-3xl font-bold mb-2">👋 Welcome to LiLi, {{ auth()->user()->name }}!</h2>
                <p class="text-white text-opacity-90 text-lg mb-4">Your AI virtual company is ready to get started. Let's activate your first AI agent!</p>
            </div>
            @if($isNewUser)
            <span class="bg-white bg-opacity-20 backdrop-blur-sm text-black text-xs font-bold px-3 py-1.5 rounded-full">
                🎉 NEW ACCOUNT
            </span>
            @endif
        </div>
        
        <!-- Getting Started Steps -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-4 border border-white border-opacity-20">
                <div class="flex items-center mb-2">
                    <span class="bg-white text-lili-600 font-bold w-7 h-7 rounded-full flex items-center justify-center text-sm mr-3">1</span>
                    <h4 class="font-semibold text-lili-600">Activate AI Agents</h4>
                </div>
                <p class="text-sm text-lili-600 text-opacity-80">Choose from Marketing, QA, Developer, and more</p>
            </div>
            
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-4 border border-white border-opacity-20">
                <div class="flex items-center mb-2">
                    <span class="bg-white text-purple-600 font-bold w-7 h-7 rounded-full flex items-center justify-center text-sm mr-3">2</span>
                    <h4 class="font-semibold text-purple-600">Connect Platforms</h4>
                </div>
                <p class="text-sm text-purple-600 text-opacity-80">Link your Facebook pages and other accounts</p>
            </div>
            
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-4 border border-white border-opacity-20">
                <div class="flex items-center mb-2">
                    <span class="bg-white text-pink-600 font-bold w-7 h-7 rounded-full flex items-center justify-center text-sm mr-3">3</span>
                    <h4 class="font-semibold text-pink-600">Create Content</h4>
                </div>
                <p class="text-sm text-pink-600 text-opacity-80">Let your AI agents generate amazing content</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('agents.index') }}" class="bg-white text-lili-600 hover:bg-opacity-90 font-bold py-3 px-8 rounded-xl transition transform hover:scale-105 shadow-lg inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Activate Your First Agent
            </a>
            <a href="#" class="text-white hover:text-opacity-80 font-medium underline">
                Watch Quick Tour (2 min)
            </a>
        </div>
    </div>
</div>
@else
<!-- Active Agents Banner -->
<div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="bg-white bg-opacity-20 rounded-full p-3 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold">{{ $activeAgentsCount }} AI Agent{{ $activeAgentsCount > 1 ? 's' : '' }} Active</h3>
                <p class="text-blue-100">Your AI assistants are ready to help you</p>
            </div>
        </div>
        <a href="{{ route('agents.dashboard') }}" class="bg-white text-blue-600 hover:bg-blue-50 font-bold py-3 px-6 rounded-lg transition">
            View Agent Dashboard →
        </a>
    </div>
</div>
@endif

<!-- Stats Cards -->
@if($activeAgentsCount > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Agent Interactions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Agent Interactions</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalInteractions }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $thisMonthInteractions }} this month</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Marketing Content (Only show if marketing agent active) -->
    @if($hasMarketingAgent)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Marketing Posts</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->getCurrentUsage()['posts_used'] ?? 0 }}</p>
                <p class="text-sm text-gray-500 mt-1">of {{ $limits['posts_per_month'] ?? 10 }} this month</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Connected Pages</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->facebookPages->count() }}</p>
                <p class="text-sm text-gray-500 mt-1">of {{ $limits['facebook_pages'] ?? 1 }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
    @else
    <!-- Active Agents by Category -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Active Agents</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $activeAgentsCount }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    @foreach($activeAgents->groupBy('agentType.category') as $category => $agents)
                        {{ $agents->count() }} {{ $category }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Success Rate</p>
                @php
                    $successfulInteractions = auth()->user()->agentInteractions()->where('success', true)->count();
                    $successRate = $totalInteractions > 0 ? round(($successfulInteractions / $totalInteractions) * 100) : 0;
                @endphp
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $successRate }}%</p>
                <p class="text-sm text-gray-500 mt-1">{{ $successfulInteractions }} of {{ $totalInteractions }} successful</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Agent Slots -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Agent Slots</p>
                @php
                    $totalSlots = auth()->user()->subscription?->package?->agent_slots ?? 1;
                    $slotsText = $totalSlots === -1 ? 'unlimited' : "of {$totalSlots}";
                @endphp
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $activeAgentsCount }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $slotsText }} slots</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Recent Agent Activity -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Recent Agent Activity</h3>
    </div>
    <div class="p-6">
        @php
            $recentActivity = auth()->user()->agentInteractions()
                ->with('userAgent.agentType')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        @endphp
        
        @if($recentActivity->count() > 0)
        <div class="space-y-4">
            @foreach($recentActivity as $activity)
            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color: {{ $activity->userAgent->agentType->color }}20;">
                    <span style="color: {{ $activity->userAgent->agentType->color }}">
                        {{ $activity->userAgent->agentType->icon }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">
                            {{ $activity->userAgent->agentType->name }} 
                            <span class="text-gray-500">• {{ $activity->action }}</span>
                        </p>
                        <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                    @if($activity->input_data)
                    <p class="text-sm text-gray-600 mt-1 truncate">
                        {{ is_array($activity->input_data) ? ($activity->input_data['prompt'] ?? 'Task executed') : $activity->input_data }}
                    </p>
                    @endif
                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            @if($activity->success)
                                <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                Success
                            @else
                                <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                                Failed
                            @endif
                        </span>
                        @if($activity->tokens_used)
                        <span>🪙 {{ number_format($activity->tokens_used) }} tokens</span>
                        @endif
                        @if($activity->execution_time_ms)
                        <span>⚡ {{ number_format($activity->execution_time_ms) }}ms</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('agents.dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                View All Activity →
            </a>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <p>No agent activity yet. Your agents will start working once you use them!</p>
        </div>
        @endif
    </div>
</div>
@else
<!-- Empty State for No Active Agents -->
<div class="bg-white rounded-lg shadow p-8 mb-8 text-center">
    <div class="max-w-md mx-auto">
        <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Ready to Get Started?</h3>
        <p class="text-gray-600 mb-6">Activate your first AI agent to see personalized analytics and insights based on their work.</p>
        <a href="{{ route('agents.index') }}" class="inline-block bg-lili-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-lili-700 transition">
            Browse AI Agents
        </a>
    </div>
</div>
@endif


@endsection
