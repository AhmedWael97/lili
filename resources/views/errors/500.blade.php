@extends('dashboard.layout')

@section('title', 'Error 500')
@section('page-title', 'Server Error')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-300">500</h1>
        <h2 class="text-3xl font-semibold text-gray-800 mt-4">Something Went Wrong</h2>
        <p class="text-gray-600 mt-2 mb-8">We're working on fixing the issue. Please try again later.</p>
        
        <div class="flex justify-center space-x-4">
            <a href="{{ route('dashboard.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Go to Dashboard
            </a>
            <button onclick="window.location.reload()" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">
                Reload Page
            </button>
        </div>
    </div>
</div>
@endsection
