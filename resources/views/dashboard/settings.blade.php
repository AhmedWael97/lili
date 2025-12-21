@extends('dashboard.layout')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Profile Information</h3>
        </div>
        <div class="p-4 sm:p-6">
            <form class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" value="{{ auth()->user()->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                    <input type="text" value="{{ auth()->user()->company }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Brand Settings</h3>
        </div>
        <div class="p-4 sm:p-6">
            <form method="POST" action="{{ route('dashboard.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand Name</label>
                    <input type="text" name="brand_name" value="{{ $agentConfig->business_name ?? auth()->user()->brandSetting->brand_name ?? '' }}" placeholder="Your brand name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                    <input type="text" name="industry" value="{{ $agentConfig->industry ?? auth()->user()->brandSetting->industry ?? '' }}" placeholder="e.g., E-commerce, Healthcare, Technology" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand Tone</label>
                    <select name="brand_tone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="professional" {{ ($agentConfig->brand_tone ?? auth()->user()->brandSetting->brand_tone ?? 'professional') == 'professional' ? 'selected' : '' }}>Professional</option>
                        <option value="casual" {{ ($agentConfig->brand_tone ?? auth()->user()->brandSetting->brand_tone ?? '') == 'casual' ? 'selected' : '' }}>Casual</option>
                        <option value="friendly" {{ ($agentConfig->brand_tone ?? auth()->user()->brandSetting->brand_tone ?? '') == 'friendly' ? 'selected' : '' }}>Friendly</option>
                        <option value="authoritative" {{ ($agentConfig->brand_tone ?? auth()->user()->brandSetting->brand_tone ?? '') == 'authoritative' ? 'selected' : '' }}>Authoritative</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Voice Characteristics</label>
                    <input type="text" name="voice_characteristics" value="{{ $agentConfig->brand_personality ?? auth()->user()->brandSetting->voice_characteristics ?? '' }}" placeholder="e.g., engaging, authentic, witty" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                    <textarea name="target_audience" rows="3" placeholder="Describe your target audience..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ $agentConfig && $agentConfig->target_audience ? ($agentConfig->target_audience['age'] ?? '') . ', ' . ($agentConfig->target_audience['location'] ?? '') . ', ' . ($agentConfig->target_audience['interests'] ?? '') : (auth()->user()->brandSetting->target_audience ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Business Goals</label>
                    <textarea name="business_goals" rows="3" placeholder="What are your marketing goals?" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ $agentConfig && $agentConfig->marketing_goals ? implode(', ', $agentConfig->marketing_goals) : (auth()->user()->brandSetting->business_goals ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Key Messages</label>
                    <textarea name="key_messages" rows="2" placeholder="Important messages to convey" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ $agentConfig->unique_value_proposition ?? auth()->user()->brandSetting->key_messages ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Words to Avoid</label>
                    <input type="text" name="forbidden_words" value="{{ $agentConfig->topics_to_avoid ?? auth()->user()->brandSetting->forbidden_words ?? '' }}" placeholder="Comma-separated words" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Words or phrases to never use in content</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Primary Colors</label>
                    <input type="text" name="primary_colors" value="{{ auth()->user()->brandSetting->primary_colors ?? '' }}" placeholder="#1877F2, #42B72A" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Comma-separated hex codes for brand colors</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                    <input type="text" name="secondary_color" value="{{ auth()->user()->brandSetting->secondary_color ?? '' }}" placeholder="#42B72A" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Secondary brand color (hex code)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Font Family</label>
                    <input type="text" name="font_family" value="{{ auth()->user()->brandSetting->font_family ?? '' }}" placeholder="e.g., Inter, Roboto, Montserrat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Visual Style</label>
                    <input type="text" name="visual_style" value="{{ auth()->user()->brandSetting->visual_style ?? '' }}" placeholder="e.g., modern, minimalist, vibrant" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand Logo</label>
                    @if(auth()->user()->brandSetting && auth()->user()->brandSetting->logo_path)
                        <div class="mb-3 flex items-center space-x-4">
                            <img src="{{ asset('storage/' . auth()->user()->brandSetting->logo_path) }}" alt="Brand Logo" class="h-20 w-auto object-contain border border-gray-200 rounded p-2 bg-white">
                            <span class="text-sm text-gray-600">Current logo</span>
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG or SVG. Max 5MB. Transparent background recommended.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                    <input type="url" name="website_url" value="{{ auth()->user()->brandSetting->website_url ?? '' }}" placeholder="https://yourwebsite.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Language *</label>
                        <select name="preferred_language" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="en" {{ (auth()->user()->brandSetting->preferred_language ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="ar" {{ (auth()->user()->brandSetting->preferred_language ?? '') == 'ar' ? 'selected' : '' }}>Arabic (العربية)</option>
                            <option value="es" {{ (auth()->user()->brandSetting->preferred_language ?? '') == 'es' ? 'selected' : '' }}>Spanish (Español)</option>
                            <option value="fr" {{ (auth()->user()->brandSetting->preferred_language ?? '') == 'fr' ? 'selected' : '' }}>French (Français)</option>
                            <option value="de" {{ (auth()->user()->brandSetting->preferred_language ?? '') == 'de' ? 'selected' : '' }}>German (Deutsch)</option>
                            <option value="it" {{ (auth()->user()->brandSetting->preferred_language ?? '') == 'it' ? 'selected' : '' }}>Italian (Italiano)</option>
                            <option value="pt" {{ (auth()->user()->brandSetting->preferred_language ?? '') == 'pt' ? 'selected' : '' }}>Portuguese (Português)</option>
                            <option value="zh" {{ (auth()->user()->brandSetting->preferred_language ?? '') == 'zh' ? 'selected' : '' }}>Chinese (中文)</option>
                            <option value="ja" {{ (auth()->user()->brandSetting->preferred_language ?? '') == 'ja' ? 'selected' : '' }}>Japanese (日本語)</option>
                            <option value="ko" {{ (auth()->user()->brandSetting->preferred_language ?? '') == 'ko' ? 'selected' : '' }}>Korean (한국어)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">All AI-generated content will be in this language</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Marketing Budget</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" name="monthly_budget" value="{{ auth()->user()->brandSetting->monthly_budget ?? '' }}" placeholder="1000.00" step="0.01" min="0" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Used by AI to suggest budget-appropriate tactics</p>
                    </div>
                </div>
                
                <!-- Image Generation Preferences -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">📸 Image Generation Preferences</h4>
                    <p class="text-xs text-gray-500 mb-4">Set your preferences once, and all AI-generated images will match your brand style automatically</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image Style</label>
                            <select name="image_style" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Style...</option>
                                <option value="photographic" {{ (auth()->user()->brandSetting->image_style ?? '') == 'photographic' ? 'selected' : '' }}>Photographic (Realistic)</option>
                                <option value="illustration" {{ (auth()->user()->brandSetting->image_style ?? '') == 'illustration' ? 'selected' : '' }}>Illustration</option>
                                <option value="minimalist" {{ (auth()->user()->brandSetting->image_style ?? '') == 'minimalist' ? 'selected' : '' }}>Minimalist</option>
                                <option value="abstract" {{ (auth()->user()->brandSetting->image_style ?? '') == 'abstract' ? 'selected' : '' }}>Abstract</option>
                                <option value="3d-render" {{ (auth()->user()->brandSetting->image_style ?? '') == '3d-render' ? 'selected' : '' }}>3D Render</option>
                                <option value="flat-design" {{ (auth()->user()->brandSetting->image_style ?? '') == 'flat-design' ? 'selected' : '' }}>Flat Design</option>
                                <option value="vintage" {{ (auth()->user()->brandSetting->image_style ?? '') == 'vintage' ? 'selected' : '' }}>Vintage</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image Mood</label>
                            <select name="image_mood" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Mood...</option>
                                <option value="professional" {{ (auth()->user()->brandSetting->image_mood ?? '') == 'professional' ? 'selected' : '' }}>Professional</option>
                                <option value="energetic" {{ (auth()->user()->brandSetting->image_mood ?? '') == 'energetic' ? 'selected' : '' }}>Energetic</option>
                                <option value="calm" {{ (auth()->user()->brandSetting->image_mood ?? '') == 'calm' ? 'selected' : '' }}>Calm & Peaceful</option>
                                <option value="playful" {{ (auth()->user()->brandSetting->image_mood ?? '') == 'playful' ? 'selected' : '' }}>Playful</option>
                                <option value="elegant" {{ (auth()->user()->brandSetting->image_mood ?? '') == 'elegant' ? 'selected' : '' }}>Elegant</option>
                                <option value="bold" {{ (auth()->user()->brandSetting->image_mood ?? '') == 'bold' ? 'selected' : '' }}>Bold & Dramatic</option>
                                <option value="warm" {{ (auth()->user()->brandSetting->image_mood ?? '') == 'warm' ? 'selected' : '' }}>Warm & Friendly</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Text in Images</label>
                            <select name="text_in_images" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="none" {{ (auth()->user()->brandSetting->text_in_images ?? 'minimal') == 'none' ? 'selected' : '' }}>No Text</option>
                                <option value="minimal" {{ (auth()->user()->brandSetting->text_in_images ?? 'minimal') == 'minimal' ? 'selected' : '' }}>Minimal Text</option>
                                <option value="prominent" {{ (auth()->user()->brandSetting->text_in_images ?? 'minimal') == 'prominent' ? 'selected' : '' }}>Prominent Text</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Aspect Ratio</label>
                            <select name="image_aspect_ratio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="1:1" {{ (auth()->user()->brandSetting->image_aspect_ratio ?? '1:1') == '1:1' ? 'selected' : '' }}>Square (1:1) - Best for Instagram/Facebook</option>
                                <option value="16:9" {{ (auth()->user()->brandSetting->image_aspect_ratio ?? '1:1') == '16:9' ? 'selected' : '' }}>Landscape (16:9) - Best for YouTube</option>
                                <option value="4:5" {{ (auth()->user()->brandSetting->image_aspect_ratio ?? '1:1') == '4:5' ? 'selected' : '' }}>Portrait (4:5) - Best for Instagram Feed</option>
                                <option value="9:16" {{ (auth()->user()->brandSetting->image_aspect_ratio ?? '1:1') == '9:16' ? 'selected' : '' }}>Story (9:16) - Best for Instagram Stories</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Composition Style</label>
                            <input type="text" name="image_composition" value="{{ auth()->user()->brandSetting->image_composition ?? '' }}" placeholder="e.g., centered subject, rule of thirds, dynamic angles, symmetrical" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">How you want elements arranged in your images</p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Elements</label>
                            <input type="text" name="preferred_elements" value="{{ auth()->user()->brandSetting->preferred_elements ?? '' }}" placeholder="e.g., people, products, nature, workspace, technology, abstract shapes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">What you want to see in your images (comma-separated)</p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Avoid Elements</label>
                            <input type="text" name="avoid_elements" value="{{ auth()->user()->brandSetting->avoid_elements ?? '' }}" placeholder="e.g., crowds, dark colors, busy backgrounds, stock photography look" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">What you DON'T want in your images (comma-separated)</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="logo_in_images" id="logo_in_images" value="1" {{ (auth()->user()->brandSetting->logo_in_images ?? false) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="logo_in_images" class="ml-2 block text-sm text-gray-700">Include logo in AI-generated images</label>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Save Brand Settings
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Connected Platforms</h3>
        </div>
        <div class="p-6">
            @php
                $facebookConnected = auth()->user()->connectedPlatforms()->where('platform', 'facebook')->where('status', 'active')->exists();
                $connectedPages = auth()->user()->facebookPages()->where('status', 'active')->count();
            @endphp
            
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-base font-semibold text-gray-900">Facebook</h4>
                        @if($facebookConnected)
                            <p class="text-sm text-green-600">✓ Connected • {{ $connectedPages }} page(s)</p>
                        @else
                            <p class="text-sm text-gray-500">Not connected</p>
                        @endif
                    </div>
                </div>
                <div>
                    @if($facebookConnected)
                        <form method="POST" action="{{ route('facebook.disconnect') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Disconnect
                            </button>
                        </form>
                    @else
                        <a href="{{ route('facebook.redirect') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Connect Facebook
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Change Password</h3>
        </div>
        <div class="p-6">
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
