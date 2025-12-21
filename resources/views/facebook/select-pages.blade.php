@extends('dashboard.layout')

@section('title', 'Select Facebook Pages')
@section('page-title', 'Select Facebook Pages')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Connect Your Facebook Pages</h2>
            <p class="text-gray-600">Select the pages you want to manage with AI Agents</p>
        </div>

        <form id="pages-form" method="POST" action="{{ route('facebook.connect-pages') }}">
            @csrf
            
            @if(count($pages) === 0)
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pages found</h3>
                    <p class="mt-1 text-sm text-gray-500">You don't have any Facebook pages associated with this account.</p>
                    <div class="mt-6">
                        <a href="{{ route('dashboard.settings') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Back to Settings
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4 mb-6">
                    @foreach($pages as $page)
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" 
                                   name="selected_pages[]" 
                                   value="{{ $page['id'] }}"
                                   data-page-id="{{ $page['id'] }}"
                                   data-page-name="{{ $page['name'] }}"
                                   data-page-token="{{ $page['access_token'] }}"
                                   data-followers="{{ $page['followers_count'] ?? 0 }}"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <div class="ml-4 flex-1">
                                <div class="flex items-center">
                                    @if(isset($page['picture']['data']['url']))
                                        <img src="{{ $page['picture']['data']['url'] }}" alt="{{ $page['name'] }}" class="h-12 w-12 rounded-full mr-3">
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $page['name'] }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ number_format($page['followers_count'] ?? 0) }} followers
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-between items-center pt-4 border-t">
                    <a href="{{ route('dashboard.settings') }}" class="text-gray-600 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Connect Selected Pages
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
document.getElementById('pages-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedPages = [];
    const checkboxes = document.querySelectorAll('input[name="selected_pages[]"]:checked');
    
    if (checkboxes.length === 0) {
        alert('Please select at least one page to connect.');
        return;
    }
    
    checkboxes.forEach(checkbox => {
        selectedPages.push({
            page_id: checkbox.dataset.pageId,
            page_name: checkbox.dataset.pageName,
            page_access_token: checkbox.dataset.pageToken,
            followers_count: parseInt(checkbox.dataset.followers) || 0
        });
    });
    
    // Create hidden input with pages data
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'pages';
    input.value = JSON.stringify(selectedPages);
    this.appendChild(input);
    
    this.submit();
});
</script>
@endsection
