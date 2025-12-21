@extends('dashboard.layout')

@section('title', 'Error 404')
@section('page-title', 'Page Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-300">404</h1>
        <h2 class="text-3xl font-semibold text-gray-800 mt-4">Page Not Found</h2>
        <p class="text-gray-600 mt-2 mb-8">The page you're looking for doesn't exist or has been moved.</p>
        
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
