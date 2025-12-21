@extends('dashboard.layout')

@section('title', 'Create Content')
@section('page-title', 'Create Content')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Usage Limit Warning -->
    @if(isset($usageSummary) && $usageSummary['has_subscription'])
        @php
            $postsUsed = $usageSummary['posts']['used'];
            $postsLimit = $usageSummary['posts']['limit'];
            $percentage = $usageSummary['posts']['percentage'];
            $isUnlimited = $usageSummary['posts']['unlimited'];
        @endphp
        
        @if(!$isUnlimited && $percentage >= 80)
            <div class="mb-6 p-4 {{ $percentage >= 100 ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }} border rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 {{ $percentage >= 100 ? 'text-red-600' : 'text-yellow-600' }} mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold {{ $percentage >= 100 ? 'text-red-900' : 'text-yellow-900' }} mb-1">
                            {{ $percentage >= 100 ? 'Monthly Limit Reached' : 'Approaching Monthly Limit' }}
                        </h4>
                        <p class="text-sm {{ $percentage >= 100 ? 'text-red-700' : 'text-yellow-700' }}">
                            You've used <strong>{{ $postsUsed }} of {{ $postsLimit }}</strong> content generations this month ({{ $percentage }}%). 
                            @if($percentage >= 100)
                                Please <a href="{{ route('dashboard.billing') }}" class="underline font-semibold">upgrade your plan</a> to continue.
                            @else
                                Consider <a href="{{ route('dashboard.billing') }}" class="underline">upgrading</a> if you need more.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif
    
    <div class="bg-white rounded-lg shadow-lg p-8">
        @if(auth()->user()->brandSetting)
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900 mb-1">Using Your Brand Settings</h4>
                        <p class="text-sm text-blue-700">
                            Content will be generated using <strong>{{ auth()->user()->brandSetting->brand_name ?? 'your brand' }}</strong> settings including:
                            tone ({{ auth()->user()->brandSetting->brand_tone }}), 
                            colors, 
                            @if(auth()->user()->brandSetting->logo_in_images && auth()->user()->brandSetting->logo_path)
                                logo overlay,
                            @endif
                            and language ({{ strtoupper(auth()->user()->brandSetting->preferred_language ?? 'en') }}).
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-yellow-900 mb-1">Set Up Your Brand First</h4>
                        <p class="text-sm text-yellow-700">
                            For better AI-generated content, 
                            <a href="{{ route('dashboard.settings') }}" class="underline font-semibold">configure your brand settings</a> 
                            including logo, colors, and tone.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Generate AI Content</h2>
            <p class="text-gray-600">Create engaging content for your Facebook pages</p>
        </div>

        @if(auth()->user()->brandSetting)
        <!-- Brand Settings Summary Card -->
        <div class="mb-6 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Your Brand Settings (Applied Automatically)
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <div class="bg-white rounded p-2">
                    <p class="text-gray-500 mb-1">Brand Name</p>
                    <p class="font-semibold text-gray-900">{{ auth()->user()->brandSetting->brand_name ?? 'Not set' }}</p>
                </div>
                <div class="bg-white rounded p-2">
                    <p class="text-gray-500 mb-1">Tone</p>
                    <p class="font-semibold text-gray-900 capitalize">{{ auth()->user()->brandSetting->brand_tone ?? 'professional' }}</p>
                </div>
                <div class="bg-white rounded p-2">
                    <p class="text-gray-500 mb-1">Language</p>
                    <p class="font-semibold text-gray-900 uppercase">{{ auth()->user()->brandSetting->preferred_language ?? 'EN' }}</p>
                </div>
                <div class="bg-white rounded p-2">
                    <p class="text-gray-500 mb-1">Logo Overlay</p>
                    <p class="font-semibold {{ auth()->user()->brandSetting->logo_in_images && auth()->user()->brandSetting->logo_path ? 'text-green-600' : 'text-gray-400' }}">
                        {{ auth()->user()->brandSetting->logo_in_images && auth()->user()->brandSetting->logo_path ? '✓ Enabled' : '✗ Disabled' }}
                    </p>
                </div>
            </div>
            @if(auth()->user()->brandSetting->primary_colors)
            <div class="mt-3 flex items-center">
                <span class="text-xs text-gray-600 mr-2">Brand Colors:</span>
                @php
                    $colors = array_map('trim', explode(',', auth()->user()->brandSetting->primary_colors));
                @endphp
                @foreach($colors as $color)
                    <div class="w-6 h-6 rounded border border-gray-300 mr-1" style="background-color: {{ $color }}" title="{{ $color }}"></div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <form id="content-form" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Facebook Page *</label>
                <select name="facebook_page_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Choose a page...</option>
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}">{{ $page->page_name }} ({{ number_format($page->follower_count) }} followers)</option>
                    @endforeach
                </select>
                @if(count($pages) === 0)
                    <p class="text-sm text-red-600 mt-1">
                        No pages connected. <a href="{{ route('dashboard.settings') }}" class="underline">Connect Facebook pages</a>
                    </p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Content Type *</label>
                <select name="content_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="post">Post</option>
                    <option value="story">Story</option>
                    <option value="reel">Reel</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Topic / Description *</label>
                <textarea name="topic" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                          placeholder="What do you want to post about? E.g., New product launch, Holiday sale, Industry tips..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tone (Optional)</label>
                <select name="tone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Use brand default ({{ auth()->user()->brandSetting->brand_tone ?? 'professional' }})</option>
                    <option value="professional">Professional</option>
                    <option value="casual">Casual</option>
                    <option value="friendly">Friendly</option>
                    <option value="authoritative">Authoritative</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Override your default brand tone for this specific content</p>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="include_image" id="include_image" value="1" checked
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="include_image" class="ml-2 block text-sm text-gray-700">
                    Generate AI image
                    @if(auth()->user()->brandSetting && auth()->user()->brandSetting->logo_in_images && auth()->user()->brandSetting->logo_path)
                        <span class="text-green-600">(with logo overlay ✓)</span>
                    @endif
                </label>
            </div>

            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('dashboard.content') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" id="generate-btn"
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Generate Content
                </button>
            </div>
        </form>

        <!-- Preview Area -->
        <div id="preview-area" class="hidden mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Content Preview</h3>
            <div id="preview-content"></div>
            
            <div class="flex justify-end space-x-4 mt-6 pt-4 border-t">
                <button onclick="editContent()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Edit
                </button>
                <button onclick="saveDraft()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save as Draft
                </button>
                <button onclick="scheduleContent()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Schedule
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="hidden mt-8 text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Generating your content...</p>
        </div>
    </div>
