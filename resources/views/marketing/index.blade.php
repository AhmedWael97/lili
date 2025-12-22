@extends('layouts.app')

@section('title', 'Marketing OS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Marketing OS</h1>
            <p class="text-gray-600">AI-Powered Marketing Strategy & Intelligence Platform</p>
        </div>

        @if(!$brand)
            <!-- Setup Brand CTA -->
            <div class="bg-white rounded-xl shadow-md p-8 mb-8 text-center">
                <svg class="w-16 h-16 text-purple-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome to Marketing OS!</h2>
                <p class="text-gray-600 mb-6">Let's start by setting up your brand profile</p>
                <a href="{{ route('marketing.os.setup-brand') }}" class="inline-block px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    Setup Brand Profile →
                </a>
            </div>
        @else
            <!-- Brand Overview -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $brand->name }}</h2>
                        <p class="text-gray-600">{{ $brand->industry }} • {{ $brand->country }} • Budget: ${{ number_format($brand->monthly_budget ?? 0) }}/mo</p>
                    </div>
                    <a href="{{ route('marketing.os.setup-brand') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Edit Brand
                    </a>
                </div>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <button onclick="generateStrategy()" class="p-6 bg-gradient-to-br from-purple-500 to-purple-700 text-white rounded-xl shadow-lg hover:shadow-xl transition text-left">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Generate Strategy</h3>
                    <p class="text-purple-100 text-sm">Create complete AI-powered marketing strategy</p>
                </button>

                <button onclick="showAddCompetitorModal()" class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition text-left border-2 border-transparent hover:border-purple-500">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-gray-900">Add Competitor</h3>
                    <p class="text-gray-600 text-sm">Analyze competitor's strategy & positioning</p>
                </button>

                <a href="{{ route('marketing.os.setup-brand') }}" class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition text-left border-2 border-transparent hover:border-green-500">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-gray-900">Brand Profile</h3>
                    <p class="text-gray-600 text-sm">Update business information & preferences</p>
                </a>
            </div>

            <!-- Competitors -->
            @if($brand->competitors->count() > 0)
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Competitors</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($brand->competitors as $competitor)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $competitor->name }}</h3>
                                <a href="{{ $competitor->website }}" target="_blank" class="text-sm text-blue-600 hover:underline">{{ $competitor->website }}</a>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick='viewCompetitorDetails(@json($competitor))' class="text-purple-600 hover:text-purple-800" title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteCompetitor({{ $competitor->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @if($competitor->strengths)
                            <div class="text-sm text-gray-600 mb-2">
                                <strong>Strengths:</strong> {{ implode(', ', array_slice($competitor->strengths, 0, 2)) }}@if(count($competitor->strengths) > 2)...@endif
                            </div>
                        @endif
                        <div class="text-xs text-gray-500">
                            Analyzed {{ $competitor->analyzed_at?->diffForHumans() ?? 'recently' }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Strategies -->
            @if($strategies->count() > 0)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Generated Strategies</h2>
                <div class="space-y-4">
                    @foreach($strategies as $strategy)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $strategy->name }}</h3>
                                <p class="text-sm text-gray-600">Generated {{ $strategy->generated_at?->diffForHumans() }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('marketing.os.view-strategy', $strategy->id) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                                    View Strategy
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif
    </div>
</div>

<!-- Add Competitor Modal -->
<div id="competitorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Add Competitor</h3>
        <form id="competitorForm" onsubmit="addCompetitor(event)">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Competitor Name</label>
                <input type="text" id="comp_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                <input type="url" id="comp_website" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <span id="compAddBtn">Add & Analyze</span>
                    <span id="compAddingBtn" class="hidden">Analyzing...</span>
                </button>
                <button type="button" onclick="hideCompetitorModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Competitor Details Modal -->
<div id="competitorDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900" id="detailCompName"></h3>
                <a id="detailCompWebsite" href="#" target="_blank" class="text-blue-600 hover:underline text-sm"></a>
            </div>
            <button onclick="hideCompetitorDetails()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Positioning -->
            <div class="bg-purple-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    Market Positioning
                </h4>
                <div class="text-gray-700" id="detailPositioning"></div>
            </div>

            <!-- Messaging -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Key Messaging
                </h4>
                <div class="text-gray-700" id="detailMessaging"></div>
            </div>

            <!-- Channels -->
            <div class="bg-green-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                    Marketing Channels
                </h4>
                <div id="detailChannels" class="flex flex-wrap gap-2 mt-2"></div>
            </div>

            <!-- SEO Data -->
            <div id="seoSection" class="bg-yellow-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    SEO Performance
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm" id="detailSEO"></div>
            </div>

            <!-- Strengths -->
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-green-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Strengths
                    </h4>
                    <ul id="detailStrengths" class="list-disc list-inside text-gray-700 space-y-1"></ul>
                </div>

                <div class="bg-red-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Weaknesses
                    </h4>
                    <ul id="detailWeaknesses" class="list-disc list-inside text-gray-700 space-y-1"></ul>
                </div>
            </div>

            <!-- Detailed Data Tabs -->
            <div class="border-t border-gray-200 mt-6 pt-6">
                <div class="flex space-x-4 border-b border-gray-200 mb-4">
                    <button onclick="showDetailTab('keywords')" id="tab-keywords" class="detail-tab px-4 py-2 font-medium text-gray-600 border-b-2 border-transparent hover:text-purple-600 hover:border-purple-600">
                        Keywords
                    </button>
                    <button onclick="showDetailTab('backlinks')" id="tab-backlinks" class="detail-tab px-4 py-2 font-medium text-gray-600 border-b-2 border-transparent hover:text-purple-600 hover:border-purple-600">
                        Backlinks
                    </button>
                    <button onclick="showDetailTab('social')" id="tab-social" class="detail-tab px-4 py-2 font-medium text-gray-600 border-b-2 border-transparent hover:text-purple-600 hover:border-purple-600">
                        Social Media
                    </button>
                </div>

                <!-- Keywords Tab -->
                <div id="content-keywords" class="detail-content hidden">
                    <div class="mb-4">
                        <h5 class="font-semibold text-gray-900 mb-2">Organic Keywords (Top 20)</h5>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Keyword</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Position</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Search Vol.</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Traffic</th>
                                    </tr>
                                </thead>
                                <tbody id="organicKeywordsTable" class="bg-white divide-y divide-gray-200 text-sm"></tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div>
                        <h5 class="font-semibold text-gray-900 mb-2">Paid Keywords (Top 10)</h5>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Keyword</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Position</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">CPC</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Traffic</th>
                                    </tr>
                                </thead>
                                <tbody id="paidKeywordsTable" class="bg-white divide-y divide-gray-200 text-sm"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Backlinks Tab -->
                <div id="content-backlinks" class="detail-content hidden">
                    <h5 class="font-semibold text-gray-900 mb-2">Top Backlinks (by Domain Rating)</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Source</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Anchor Text</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">DR</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Type</th>
                                </tr>
                            </thead>
                            <tbody id="backlinksTable" class="bg-white divide-y divide-gray-200 text-sm"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Social Tab -->
                <div id="content-social" class="detail-content hidden">
                    <h5 class="font-semibold text-gray-900 mb-3">Social Media Profiles</h5>
                    <div id="socialProfilesGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                </div>
            </div>

            <!-- Analysis Date -->
            <div class="text-center text-sm text-gray-500 pt-4 border-t border-gray-200">
                <span id="detailAnalyzedAt"></span>
            </div>
        </div>
    </div>
</div>

<script>
let currentCompetitorId = null;

function showAddCompetitorModal() {
    document.getElementById('competitorModal').classList.remove('hidden');
}

function hideCompetitorModal() {
    document.getElementById('competitorModal').classList.add('hidden');
    document.getElementById('competitorForm').reset();
}

function viewCompetitorDetails(competitor) {
    currentCompetitorId = competitor.id;
    
    document.getElementById('detailCompName').textContent = competitor.name;
    document.getElementById('detailCompWebsite').textContent = competitor.website;
    document.getElementById('detailCompWebsite').href = competitor.website;
    
    // Handle positioning - display statement and differentiation nicely
    const positioningDiv = document.getElementById('detailPositioning');
    positioningDiv.innerHTML = '';
    if (competitor.positioning) {
        if (typeof competitor.positioning === 'object' && !Array.isArray(competitor.positioning)) {
            if (competitor.positioning.statement) {
                const statement = document.createElement('p');
                statement.className = 'mb-2';
                statement.textContent = competitor.positioning.statement;
                positioningDiv.appendChild(statement);
            }
            if (competitor.positioning.differentiation) {
                const diff = document.createElement('p');
                diff.className = 'text-sm text-gray-600 italic';
                diff.innerHTML = '<strong>Differentiation:</strong> ' + competitor.positioning.differentiation;
                positioningDiv.appendChild(diff);
            }
            if (!competitor.positioning.statement && !competitor.positioning.differentiation) {
                positioningDiv.textContent = 'No positioning data available';
            }
        } else if (typeof competitor.positioning === 'string') {
            positioningDiv.textContent = competitor.positioning;
        } else if (Array.isArray(competitor.positioning)) {
            positioningDiv.textContent = competitor.positioning.join(', ');
        }
    } else {
        positioningDiv.textContent = 'No positioning data available';
    }
    
    // Handle messaging - display key messages and tone nicely
    const messagingDiv = document.getElementById('detailMessaging');
    messagingDiv.innerHTML = '';
    if (competitor.messaging) {
        if (typeof competitor.messaging === 'object' && !Array.isArray(competitor.messaging)) {
            if (competitor.messaging.key_messages && Array.isArray(competitor.messaging.key_messages)) {
                const messagesTitle = document.createElement('div');
                messagesTitle.className = 'font-medium mb-1';
                messagesTitle.textContent = 'Key Messages:';
                messagingDiv.appendChild(messagesTitle);
                
                const messagesList = document.createElement('ul');
                messagesList.className = 'list-disc list-inside mb-3';
                competitor.messaging.key_messages.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    messagesList.appendChild(li);
                });
                messagingDiv.appendChild(messagesList);
            }
            if (competitor.messaging.tone) {
                const tone = document.createElement('p');
                tone.className = 'text-sm text-gray-600';
                tone.innerHTML = '<strong>Tone:</strong> ' + competitor.messaging.tone;
                messagingDiv.appendChild(tone);
            }
            if (!competitor.messaging.key_messages && !competitor.messaging.tone) {
                messagingDiv.textContent = 'No messaging data available';
            }
        } else if (typeof competitor.messaging === 'string') {
            messagingDiv.textContent = competitor.messaging;
        } else if (Array.isArray(competitor.messaging)) {
            const list = document.createElement('ul');
            list.className = 'list-disc list-inside';
            competitor.messaging.forEach(msg => {
                const li = document.createElement('li');
                li.textContent = msg;
                list.appendChild(li);
            });
            messagingDiv.appendChild(list);
        }
    } else {
        messagingDiv.textContent = 'No messaging data available';
    }
    
    // Channels
    const channelsDiv = document.getElementById('detailChannels');
    channelsDiv.innerHTML = '';
    if (competitor.channels && competitor.channels.length > 0) {
        competitor.channels.forEach(channel => {
            const badge = document.createElement('span');
            badge.className = 'px-3 py-1 bg-white rounded-full text-sm font-medium text-gray-700 border border-gray-200';
            badge.textContent = channel;
            channelsDiv.appendChild(badge);
        });
    } else {
        channelsDiv.innerHTML = '<span class="text-gray-500">No channel data available</span>';
    }
    
    // SEO Data - Extract from nested structure
    const seoDiv = document.getElementById('detailSEO');
    seoDiv.innerHTML = '';
    if (competitor.seo_data && typeof competitor.seo_data === 'object') {
        const seoMetrics = [];
        
        // Extract from backlinks object (Ahrefs)
        if (competitor.seo_data.backlinks) {
            const bl = competitor.seo_data.backlinks;
            if (bl.domain_rating) seoMetrics.push({ label: 'Domain Rating', value: bl.domain_rating });
            if (bl.backlinks) seoMetrics.push({ label: 'Backlinks', value: bl.backlinks.toLocaleString() });
            if (bl.referring_domains) seoMetrics.push({ label: 'Referring Domains', value: bl.referring_domains.toLocaleString() });
        }
        
        // Extract from seo object (SEMrush)
        if (competitor.seo_data.seo) {
            const seo = competitor.seo_data.seo;
            if (seo.organic_keywords) seoMetrics.push({ label: 'Organic Keywords', value: seo.organic_keywords.toLocaleString() });
            if (seo.organic_traffic) seoMetrics.push({ label: 'Organic Traffic', value: seo.organic_traffic.toLocaleString() });
            if (seo.organic_cost) seoMetrics.push({ label: 'Organic Cost', value: '$' + seo.organic_cost.toLocaleString() });
            if (seo.adwords_keywords) seoMetrics.push({ label: 'Paid Keywords', value: seo.adwords_keywords.toLocaleString() });
            if (seo.adwords_traffic) seoMetrics.push({ label: 'Paid Traffic', value: seo.adwords_traffic.toLocaleString() });
            if (seo.adwords_cost) seoMetrics.push({ label: 'Paid Cost', value: '$' + seo.adwords_cost.toLocaleString() });
        }
        
        // Extract from traffic object (SimilarWeb)
        if (competitor.seo_data.traffic) {
            const traffic = competitor.seo_data.traffic;
            if (traffic.visits) seoMetrics.push({ label: 'Monthly Visits', value: traffic.visits.toLocaleString() });
            if (traffic.bounce_rate) seoMetrics.push({ label: 'Bounce Rate', value: traffic.bounce_rate + '%' });
            if (traffic.pages_per_visit) seoMetrics.push({ label: 'Pages/Visit', value: traffic.pages_per_visit });
            if (traffic.avg_visit_duration) seoMetrics.push({ label: 'Avg Duration', value: traffic.avg_visit_duration + 's' });
        }
        
        if (seoMetrics.length > 0) {
            seoMetrics.forEach(metric => {
                const item = document.createElement('div');
                item.innerHTML = `<div class="font-medium text-gray-900">${metric.value}</div><div class="text-xs text-gray-600">${metric.label}</div>`;
                seoDiv.appendChild(item);
            });
        } else {
            seoDiv.innerHTML = '<div class="col-span-full text-gray-500">No SEO data available</div>';
        }
    } else {
        seoDiv.innerHTML = '<div class="col-span-full text-gray-500">No SEO data available</div>';
    }
    
    // Strengths
    const strengthsList = document.getElementById('detailStrengths');
    strengthsList.innerHTML = '';
    if (competitor.strengths && competitor.strengths.length > 0) {
        competitor.strengths.forEach(strength => {
            const li = document.createElement('li');
            li.textContent = strength;
            li.className = 'text-sm';
            strengthsList.appendChild(li);
        });
    } else {
        strengthsList.innerHTML = '<li class="text-gray-500 text-sm">No strengths data available</li>';
    }
    
    // Weaknesses
    const weaknessesList = document.getElementById('detailWeaknesses');
    weaknessesList.innerHTML = '';
    if (competitor.weaknesses && competitor.weaknesses.length > 0) {
        competitor.weaknesses.forEach(weakness => {
            const li = document.createElement('li');
            li.textContent = weakness;
            li.className = 'text-sm';
            weaknessesList.appendChild(li);
        });
    } else {
        weaknessesList.innerHTML = '<li class="text-gray-500 text-sm">No weaknesses data available</li>';
    }
    
    // Analyzed date
    document.getElementById('detailAnalyzedAt').textContent = competitor.analyzed_at 
        ? `Analyzed on ${new Date(competitor.analyzed_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}`
        : 'Analysis date not available';
    
    // Show first tab by default
    showDetailTab('keywords');
    
    document.getElementById('competitorDetailsModal').classList.remove('hidden');
}

function hideCompetitorDetails() {
    document.getElementById('competitorDetailsModal').classList.add('hidden');
    currentCompetitorId = null;
}

function showDetailTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.detail-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.detail-tab').forEach(el => {
        el.classList.remove('text-purple-600', 'border-purple-600');
        el.classList.add('text-gray-600', 'border-transparent');
    });
    
    // Show selected tab
    document.getElementById('content-' + tab).classList.remove('hidden');
    const tabBtn = document.getElementById('tab-' + tab);
    tabBtn.classList.remove('text-gray-600', 'border-transparent');
    tabBtn.classList.add('text-purple-600', 'border-purple-600');
    
    // Load data if not already loaded
    if (tab === 'keywords') {
        loadKeywords();
    } else if (tab === 'backlinks') {
        loadBacklinks();
    } else if (tab === 'social') {
        loadSocialProfiles();
    }
}

