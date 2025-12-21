@extends('dashboard.layout')

@section('title', 'Access Denied')
@section('page-title', 'Access Denied')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-300">403</h1>
        <h2 class="text-3xl font-semibold text-gray-800 mt-4">Access Denied</h2>
        <p class="text-gray-600 mt-2 mb-8">You don't have permission to access this resource.</p>
        
        @if(isset($upgrade) && $upgrade)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 max-w-md mx-auto mb-8">
            <h3 class="font-semibold text-yellow-900 mb-2">Upgrade Required</h3>
            <p class="text-yellow-800 text-sm mb-4">This feature requires a higher subscription plan.</p>
            <a href="{{ route('dashboard.billing') }}" class="inline-block bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">
                View Plans
            </a>
        </div>
        @endif
        
        <div class="flex justify-center space-x-4">
            <a href="{{ route('dashboard.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Go to Dashboard
            </a>
            <button onclick="window.history.back()" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">
                Go Back
            </button>
        </div>
    </div>
</div>
@endsection
