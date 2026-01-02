@extends('layouts.app')

@section('header', 'Market Research')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Market Research Intelligence</h1>
                <p class="text-indigo-100 text-lg">Get AI-powered insights about your competitors and market opportunities</p>
            </div>
            <div class="hidden md:block">
                <svg class="w-24 h-24 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- New Research Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Start New Research</h2>
        
        <form id="research-form" class="space-y-6">
            @csrf
            <div>
                <label for="business_idea" class="block text-sm font-medium text-gray-700 mb-2">
                    Business Idea *
                </label>
                <textarea 
                    id="business_idea" 
                    name="business_idea" 
                    rows="4" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="Example: Organic coffee shop with coworking space and local pastries"></textarea>
                <p class="mt-2 text-sm text-gray-500">Describe your business idea in detail. The more specific you are, the better insights you'll get.</p>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Target Location *
                </label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="Example: Austin, TX">
                <p class="mt-2 text-sm text-gray-500">Enter the city and state where you plan to operate.</p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    <svg class="inline w-5 h-5 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Processing takes 2-3 minutes
                </div>
                <button 
                    type="submit" 
                    id="submit-btn"
                    class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Start Research
                </button>
            </div>
        </form>

        <!-- Loading State -->
        <div id="loading-state" class="hidden mt-6 p-6 bg-indigo-50 rounded-lg border border-indigo-200">
            <div class="flex items-center gap-4">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                <div>
                    <h3 class="font-semibold text-indigo-900">Research in Progress...</h3>
                    <p class="text-sm text-indigo-700 mt-1">Our AI agents are analyzing the market. This takes 2-3 minutes.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Research -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Research</h2>
            <a href="{{ route('market-research.requests') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                View All →
            </a>
        </div>

        <div id="recent-research" class="space-y-4">
            <div class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>No research requests yet. Start your first analysis above!</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('research-form');
    const submitBtn = document.getElementById('submit-btn');
    const loadingState = document.getElementById('loading-state');
    
    // Load recent research
    loadRecentResearch();
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const businessIdea = document.getElementById('business_idea').value;
        const location = document.getElementById('location').value;
        
        // Show loading state
        loadingState.classList.remove('hidden');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
        
        try {
            const response = await fetch('/api/market-research', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    business_idea: businessIdea,
                    location: location
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                const requestId = data.data.request_id;
                
                // Redirect to report page with polling
                window.location.href = `/market-research/report/${requestId}`;
            } else {
                alert('Error: ' + (data.message || 'Failed to submit research request'));
                loadingState.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg> Start Research';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            loadingState.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg> Start Research';
        }
    });
    
    async function loadRecentResearch() {
        try {
            const response = await fetch('/api/market-research/requests');
            const data = await response.json();
            
            if (data.success && data.data.data.length > 0) {
                const container = document.getElementById('recent-research');
                container.innerHTML = data.data.data.slice(0, 5).map(request => `
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">${request.business_idea}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    ${request.location}
                                </span>
                                <span class="mx-2">•</span>
                                ${new Date(request.created_at).toLocaleDateString()}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-xs font-medium rounded-full ${getStatusBadge(request.status)}">
                                ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                            </span>
                            ${request.status === 'completed' ? 
                                `<a href="/market-research/report/${request.id}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">View Report →</a>` :
                                `<span class="text-gray-400 text-sm">Processing...</span>`
                            }
                        </div>
                    </div>
                `).join('');
            }
        } catch (error) {
            console.error('Error loading research:', error);
        }
    }
    
    function getStatusBadge(status) {
        const badges = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'processing': 'bg-blue-100 text-blue-800',
            'completed': 'bg-green-100 text-green-800',
            'failed': 'bg-red-100 text-red-800'
        };
        return badges[status] || 'bg-gray-100 text-gray-800';
    }
});
</script>
@endpush
@endsection