async function loadKeywords() {
    if (!currentCompetitorId) return;
    
    try {
        const response = await fetch(`/marketing/os/competitor/${currentCompetitorId}/keywords`);
        const data = await response.json();
        
        if (data.success) {
            // Organic keywords
            const organicTable = document.getElementById('organicKeywordsTable');
            organicTable.innerHTML = '';
            
            if (data.organic && data.organic.length > 0) {
                data.organic.forEach(kw => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2">${kw.keyword || '-'}</td>
                        <td class="px-4 py-2">${kw.position || '-'}</td>
                        <td class="px-4 py-2">${kw.search_volume ? kw.search_volume.toLocaleString() : '-'}</td>
                        <td class="px-4 py-2">${kw.traffic ? kw.traffic.toLocaleString() : '-'}</td>
                    `;
                    organicTable.appendChild(row);
                });
            } else {
                organicTable.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">No organic keywords data</td></tr>';
            }
            
            // Paid keywords
            const paidTable = document.getElementById('paidKeywordsTable');
            paidTable.innerHTML = '';
            
            if (data.paid && data.paid.length > 0) {
                data.paid.forEach(kw => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2">${kw.keyword || '-'}</td>
                        <td class="px-4 py-2">${kw.position || '-'}</td>
                        <td class="px-4 py-2">${kw.cpc ? '$' + parseFloat(kw.cpc).toFixed(2) : '-'}</td>
                        <td class="px-4 py-2">${kw.traffic ? kw.traffic.toLocaleString() : '-'}</td>
                    `;
                    paidTable.appendChild(row);
                });
            } else {
                paidTable.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">No paid keywords data</td></tr>';
            }
        }
    } catch (error) {
        console.error('Error loading keywords:', error);
    }
}

