@extends('layouts.app')

@section('header', 'Market Research Report')

@section('content')
<div class="space-y-6">
    <!-- Loading State -->
    <div id="loading-container" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mx-auto mb-4"></div>
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
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-8 text-white">
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
            document.getElementById('executive-summary').innerHTML = `<p class="whitespace-pre-line">${report.executive_summary || 'No summary available'}</p>`;
            
            // Market Overview
            if (report.market_analysis) {
                const ma = report.market_analysis;
                document.getElementById('market-overview').innerHTML = `
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600">Market Size</span>
                        <span class="font-semibold text-gray-900">${ma.market_size_estimate || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600">Growth Rate</span>
                        <span class="font-semibold text-green-600">${ma.growth_rate ? ma.growth_rate + '%' : 'N/A'}</span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-600">Target Audience</span>
                        <span class="font-semibold text-gray-900">${typeof ma.target_audience === 'object' && ma.target_audience !== null ? (ma.target_audience.primary || JSON.stringify(ma.target_audience)) : (ma.target_audience || 'N/A')}</span>
                    </div>
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
                        document.getElementById('opportunities').innerHTML = opps.map(opp => {
                            let oppText;
                            if (typeof opp === 'string') {
                                oppText = opp;
                            } else if (typeof opp === 'object' && opp !== null) {
                                oppText = opp.opportunity || opp.title || opp.description || JSON.stringify(opp);
                            } else {
                                oppText = String(opp);
                            }
                            return `
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">${oppText}</span>
                                </li>
                            `;
                        }).join('');
                    }
                }
                
                // Threats
                if (ma.threats) {
                    const threats = Array.isArray(ma.threats) ? ma.threats : [];
                    if (threats.length > 0) {
                        document.getElementById('threats').innerHTML = threats.map(threat => {
                            let threatText;
                            if (typeof threat === 'string') {
                                threatText = threat;
                            } else if (typeof threat === 'object' && threat !== null) {
                                threatText = threat.threat || threat.title || threat.description || JSON.stringify(threat);
                            } else {
                                threatText = String(threat);
                            }
                            return `
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">${threatText}</span>
                                </li>
                            `;
                        }).join('');
                    }
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
                
                document.getElementById('competitors').innerHTML = uniqueCompetitors.map((comp, index) => `
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900">${index + 1}. ${comp.business_name}</h3>
                                ${comp.website ? `<a href="${comp.website}" target="_blank" class="text-sm text-indigo-600 hover:underline break-all">${comp.website}</a>` : '<span class="text-sm text-gray-500">No website available</span>'}
                                ${comp.social_media && (comp.social_media.facebook || comp.social_media.instagram || comp.social_media.twitter) ? `
                                <div class="mt-2 flex gap-3 text-xs">
                                    ${comp.social_media.facebook ? `<span class="text-blue-600">📘 @${comp.social_media.facebook}</span>` : ''}
                                    ${comp.social_media.instagram ? `<span class="text-pink-600">📸 @${comp.social_media.instagram}</span>` : ''}
                                    ${comp.social_media.twitter ? `<span class="text-blue-400">🐦 @${comp.social_media.twitter}</span>` : ''}
                                </div>
                                ` : '<div class="mt-2 text-xs text-gray-400">Social media profiles not available</div>'}
                            </div>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full ml-4 flex-shrink-0">
                                ${comp.relevance_score || 0}
                            </span>
                        </div>
                        
                        ${comp.metrics && typeof comp.metrics === 'object' && !Array.isArray(comp.metrics) && Object.keys(comp.metrics).length > 0 ? `
                        <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <svg class="w-5 h-5 text-blue-500 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">${comp.metrics.facebook_followers || 0}</div>
                                <div class="text-xs text-gray-500">Facebook</div>
                            </div>
                            <div class="text-center">
                                <svg class="w-5 h-5 text-pink-500 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">${comp.metrics.instagram_followers || 0}</div>
                                <div class="text-xs text-gray-500">Instagram</div>
                            </div>
                            <div class="text-center">
                                <svg class="w-5 h-5 text-blue-400 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">${comp.metrics.twitter_followers || 0}</div>
                                <div class="text-xs text-gray-500">Twitter</div>
                            </div>
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
                        const title = typeof rec === 'string' ? rec : (rec.title || rec.recommendation || 'Recommendation');
                        const description = typeof rec === 'object' ? rec.description : '';
                        const priority = typeof rec === 'object' ? rec.priority : null;
                        return `
                            <div class="border-l-4 border-indigo-500 bg-indigo-50 p-4 rounded-r-lg">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        ${index + 1}
                                    </span>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">${title}</h4>
                                        ${description ? `<p class="text-gray-700 text-sm">${description}</p>` : ''}
                                        ${priority ? `<span class="inline-block mt-2 px-2 py-1 text-xs font-medium rounded ${getPriorityClass(priority)}">${priority.toUpperCase()}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            }
            
            // Action Plan
            if (report.action_plan) {
                const plan = Array.isArray(report.action_plan) ? report.action_plan : [];
                if (plan.length > 0) {
                    document.getElementById('action-plan').innerHTML = plan.map(week => {
                        const tasks = Array.isArray(week.tasks) ? week.tasks : [];
                        return `
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Week ${week.week}: ${week.focus}
                            </h3>
                            <ul class="space-y-2">
                                ${tasks.map(task => {
                                    let taskText, taskDesc;
                                    if (typeof task === 'string') {
                                        taskText = task;
                                    } else if (typeof task === 'object' && task !== null) {
                                        taskText = task.task || task.title || 'Task';
                                        taskDesc = task.description || null;
                                    } else {
                                        taskText = String(task);
                                    }
                                    return `
                                        <li class="flex items-start gap-2">
                                            <input type="checkbox" class="mt-1 w-4 h-4 text-indigo-600 rounded">
                                            <div class="text-gray-700">
                                                <div class="font-medium">${taskText}</div>
                                                ${taskDesc ? `<div class="text-sm text-gray-500 mt-1">${taskDesc}</div>` : ''}
                                            </div>
                                        </li>
                                    `;
                                }).join('')}
                            </ul>
                        </div>
                        `;
                    }).join('');
                }
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
