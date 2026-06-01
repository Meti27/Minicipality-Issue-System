<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Municipality Issue System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased bg-muni-cream">
        <a href="#main-content"
           class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-lg">
            Skip to main content
        </a>
        <div class="min-h-screen">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white border-b border-gray-200 shadow-sm">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main id="main-content" class="{{ auth()->check() && auth()->user()->role === 'citizen' ? 'pb-20 sm:pb-0' : '' }}">
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
