<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Complaint & Issue Tracking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

    {{-- Navbar --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5 text-blue-700 font-bold text-lg">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="hidden sm:block">Municipality IS</span>
            </a>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative pt-16 overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700">
        <div class="absolute top-1/4 -left-32 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 right-0 w-80 h-80 bg-indigo-900/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 text-center">
            @guest
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 text-sm font-medium text-white/90 mb-8 border border-white/20">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Open to all citizens &middot; Free to use
                </div>
            @endguest

            <h1 class="text-5xl sm:text-6xl font-extrabold text-white leading-tight tracking-tight mb-6">
                Report. Track.<br class="hidden sm:block"> Resolve.
            </h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto mb-10 leading-relaxed">
                Submit infrastructure complaints — potholes, broken streetlights, water leaks and more — and follow every step from submission to resolution in real time.
            </p>

            @guest
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Register as a Citizen
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white/10 text-white font-semibold rounded-xl border border-white/25 hover:bg-white/20 transition backdrop-blur-sm text-base">
                        Sign In
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            @else
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg text-base">
                    Go to Dashboard
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endauth

            <div class="mt-16 grid grid-cols-3 gap-8 max-w-lg mx-auto border-t border-white/20 pt-8">
                @foreach([
                    ['num' => '6',     'label' => 'Issue Categories'],
                    ['num' => '100%',  'label' => 'Transparent Process'],
                    ['num' => '24/7',  'label' => 'Online Tracking'],
                ] as $s)
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white">{{ $s['num'] }}</p>
                        <p class="text-xs text-blue-200 mt-0.5">{{ $s['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="relative h-16">
            <svg viewBox="0 0 1440 64" fill="none" xmlns="http://www.w3.org/2000/svg"
                 class="absolute bottom-0 w-full" preserveAspectRatio="none">
                <path d="M0 64L48 58.7C96 53.3 192 42.7 288 42.7C384 42.7 480 53.3 576 56C672 58.7 768 53.3 864 45.3C960 37.3 1056 26.7 1152 24C1248 21.3 1344 26.7 1392 29.3L1440 32V64H0Z" fill="#f9fafb"/>
            </svg>
        </div>
    </section>

    {{-- Features --}}
    <section class="bg-gray-50 py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-900">Everything you need to report issues</h2>
                <p class="mt-3 text-gray-500 text-lg max-w-xl mx-auto">A simple, transparent process from complaint to resolution.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    [
                        'title'      => 'Easy Submission',
                        'desc'       => 'Fill in a simple form with a title, location, and optional photo. Your complaint is logged instantly and assigned a tracking ID.',
                        'icon_bg'    => 'bg-blue-100',
                        'icon_color' => 'text-blue-600',
                        'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
                    ],
                    [
                        'title'      => 'Real-Time Tracking',
                        'desc'       => 'Watch your complaint move through review, validation, in-progress, and resolution stages — with a full timestamped status history.',
                        'icon_bg'    => 'bg-green-100',
                        'icon_color' => 'text-green-600',
                        'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                    [
                        'title'      => 'Instant Notifications',
                        'desc'       => 'Receive in-app notifications every time municipality staff updates the status of your complaint — no need to check manually.',
                        'icon_bg'    => 'bg-yellow-100',
                        'icon_color' => 'text-yellow-600',
                        'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
                    ],
                ] as $feat)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 {{ $feat['icon_bg'] }} rounded-xl flex items-center justify-center mb-5">
                            <svg class="w-6 h-6 {{ $feat['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $feat['icon'] !!}
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $feat['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $feat['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="bg-white py-20 border-t border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-900">How it works</h2>
                <p class="mt-3 text-gray-500">Three simple steps from problem to resolution.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                @foreach([
                    ['step' => '01', 'title' => 'Submit a Complaint',  'desc' => 'Create a free account and submit your complaint with a description, location, and optional photo.'],
                    ['step' => '02', 'title' => 'Staff Reviews',       'desc' => 'Municipality staff validates your complaint and assigns it to the appropriate department.'],
                    ['step' => '03', 'title' => 'Issue Resolved',      'desc' => 'Track progress and receive notifications when the issue is resolved. Transparent and accountable.'],
                ] as $step)
                    <div class="text-center">
                        <div class="w-14 h-14 bg-blue-600 text-white font-bold text-lg rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md shadow-blue-200">
                            {{ $step['step'] }}
                        </div>
                        <h3 class="font-semibold text-gray-900 text-lg mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Lifecycle --}}
    <section class="bg-gray-50 border-t border-gray-100 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Complaint Lifecycle</h2>
            <p class="text-sm text-gray-500 mb-8">Every complaint follows a transparent, traceable process.</p>
            <div class="flex flex-wrap items-center justify-center gap-2">
                @foreach([
                    ['label' => 'Submitted',      'color' => 'bg-blue-100 text-blue-700 border border-blue-200'],
                    ['label' => 'Pending Review', 'color' => 'bg-yellow-100 text-yellow-700 border border-yellow-200'],
                    ['label' => 'Validated',      'color' => 'bg-indigo-100 text-indigo-700 border border-indigo-200'],
                    ['label' => 'In Progress',    'color' => 'bg-orange-100 text-orange-700 border border-orange-200'],
                    ['label' => 'Resolved',       'color' => 'bg-green-100 text-green-700 border border-green-200'],
                    ['label' => 'Closed',         'color' => 'bg-gray-100 text-gray-600 border border-gray-200'],
                ] as $i => $step)
                    @if($i > 0)
                        <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif
                    <span class="px-3.5 py-1.5 rounded-full text-sm font-medium {{ $step['color'] }}">{{ $step['label'] }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    @guest
    <section class="bg-gradient-to-r from-blue-700 to-indigo-700 py-16">
        <div class="max-w-2xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to report an issue?</h2>
            <p class="text-blue-100 text-lg mb-8">Join citizens who are already using the platform to improve their community.</p>
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition shadow-lg text-base">
                Get Started — It's Free
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </section>
    @endguest

    <footer class="bg-white border-t border-gray-100 py-8 text-center text-sm text-gray-400">
        &copy; {{ date('Y') }} Municipality Issue System &mdash; University Software Engineering Project.
    </footer>

</body>
</html>
