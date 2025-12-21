@extends('dashboard.layout')

@section('title', 'AI Agents Marketplace')
@section('page-title', 'AI Agents')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ 
    selectedCategory: 'all',
    searchQuery: '',
    agents: {{ json_encode($availableAgents->map(function($agent) use ($activeAgents) {
        return [
            'id' => $agent->id,
            'code' => $agent->code,
            'name' => $agent->name,
            'icon' => $agent->icon,
            'color' => $agent->color,
            'category' => $agent->category,
            'description' => $agent->description,
            'features' => $agent->features,
            'isActive' => $activeAgents->where('agent_type_id', $agent->id)->first() ? true : false,
            'activatedAt' => $activeAgents->where('agent_type_id', $agent->id)->first()?->activated_at?->diffForHumans()
        ];
    })->values()) }},
    get filteredAgents() {
        let filtered = this.agents;
        if (this.selectedCategory !== 'all') {
            filtered = filtered.filter(agent => agent.category.toLowerCase() === this.selectedCategory.toLowerCase());
        }
        if (this.searchQuery) {
            filtered = filtered.filter(agent => 
                agent.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                agent.description.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        }
        return filtered;
    },
    get categories() {
        return ['all', ...new Set(this.agents.map(a => a.category.toLowerCase()))];
    }
}">

    <!-- Header Section with Stats -->
    <div class="bg-gradient-to-r from-lili-600 to-purple-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-2">AI Agents Marketplace</h1>
                <p class="text-white text-opacity-90 text-lg">Build your virtual AI company with specialized agents</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl text-lili-600 font-bold">{{ $activeAgents->count() }}</div>
                    <div class="text-sm text-lili-600 text-opacity-90">Active Agents</div>
                </div>
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-3xl text-purple-600 font-bold">
                        @if($totalSlots === -1)
                            ∞
                        @else
                            {{ $availableSlots }}
                        @endif
                    </div>
                    <div class="text-sm text-purple-600 text-opacity-90">
                        {{ $totalSlots === -1 ? 'Unlimited' : 'Available' }} Slots
                    </div>
                </div>
            </div>
        </div>
        
        @if($activeAgents->count() > 0)
        <div class="mt-6 flex items-center justify-between bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>You have active agents ready to work</span>
            </div>
            <a href="{{ route('agents.dashboard') }}" class="bg-white text-lili-600 hover:bg-opacity-90 font-bold py-2 px-6 rounded-lg transition">
                View Dashboard →
            </a>
        </div>
        @endif
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg mb-6 flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-green-400 hover:text-green-600">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
            </svg>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-6 flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="text-red-400 hover:text-red-600">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
            </svg>
        </button>
    </div>
    @endif

    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Search agents by name or description..." 
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lili-500 focus:border-transparent">
                </div>
            </div>
            
            <!-- Category Filter Tabs -->
            <div class="flex gap-2 overflow-x-auto pb-2 lg:pb-0">
                <template x-for="cat in categories" :key="cat">
                    <button @click="selectedCategory = cat"
                            :class="selectedCategory === cat ? 'bg-lili-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg font-medium capitalize whitespace-nowrap transition"
                            x-text="cat">
                    </button>
                </template>
            </div>
        </div>
        
        <!-- Results Count -->
        <div class="mt-4 text-sm text-gray-600">
            Showing <span x-text="filteredAgents.length"></span> of <span x-text="agents.length"></span> agents
        </div>
    </div>

    <!-- Agents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="agent in filteredAgents" :key="agent.id">
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border-2"
                 :class="agent.isActive ? 'border-green-500' : 'border-gray-100'">
                
                <!-- Card Header -->
                <div class="p-6 pb-4" :style="`background: linear-gradient(135deg, ${agent.color}10 0%, ${agent.color}05 100%)`">
                    <div class="flex items-start justify-between mb-4">
                        <div class="text-5xl" x-text="agent.icon"></div>
                        <template x-if="agent.isActive">
                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                Active
                            </span>
                        </template>
                        <template x-if="!agent.isActive">
                            <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full capitalize" x-text="agent.category"></span>
                        </template>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2" :style="`color: ${agent.color}`" x-text="agent.name"></h3>
                    <p class="text-gray-600 text-sm leading-relaxed" x-text="agent.description"></p>
                </div>
                
                <!-- Features -->
                <div class="px-6 py-4 bg-gray-50">
                    <p class="text-xs font-bold text-gray-700 uppercase tracking-wide mb-3">Key Features</p>
                    <ul class="space-y-2">
                        <template x-for="(feature, index) in agent.features.slice(0, 3)" :key="index">
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                </svg>
                                <span x-text="feature"></span>
                            </li>
                        </template>
                        <template x-if="agent.features.length > 3">
                            <li class="text-xs text-gray-500 ml-6">
                                <span x-text="`+ ${agent.features.length - 3} more features`"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                
                <!-- Actions -->
                <div class="p-6 pt-4">
                    <template x-if="agent.isActive">
                        <div>
                            <div class="flex gap-2 mb-3">
                                <form :action="`{{ url('agents') }}/${agent.code}/deactivate`" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Deactivate
                                    </button>
                                </form>
                                <a :href="`{{ url('agents') }}/${agent.code}/analytics`" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Analytics
                                </a>
                            </div>
                            <p class="text-xs text-green-600 text-center flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                                </svg>
                                <span x-text="`Active since ${agent.activatedAt}`"></span>
                            </p>
                        </div>
                    </template>
                    <template x-if="!agent.isActive">
                        <div>
                            <form :action="`{{ url('agents') }}/${agent.code}/activate`" method="POST">
                                @csrf
                                <button type="submit" 
                                        :disabled="{{ $availableSlots === 0 && $totalSlots !== -1 ? 'true' : 'false' }}"
                                        class="w-full bg-gradient-to-r from-lili-600 to-purple-600 hover:from-lili-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Activate Agent
                                </button>
                            </form>
                            @if($availableSlots === 0 && $totalSlots !== -1)
                            <p class="text-xs text-amber-600 mt-3 text-center flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                                </svg>
                                No slots available - Upgrade your plan
                            </p>
                            @endif
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
    
    <!-- Empty State -->
    <div x-show="filteredAgents.length === 0" x-cloak class="text-center py-16">
        <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No agents found</h3>
        <p class="text-gray-600">Try adjusting your search or filter criteria</p>
    </div>
</div>
@endsection