</div>

<script>
let generatedContent = null;

document.getElementById('content-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const generateBtn = document.getElementById('generate-btn');
    const loadingState = document.getElementById('loading-state');
    const previewArea = document.getElementById('preview-area');
    
    // Show loading
    generateBtn.disabled = true;
    loadingState.classList.remove('hidden');
    previewArea.classList.add('hidden');
    
    try {
        const response = await fetch('{{ route("content.generate") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            generatedContent = result.content;
            displayPreview(result.preview);
            previewArea.classList.remove('hidden');
        } else {
            alert('Failed to generate content: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    } finally {
        generateBtn.disabled = false;
        loadingState.classList.add('hidden');
    }
});

function displayPreview(preview) {
    const content = document.getElementById('preview-content');
    
    let html = '<div class="space-y-4">';
    
    if (preview.image_url) {
        html += `<div class="mb-4">
            <img src="${preview.image_url}" alt="Generated content" class="w-full max-w-lg rounded-lg border border-gray-200">
        </div>`;
    }
    
    html += `<div class="bg-white p-4 rounded-lg border border-gray-200">
        <p class="text-gray-900 whitespace-pre-wrap">${preview.caption}</p>
    </div>`;
    
    if (preview.hashtags && preview.hashtags.length > 0) {
        html += `<div class="flex flex-wrap gap-2">`;
        preview.hashtags.forEach(tag => {
            html += `<span class="text-blue-600 text-sm">#${tag}</span>`;
        });
        html += `</div>`;
    }
    
    if (preview.cta) {
        html += `<div class="text-sm text-gray-600">CTA: ${preview.cta}</div>`;
    }
    
    html += '</div>';
    content.innerHTML = html;
}

function editContent() {
    document.getElementById('preview-area').classList.add('hidden');
}

function saveDraft() {
    if (generatedContent) {
        window.location.href = '{{ route("dashboard.content") }}';
    }
}

function scheduleContent() {
    // Implement scheduling logic
    alert('Scheduling feature coming soon!');
}
</script>
@endsection
