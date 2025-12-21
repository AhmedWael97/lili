@extends('dashboard.layout')

@section('title', 'Platforms')
@section('page-title', 'Connected Platforms')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Facebook Connection -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Facebook Pages</h3>
                @if(auth()->user()->connectedPlatforms->where('platform', 'facebook')->count() == 0)
                <a href="{{ route('facebook.redirect') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Connect Facebook
                </a>
                @else
                <form method="POST" action="{{ route('facebook.disconnect') }}">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Disconnect
                    </button>
                </form>
                @endif
            </div>
            
            <div class="p-6">
                @if(auth()->user()->facebookPages->count() > 0)
                <div class="space-y-4">
                    @foreach(auth()->user()->facebookPages as $page)
                    <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $page->page_name }}</h4>
                                <p class="text-sm text-gray-600">{{ number_format($page->follower_count) }} followers</p>
                                @if($page->page_category)
                                <p class="text-xs text-gray-500">{{ $page->page_category }}</p>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Connected
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Connected Pages</h3>
                    <p class="text-gray-600 mb-4">Connect your Facebook account to start managing your pages</p>
                    <a href="{{ route('facebook.redirect') }}" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Connect Facebook
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Usage Limits -->
    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Package Limits</h3>
            
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Facebook Pages</span>
                        <span class="font-medium">{{ auth()->user()->facebookPages->count() }} / {{ $limits['facebook_pages'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $limits['facebook_pages'] > 0 ? (auth()->user()->facebookPages->count() / $limits['facebook_pages'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Posts This Month</span>
                        <span class="font-medium">{{ auth()->user()->getCurrentUsage()['posts_used'] ?? 0 }} / {{ $limits['posts_per_month'] ?? 10 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($limits['posts_per_month'] ?? 10) > 0 ? ((auth()->user()->getCurrentUsage()['posts_used'] ?? 0) / ($limits['posts_per_month'] ?? 10) * 100) : 0 }}%"></div>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-2">Current Plan</h4>
                    <p class="text-2xl font-bold text-blue-600">{{ auth()->user()->subscription->package_name ?? 'Free' }}</p>
                    <a href="{{ route('dashboard.billing') }}" class="mt-4 block text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                        Upgrade Plan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
