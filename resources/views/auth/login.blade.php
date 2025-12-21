<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - LiLi</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white min-h-screen" x-data="{ showPassword: false, isSubmitting: false }">
    
    <div class="min-h-screen flex">
        <!-- Left Column - Login Form -->
        <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24 py-12 bg-gray-50">
            <div class="mx-auto w-full max-w-sm">
                <div class="mb-8">
                    <a href="/" class="flex items-center gap-2 text-lili-600 hover:text-lili-700 transition mb-8 group">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span class="text-sm font-medium">Back to home</span>
                    </a>
                    
                    <div>
                        <a href="/" class="inline-block">
                            <h1 class="text-3xl font-bold text-lili-600 mb-2">LiLi</h1>
                        </a>
                        <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
                        <p class="mt-2 text-gray-600">Sign in to access your virtual employees</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-r-lg relative" role="alert">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="font-medium text-sm">Please fix the following errors:</p>
                                <ul class="mt-1 text-sm space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button @click="show = false" class="ml-4 text-red-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-r-lg relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                </svg>
                                <span class="text-sm font-medium">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-green-400 hover:text-green-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" @submit="isSubmitting = true" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-lili-500 focus:border-transparent transition placeholder-gray-400 text-gray-900 bg-white"
                                   placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                                   class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-lili-500 focus:border-transparent transition placeholder-gray-400 text-gray-900 bg-white"
                                   placeholder="Enter your password">
                            <button type="button" @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" name="remember" 
                                   class="rounded border-gray-300 text-lili-600 focus:ring-lili-500 focus:ring-offset-0 transition">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition">Remember me</span>
                        </label>

                        <a href="#" class="text-sm font-medium text-lili-600 hover:text-lili-700 transition">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white bg-gradient-to-r from-lili-600 to-lili-700 hover:from-lili-700 hover:to-lili-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lili-500 transition transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <span x-show="!isSubmitting">Sign in</span>
                        <span x-show="isSubmitting" class="flex items-center" style="display: none;">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Signing in...
                        </span>
                    </button>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-gray-50 text-gray-500 font-medium">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('facebook.redirect') }}" 
                           class="w-full flex items-center justify-center gap-3 px-4 py-3 border-2 border-gray-200 rounded-xl hover:border-lili-300 hover:bg-white transition transform hover:scale-[1.02] active:scale-[0.98] group bg-white">
                            <svg class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span class="font-medium text-gray-700 group-hover:text-gray-900">Continue with Facebook</span>
                        </a>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="font-semibold text-lili-600 hover:text-lili-700 transition">
                            Create account
                        </a>
                    </p>
                </div>
                
                <p class="mt-6 text-center text-xs text-gray-500">
                    By signing in, you agree to our 
                    <a href="#" class="underline hover:text-gray-700">Terms</a> and 
                    <a href="#" class="underline hover:text-gray-700">Privacy Policy</a>
                </p>
            </div>
        </div>
        
        <!-- Right Column - Branded Section -->
        <div class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-gray-900 via-lili-900 to-gray-900 relative overflow-hidden">
            <!-- Animated background elements -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 -right-4 w-96 h-96 bg-lili-600 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
                <div class="absolute bottom-0 -left-4 w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl animate-blob" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl animate-blob" style="animation-delay: 4s;"></div>
            </div>
            
            <div class="relative z-10 flex flex-col justify-center px-12 py-12 w-full">
                <div class="text-white space-y-6">
                    <h2 class="text-4xl font-bold leading-tight">
                        Your AI Virtual<br/>Employees Await
                    </h2>
                    <p class="text-lg text-gray-300">
                        Join thousands of businesses using AI-powered virtual employees to transform their marketing and social media presence.
                    </p>
                    
                    <!-- Digital illustration -->
                    <div class="mt-8">
                        <svg viewBox="0 0 600 400" class="w-full max-w-md mx-auto" xmlns="http://www.w3.org/2000/svg">
                            <!-- Computer screen -->
                            <rect x="100" y="80" width="400" height="240" rx="8" fill="#1e293b" stroke="#475569" stroke-width="2"/>
                            <rect x="110" y="90" width="380" height="200" fill="#0f172a"/>
                            
                            <!-- Code lines -->
                            <line x1="130" y1="110" x2="250" y2="110" stroke="#6366f1" stroke-width="3" stroke-linecap="round"/>
                            <line x1="130" y1="130" x2="300" y2="130" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round"/>
                            <line x1="130" y1="150" x2="220" y2="150" stroke="#6366f1" stroke-width="3" stroke-linecap="round"/>
                            <line x1="130" y1="170" x2="280" y2="170" stroke="#a855f7" stroke-width="3" stroke-linecap="round"/>
                            
                            <!-- AI brain icon -->
                            <circle cx="400" cy="170" r="40" fill="#8b5cf6" opacity="0.2"/>
                            <circle cx="400" cy="170" r="30" fill="none" stroke="#a855f7" stroke-width="2"/>
                            <circle cx="390" cy="165" r="3" fill="#c084fc"/>
                            <circle cx="410" cy="165" r="3" fill="#c084fc"/>
                            <circle cx="400" cy="180" r="3" fill="#c084fc"/>
                            <line x1="390" y1="165" x2="400" y2="180" stroke="#c084fc" stroke-width="1.5"/>
                            <line x1="410" y1="165" x2="400" y2="180" stroke="#c084fc" stroke-width="1.5"/>
                            
                            <!-- Chart/Analytics -->
                            <rect x="130" y="200" width="50" height="60" fill="#6366f1" opacity="0.6" rx="2"/>
                            <rect x="190" y="180" width="50" height="80" fill="#8b5cf6" opacity="0.6" rx="2"/>
                            <rect x="250" y="160" width="50" height="100" fill="#a855f7" opacity="0.6" rx="2"/>
                            
                            <!-- Stand -->
                            <rect x="280" y="320" width="40" height="60" fill="#475569"/>
                            <ellipse cx="300" cy="380" rx="80" ry="15" fill="#334155"/>
                            
                            <!-- Floating elements -->
                            <circle cx="150" cy="50" r="8" fill="#6366f1" opacity="0.6">
                                <animate attributeName="cy" values="50;40;50" dur="3s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="450" cy="60" r="6" fill="#8b5cf6" opacity="0.6">
                                <animate attributeName="cy" values="60;50;60" dur="4s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="500" cy="300" r="10" fill="#a855f7" opacity="0.6">
                                <animate attributeName="cy" values="300;290;300" dur="3.5s" repeatCount="indefinite"/>
                            </circle>
                        </svg>
                    </div>
                    
                    <!-- Features list -->
                    <div class="mt-8 space-y-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-lili-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <p class="font-semibold">AI-Powered Marketing</p>
                                <p class="text-sm text-gray-400">Automated content creation 24/7</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-lili-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <p class="font-semibold">Multi-Niche Expertise</p>
                                <p class="text-sm text-gray-400">Trained across 12+ industries</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-lili-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <p class="font-semibold">Real-Time Analytics</p>
                                <p class="text-sm text-gray-400">Track performance insights instantly</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
