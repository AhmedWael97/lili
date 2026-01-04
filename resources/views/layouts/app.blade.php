<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LiLi') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 fixed h-full z-10 hidden md:block">
            <div class="h-16 flex items-center px-6 border-b border-gray-200">
                <a href="/dashboard" class="text-xl font-bold text-lili-600 flex items-center gap-2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>LiLi</span>
                </a>
            </div>
            
            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">

                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Tools</div>

                <a href="{{ route('market-research.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('market-research.*') ? 'bg-lili-50 text-lili-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Market Research
                </a>

                {{-- <div class="mt-8 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Settings</div>

                <a href="{{ route('dashboard.settings') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard.settings') ? 'bg-lili-50 text-lili-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </a>

                <a href="{{ route('dashboard.billing') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard.billing') ? 'bg-lili-50 text-lili-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Billing
                </a> --}}
            </nav>
            
            <div class="absolute bottom-0 w-full border-t border-gray-200 p-4 bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-lili-100 flex items-center justify-center text-lili-600 font-bold">
                        {{ substr(Auth::user()?->name ?? 'User', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ Auth::user()?->name ?? 'John Doe' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ Auth::user()?->email ?? 'john@example.com' }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-4">
                    <button class="p-2 text-gray-400 hover:text-gray-600 relative">
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </button>
                    
                    <div class="h-8 w-px bg-gray-200 mx-2"></div>
                    
                    <a href="{{ route('market-research.index') }}" class="bg-lili-600 hover:bg-lili-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        Market Research
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
