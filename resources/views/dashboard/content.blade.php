@extends('layouts.marketing-os')

@section('title', 'Content')

@section('content')
<div class="py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Content Management</h1>
        
        <div class="mb-6 flex items-center justify-between">
    <div class="flex space-x-4">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">All</button>
        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Draft</button>
        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Scheduled</button>
        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Published</button>
    </div>
    
    <div class="flex space-x-3">
        <a href="{{ route('marketing.studio.content') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            AI Studio
        </a>
        @if(auth()->user()->facebookPages()->where('status', 'active')->count() > 0)
        <a href="{{ route('content.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create & Publish
        </a>
        @endif
    </div>
</div>

<!-- Content Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($contents as $content)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($content->image_url)
        <img src="{{ $content->image_url }}" alt="Post preview" class="w-full h-48 object-cover">
        @else
        <div class="w-full h-48 bg-gradient-to-br from-purple-100 to-blue-100 flex items-center justify-center">
            <svg class="w-16 h-16 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        @endif
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium 
                    @if($content->status === 'draft') bg-yellow-100 text-yellow-800
                    @elseif($content->status === 'scheduled') bg-blue-100 text-blue-800
                    @elseif($content->status === 'published') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800
                    @endif
                    px-2 py-1 rounded capitalize">
                    {{ $content->status }}
                </span>
                <span class="text-xs text-gray-500">{{ $content->created_at->diffForHumans() }}</span>
            </div>
            @if(isset($content->metadata['strategy_day']))
            <div class="mb-2">
                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">{{ $content->metadata['strategy_day'] }}</span>
                @if(isset($content->metadata['topic']))
                <span class="text-xs text-gray-600">• {{ Str::limit($content->metadata['topic'], 30) }}</span>
                @endif
            </div>
            @endif
            <p class="text-sm text-gray-700 mb-4 line-clamp-3">
                {{ Str::limit($content->caption, 150) }}
            </p>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    @if($content->status === 'draft')
                    <button onclick="editContent({{ $content->id }})" class="text-blue-600 hover:text-blue-700 text-sm">Edit</button>
                    @if($facebookPages->count() > 0)
                    <button onclick="scheduleContent({{ $content->id }})" class="text-green-600 hover:text-green-700 text-sm">Schedule</button>
                    @endif
                    @elseif($content->status === 'scheduled')
                    <span class="text-xs text-gray-600">{{ $content->scheduled_at->format('M d, Y H:i') }}</span>
                    @elseif($content->status === 'published')
                    <span class="text-xs text-gray-600">{{ $content->published_at->format('M d, Y H:i') }}</span>
                    @endif
                </div>
                <button onclick="deleteContent({{ $content->id }})" class="text-red-600 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @empty
    <!-- Empty State -->
    <div class="col-span-full">
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No content yet</h3>
            <p class="text-gray-600 mb-6">Start creating engaging posts with AI assistance</p>
            <a href="{{ route('marketing.studio.content') }}" class="inline-block bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-3 rounded-lg hover:from-purple-700 hover:to-blue-700">
                Create Your First Post with AI
            </a>
        </div>
    </div>
    @endforelse
</div>

<script>
function editContent(id) {
    // TODO: Implement edit modal or redirect to edit page
    window.location.href = `/ai-studio/content?edit=${id}`;
}

function scheduleContent(id) {
    // TODO: Implement schedule modal
    alert('Schedule functionality coming soon!');
}

function deleteContent(id) {
    if (confirm('Are you sure you want to delete this content?')) {
        fetch(`/ai-studio/content/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to delete content');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>
    </div>
</div>
@endsection
