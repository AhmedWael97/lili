@extends('dashboard.layout')

@section('title', 'AI Content Creator')
@section('page-title', 'AI Content Creator')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
        <!-- Input Form -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 lg:p-8">
            <div class="mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Create Your Content</h2>
                <p class="text-sm sm:text-base text-gray-600">AI will write captions and generate images for you</p>
            </div>

            <form id="content-form" class="space-y-4 sm:space-y-6">
                @csrf
                
                <!-- Hidden fields for agent configuration -->
                @if($agentConfig)
                    <input type="hidden" name="agent_config[business_name]" value="{{ $agentConfig->business_name }}">
                    <input type="hidden" name="agent_config[industry]" value="{{ $agentConfig->industry }}">
                    <input type="hidden" name="agent_config[products_services]" value="{{ $agentConfig->products_services }}">
                    <input type="hidden" name="agent_config[unique_value_proposition]" value="{{ $agentConfig->unique_value_proposition }}">
                    <input type="hidden" name="agent_config[brand_tone]" value="{{ $agentConfig->brand_tone }}">
                    <input type="hidden" name="agent_config[target_audience]" value="{{ json_encode($agentConfig->target_audience) }}">
                    <input type="hidden" name="agent_config[pain_points]" value="{{ $agentConfig->pain_points }}">
                    <input type="hidden" name="agent_config[focus_keywords]" value="{{ json_encode($agentConfig->focus_keywords) }}">
                    <input type="hidden" name="agent_config[topics_to_avoid]" value="{{ $agentConfig->topics_to_avoid }}">
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand Name *</label>
                    <input type="text" name="brand_name" required 
                           value="{{ $agentConfig->business_name ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
                           placeholder="Your Brand">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">What's your post about? *</label>
                    <textarea name="topic" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
                              placeholder="Describe what you want to post about..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience *</label>
                    @php
                        $targetAudienceValue = '';
                        if ($agentConfig && $agentConfig->target_audience) {
                            $ta = $agentConfig->target_audience;
                            $parts = array_filter([
                                $ta['age'] ?? '',
                                $ta['location'] ?? '',
                                $ta['interests'] ?? ''
                            ]);
                            $targetAudienceValue = implode(', ', $parts);
                        }
                    @endphp
                    <input type="text" name="target_audience" required 
                           value="{{ $targetAudienceValue }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
                           placeholder="Who is this for?">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tone *</label>
                    <select name="tone" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                        <option value="professional" {{ ($agentConfig->brand_tone ?? '') == 'professional' ? 'selected' : '' }}>Professional</option>
                        <option value="casual" {{ ($agentConfig->brand_tone ?? '') == 'casual' ? 'selected' : '' }}>Casual</option>
                        <option value="friendly" {{ ($agentConfig->brand_tone ?? 'friendly') == 'friendly' ? 'selected' : '' }}>Friendly</option>
                        <option value="authoritative" {{ ($agentConfig->brand_tone ?? '') == 'authoritative' ? 'selected' : '' }}>Authoritative</option>
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="include_image" id="include_image" checked 
                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="include_image" class="ml-2 block text-sm text-gray-900">
                        Generate AI Image (DALL-E 3)
                    </label>
                </div>
                
                @if(isset($brandSettings) && ($brandSettings->image_style || $brandSettings->image_mood))
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-xs font-semibold text-blue-900 mb-1">✓ Your Image Preferences Active</p>
                        <p class="text-xs text-blue-700">
                            @if($brandSettings->image_style)
                                Style: <span class="font-medium">{{ ucfirst($brandSettings->image_style) }}</span>
                            @endif
                            @if($brandSettings->image_mood)
                                • Mood: <span class="font-medium">{{ ucfirst($brandSettings->image_mood) }}</span>
                            @endif
                            @if($brandSettings->image_aspect_ratio)
                                • Ratio: <span class="font-medium">{{ $brandSettings->image_aspect_ratio }}</span>
                            @endif
                        </p>
                        <a href="{{ route('dashboard.settings') }}" class="text-xs text-blue-600 hover:underline">Edit preferences</a>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-800">
                            💡 <a href="{{ route('dashboard.settings') }}" class="font-medium text-yellow-900 hover:underline">Set your image preferences</a> to get consistent brand-matched images every time!
                        </p>
                    </div>
                @endif

                <button type="submit" id="generate-content-btn" 
                        class="w-full px-6 py-3 bg-gradient-to-r from-pink-600 to-orange-600 text-white rounded-lg hover:from-pink-700 hover:to-orange-700 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Generate Content
                </button>
            </form>
        </div>

        <!-- Preview Area -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 lg:p-8">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Preview</h3>
            
            <div id="preview-area" class="text-center text-gray-400 py-12">
                <svg class="w-24 h-24 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p>Your generated content will appear here</p>
            </div>

            <div id="content-preview" class="hidden"></div>
        </div>
    </div>
