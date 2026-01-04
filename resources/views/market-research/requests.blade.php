@extends('layouts.app')

@section('header', 'All Research Requests')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Research History</h1>
            <p class="text-gray-600 mt-1">View all your market research requests and reports</p>
        </div>
        <a href="{{ route('market-research.index') }}" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Research
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <div id="requests-container" class="space-y-4">
                <div class="text-center py-12 text-gray-500">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                    <p>Loading research requests...</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div id="pagination" class="hidden mt-6 flex items-center justify-between border-t border-gray-200 pt-4"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadRequests();
});

async function loadRequests(page = 1) {
    try {
        const response = await fetch(`/api/market-research/requests?page=${page}`);
        const data = await response.json();
        
        if (data.success && data.data.data.length > 0) {
            const container = document.getElementById('requests-container');
            container.innerHTML = data.data.data.map(request => `
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">${request.business_idea}</h3>
                                <span class="px-3 py-1 text-xs font-medium rounded-full ${getStatusBadge(request.status)}">
                                    ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    ${request.location}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    ${new Date(request.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}
                                </span>
                                ${request.completed_at ? `
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Completed ${new Date(request.completed_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}
                                </span>
                                ` : ''}
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 ml-4">
                            ${request.status === 'completed' ? `
                                <a href="/market-research/report/${request.id}" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Report
                                </a>
                            ` : request.status === 'pending_verification' ? `
                                <a href="/market-research/${request.id}/verify" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition flex items-center gap-2 animate-pulse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Verify Data
                                </a>
                            ` : request.status === 'processing' ? `
                                <button disabled class="px-4 py-2 bg-gray-100 text-gray-400 font-medium rounded-lg cursor-not-allowed flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </button>
                            ` : request.status === 'pending' ? `
                                <button disabled class="px-4 py-2 bg-gray-100 text-gray-400 font-medium rounded-lg cursor-not-allowed flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pending
                                </button>
                            ` : `
                                <button onclick="retryResearch(${request.id})" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Retry
                                </button>
                            `}
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Show pagination if needed
            if (data.data.last_page > 1) {
                const pagination = document.getElementById('pagination');
                pagination.classList.remove('hidden');
                pagination.innerHTML = `
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">${data.data.from}</span> to <span class="font-medium">${data.data.to}</span> of <span class="font-medium">${data.data.total}</span> results
                    </div>
                    <div class="flex gap-2">
                        ${data.data.current_page > 1 ? `
                            <button onclick="loadRequests(${data.data.current_page - 1})" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Previous
                            </button>
                        ` : ''}
                        ${data.data.current_page < data.data.last_page ? `
                            <button onclick="loadRequests(${data.data.current_page + 1})" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Next
                            </button>
                        ` : ''}
                    </div>
                `;
            }
        } else {
            const container = document.getElementById('requests-container');
            container.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>No research requests yet.</p>
                    <a href="/market-research" class="mt-4 inline-block text-indigo-600 hover:text-indigo-700 font-medium">
                        Start your first research →
                    </a>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading requests:', error);
        const container = document.getElementById('requests-container');
        container.innerHTML = `
            <div class="text-center py-12 text-red-500">
                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>Failed to load research requests.</p>
            </div>
        `;
    }
}

function getStatusBadge(status) {
    const badges = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'pending_verification': 'bg-orange-100 text-orange-800',
        'completed': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
}

function retryResearch(id) {
    alert('Retry functionality coming soon!');
}
</script>
@endpush
@endsection
