<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LiLi - AI Virtual Employees for Marketing & Social Media Automation</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="LiLi provides AI virtual employees specialized in marketing, content creation, and social media management. Hire virtual team members for copywriting, design, strategy, community management, and advertising across multiple niches.">
    <meta name="keywords" content="AI virtual employees, virtual marketing team, AI social media manager, automated content creation, AI copywriter, virtual community manager, AI marketing automation, social media automation, virtual team members, AI employees">
    <meta name="author" content="LiLi Platform">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="LiLi - AI Virtual Employees for Your Marketing Team">
    <meta property="og:description" content="Build your virtual marketing team with AI employees specialized in different niches. From strategy to execution, 24/7.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="LiLi - AI Virtual Employees for Marketing">
    <meta name="twitter:description" content="Hire AI virtual employees for your marketing team. Specialized in multiple niches, working 24/7.">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Structured Data for SEO -->
    {{-- <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "LiLi",
        "applicationCategory": "BusinessApplication",
        "description": "AI virtual employees platform for marketing automation, social media management, and content creation across multiple business niches.",
        "offers": {
            "@type": "AggregateOffer",
            "priceCurrency": "USD",
            "lowPrice": "0",
            "highPrice": "299",
            "offerCount": "4"
        },
        "operatingSystem": "Web-based",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "127"
        }
    }
    </script> --}}
