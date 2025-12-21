<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account - LiLi</title>
    
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
        .package-card input:checked ~ .package-content {
            @apply border-lili-500 bg-lili-50;
        }
        .package-card input:checked ~ .package-content .checkmark {
            @apply opacity-100;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white min-h-screen" 
      x-data="{ 
          showPassword: false, 
          showConfirmPassword: false,
          isSubmitting: false,
          selectedPackage: '{{ $packages->where('name', 'Professional')->first()->id ?? $packages->skip(2)->first()->id ?? '' }}',
          passwordStrength: 0,
          checkPasswordStrength() {
              const password = this.$refs.password.value;
              let strength = 0;
              if (password.length >= 8) strength++;
              if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
              if (password.match(/\d/)) strength++;
              if (password.match(/[^a-zA-Z\d]/)) strength++;
              this.passwordStrength = strength;
          }
      }">
    
    <div class="min-h-screen flex">
        <!-- Left Column - Register Form -->
        <div class="flex-1 flex flex-col justify-start px-4 sm:px-6 lg:px-16 xl:px-20 py-8 bg-gray-50 overflow-y-auto max-h-screen">
            <div class="mx-auto w-full max-w-lg">
                <div class="mb-6">
                    <a href="/" class="flex items-center gap-2 text-lili-600 hover:text-lili-700 transition mb-6 group">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span class="text-sm font-medium">Back to home</span>
                    </a>
                    
                    <div>
                        <a href="/" class="inline-block">
                            <h1 class="text-3xl font-bold text-lili-600 mb-2">LiLi</h1>
                        </a>
                        <h2 class="text-2xl font-bold text-gray-900">Create your account</h2>
                        <p class="mt-2 text-gray-600">Start hiring your AI virtual employees</p>
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

                <form method="POST" action="{{ route('register') }}" @submit="isSubmitting = true" class="space-y-5">
                    @csrf

                    <!-- Personal Information -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-lili-100 text-lili-600 text-sm font-bold mr-2">1</span>
                            Personal Info
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-lili-500 focus:border-transparent transition placeholder-gray-400 bg-white"
                                           placeholder="John Doe">
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                        </svg>
                                    </div>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-lili-500 focus:border-transparent transition placeholder-gray-400 bg-white"
                                           placeholder="you@example.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-lili-100 text-lili-600 text-sm font-bold mr-2">2</span>
                            Security
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input id="password" x-ref="password" :type="showPassword ? 'text' : 'password'" 
                                           name="password" required @input="checkPasswordStrength"
                                           class="block w-full pl-10 pr-12 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-lili-500 focus:border-transparent transition placeholder-gray-400 bg-white"
                                           placeholder="Min. 8 characters">
                                    <button type="button" @click="showPassword = !showPassword" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <div class="flex gap-1">
                                        <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 1 ? 'bg-red-500' : 'bg-gray-200'"></div>
                                        <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 2 ? 'bg-yellow-500' : 'bg-gray-200'"></div>
                                        <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 3 ? 'bg-blue-500' : 'bg-gray-200'"></div>
                                        <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 4 ? 'bg-green-500' : 'bg-gray-200'"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1" x-show="passwordStrength > 0">
                                        <span x-show="passwordStrength === 1">Weak</span>
                                        <span x-show="passwordStrength === 2">Fair</span>
                                        <span x-show="passwordStrength === 3">Good</span>
                                        <span x-show="passwordStrength === 4">Strong</span>
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" 
                                           name="password_confirmation" required
                                           class="block w-full pl-10 pr-12 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-lili-500 focus:border-transparent transition placeholder-gray-400 bg-white"
                                           placeholder="Confirm your password">
                                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg x-show="!showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Package Selection -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-lili-100 text-lili-600 text-sm font-bold mr-2">3</span>
                            Choose Plan
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($packages as $package)
                            @php
                                $packageNameLower = strtolower($package->name);
                                $isPopular = $packageNameLower === 'professional';
                            @endphp
                            <label class="package-card cursor-pointer">
                                <input type="radio" name="package_id" value="{{ $package->id }}" 
                                       class="sr-only" 
                                       x-model="selectedPackage"
                                       {{ $isPopular ? 'checked' : '' }} 
                                       required>
                                <div class="package-content relative p-3 border-2 rounded-xl transition-all hover:shadow-md">
                                    <div class="checkmark absolute top-2 right-2 w-5 h-5 bg-lili-600 rounded-full flex items-center justify-center opacity-0 transition-opacity">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    
                                    @if($isPopular)
                                        <span class="absolute -top-2 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-lili-600 to-purple-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                            Popular
                                        </span>
                                    @endif
                                    
                                    <div class="mt-1">
                                        <h4 class="text-sm font-bold text-gray-900">{{ $package->name }}</h4>
                                        <div class="mt-1 flex items-baseline">
                                            <span class="text-xl font-extrabold text-gray-900">${{ number_format($package->price, 0) }}</span>
                                            <span class="ml-1 text-xs text-gray-500">/mo</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-500 text-center">3-day free trial • Cancel anytime</p>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start pt-2">
                        <input type="checkbox" name="terms" id="terms" required
                               class="mt-1 rounded border-gray-300 text-lili-600 focus:ring-lili-500 focus:ring-offset-0">
                        <label for="terms" class="ml-2 text-xs text-gray-600 leading-relaxed">
                            I agree to 
                            <a href="#" class="font-medium text-lili-600 hover:text-lili-700 underline">Terms</a> & 
                            <a href="#" class="font-medium text-lili-600 hover:text-lili-700 underline">Privacy</a>
                        </label>
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white bg-gradient-to-r from-lili-600 to-lili-700 hover:from-lili-700 hover:to-lili-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lili-500 transition transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <span x-show="!isSubmitting">Create Account</span>
                        <span x-show="isSubmitting" class="flex items-center" style="display: none;">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating...
                        </span>
                    </button>
                </form>

                <div class="mt-4">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-3 bg-gray-50 text-gray-500 font-medium">Or sign up with</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('facebook.redirect') }}" 
                           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-gray-200 rounded-xl hover:border-lili-300 hover:bg-white transition transform hover:scale-[1.02] active:scale-[0.98] group bg-white">
                            <svg class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Facebook</span>
                        </a>
                    </div>
                </div>

                <div class="mt-6 text-center pb-4">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="font-semibold text-lili-600 hover:text-lili-700 transition">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Branded Section -->
        <div class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-gray-900 via-lili-900 to-gray-900 relative overflow-hidden">
            <!-- Animated background -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 -right-4 w-96 h-96 bg-lili-600 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
                <div class="absolute bottom-0 -left-4 w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl animate-blob" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl animate-blob" style="animation-delay: 4s;"></div>
            </div>
            
            <div class="relative z-10 flex flex-col justify-center px-12 py-12 w-full">
                <div class="text-white space-y-6">
                    <div class="inline-flex items-center gap-2 bg-lili-800/50 backdrop-blur-sm px-4 py-2 rounded-full border border-lili-700">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lili-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-lili-400"></span>
                        </span>
                        <span class="text-sm font-medium">Join 10,000+ businesses</span>
                    </div>
                    
                    <h2 class="text-4xl font-bold leading-tight">
                        Build Your Virtual<br/>Marketing Team
                    </h2>
                    <p class="text-lg text-gray-300">
                        Get started in minutes with AI-powered employees that understand your business and work 24/7.
                    </p>
                    
                    <!-- Digital Team Illustration -->
                    <div class="mt-8">
                        <svg viewBox="0 0 600 400" class="w-full max-w-md mx-auto" xmlns="http://www.w3.org/2000/svg">
                            <!-- Team members circles -->
                            <circle cx="200" cy="150" r="50" fill="#6366f1" opacity="0.3"/>
                            <circle cx="300" cy="150" r="50" fill="#8b5cf6" opacity="0.3"/>
                            <circle cx="400" cy="150" r="50" fill="#a855f7" opacity="0.3"/>
                            
                            <!-- Robot heads -->
                            <rect x="175" y="135" width="50" height="45" rx="8" fill="#1e293b" stroke="#475569" stroke-width="2"/>
                            <circle cx="190" cy="150" r="4" fill="#6366f1"/>
                            <circle cx="210" cy="150" r="4" fill="#6366f1"/>
                            <rect x="187" y="165" width="26" height="3" rx="1.5" fill="#475569"/>
                            
                            <rect x="275" y="135" width="50" height="45" rx="8" fill="#1e293b" stroke="#475569" stroke-width="2"/>
                            <circle cx="290" cy="150" r="4" fill="#8b5cf6"/>
                            <circle cx="310" cy="150" r="4" fill="#8b5cf6"/>
                            <rect x="287" y="165" width="26" height="3" rx="1.5" fill="#475569"/>
                            
                            <rect x="375" y="135" width="50" height="45" rx="8" fill="#1e293b" stroke="#475569" stroke-width="2"/>
                            <circle cx="390" cy="150" r="4" fill="#a855f7"/>
                            <circle cx="410" cy="150" r="4" fill="#a855f7"/>
                            <rect x="387" y="165" width="26" height="3" rx="1.5" fill="#475569"/>
                            
                            <!-- Connecting lines -->
                            <line x1="225" y1="175" x2="275" y2="175" stroke="#475569" stroke-width="2" stroke-dasharray="5,5"/>
                            <line x1="325" y1="175" x2="375" y2="175" stroke="#475569" stroke-width="2" stroke-dasharray="5,5"/>
                            
                            <!-- Task indicators -->
                            <rect x="150" y="240" width="100" height="60" rx="8" fill="#1e293b" opacity="0.8"/>
                            <text x="200" y="265" text-anchor="middle" fill="#6366f1" font-size="12" font-weight="bold">Content</text>
                            <text x="200" y="280" text-anchor="middle" fill="#9ca3af" font-size="10">✓ Posted</text>
                            
                            <rect x="270" y="240" width="100" height="60" rx="8" fill="#1e293b" opacity="0.8"/>
                            <text x="320" y="265" text-anchor="middle" fill="#8b5cf6" font-size="12" font-weight="bold">Design</text>
                            <text x="320" y="280" text-anchor="middle" fill="#9ca3af" font-size="10">✓ Created</text>
                            
                            <rect x="390" y="240" width="100" height="60" rx="8" fill="#1e293b" opacity="0.8"/>
                            <text x="440" y="265" text-anchor="middle" fill="#a855f7" font-size="12" font-weight="bold">Analysis</text>
                            <text x="440" y="280" text-anchor="middle" fill="#9ca3af" font-size="10">✓ Done</text>
                            
                            <!-- Floating sparkles -->
                            <circle cx="150" cy="100" r="4" fill="#6366f1" opacity="0.6">
                                <animate attributeName="opacity" values="0.6;1;0.6" dur="2s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="450" cy="120" r="4" fill="#a855f7" opacity="0.6">
                                <animate attributeName="opacity" values="0.6;1;0.6" dur="2.5s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="520" cy="200" r="3" fill="#8b5cf6" opacity="0.6">
                                <animate attributeName="opacity" values="0.6;1;0.6" dur="3s" repeatCount="indefinite"/>
                            </circle>
                        </svg>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 mt-8 pt-6 border-t border-gray-700">
                        <div>
                            <p class="text-3xl font-bold text-lili-400">12+</p>
                            <p class="text-sm text-gray-400">Industries</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-lili-400">24/7</p>
                            <p class="text-sm text-gray-400">Available</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-lili-400">10K+</p>
                            <p class="text-sm text-gray-400">Businesses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
