@extends('dashboard.layout')

@section('title', 'Agent Dashboard')
@section('page-title', 'Agent Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto">
            <!-- Agent Slots Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Active Agents</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                @if($totalSlots === -1)
                                    You have <span class="font-bold text-blue-600">{{ $usedSlots }}</span> agent(s) active (unlimited)
                                @else
                                    Using <span class="font-bold text-blue-600">{{ $usedSlots }}</span> of <span class="font-bold">{{ $totalSlots }}</span> agent slots
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('agents.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Manage Agents
                        </a>
                    </div>

                    @if($totalSlots !== -1)
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($usedSlots / $totalSlots) * 100 }}%"></div>
                        </div>
                    @endif
                </div>
            </div>

            @if($activeAgents->isEmpty())
                <!-- No Agents Active -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Active Agents</h3>
                        <p class="text-gray-600 mb-6">
                            Activate AI agents to help you with marketing, development, QA, accounting, and customer service.
                        </p>
                        <a href="{{ route('agents.index') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                            Browse Agent Marketplace
                        </a>
                    </div>
                </div>
            @else
                <!-- Active Agents Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    @foreach($activeAgents as $userAgent)
                        @php
                            $agentType = $userAgent->agentType;
                        @endphp
                        
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4" style="border-color: {{ $agentType->color }}">
                            <div class="p-6">
                                <!-- Agent Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="text-4xl mr-3">{{ $agentType->icon }}</div>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900">{{ $agentType->name }}</h4>
                                            <span class="inline-block bg-gray-200 rounded-full px-2 py-1 text-xs font-semibold text-gray-700">
                                                {{ ucfirst($agentType->category) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agent Stats -->
                                <div class="grid grid-cols-2 gap-4 mb-4 py-4 border-t border-b border-gray-200">
                                    <div>
                                        <p class="text-xs text-gray-600">Interactions</p>
                                        <p class="text-xl font-bold text-gray-900">{{ $userAgent->interaction_count }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Last Used</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $userAgent->last_used_at ? $userAgent->last_used_at->diffForHumans() : 'Never' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="space-y-2">
                                    @if($agentType->code === 'marketing')
                                        <a href="{{ route('ai-studio.index') }}" class="block w-full bg-blue-500 hover:bg-blue-700 text-white text-center font-bold py-2 px-4 rounded text-sm">
                                            Use Agent
                                        </a>
                                    @else
                                        <button class="block w-full bg-gray-300 text-gray-600 text-center font-bold py-2 px-4 rounded text-sm cursor-not-allowed" disabled>
                                            Coming Soon
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('agents.analytics', $agentType->code) }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-center font-bold py-2 px-4 rounded text-sm">
                                        View Analytics
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                        
                        @if($recentInteractions->flatten()->isEmpty())
                            <p class="text-center text-gray-500 py-8">No recent interactions yet. Start using your agents!</p>
                        @else
                            <div class="space-y-4">
                                @foreach($activeAgents as $userAgent)
                                    @if(isset($recentInteractions[$userAgent->agent_type_id]) && $recentInteractions[$userAgent->agent_type_id]->count() > 0)
                                        <div class="border-l-4 pl-4" style="border-color: {{ $userAgent->agentType->color }}">
                                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                                <span class="mr-2">{{ $userAgent->agentType->icon }}</span>
                                                {{ $userAgent->agentType->name }}
                                            </h4>
                                            
                                            <div class="space-y-2">
                                                @foreach($recentInteractions[$userAgent->agent_type_id] as $interaction)
                                                    <div class="bg-gray-50 rounded p-3">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center">
                                                                @if($interaction->success)
                                                                    <span class="text-green-500 mr-2">✓</span>
                                                                @else
                                                                    <span class="text-red-500 mr-2">✗</span>
                                                                @endif
                                                                <span class="text-sm text-gray-700">{{ $interaction->action }}</span>
                                                            </div>
                                                            <span class="text-xs text-gray-500">{{ $interaction->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        
                                                        @if($interaction->tokens_used)
                                                            <p class="text-xs text-gray-500 mt-1">
                                                                {{ $interaction->tokens_used }} tokens • {{ $interaction->execution_time_ms }}ms
                                                            </p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
@endsection
