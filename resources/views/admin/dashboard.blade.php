@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('page-title', 'System Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm font-medium text-gray-600">Total Users</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</p>
        <p class="text-sm text-gray-500 mt-1">Registered accounts</p>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm font-medium text-gray-600">Active Subscriptions</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['active_subscriptions'] }}</p>
        <p class="text-sm text-green-600 mt-1">Paying customers</p>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm font-medium text-gray-600">Total Content</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_content'] }}</p>
        <p class="text-sm text-gray-500 mt-1">Posts created</p>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format(array_sum($stats['monthly_revenue']), 2) }}</p>
        <p class="text-sm text-gray-500 mt-1">Current month</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Users</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($recentUsers as $user)
                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                    <div>
                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium px-2 py-1 rounded {{ $user->subscription ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->subscription->package_name ?? 'Free' }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($recentActivity as $log)
                <div class="flex items-start space-x-3 text-sm">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-gray-900">
                            <span class="font-medium">{{ $log->user->name ?? 'System' }}</span>
                            {{ $log->action }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Revenue by Package -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Revenue by Package</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-4 gap-4">
            @foreach($stats['monthly_revenue'] as $package => $revenue)
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 capitalize">{{ $package }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($revenue, 2) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
