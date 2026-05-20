<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Complaint & Issue Tracking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideClip {
            from { clip-path: polygon(100% 0, 100% 0, 100% 100%, 100% 100%); }
            to   { clip-path: polygon(20% 0, 100% 0, 100% 100%, 0% 100%); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .anim-fade-up          { animation: fadeUp  0.6s ease-out both; }
        .anim-fade-in          { animation: fadeIn  0.8s ease-out both; }
        .anim-slide-clip       { animation: slideClip 1.3s cubic-bezier(0.16, 1, 0.3, 1) both; animation-delay: 0.1s; }
        .delay-100 { animation-delay: 0.10s; }
        .delay-200 { animation-delay: 0.20s; }
        .delay-350 { animation-delay: 0.35s; }
        .delay-500 { animation-delay: 0.50s; }
        .delay-650 { animation-delay: 0.65s; }
    </style>
</head>
<body class="font-sans antialiased overflow-x-hidden" style="background: #355872">

    {{-- Navbar --}}
    <header class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md border-b border-white/10" style="background: rgba(53,88,114,0.92)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#7AAACE">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-base tracking-tight hidden sm:block">Municipality IS</span>
            </a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 text-sm font-semibold rounded-lg transition text-white" style="background:#7AAACE; hover:opacity-90">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-white transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 text-sm font-semibold rounded-lg transition text-white" style="background:#7AAACE; hover:opacity-90">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ===== HERO: Split Panel ===== --}}
    <section class="relative min-h-screen flex overflow-hidden pt-16" style="background:#355872">

        {{-- Left panel: content --}}
        <div class="relative z-10 flex flex-col justify-between w-full md:w-3/5 lg:w-[58%] px-8 md:px-12 lg:px-20 py-16 md:py-20">

            {{-- Brand mark --}}
            <div class="anim-fade-up delay-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#7AAACE">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm leading-none">Municipality IS</p>
                        <p class="text-white/40 text-xs tracking-widest uppercase mt-0.5">Complaint Tracking System</p>
                    </div>
                </div>
            </div>

            {{-- Main content --}}
            <div class="my-auto py-16 md:py-20">

                @guest
                    <div class="anim-fade-up delay-200 inline-flex items-center gap-2 border border-white/20 bg-white/10 rounded-full px-4 py-1.5 text-xs font-medium text-white/70 mb-10">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                        Open to all citizens &middot; Free to use
                    </div>
                @endguest

                <h1 class="anim-fade-up delay-200 text-6xl sm:text-7xl lg:text-8xl font-black text-white leading-none tracking-tight">
                    Report.<br>
                    Track.<br>
                    <span style="color:#9CD5FF">Resolve.</span>
                </h1>

                <div class="anim-fade-up delay-350 h-1 w-20 mt-8 mb-8" style="background:#9CD5FF"></div>

                <p class="anim-fade-up delay-350 text-white/60 text-lg max-w-md leading-relaxed mb-10">
                    Submit infrastructure issues — potholes, broken streetlights, water leaks — and follow every step from submission to resolution.
                </p>

                <div class="anim-fade-up delay-500 flex flex-col sm:flex-row gap-4">
                    @guest
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center gap-2 px-7 py-3.5 text-muni-dark font-bold rounded-xl transition-colors text-base" style="background:#9CD5FF">
                            Register as a Citizen
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center gap-2 px-7 py-3.5 border border-white/30 text-white/80 font-semibold rounded-xl hover:border-white/60 hover:text-white transition-colors text-base">
                            Sign In
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center gap-2 px-7 py-3.5 text-muni-dark font-bold rounded-xl transition-colors text-base" style="background:#9CD5FF">
                            Go to Dashboard
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Footer info row --}}
            <div class="anim-fade-up delay-650 border-t border-white/20 pt-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs text-white/50">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 shrink-0" style="color:#9CD5FF" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" stroke-width="1.5"/>
                            <line x1="2" x2="22" y1="12" y2="12" stroke-width="1.5"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" stroke-width="1.5"/>
                        </svg>
                        <span>municipality.gov.example</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 shrink-0" style="color:#9CD5FF" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span>+1 (555) 000-0000</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 shrink-0" style="color:#9CD5FF" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>City Hall, Main Street</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right panel: distinct blue-indigo with clip-path animation --}}
        <div class="anim-slide-clip hidden md:flex absolute right-0 top-0 bottom-0 w-[48%] overflow-hidden items-center justify-center"
             style="background: linear-gradient(135deg, #2a4760 0%, #355872 40%, #7AAACE 100%);">

            {{-- Grid pattern overlay --}}
            <div class="absolute inset-0 opacity-20">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="56" height="56" patternUnits="userSpaceOnUse">
                            <path d="M 56 0 L 0 0 0 56" fill="none" stroke="white" stroke-width="0.8"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)"/>
                </svg>
            </div>

            {{-- Top-right glow --}}
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            {{-- Bottom-left glow --}}
            <div class="absolute -bottom-20 left-0 w-64 h-64 bg-indigo-900/40 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Stat floating cards --}}
            <div class="relative z-10 anim-fade-in delay-650 flex flex-col gap-5 px-12">
                @foreach([
                    [
                        'num'   => '6',
                        'label' => 'Issue Categories',
                        'icon'  => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
                    ],
                    [
                        'num'   => '100%',
                        'label' => 'Transparent Process',
                        'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    ],
                    [
                        'num'   => '24/7',
                        'label' => 'Online Tracking',
                        'icon'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                ] as $s)
                    <div class="bg-white/[0.15] backdrop-blur-sm border border-white/20 rounded-2xl px-6 py-5 flex items-center gap-4 hover:bg-white/25 transition-colors">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-white tabular-nums leading-none">{{ $s['num'] }}</p>
                            <p class="text-xs text-blue-100 mt-1 font-medium">{{ $s['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== FEATURES ===== --}}
    <section class="py-24 border-t border-white/10" style="background:#F7F8F0">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16">
                <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color:#355872">How it works</p>
                <h2 class="text-4xl font-black text-slate-900 leading-tight mb-5">
                    Everything you need<br>to report issues.
                </h2>
                <div class="h-1 w-16" style="background:#355872"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    [
                        'num'   => '01',
                        'title' => 'Easy Submission',
                        'desc'  => 'Fill in a simple form with a title, location, and photo. Your complaint is logged instantly and tracked.',
                        'bg'    => 'bg-muni-dark',
                    ],
                    [
                        'num'   => '02',
                        'title' => 'Real-Time Tracking',
                        'desc'  => 'Watch your complaint move through review, validation, and resolution with full timestamped history.',
                        'bg'    => 'bg-muni-mid',
                    ],
                    [
                        'num'   => '03',
                        'title' => 'Instant Notifications',
                        'desc'  => 'Receive in-app notifications every time municipality staff updates your complaint status.',
                        'bg'    => 'bg-muni-darker',
                    ],
                ] as $feat)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                        <div class="w-14 h-14 {{ $feat['bg'] }} rounded-2xl flex items-center justify-center mb-6 group-hover:scale-105 transition-transform">
                            <span class="text-white font-black text-xl">{{ $feat['num'] }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $feat['title'] }}</h3>
                        <p class="text-slate-500 leading-relaxed">{{ $feat['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== LIFECYCLE ===== --}}
    <section class="bg-white border-t border-gray-100 py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color:#355872">Complaint Lifecycle</p>
                <h2 class="text-3xl font-black text-slate-900">Every complaint, fully traceable</h2>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3">
                @foreach([
                    ['label' => 'Submitted',      'color' => 'bg-muni-mid    text-white'],
                    ['label' => 'Pending Review', 'color' => 'bg-yellow-500  text-white'],
                    ['label' => 'Validated',      'color' => 'bg-indigo-600  text-white'],
                    ['label' => 'In Progress',    'color' => 'bg-orange-500  text-white'],
                    ['label' => 'Resolved',       'color' => 'bg-green-600   text-white'],
                    ['label' => 'Closed',         'color' => 'bg-muni-dark   text-white'],
                ] as $i => $step)
                    @if($i > 0)
                        <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif
                    <span class="px-4 py-2 rounded-xl text-sm font-bold {{ $step['color'] }}">{{ $step['label'] }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== CTA ===== --}}
    @guest
    <section class="border-t border-white/10 py-24" style="background:#2a4760">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="text-4xl sm:text-5xl font-black text-white mb-5">Ready to report<br>an issue?</h2>
            <div class="h-1 w-16 mx-auto mb-8" style="background:#9CD5FF"></div>
            <p class="text-white/60 text-lg mb-10 max-w-xl mx-auto leading-relaxed">
                Join citizens already using the platform to improve their community.
            </p>
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2.5 px-8 py-4 font-bold rounded-xl transition-colors text-lg text-muni-dark" style="background:#9CD5FF">
                Get Started — It's Free
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </section>
    @endguest

    <footer class="border-t border-white/10 py-8 text-center text-sm text-white/40" style="background:#2a4760">
        &copy; {{ date('Y') }} Municipality Issue System &mdash; University Software Engineering Project.
    </footer>

</body>
</html>
