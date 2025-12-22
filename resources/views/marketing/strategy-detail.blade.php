@extends('layouts.app')

@section('title', 'Strategy - ' . $strategy->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <a href="{{ route('marketing.os.index') }}" class="text-purple-600 hover:underline">← Back to Dashboard</a>
        </div>

        <div class="bg-white rounded-xl shadow-md p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $strategy->name }}</h1>
            <p class="text-gray-600">Generated {{ $strategy->generated_at->format('M d, Y') }}</p>
        </div>

        <!-- SWOT Analysis -->
        @if($strategy->swot_analysis)
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">SWOT Analysis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border-2 border-green-200 rounded-lg p-4 bg-green-50">
                    <h3 class="font-bold text-green-800 mb-2">Strengths</h3>
                    <ul class="space-y-1">
                        @foreach($strategy->swot_analysis['strengths'] ?? [] as $item)
                        <li class="text-sm">• {{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="border-2 border-red-200 rounded-lg p-4 bg-red-50">
                    <h3 class="font-bold text-red-800 mb-2">Weaknesses</h3>
                    <ul class="space-y-1">
                        @foreach($strategy->swot_analysis['weaknesses'] ?? [] as $item)
                        <li class="text-sm">• {{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="border-2 border-blue-200 rounded-lg p-4 bg-blue-50">
                    <h3 class="font-bold text-blue-800 mb-2">Opportunities</h3>
                    <ul class="space-y-1">
                        @foreach($strategy->swot_analysis['opportunities'] ?? [] as $item)
                        <li class="text-sm">• {{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="border-2 border-orange-200 rounded-lg p-4 bg-orange-50">
                    <h3 class="font-bold text-orange-800 mb-2">Threats</h3>
                    <ul class="space-y-1">
                        @foreach($strategy->swot_analysis['threats'] ?? [] as $item)
                        <li class="text-sm">• {{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Channel Strategy -->
        @if($strategy->channel_strategy)
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Channel Strategy</h2>
            <div class="space-y-4">
                @foreach($strategy->channel_strategy['primary_channels'] ?? [] as $channel)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">{{ $channel['channel'] }}</h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">{{ $channel['priority'] }}</span>
                    </div>
                    <p class="text-gray-600 text-sm">{{ $channel['rationale'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Budget Allocation -->
        @if($strategy->budget_allocation)
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Budget Allocation</h2>
            @if(isset($strategy->budget_allocation['rationale']))
            <p class="text-gray-600 mb-4">{{ $strategy->budget_allocation['rationale'] }}</p>
            @endif
            <div class="space-y-2">
                @foreach($strategy->budget_allocation['breakdown'] ?? [] as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">{{ $item['channel'] }}</span>
                    <div class="text-right">
                        <span class="font-bold text-purple-600">{{ $item['percentage'] }}%</span>
                        <span class="text-gray-600 text-sm ml-2">${{ number_format($item['amount']) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Execution Priorities -->
        @if($strategy->execution_priorities)
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Execution Roadmap</h2>
            <div class="space-y-3">
                @foreach($strategy->execution_priorities as $priority)
                <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg">
                    <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                        {{ $priority['priority'] }}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $priority['action'] }}</h3>
                        <p class="text-sm text-gray-600">{{ $priority['timeline'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
