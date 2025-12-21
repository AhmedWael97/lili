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
                <!-- Facebook Login Button -->
                <div class="flex justify-center mb-4">
                    <fb:login-button 
                        config_id="859748670085408"
                        onlogin="checkLoginState();">
                    </fb:login-button>
                </div>
                
                {{-- <p class="text-gray-500 text-sm mb-2">or</p>
                <a href="{{ route('facebook.redirect') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center"></a>
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
                    
                    <!-- Connect Button triggers modal -->
                    <button onclick="document.getElementById('permission-modal').classList.remove('hidden')" 
                            class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Connect Facebook
                    </button>
                </div>
                
                <!-- Permission Explanation Modal -->
                <div id="permission-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-lg bg-white">
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-2xl font-bold text-gray-900">Connect Facebook Pages</h3>
                                <button onclick="document.getElementById('permission-modal').classList.add('hidden')" 
                                        class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="text-left space-y-4">
                                <p class="text-gray-600">To help you manage your Facebook Pages with AI-powered content and analytics, we need the following permissions:</p>
                                
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">View your Pages</p>
                                            <p class="text-sm text-gray-600">To display your Pages and their information in your dashboard</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">Read engagement data</p>
                                            <p class="text-sm text-gray-600">To provide analytics on post performance (likes, comments, shares)</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">Manage Page settings</p>
                                            <p class="text-sm text-gray-600">To access Page configuration and receive real-time notifications</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">Post on your behalf</p>
                                            <p class="text-sm text-gray-600">To publish AI-generated content when you approve it</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-sm text-blue-800">
                                            <strong>Your privacy matters:</strong> We only access data necessary to provide our services. 
                                            You remain in full control and can disconnect at any time.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-3 mt-6">
                                <button onclick="document.getElementById('permission-modal').classList.add('hidden')" 
                                        class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                                    Cancel
                                </button>
                                <a href="{{ route('facebook.redirect') }}" 
                                   class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Continue to Facebook
                                </a>
                            </div>
                        </div>
                    </div>
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
