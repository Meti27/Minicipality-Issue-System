<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Municipality Issue System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex">

            {{-- Left branding panel --}}
            <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-gradient-to-br from-muni-darker via-muni-dark to-muni-dark flex-col items-center justify-center p-12 relative overflow-hidden">
                <div class="absolute top-1/4 -left-16 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-1/4 right-0 w-72 h-72 bg-indigo-900/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative text-center max-w-sm">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-6 border border-white/20 shadow-lg">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-3">Municipality Issue System</h1>
                    <p class="text-blue-100 leading-relaxed text-sm">
                        Submit, track, and resolve infrastructure complaints in your community — transparently and efficiently.
                    </p>

                    <div class="mt-10 grid grid-cols-3 gap-6 pt-8 border-t border-white/20">
                        @foreach([
                            ['value' => '6',    'label' => 'Categories'],
                            ['value' => '100%', 'label' => 'Transparent'],
                            ['value' => '24/7', 'label' => 'Available'],
                        ] as $stat)
                            <div class="text-center">
                                <p class="text-xl font-bold text-white">{{ $stat['value'] }}</p>
                                <p class="text-xs text-blue-200 mt-0.5">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 space-y-3 text-left">
                        @foreach([
                            'Report potholes, broken lights & more',
                            'Track your complaint in real time',
                            'Get notified on every status update',
                        ] as $bullet)
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-blue-100">{{ $bullet }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right form panel --}}
            <div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-12 bg-white">
                {{-- Mobile logo --}}
                <div class="lg:hidden mb-8 text-center">
                    <a href="/" class="inline-flex items-center gap-2 text-muni-dark font-bold text-xl">
                        <div class="w-8 h-8 bg-muni-dark rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        Municipality IS
                    </a>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>

                <p class="mt-8 text-xs text-gray-400">
                    <a href="/" class="hover:text-gray-600 transition">&larr; Back to home</a>
                </p>
            </div>
        </div>
    </body>
</html>
