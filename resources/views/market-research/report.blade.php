@extends('layouts.app')

@section('header', 'Market Research Report')

@section('content')
<div class="space-y-6">
    <!-- Loading State -->
    <div id="loading-container" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-lili-600 mx-auto mb-4"></div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Analyzing Market...</h2>
        <p class="text-gray-600 mb-6">Our AI agents are researching competitors, analyzing social media, and generating insights.</p>
        <div class="max-w-md mx-auto space-y-3">
            <div class="flex items-center gap-3">
                <div id="step1" class="flex items-center gap-2 text-gray-400">
                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Finding Competitors</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div id="step2" class="flex items-center gap-2 text-gray-400">
                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Analyzing Social Media</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div id="step3" class="flex items-center gap-2 text-gray-400">
                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Generating Market Analysis</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div id="step4" class="flex items-center gap-2 text-gray-400">
                    <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Creating Report</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Container (Hidden Initially) -->
    <div id="report-container" class="hidden space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-lili-500 to-purple-600 rounded-xl shadow-lg p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2" id="report-title">Market Research Report</h1>
                    <p class="text-indigo-100" id="report-subtitle">Complete market analysis and competitive intelligence</p>
                </div>
                <button onclick="window.print()" class="px-4 py-2 bg-white text-indigo-600 rounded-lg font-medium hover:bg-indigo-50 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Executive Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Executive Summary
            </h2>
            <div id="executive-summary" class="prose max-w-none text-gray-700"></div>
        </div>

        <!-- Market Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Market Overview</h3>
                <div id="market-overview" class="space-y-4"></div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Competition Level</h3>
                <div id="competition-level" class="space-y-4"></div>
            </div>
        </div>

        <!-- Opportunities & Threats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Opportunities
                </h3>
                <ul id="opportunities" class="space-y-2"></ul>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Threats
                </h3>
                <ul id="threats" class="space-y-2"></ul>
            </div>
        </div>

        <!-- Competitors -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Top Competitors
            </h2>
            <div id="competitors" class="space-y-4"></div>
        </div>

        <!-- Recommendations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                Strategic Recommendations
            </h2>
            <div id="recommendations" class="space-y-4"></div>
        </div>

        <!-- Action Plan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                30-Day Action Plan
            </h2>
            <div id="action-plan" class="space-y-6"></div>
        </div>
    </div>

    <!-- Error State -->
    <div id="error-container" class="hidden bg-white rounded-xl shadow-sm border border-red-200 p-12 text-center">
        <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Research Failed</h2>
        <p class="text-gray-600 mb-6">There was an error processing your research request.</p>
        <a href="{{ route('market-research.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700">
            Try Again
        </a>
    </div>
</div>

@push('scripts')
<script>
const requestId = {{ $requestId }};
let pollInterval;

document.addEventListener('DOMContentLoaded', function() {
    checkStatus();
    pollInterval = setInterval(checkStatus, 5000); // Poll every 5 seconds
});

async function checkStatus() {
    try {
        const response = await fetch(`/api/market-research/${requestId}/status`);
        const data = await response.json();
        
        if (data.success) {
            const status = data.data.status;
            
            if (status === 'completed') {
                clearInterval(pollInterval);
                loadReport();
            } else if (status === 'failed') {
                clearInterval(pollInterval);
                showError();
            }
            // Update progress steps based on status
            // You can add more sophisticated progress tracking here
        }
    } catch (error) {
        console.error('Error checking status:', error);
    }
}

