@extends('dashboard.layout')

@section('title', 'Analytics')
@section('page-title', 'Analytics & Insights')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-600">Total Reach</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">24.5K</p>
        <p class="text-sm text-green-600 mt-1">↑ 12.5%</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-600">Engagement Rate</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">4.8%</p>
        <p class="text-sm text-green-600 mt-1">↑ 2.3%</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-600">Post Impressions</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">89.2K</p>
        <p class="text-sm text-green-600 mt-1">↑ 8.7%</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-600">New Followers</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">+342</p>
        <p class="text-sm text-green-600 mt-1">↑ 18.2%</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Overview</h3>
    <div class="h-64 flex items-center justify-center text-gray-500">
        Chart will be displayed here (integrate Chart.js or similar)
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Posts</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                <div class="flex-1">
                    <p class="text-sm text-gray-900 font-medium">Post about new product launch</p>
                    <p class="text-xs text-gray-500">2 days ago</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">2.4K</p>
                    <p class="text-xs text-gray-500">engagements</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Audience Demographics</h3>
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>25-34 years</span>
                    <span>45%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>35-44 years</span>
                    <span>30%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 30%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>18-24 years</span>
                    <span>25%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