async function loadBacklinks() {
    if (!currentCompetitorId) return;
    
    try {
        const response = await fetch(`/marketing/os/competitor/${currentCompetitorId}/backlinks`);
        const data = await response.json();
        
        if (data.success) {
            const table = document.getElementById('backlinksTable');
            table.innerHTML = '';
            
            if (data.backlinks && data.backlinks.length > 0) {
                data.backlinks.forEach(bl => {
                    const sourceDomain = new URL(bl.source_url).hostname;
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2"><a href="${bl.source_url}" target="_blank" class="text-blue-600 hover:underline">${sourceDomain}</a></td>
                        <td class="px-4 py-2">${bl.anchor_text || '-'}</td>
                        <td class="px-4 py-2 font-medium">${bl.domain_rating || '-'}</td>
                        <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded ${bl.link_type === 'dofollow' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">${bl.link_type || '-'}</span></td>
                    `;
                    table.appendChild(row);
                });
            } else {
                table.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">No backlinks data</td></tr>';
            }
        }
    } catch (error) {
        console.error('Error loading backlinks:', error);
    }
}

async function loadSocialProfiles() {
    if (!currentCompetitorId) return;
    
    try {
        const response = await fetch(`/marketing/os/competitor/${currentCompetitorId}/social`);
        const data = await response.json();
        
        if (data.success) {
            const grid = document.getElementById('socialProfilesGrid');
            grid.innerHTML = '';
            
            if (data.profiles && data.profiles.length > 0) {
                data.profiles.forEach(profile => {
                    const card = document.createElement('div');
                    card.className = 'border border-gray-200 rounded-lg p-4';
                    card.innerHTML = `
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                ${profile.platform.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <h6 class="font-semibold text-gray-900 capitalize">${profile.platform}</h6>
                                <a href="${profile.profile_url}" target="_blank" class="text-xs text-blue-600 hover:underline">@${profile.username}</a>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-gray-600">Followers:</span> <span class="font-medium">${profile.followers ? profile.followers.toLocaleString() : '-'}</span></div>
                            <div><span class="text-gray-600">Posts:</span> <span class="font-medium">${profile.posts_count ? profile.posts_count.toLocaleString() : '-'}</span></div>
                            <div><span class="text-gray-600">Engagement:</span> <span class="font-medium">${profile.engagement_rate ? profile.engagement_rate + '%' : '-'}</span></div>
                            <div><span class="text-gray-600">Frequency:</span> <span class="font-medium">${profile.posting_frequency || '-'}</span></div>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            } else {
                grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-4">No social media data</div>';
            }
        }
    } catch (error) {
        console.error('Error loading social profiles:', error);
    }
}

async function addCompetitor(event) {
    event.preventDefault();
    
    const addBtn = document.getElementById('compAddBtn');
    const addingBtn = document.getElementById('compAddingBtn');
    addBtn.classList.add('hidden');
    addingBtn.classList.remove('hidden');

    try {
        const response = await fetch('{{ route("marketing.os.add-competitor") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: document.getElementById('comp_name').value,
                website: document.getElementById('comp_website').value
            })
        });

        const data = await response.json();

        if (data.success) {
            alert('Competitor analyzed successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error adding competitor: ' + error.message);
    } finally {
        addBtn.classList.remove('hidden');
        addingBtn.classList.add('hidden');
    }
}

async function deleteCompetitor(id) {
    if (!confirm('Delete this competitor?')) return;

    try {
        const response = await fetch(`/marketing/os/competitor/${id}`, {
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
        alert('Error: ' + error.message);
    }
}

async function generateStrategy() {
    if (!confirm('Generate a complete AI-powered marketing strategy? This may take 1-2 minutes.')) return;

    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<div class="flex items-center justify-center"><svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generating Strategy...</div>';

    try {
        const response = await fetch('{{ route("marketing.os.generate-strategy") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success) {
            alert('Strategy generated successfully!');
            window.location.href = `/marketing/os/strategy/${data.strategy_id}`;
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mb-4"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></div><h3 class="font-semibold text-lg mb-2">Generate Strategy</h3><p class="text-purple-100 text-sm">Create complete AI-powered marketing strategy</p>';
        }
    } catch (error) {
        alert('Error: ' + error.message);
        btn.disabled = false;
    }
}
</script>
@endsection
