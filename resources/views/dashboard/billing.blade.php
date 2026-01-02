@extends('layouts.marketing-os')

@section('title', 'Billing')

@section('content')
<div class="py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Billing & Subscription</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Current Plan -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ auth()->user()->subscription->package_name ?? 'Free' }} Plan</h3>
                    <p class="text-gray-600">
                        @if(auth()->user()->subscription && auth()->user()->subscription->package_name !== 'free')
                        ${{ auth()->user()->subscription->package_price }}/month
                        @else
                        Free forever
                        @endif
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Active
                </span>
            </div>
            
            @if(auth()->user()->subscription && auth()->user()->subscription->package_name !== 'free')
            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm text-gray-600 mb-2">Next billing date: <span class="font-medium">{{ auth()->user()->subscription->current_period_end->format('F j, Y') }}</span></p>
                <div class="flex space-x-4 mt-4">
                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">Change Plan</button>
                    <button class="text-red-600 hover:text-red-700 text-sm font-medium">Cancel Subscription</button>
                </div>
            </div>
            @endif
        </div>

        <!-- Available Plans -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Upgrade Your Plan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Starter Plan -->
                <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-blue-500 transition">
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Starter</h4>
                    <p class="text-3xl font-bold text-gray-900 mb-4">$29<span class="text-sm text-gray-600">/mo</span></p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            3 Facebook Pages
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            100 Posts/Month
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            500 Comment Replies
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            100 DM Responses
                        </li>
                    </ul>
                    <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Upgrade to Starter
                    </button>
                </div>

                <!-- Professional Plan -->
                <div class="border-2 border-blue-500 rounded-lg p-6 relative">
                    <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs px-3 py-1 rounded-bl-lg rounded-tr-lg">
                        Popular
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Professional</h4>
                    <p class="text-3xl font-bold text-gray-900 mb-4">$99<span class="text-sm text-gray-600">/mo</span></p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            10 Facebook Pages
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            500 Posts/Month
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Unlimited Replies
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            $5K Ad Spend/Month
                        </li>
                    </ul>
                    <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Upgrade to Professional
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Summary -->
    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Usage</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Posts</span>
                        <span class="font-medium">{{ $usage->posts_count ?? 0 }} / {{ $packageFeatures['posts_per_month'] ?? 10 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($packageFeatures['posts_per_month'] ?? 10) > 0 ? (($usage->posts_count ?? 0) / ($packageFeatures['posts_per_month'] ?? 10) * 100) : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Comment Replies</span>
                        <span class="font-medium">{{ $usage->comment_replies_count ?? 0 }} / {{ $packageFeatures['comment_replies_per_month'] ?? 50 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($packageFeatures['comment_replies_per_month'] ?? 50) > 0 ? (($usage->comment_replies_count ?? 0) / ($packageFeatures['comment_replies_per_month'] ?? 50) * 100) : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">DM Responses</span>
                        <span class="font-medium">{{ $usage->messages_count ?? 0 }} / {{ $packageFeatures['dm_responses_per_month'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($packageFeatures['dm_responses_per_month'] ?? 0) > 0 ? (($usage->messages_count ?? 0) / ($packageFeatures['dm_responses_per_month'] ?? 0) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
@endsection