</head>
<body class="font-sans antialiased text-gray-900 bg-white" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1000)">
    
    <div x-show="loading" 
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-white">
         <div class="flex flex-col items-center gap-4">
            <div class="relative">
                <div class="w-16 h-16 border-4 border-lili-100 rounded-full"></div>
                <div class="w-16 h-16 border-4 border-lili-600 rounded-full animate-spin absolute top-0 left-0 border-t-transparent"></div>
            </div>
            <span class="text-lili-600 font-bold text-xl tracking-widest animate-pulse">LiLi</span>
         </div>
    </div>
    
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center gap-2 text-xl font-bold text-lili-600">
                        <span>LiLi</span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="/dashboard" class="text-sm font-medium text-gray-700 hover:text-gray-900">Dashboard</a>
                    @endauth
                    @guest
                        <a href="/login" class="text-sm font-medium text-gray-700 hover:text-gray-900">Log in</a>
                        <a href="/register" class="px-3 py-1.5 text-xs bg-lili-600 text-white rounded-full hover:bg-lili-700">Sign Up</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <div class="relative overflow-hidden bg-white pt-16 pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">Hire AI Virtual Employees</span>
                    <span class="block text-lili-600">For Your Company Team</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Build your virtual workforce with specialized AI employees across multiple niches.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/register" class="px-6 py-3 bg-lili-600 text-white rounded-full hover:bg-lili-700">Hire Your Virtual Team</a>
                    <a href="#features" class="px-6 py-3 bg-white text-gray-700 border border-gray-300 rounded-full hover:bg-gray-50">Learn More</a>
                </div>
                <p class="mt-4 text-sm text-gray-500">
                    Start your 3-day free trial. No credit card required. 100+ businesses trust our virtual employees.
                </p>
            </div>
        </div>
        
        <!-- Decorative background elements -->
        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-full h-full z-0 opacity-30 pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
            <div class="absolute top-20 right-10 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
        </div>
    </div>

    <div id="features" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-gray-900">Your AI Team Across Multiple Niches</h2>
                <p class="mt-4 text-xl text-gray-500">Virtual employees trained in specific industries</p>
            </div>

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="text-3xl mb-4">🧠</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Marketing Strategist</h3>
                    <p class="text-gray-500">Analyzes performance, researches trends, and creates data-driven content calendars 24/7.</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="text-3xl mb-4">✍️</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Professional Copywriter</h3>
                    <p class="text-gray-500">Writes compelling captions, product descriptions, and ad copy in your brand voice.</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="text-3xl mb-4">🎨</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Creative Designer</h3>
                    <p class="text-gray-500">Creates niche-specific visuals, product images, and social media graphics.</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="text-3xl mb-4">💬</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Community Manager</h3>
                    <p class="text-gray-500">Responds to comments, DMs, and customer inquiries 24/7 in your brand voice.</p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="text-3xl mb-4">📊</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Advertising Specialist</h3>
                    <p class="text-gray-500">Builds and optimizes campaigns across Facebook, Instagram, and Google.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900">Virtual Employees Across Every Niche</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">🛍️</span>
                    <p class="text-sm font-medium text-gray-700">E-Commerce</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">💼</span>
                    <p class="text-sm font-medium text-gray-700">B2B SaaS</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">🏠</span>
                    <p class="text-sm font-medium text-gray-700">Real Estate</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">🍽️</span>
                    <p class="text-sm font-medium text-gray-700">Restaurants</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">💪</span>
                    <p class="text-sm font-medium text-gray-700">Fitness</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">👗</span>
                    <p class="text-sm font-medium text-gray-700">Fashion</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">💊</span>
                    <p class="text-sm font-medium text-gray-700">Healthcare</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">📚</span>
                    <p class="text-sm font-medium text-gray-700">Education</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">✈️</span>
                    <p class="text-sm font-medium text-gray-700">Travel</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">🎨</span>
                    <p class="text-sm font-medium text-gray-700">Creative Arts</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">🔧</span>
                    <p class="text-sm font-medium text-gray-700">Professional Services</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <span class="text-2xl mb-2 block">🎯</span>
                    <p class="text-sm font-medium text-gray-700">+ Many More</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div id="how-it-works" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">How to Build Your Virtual Team</h2>
                <p class="mt-4 text-lg text-gray-500">Hire AI virtual employees in minutes, not weeks.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                <div>
                    <div class="w-16 h-16 bg-lili-100 rounded-full flex items-center justify-center text-lili-600 text-2xl font-bold mx-auto mb-6">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Choose Your Virtual Team</h3>
                    <p class="text-gray-500">Select which virtual employees you need based on your niche and business goals.</p>
                </div>
                <div>
                    <div class="w-16 h-16 bg-lili-100 rounded-full flex items-center justify-center text-lili-600 text-2xl font-bold mx-auto mb-6">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Train Them on Your Brand</h3>
                    <p class="text-gray-500">Your virtual employees learn your brand voice, target audience, and industry-specific requirements instantly.</p>
                </div>
                <div>
                    <div class="w-16 h-16 bg-lili-100 rounded-full flex items-center justify-center text-lili-600 text-2xl font-bold mx-auto mb-6">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">They Work 24/7</h3>
                    <p class="text-gray-500">Your AI virtual workforce starts creating content, managing communities, and optimizing campaigns around the clock.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="bg-gray-50 py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Businesses Trust Our Virtual Employees</h2>
                <p class="mt-4 text-lg text-gray-500">See how virtual teams are transforming businesses across different niches.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-1 text-yellow-400 mb-4">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-gray-600 mb-6">"Our virtual marketing team has completely transformed how we handle social media. These AI employees understand our e-commerce niche perfectly and create content that converts."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                        <div>
                            <p class="font-bold text-gray-900">Sarah Johnson</p>
                            <p class="text-sm text-gray-500">Marketing Director</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-1 text-yellow-400 mb-4">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-gray-600 mb-6">"Hiring virtual employees was the best decision for our SaaS startup. We have a full marketing team working for us 24/7 at a fraction of traditional hiring costs."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                        <div>
                            <p class="font-bold text-gray-900">Mike Chen</p>
                            <p class="text-sm text-gray-500">Founder, TechStart</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-1 text-yellow-400 mb-4">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-gray-600 mb-6">"As a restaurant owner, I was skeptical about AI. But our virtual community manager responds better than our previous staff. Game-changer!"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                        <div>
                            <p class="font-bold text-gray-900">Emily Rodriguez</p>
                            <p class="text-sm text-gray-500">Owner, Boutique Cafe</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Form -->
    <div class="bg-lili-50 py-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-8">Get in Touch</h2>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-lili-500 focus:ring-lili-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-lili-500 focus:ring-lili-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-lili-500 focus:ring-lili-500"></textarea>
                    </div>
                    <button type="submit" class="w-full px-6 py-3 bg-lili-600 text-white rounded-lg hover:bg-lili-700">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="col-span-2 md:col-span-1">
                    <a href="/" class="flex items-center gap-2 text-xl font-bold text-lili-600 mb-4">
                        <span>LiLi</span>
                    </a>
                    <p class="text-gray-500 text-sm">Building AI virtual employees for businesses across multiple niches. Your virtual workforce, working 24/7.</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase mb-4">Product</h3>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-base text-gray-500 hover:text-gray-900">Features</a></li>
                        <li><a href="#pricing" class="text-base text-gray-500 hover:text-gray-900">Pricing</a></li>
                        <li><a href="/ai-studio" class="text-base text-gray-500 hover:text-gray-900">AI Studio</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase mb-4">Company</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-base text-gray-500 hover:text-gray-900">About</a></li>
                        <li><a href="#contact" class="text-base text-gray-500 hover:text-gray-900">Contact</a></li>
                        <li><a href="#" class="text-base text-gray-500 hover:text-gray-900">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase mb-4">Legal</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-base text-gray-500 hover:text-gray-900">Privacy</a></li>
                        <li><a href="#" class="text-base text-gray-500 hover:text-gray-900">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-200 pt-8">
                <p class="text-base text-gray-400 text-center">&copy; 2025 LiLi Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
