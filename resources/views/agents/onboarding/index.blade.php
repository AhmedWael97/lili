@extends('dashboard.layout')

@section('title', 'Agent Onboarding')
@section('page-title', 'Setup Your Marketing Agent')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
            <div class="font-semibold mb-2">Please fix the following errors:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-8">
        <!-- Progress Steps -->
        <div class="mb-8" x-data="{ 
            currentStep: {{ old('currentStep', $errors->any() ? 1 : 1) }},
            init() {
                // If there are validation errors, try to detect which step
                @if($errors->any())
                    if ({{ $errors->has('business_name') || $errors->has('industry') || $errors->has('products_services') ? 'true' : 'false' }}) {
                        this.currentStep = 1;
                    } else if ({{ $errors->has('brand_tone') || $errors->has('target_audience.*') || $errors->has('pain_points') ? 'true' : 'false' }}) {
                        this.currentStep = 2;
                    } else if ({{ $errors->has('marketing_goals') || $errors->has('current_platforms') ? 'true' : 'false' }}) {
                        this.currentStep = 3;
                    } else {
                        this.currentStep = 4;
                    }
                @endif
            }
        }">
            <div class="flex justify-between items-center mb-8">
                <div class="flex-1 text-center" :class="currentStep >= 1 ? '' : 'opacity-50'">
                    <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-white font-semibold" 
                         :class="currentStep >= 1 ? 'bg-blue-600' : 'bg-gray-300'">1</div>
                    <p class="text-xs mt-2">Business</p>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2" :class="currentStep >= 2 ? 'bg-blue-600' : ''"></div>
                
                <div class="flex-1 text-center" :class="currentStep >= 2 ? '' : 'opacity-50'">
                    <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-white font-semibold"
                         :class="currentStep >= 2 ? 'bg-blue-600' : 'bg-gray-300'">2</div>
                    <p class="text-xs mt-2">Brand & Audience</p>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2" :class="currentStep >= 3 ? 'bg-blue-600' : ''"></div>
                
                <div class="flex-1 text-center" :class="currentStep >= 3 ? '' : 'opacity-50'">
                    <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-white font-semibold"
                         :class="currentStep >= 3 ? 'bg-blue-600' : 'bg-gray-300'">3</div>
                    <p class="text-xs mt-2">Goals & Strategy</p>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2" :class="currentStep >= 4 ? 'bg-blue-600' : ''"></div>
                
                <div class="flex-1 text-center" :class="currentStep >= 4 ? '' : 'opacity-50'">
                    <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-white font-semibold"
                         :class="currentStep >= 4 ? 'bg-blue-600' : 'bg-gray-300'">4</div>
                    <p class="text-xs mt-2">Content & Preferences</p>
                </div>
            </div>

            <form method="POST" action="{{ route('agents.onboarding.store', $agentCode) }}" novalidate>
                @csrf

                <!-- Step 1: Business Foundation -->
                <div x-show="currentStep === 1" x-transition>
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Tell us about your business</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                            <input type="text" name="business_name" value="{{ old('business_name', $configuration->business_name) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Industry *</label>
                            <input type="text" name="industry" value="{{ old('industry', $configuration->industry) }}" 
                                   placeholder="e.g., E-commerce, SaaS, Healthcare" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Products/Services *</label>
                            <textarea name="products_services" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                      required>{{ old('products_services', $configuration->products_services) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Describe what you sell or offer</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">What makes you different?</label>
                            <textarea name="unique_value_proposition" rows="2" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('unique_value_proposition', $configuration->unique_value_proposition) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Your unique value proposition</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Main Competitors</label>
                            <input type="text" name="competitors" value="{{ old('competitors', $configuration->competitors) }}" 
                                   placeholder="Company A, Company B, Company C" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button type="button" @click="currentStep = 2" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Next Step
                        </button>
                    </div>
                </div>

                <!-- Step 2: Brand & Audience -->
                <div x-show="currentStep === 2" x-transition x-cloak>
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Your brand and target audience</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Brand Tone *</label>
                            <select name="brand_tone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select tone...</option>
                                <option value="professional" {{ old('brand_tone', $configuration->brand_tone) == 'professional' ? 'selected' : '' }}>Professional</option>
                                <option value="casual" {{ old('brand_tone', $configuration->brand_tone) == 'casual' ? 'selected' : '' }}>Casual & Friendly</option>
                                <option value="playful" {{ old('brand_tone', $configuration->brand_tone) == 'playful' ? 'selected' : '' }}>Playful & Fun</option>
                                <option value="luxury" {{ old('brand_tone', $configuration->brand_tone) == 'luxury' ? 'selected' : '' }}>Luxury & Elegant</option>
                                <option value="inspirational" {{ old('brand_tone', $configuration->brand_tone) == 'inspirational' ? 'selected' : '' }}>Inspirational</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">Brand Colors</label>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="text-xs text-gray-600">Primary</label>
                                    <input type="color" name="brand_colors[primary]" value="{{ old('brand_colors.primary', is_array($configuration->brand_colors ?? null) ? ($configuration->brand_colors['primary'] ?? '#3B82F6') : '#3B82F6') }}" 
                                           class="w-full h-10 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Secondary</label>
                                    <input type="color" name="brand_colors[secondary]" value="{{ old('brand_colors.secondary', is_array($configuration->brand_colors ?? null) ? ($configuration->brand_colors['secondary'] ?? '#8B5CF6') : '#8B5CF6') }}" 
                                           class="w-full h-10 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Accent</label>
                                    <input type="color" name="brand_colors[accent]" value="{{ old('brand_colors.accent', is_array($configuration->brand_colors ?? null) ? ($configuration->brand_colors['accent'] ?? '#10B981') : '#10B981') }}" 
                                           class="w-full h-10 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Brand Story</label>
                            <textarea name="brand_story" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('brand_story', $configuration->brand_story) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Your company's story and mission</p>
                        </div>

                        <div class="border-t pt-6 mt-6">
                            <h4 class="font-semibold text-gray-900 mb-4">Target Audience</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Age Range *</label>
                                    <input type="text" name="target_audience[age]" value="{{ old('target_audience.age', is_array($configuration->target_audience ?? null) ? ($configuration->target_audience['age'] ?? '') : '') }}" 
                                           placeholder="e.g., 25-45" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                                    <input type="text" name="target_audience[location]" value="{{ old('target_audience.location', is_array($configuration->target_audience ?? null) ? ($configuration->target_audience['location'] ?? '') : '') }}" 
                                           placeholder="e.g., USA, Global, California" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Interests</label>
                                <input type="text" name="target_audience[interests]" value="{{ old('target_audience.interests', is_array($configuration->target_audience ?? null) ? ($configuration->target_audience['interests'] ?? '') : '') }}" 
                                       placeholder="e.g., Technology, Fitness, Travel" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pain Points *</label>
                                <textarea name="pain_points" rows="2" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                          required>{{ old('pain_points', $configuration->pain_points) }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">What problems does your audience face?</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep = 1" 
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Previous
                        </button>
                        <button type="button" @click="currentStep = 3" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Next Step
                        </button>
                    </div>
                </div>

                <!-- Step 3: Goals & Strategy -->
                <div x-show="currentStep === 3" x-transition x-cloak>
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Marketing goals and budget</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Primary Goals * (Select all that apply)</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="marketing_goals[]" value="brand_awareness" 
                                           {{ in_array('brand_awareness', old('marketing_goals', $configuration->marketing_goals ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Brand Awareness</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="marketing_goals[]" value="lead_generation" 
                                           {{ in_array('lead_generation', old('marketing_goals', $configuration->marketing_goals ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Lead Generation</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="marketing_goals[]" value="sales" 
                                           {{ in_array('sales', old('marketing_goals', $configuration->marketing_goals ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Increase Sales</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="marketing_goals[]" value="engagement" 
                                           {{ in_array('engagement', old('marketing_goals', $configuration->marketing_goals ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Social Engagement</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="marketing_goals[]" value="customer_retention" 
                                           {{ in_array('customer_retention', old('marketing_goals', $configuration->marketing_goals ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Customer Retention</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Budget</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" name="monthly_budget" value="{{ old('monthly_budget', $configuration->monthly_budget) }}" 
                                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                           min="0" step="100">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Timeline</label>
                                <input type="text" name="timeline" value="{{ old('timeline', $configuration->timeline) }}" 
                                       placeholder="e.g., 3 months, Q1 2025" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Current Platforms (Check what you use)</label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['Facebook', 'Instagram', 'Twitter', 'LinkedIn', 'TikTok', 'YouTube'] as $platform)
                                <label class="flex items-center">
                                    <input type="checkbox" name="current_platforms[]" value="{{ strtolower($platform) }}" 
                                           {{ in_array(strtolower($platform), old('current_platforms', $configuration->current_platforms ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $platform }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">What's Working?</label>
                                <textarea name="whats_working" rows="2" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('whats_working', $configuration->whats_working) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">What's Not Working?</label>
                                <textarea name="whats_not_working" rows="2" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('whats_not_working', $configuration->whats_not_working) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep = 2" 
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Previous
                        </button>
                        <button type="button" @click="currentStep = 4" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Next Step
                        </button>
                    </div>
                </div>

                <!-- Step 4: Content & Preferences -->
                <div x-show="currentStep === 4" x-transition x-cloak>
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Content strategy and preferences</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Content Types Needed * (Select all that apply)</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="content_types[]" value="social_posts" 
                                           {{ in_array('social_posts', old('content_types', $configuration->content_types ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Social Media Posts</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="content_types[]" value="blog_articles" 
                                           {{ in_array('blog_articles', old('content_types', $configuration->content_types ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Blog Articles</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="content_types[]" value="ads" 
                                           {{ in_array('ads', old('content_types', $configuration->content_types ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Ad Campaigns</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="content_types[]" value="email_campaigns" 
                                           {{ in_array('email_campaigns', old('content_types', $configuration->content_types ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Email Campaigns</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Posting Frequency *</label>
                            <select name="posting_frequency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select frequency...</option>
                                <option value="daily" {{ old('posting_frequency', $configuration->posting_frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="3x_week" {{ old('posting_frequency', $configuration->posting_frequency) == '3x_week' ? 'selected' : '' }}>3x per Week</option>
                                <option value="weekly" {{ old('posting_frequency', $configuration->posting_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="biweekly" {{ old('posting_frequency', $configuration->posting_frequency) == 'biweekly' ? 'selected' : '' }}>Bi-weekly</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Focus Keywords</label>
                            <input type="text" name="focus_keywords" value="{{ old('focus_keywords', is_array($configuration->focus_keywords ?? null) ? implode(', ', $configuration->focus_keywords) : '') }}" 
                                   placeholder="keyword1, keyword2, keyword3" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Separate keywords with commas</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Topics to Avoid</label>
                            <textarea name="topics_to_avoid" rows="2" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('topics_to_avoid', $configuration->topics_to_avoid) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Any sensitive topics or competitors you don't want mentioned</p>
                        </div>

                        <div class="border-t pt-6 mt-6">
                            <h4 class="font-semibold text-gray-900 mb-4">Communication Preferences</h4>
                            
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="requires_approval" value="1" 
                                           {{ old('requires_approval', $configuration->requires_approval ?? true) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">I want to approve content before publishing</span>
                                </label>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Communication Method *</label>
                                    <select name="communication_preference" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select method...</option>
                                        <option value="dashboard" {{ old('communication_preference', $configuration->communication_preference) == 'dashboard' ? 'selected' : '' }}>Dashboard Only</option>
                                        <option value="email" {{ old('communication_preference', $configuration->communication_preference) == 'email' ? 'selected' : '' }}>Email Notifications</option>
                                        <option value="both" {{ old('communication_preference', $configuration->communication_preference) == 'both' ? 'selected' : '' }}>Both</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep = 3" 
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Previous
                        </button>
                        <button type="submit" 
                                class="px-8 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            Complete Setup
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