</div>

<script>
let currentContent = null;

document.getElementById('content-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('generate-content-btn');
    const previewArea = document.getElementById('preview-area');
    const contentPreview = document.getElementById('content-preview');
    
    // Show loading
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-2 inline" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Generating...
    `;
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    data.include_image = formData.get('include_image') === 'on';
    
    try {
        const response = await fetch('{{ route('marketing.studio.generate-content') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentContent = result.content;
            displayContent(result.content);
            previewArea.classList.add('hidden');
            contentPreview.classList.remove('hidden');
        } else {
            alert('Error: ' + (result.error || 'Failed to generate content'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please check your configuration.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            Generate Content
        `;
    }
});

function displayContent(content) {
    const preview = document.getElementById('content-preview');
    
    let html = '<div class="space-y-6">';
    
    // Image
    if (content.image_url) {
        html += `
            <div class="rounded-lg overflow-hidden">
                <img src="${content.image_url}" alt="Generated" class="w-full">
            </div>
        `;
    }
    
    // Caption
    html += `
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-gray-900 whitespace-pre-wrap">${escapeHtml(content.caption)}</p>
            <p class="text-xs text-gray-500 mt-2">${content.character_count || content.caption.length} characters</p>
        </div>
    `;
    
    // Hashtags
    if (content.hashtags && content.hashtags.length > 0) {
        html += '<div class="flex flex-wrap gap-2">';
        content.hashtags.forEach(tag => {
            html += `<span class="text-blue-600 text-sm">#${tag}</span>`;
        });
        html += '</div>';
    }
    
    // CTA
    if (content.cta) {
        html += `
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-sm text-blue-900"><strong>CTA:</strong> ${content.cta}</p>
            </div>
        `;
    }
    
    // Actions
    html += `
        <div class="flex flex-col space-y-3">
            <button onclick="saveDraft()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                💾 Save as Draft
            </button>
            <button onclick="copyToClipboard()" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                📋 Copy Caption
            </button>
        </div>
    `;
    
    if (content.image_error) {
        html += `<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800">${content.image_error}</div>`;
    }
    
    html += '</div>';
    
    preview.innerHTML = html;
}

async function saveDraft() {
    if (!currentContent) return;
    
    try {
        const response = await fetch('{{ route('marketing.studio.save-draft') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                caption: currentContent.caption,
                image_url: currentContent.image_url,
                metadata: {
                    hashtags: currentContent.hashtags,
                    cta: currentContent.cta,
                    tone: document.querySelector('[name="tone"]').value
                }
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Content saved as draft! You can publish it later from the Content page.', 'success');
        } else {
            showNotification('Failed to save: ' + result.error, 'error');
        }
    } catch (error) {
        showNotification('An error occurred', 'error');
    }
}

function copyToClipboard() {
    if (!currentContent) return;
    
    navigator.clipboard.writeText(currentContent.caption).then(() => {
        showNotification('Caption copied to clipboard!', 'success');
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    } text-white`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
