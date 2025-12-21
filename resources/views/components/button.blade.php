@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'bg-lili-600 text-white hover:bg-lili-700 focus:ring-lili-500 border border-transparent shadow-[0_0_15px_rgba(35,76,106,0.5)] hover:shadow-[0_0_25px_rgba(35,76,106,0.6)] transition-all duration-300',
        'secondary' => 'bg-white text-gray-700 hover:bg-gray-50 focus:ring-lili-500 border border-gray-300 shadow-sm hover:shadow-md',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 border border-transparent shadow-lg shadow-red-500/30',
        'ghost' => 'bg-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:ring-gray-500',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs rounded-md',
        'md' => 'px-4 py-2 text-sm rounded-md',
        'lg' => 'px-6 py-3 text-base rounded-lg',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