async function loadReport() {
    try {
        const response = await fetch(`/api/market-research/${requestId}/report`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        if (data.success && data.data) {
            const report = data.data;
            
            // Update title
            document.getElementById('report-title').textContent = report.business_idea;
            document.getElementById('report-subtitle').textContent = `${report.location} • Generated ${new Date(report.completed_at || report.created_at).toLocaleDateString()}`;
            
            // Executive Summary
            const executiveSummary = report.executive_summary || 'No summary available';
            const summaryTruncated = executiveSummary.length > 500 ? executiveSummary.substring(0, 500) + '...' : executiveSummary;
            document.getElementById('executive-summary').innerHTML = `<p class="whitespace-pre-line">${summaryTruncated}</p>${executiveSummary.length > 500 ? '<button onclick="this.previousElementSibling.textContent = \'' + executiveSummary.replace(/'/g, "\\'") + '\'; this.remove();" class="text-indigo-600 hover:underline mt-2 text-sm font-medium">Read More →</button>' : ''}`;
            
            // Market Overview
            if (report.market_analysis) {
                const ma = report.market_analysis;
                
                // Format target audience properly
                let targetAudienceText = 'Analysis in progress';
                if (ma.target_audience) {
                    if (typeof ma.target_audience === 'object' && ma.target_audience !== null) {
                        if (ma.target_audience.primary) {
                            targetAudienceText = ma.target_audience.primary;
                        } else if (Array.isArray(ma.target_audience)) {
                            targetAudienceText = ma.target_audience.join(', ');
                        } else {
                            targetAudienceText = Object.values(ma.target_audience).filter(v => typeof v === 'string').join(', ') || JSON.stringify(ma.target_audience);
                        }
                    } else {
                        targetAudienceText = String(ma.target_audience);
                    }
                }
                
                document.getElementById('market-overview').innerHTML = `
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600">Market Size</span>
                        <span class="font-semibold text-gray-900">${ma.market_size_estimate && ma.market_size_estimate !== 'Analysis in progress' ? ma.market_size_estimate : '<span class="text-yellow-600">Analyzing...</span>'}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600">Growth Rate</span>
                        <span class="font-semibold ${ma.growth_rate && ma.growth_rate > 0 ? 'text-green-600' : 'text-yellow-600'}">${ma.growth_rate && ma.growth_rate > 0 ? ma.growth_rate + '%' : 'Analyzing...'}</span>
                    </div>
                    <div class="py-3">
                        <span class="text-gray-600 block mb-2">Target Audience</span>
                        <span class="font-semibold text-gray-900 block">${targetAudienceText}</span>
                    </div>
                    ${ma.ai_analysis ? `
                    <div class="pt-3 border-t border-gray-200">
                        <span class="text-gray-600 block mb-2 text-sm">AI Analysis</span>
                        <p class="text-gray-700 text-sm">${ma.ai_analysis}</p>
                    </div>
                    ` : ''}
                `;
                
                document.getElementById('competition-level').innerHTML = `
                    <div class="text-center py-4">
                        <div class="text-4xl font-bold mb-2 ${getCompetitionColor(ma.competition_level)}">
                            ${(ma.competition_level || 'Medium').toUpperCase()}
                        </div>
                        <p class="text-gray-600">Competition Level</p>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        ${Array.isArray(ma.barriers_to_entry) && ma.barriers_to_entry.length > 0 ? ma.barriers_to_entry.join('. ') : (ma.barriers_to_entry || 'Market entry analysis pending')}
                    </div>
                `;
                
                // Opportunities
                if (ma.opportunities) {
                    const opps = Array.isArray(ma.opportunities) ? ma.opportunities : [];
                    if (opps.length > 0) {
                        document.getElementById('opportunities').innerHTML = opps.map((opp, idx) => {
                            let oppTitle, oppDesc, oppPriority;
                            if (typeof opp === 'string') {
                                oppTitle = opp;
                            } else if (typeof opp === 'object' && opp !== null) {
                                oppTitle = opp.opportunity || opp.title || opp.name || 'Market Opportunity';
                                oppDesc = opp.description || opp.details || null;
                                oppPriority = opp.priority || opp.impact || null;
                            } else {
                                oppTitle = String(opp);
                            }
                            return `
                                <li class="bg-green-50 rounded-lg p-3 border-l-4 border-green-500">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900">${idx + 1}. ${oppTitle}</div>
                                            ${oppDesc ? `<div class="text-sm text-gray-700 mt-1">${oppDesc}</div>` : ''}
                                            ${oppPriority ? `<span class="inline-block mt-2 px-2 py-1 bg-green-200 text-green-800 text-xs font-medium rounded">${oppPriority}</span>` : ''}
                                        </div>
                                    </div>
                                </li>
                            `;
                        }).join('');
                    } else {
                        document.getElementById('opportunities').innerHTML = '<li class="text-gray-500 text-sm">No specific opportunities identified yet. Market analysis in progress.</li>';
                    }
                } else {
                    document.getElementById('opportunities').innerHTML = '<li class="text-gray-500 text-sm">Market opportunities are being analyzed...</li>';
                }
                
                // Threats
                if (ma.threats) {
                    const threats = Array.isArray(ma.threats) ? ma.threats : [];
                    if (threats.length > 0) {
                        document.getElementById('threats').innerHTML = threats.map((threat, idx) => {
                            let threatTitle, threatDesc, threatSeverity;
                            if (typeof threat === 'string') {
                                threatTitle = threat;
                            } else if (typeof threat === 'object' && threat !== null) {
                                threatTitle = threat.threat || threat.title || threat.name || 'Market Threat';
                                threatDesc = threat.description || threat.details || null;
                                threatSeverity = threat.severity || threat.impact || null;
                            } else {
                                threatTitle = String(threat);
                            }
                            return `
                                <li class="bg-red-50 rounded-lg p-3 border-l-4 border-red-500">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900">${idx + 1}. ${threatTitle}</div>
                                            ${threatDesc ? `<div class="text-sm text-gray-700 mt-1">${threatDesc}</div>` : ''}
                                            ${threatSeverity ? `<span class="inline-block mt-2 px-2 py-1 bg-red-200 text-red-800 text-xs font-medium rounded">${threatSeverity}</span>` : ''}
                                        </div>
                                    </div>
                                </li>
                            `;
                        }).join('');
                    } else {
                        document.getElementById('threats').innerHTML = '<li class="text-gray-500 text-sm">No specific threats identified yet. Market analysis in progress.</li>';
                    }
                } else {
                    document.getElementById('threats').innerHTML = '<li class="text-gray-500 text-sm">Market threats are being analyzed...</li>';
                }
            }
            
            // Competitors
            if (report.competitors && report.competitors.length > 0) {
                console.log('Total competitors:', report.competitors.length);
                console.log('First competitor:', report.competitors);
                
                // Get unique competitors by business name and filter those with social media
                const seenNames = new Set();
                const uniqueCompetitors = report.competitors
                    .filter(comp => {
                        // Skip duplicates
                        if (seenNames.has(comp.business_name)) {
                            return false;
                        }
                        seenNames.add(comp.business_name);
                        
                        // Only show competitors with at least one social media handle
                        return comp.social_media && (
                            comp.social_media.facebook || 
                            comp.social_media.instagram || 
                            comp.social_media.twitter || 
                            comp.social_media.linkedin
                        );
                    });
                
                console.log('Unique competitors with social media:', uniqueCompetitors.length);
                
                // Parse intelligence if it's a JSON string for all competitors
                uniqueCompetitors.forEach(comp => {
                    if (comp.intelligence && typeof comp.intelligence === 'string') {
                        try {
                            comp.intelligence = JSON.parse(comp.intelligence);
                        } catch (e) {
                            console.error('Error parsing intelligence:', e);
                            comp.intelligence = null;
                        }
                    }
                });
                
                document.getElementById('competitors').innerHTML = uniqueCompetitors.map((comp, index) => `
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900">${index + 1}. ${comp.business_name}</h3>
                                ${comp.website ? `<a href="${comp.website}" target="_blank" class="text-sm text-indigo-600 hover:underline break-all">${comp.website}</a>` : '<span class="text-sm text-gray-500">No website available</span>'}
                                ${comp.phone ? `<div class="text-sm text-gray-600 mt-1">📞 ${comp.phone}</div>` : ''}
                                ${comp.address ? `<div class="text-sm text-gray-600 mt-1">📍 ${comp.address}</div>` : ''}
                                ${comp.category ? `<div class="text-xs text-gray-500 mt-1">Category: ${comp.category}</div>` : ''}
                                ${comp.social_media && (comp.social_media.facebook || comp.social_media.instagram || comp.social_media.twitter || comp.social_media.linkedin) ? `
                                <div class="mt-2 flex flex-wrap gap-3 text-xs">
                                    ${comp.social_media.facebook ? `<span class="text-blue-600">📘 @${comp.social_media.facebook}</span>` : ''}
                                    ${comp.social_media.instagram ? `<span class="text-pink-600">📸 @${comp.social_media.instagram}</span>` : ''}
                                    ${comp.social_media.twitter ? `<span class="text-blue-400">🐦 @${comp.social_media.twitter}</span>` : ''}
                                    ${comp.social_media.linkedin ? `<a href="${comp.social_media.linkedin}" target="_blank" class="text-blue-700 hover:underline">💼 LinkedIn</a>` : ''}
                                </div>
                                ` : '<div class="mt-2 text-xs text-gray-400">Social media profiles not available</div>'}
                            </div>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full ml-4 flex-shrink-0">
                                Score: ${comp.relevance_score || 0}
                            </span>
                        </div>
                        
                        ${comp.metrics && typeof comp.metrics === 'object' && !Array.isArray(comp.metrics) && Object.keys(comp.metrics).length > 0 ? `
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Social Media Metrics</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                ${comp.metrics.facebook_followers ? `
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-500 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path>
                                    </svg>
                                    <div class="text-sm font-medium text-gray-900">${comp.metrics.facebook_followers.toLocaleString()}</div>
                                    <div class="text-xs text-gray-500">Followers</div>
                                    ${comp.metrics.facebook_engagement ? `<div class="text-xs text-green-600 mt-1">${comp.metrics.facebook_engagement}% eng.</div>` : ''}
                                    ${comp.metrics.facebook_posts ? `<div class="text-xs text-gray-500">${comp.metrics.facebook_posts} posts</div>` : ''}
                                </div>` : ''}
                                ${comp.metrics.instagram_followers ? `
                                <div class="text-center p-3 bg-pink-50 rounded-lg">
                                    <svg class="w-5 h-5 text-pink-500 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                                    </svg>
                                    <div class="text-sm font-medium text-gray-900">${comp.metrics.instagram_followers.toLocaleString()}</div>
                                    <div class="text-xs text-gray-500">Followers</div>
                                    ${comp.metrics.instagram_engagement ? `<div class="text-xs text-green-600 mt-1">${comp.metrics.instagram_engagement}% eng.</div>` : ''}
                                    ${comp.metrics.instagram_posts ? `<div class="text-xs text-gray-500">${comp.metrics.instagram_posts} posts</div>` : ''}
                                </div>` : ''}
                                ${comp.metrics.twitter_followers ? `
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-400 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"></path>
                                    </svg>
                                    <div class="text-sm font-medium text-gray-900">${comp.metrics.twitter_followers.toLocaleString()}</div>
                                    <div class="text-xs text-gray-500">Followers</div>
                                    ${comp.metrics.twitter_engagement ? `<div class="text-xs text-green-600 mt-1">${comp.metrics.twitter_engagement}% eng.</div>` : ''}
                                    ${comp.metrics.twitter_posts ? `<div class="text-xs text-gray-500">${comp.metrics.twitter_posts} posts</div>` : ''}
                                </div>` : ''}
                                ${comp.metrics.linkedin_followers ? `
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-700 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path>
                                    </svg>
                                    <div class="text-sm font-medium text-gray-900">${comp.metrics.linkedin_followers.toLocaleString()}</div>
                                    <div class="text-xs text-gray-500">Followers</div>
                                </div>` : ''}
                            </div>
                        </div>
                        ` : ''}
                        
                        ${comp.intelligence && (comp.intelligence.content_themes || comp.intelligence.strengths || comp.intelligence.weaknesses || comp.intelligence.ai_insights) ? `
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">🎯 Competitive Intelligence</h4>
                            
                            ${comp.intelligence.content_themes && Array.isArray(comp.intelligence.content_themes) && comp.intelligence.content_themes.length > 0 ? `
                            <div class="mb-4 p-3 bg-purple-50 rounded-lg">
                                <span class="text-xs font-semibold text-purple-800 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                                    Content Themes
                                </span>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    ${comp.intelligence.content_themes.map(theme => `<span class="px-2 py-1 bg-purple-200 text-purple-900 text-xs font-medium rounded-full">${theme}</span>`).join('')}
                                </div>
                            </div>` : ''}
                            
                            ${comp.intelligence.top_hashtags && Array.isArray(comp.intelligence.top_hashtags) && comp.intelligence.top_hashtags.length > 0 ? `
                            <div class="mb-4 p-3 bg-indigo-50 rounded-lg">
                                <span class="text-xs font-semibold text-indigo-800">#️⃣ Top Hashtags</span>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    ${comp.intelligence.top_hashtags.map(tag => `<span class="text-xs text-indigo-700 font-medium">${tag}</span>`).join(' • ')}
                                </div>
                            </div>` : ''}
                            
                            ${comp.intelligence.best_posting_times ? `
                            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                                <span class="text-xs font-semibold text-blue-800">⏰ Best Posting Times</span>
                                <p class="text-xs text-blue-900 mt-1">${comp.intelligence.best_posting_times}</p>
                            </div>` : ''}
                            
                            ${comp.intelligence.engagement_patterns && typeof comp.intelligence.engagement_patterns === 'object' ? `
                            <div class="mb-4 p-3 bg-yellow-50 rounded-lg">
                                <span class="text-xs font-semibold text-yellow-800">📊 Engagement Patterns</span>
                                ${comp.intelligence.engagement_patterns.high_engagement_content ? `
                                <div class="mt-2">
                                    <div class="text-xs font-medium text-green-700 mb-1">✓ High Engagement:</div>
                                    <p class="text-xs text-gray-700 ml-3">${comp.intelligence.engagement_patterns.high_engagement_content}</p>
                                </div>` : ''}
                                ${comp.intelligence.engagement_patterns.low_engagement_content ? `
                                <div class="mt-2">
                                    <div class="text-xs font-medium text-red-700 mb-1">✗ Low Engagement:</div>
                                    <p class="text-xs text-gray-700 ml-3">${comp.intelligence.engagement_patterns.low_engagement_content}</p>
                                </div>` : ''}
                            </div>` : ''}
                            
                            ${comp.intelligence.strengths && Array.isArray(comp.intelligence.strengths) && comp.intelligence.strengths.length > 0 ? `
                            <div class="mb-4 p-3 bg-green-50 rounded-lg">
                                <span class="text-xs font-semibold text-green-800">💪 Strengths</span>
                                <ul class="mt-2 space-y-1">
                                    ${comp.intelligence.strengths.map(s => `<li class="text-xs text-gray-700 flex items-start gap-2"><span class="text-green-600 flex-shrink-0">✓</span><span>${s}</span></li>`).join('')}
                                </ul>
                            </div>` : ''}
                            
                            ${comp.intelligence.weaknesses && Array.isArray(comp.intelligence.weaknesses) && comp.intelligence.weaknesses.length > 0 ? `
                            <div class="mb-4 p-3 bg-red-50 rounded-lg">
                                <span class="text-xs font-semibold text-red-800">⚠️ Weaknesses</span>
                                <ul class="mt-2 space-y-1">
                                    ${comp.intelligence.weaknesses.map(w => `<li class="text-xs text-gray-700 flex items-start gap-2"><span class="text-red-600 flex-shrink-0">✗</span><span>${w}</span></li>`).join('')}
                                </ul>
                            </div>` : ''}
                            
                            ${comp.intelligence.ai_insights ? `
                            <div class="mb-4 p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg border border-indigo-200">
                                <span class="text-xs font-semibold text-indigo-900 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                                    AI Strategic Insights
                                </span>
                                <p class="text-xs text-gray-800 mt-2 leading-relaxed whitespace-pre-line">${comp.intelligence.ai_insights}</p>
                            </div>` : ''}
                        </div>
                        ` : ''}
                    </div>
                `).join('');
            } else {
                console.log('No competitors found in report');
                document.getElementById('competitors').innerHTML = '<p class="text-gray-500 text-center py-8">No competitors found</p>';
            }
            
            // Recommendations
            if (report.recommendations) {
                const recs = Array.isArray(report.recommendations) ? report.recommendations : [];
                if (recs.length > 0) {
                    document.getElementById('recommendations').innerHTML = recs.map((rec, index) => {
                        const title = typeof rec === 'string' ? rec : (rec.title || rec.recommendation || rec.strategy || 'Strategic Recommendation');
                        const description = typeof rec === 'object' ? (rec.description || rec.details || rec.rationale) : '';
                        const priority = typeof rec === 'object' ? rec.priority : null;
                        const impact = typeof rec === 'object' ? rec.impact : null;
                        const timeline = typeof rec === 'object' ? rec.timeline : null;
                        const resources = typeof rec === 'object' && Array.isArray(rec.resources) ? rec.resources : null;
                        return `
                            <div class="border-l-4 border-indigo-500 bg-indigo-50 p-4 rounded-r-lg">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        ${index + 1}
                                    </span>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900 mb-2 text-lg">${title}</h4>
                                        ${description ? `<p class="text-gray-700 text-sm mb-3">${description}</p>` : ''}
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            ${priority ? `<span class="px-3 py-1 text-xs font-medium rounded-full ${getPriorityClass(priority)}">Priority: ${priority.toUpperCase()}</span>` : ''}
                                            ${impact ? `<span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Impact: ${impact}</span>` : ''}
                                            ${timeline ? `<span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Timeline: ${timeline}</span>` : ''}
                                        </div>
                                        ${resources && resources.length > 0 ? `
                                        <div class="mt-3">
                                            <span class="text-xs font-semibold text-gray-700">Resources Needed:</span>
                                            <ul class="mt-1 ml-4 list-disc text-xs text-gray-600">
                                                ${resources.map(r => `<li>${r}</li>`).join('')}
                                            </ul>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    document.getElementById('recommendations').innerHTML = '<p class="text-gray-500 text-center py-8">Strategic recommendations are being generated...</p>';
                }
            } else {
                document.getElementById('recommendations').innerHTML = '<p class="text-gray-500 text-center py-8">Strategic recommendations are being generated...</p>';
            }
            
            // Action Plan
            if (report.action_plan) {
                const plan = Array.isArray(report.action_plan) ? report.action_plan : [];
                if (plan.length > 0) {
                    document.getElementById('action-plan').innerHTML = plan.map(week => {
                        const tasks = Array.isArray(week.tasks) ? week.tasks : [];
                        const weekGoal = week.goal || week.objective || null;
                        const weekDeliverables = week.deliverables || [];
                        return `
                        <div class="border-2 border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 text-lg mb-2 flex items-center gap-2">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Week ${week.week}: ${week.focus || 'Focus Area'}
                                    </h3>
                                    ${weekGoal ? `<p class="text-sm text-gray-600 mb-3 ml-8"><strong>Goal:</strong> ${weekGoal}</p>` : ''}
                                </div>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full">Week ${week.week}</span>
                            </div>
                            ${tasks.length > 0 ? `
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 ml-8">Tasks:</h4>
                                <ul class="space-y-3">
                                    ${tasks.map((task, idx) => {
                                        let taskText, taskDesc, taskPriority, taskDuration;
                                        if (typeof task === 'string') {
                                            taskText = task;
                                        } else if (typeof task === 'object' && task !== null) {
                                            taskText = task.task || task.title || task.action || 'Task';
                                            taskDesc = task.description || task.details || null;
                                            taskPriority = task.priority || null;
                                            taskDuration = task.duration || task.time || null;
                                        } else {
                                            taskText = String(task);
                                        }
                                        return `
                                            <li class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <input type="checkbox" class="mt-1.5 w-5 h-5 text-indigo-600 rounded border-gray-300">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">${idx + 1}. ${taskText}</div>
                                                    ${taskDesc ? `<div class="text-sm text-gray-600 mt-1">${taskDesc}</div>` : ''}
                                                    <div class="flex flex-wrap gap-2 mt-2">
                                                        ${taskPriority ? `<span class="px-2 py-1 text-xs font-medium rounded ${getPriorityClass(taskPriority)}">${taskPriority}</span>` : ''}
                                                        ${taskDuration ? `<span class="px-2 py-1 text-xs font-medium rounded bg-gray-200 text-gray-700">⏱️ ${taskDuration}</span>` : ''}
                                                    </div>
                                                </div>
                                            </li>
                                        `;
                                    }).join('')}
                                </ul>
                            </div>
                            ` : ''}
                            ${Array.isArray(weekDeliverables) && weekDeliverables.length > 0 ? `
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 ml-8">Expected Deliverables:</h4>
                                <ul class="ml-12 space-y-1">
                                    ${weekDeliverables.map(d => `<li class="text-sm text-gray-600 flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>${d}</li>`).join('')}
                                </ul>
                            </div>
                            ` : ''}
                        </div>
                        `;
                    }).join('');
                } else {
                    document.getElementById('action-plan').innerHTML = '<p class="text-gray-500 text-center py-8">30-day action plan is being generated...</p>';
                }
            } else {
                document.getElementById('action-plan').innerHTML = '<p class="text-gray-500 text-center py-8">30-day action plan is being generated...</p>';
            }
            
            // Show report, hide loading
            document.getElementById('loading-container').classList.add('hidden');
            document.getElementById('report-container').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading report:', error);
        showError();
    }
}

function showError() {
    document.getElementById('loading-container').classList.add('hidden');
    document.getElementById('error-container').classList.remove('hidden');
}

function getCompetitionColor(level) {
    const colors = {
        'low': 'text-green-600',
        'medium': 'text-yellow-600',
        'high': 'text-red-600'
    };
    return colors[level?.toLowerCase()] || 'text-gray-600';
}

function getPriorityClass(priority) {
    const classes = {
        'high': 'bg-red-100 text-red-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'low': 'bg-green-100 text-green-800'
    };
    return classes[priority?.toLowerCase()] || 'bg-gray-100 text-gray-800';
}
</script>
@endpush
@endsection
