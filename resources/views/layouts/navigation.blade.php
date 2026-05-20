<nav x-data="{ open: false }" class="bg-muni-dark border-b border-muni-darker">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex items-center">
                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                    <div class="w-8 h-8 bg-muni-mid rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-base tracking-tight hidden sm:block">Municipality IS</span>
                </a>

                {{-- Desktop Navigation Links --}}
                <div class="hidden sm:flex sm:items-center sm:gap-1 sm:ms-8">
                    @auth
                        @if(Auth::user()->role === 'citizen')
                            <a href="{{ route('citizen.dashboard') }}"
                               class="{{ request()->routeIs('citizen.dashboard') ? 'bg-muni-darker text-white' : 'text-white/60 hover:text-white hover:bg-muni-darker' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                Dashboard
                            </a>
                            <a href="{{ route('citizen.complaints.index') }}"
                               class="{{ request()->routeIs('citizen.complaints.*') && !request()->routeIs('citizen.complaints.create') ? 'bg-muni-darker text-white' : 'text-white/60 hover:text-white hover:bg-muni-darker' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                My Complaints
                            </a>
                            <a href="{{ route('citizen.complaints.create') }}"
                               class="{{ request()->routeIs('citizen.complaints.create') ? 'bg-muni-light text-muni-dark' : 'text-white/60 hover:text-white hover:bg-muni-darker' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                Submit
                            </a>
                        @elseif(Auth::user()->role === 'staff')
                            <a href="{{ route('staff.dashboard') }}"
                               class="{{ request()->routeIs('staff.dashboard') ? 'bg-muni-darker text-white' : 'text-white/60 hover:text-white hover:bg-muni-darker' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                Dashboard
                            </a>
                            <a href="{{ route('staff.complaints.index') }}"
                               class="{{ request()->routeIs('staff.complaints.*') ? 'bg-muni-darker text-white' : 'text-white/60 hover:text-white hover:bg-muni-darker' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                Complaints
                            </a>
                        @elseif(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="{{ request()->routeIs('admin.dashboard') ? 'bg-muni-darker text-white' : 'text-white/60 hover:text-white hover:bg-muni-darker' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                               class="{{ request()->routeIs('admin.users.*') ? 'bg-muni-darker text-white' : 'text-white/60 hover:text-white hover:bg-muni-darker' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                Users
                            </a>
                            <a href="{{ route('admin.categories.index') }}"
                               class="{{ request()->routeIs('admin.categories.*') ? 'bg-muni-darker text-white' : 'text-white/60 hover:text-white hover:bg-muni-darker' }} px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                Categories
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Right side --}}
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                @auth
                    @php
                        $recentNotifs = Auth::user()->notifications()
                            ->with('complaint')
                            ->latest()
                            ->take(6)
                            ->get();
                        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
                    @endphp

                    {{-- Notification bell (citizens only) --}}
                    @if(Auth::user()->role === 'citizen')
                        <div x-data="{ notifOpen: false }" class="relative">
                            <button @click="notifOpen = !notifOpen"
                                    class="relative p-2 text-white/60 hover:text-white hover:bg-muni-darker rounded-lg focus:outline-none transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-label="Notifications">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($unreadCount > 0)
                                    <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold leading-none">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="notifOpen"
                                 x-cloak
                                 @click.outside="notifOpen = false"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                                    @if($unreadCount > 0)
                                        <form method="POST" action="{{ route('citizen.notifications.markAllRead') }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-muni-dark hover:underline">Mark all read</button>
                                        </form>
                                    @endif
                                </div>
                                @if($recentNotifs->isEmpty())
                                    <div class="px-4 py-8 text-center">
                                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <p class="text-sm text-gray-400">No notifications yet.</p>
                                    </div>
                                @else
                                    <ul class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                                        @foreach($recentNotifs as $notif)
                                            <li class="{{ !$notif->is_read ? 'bg-muni-light/10' : '' }}">
                                                <a href="{{ route('citizen.notifications.read', $notif) }}"
                                                   class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                                    <p class="text-sm text-gray-800 leading-snug">{{ $notif->message }}</p>
                                                    <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                                    <a href="{{ route('citizen.notifications.index') }}"
                                       class="text-xs text-muni-dark hover:underline font-medium">View all notifications</a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- User dropdown --}}
                    @php
                        $rolePill = match(Auth::user()->role) {
                            'admin' => 'bg-purple-600 text-white',
                            'staff' => 'bg-amber-500 text-white',
                            default => 'bg-muni-mid text-white',
                        };
                    @endphp

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-muni-darker text-sm font-medium rounded-lg text-white bg-muni-darker hover:bg-muni-dark/80 focus:outline-none transition-colors">
                                <div class="w-7 h-7 bg-muni-mid rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:block max-w-[110px] truncate">{{ Auth::user()->name }}</span>
                                <span class="text-xs px-1.5 py-0.5 rounded-md font-semibold {{ $rolePill }}">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                                <svg class="w-4 h-4 text-white/40" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Signed in as</p>
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            {{-- Hamburger (non-citizen or tablet+) --}}
            <div class="-me-2 flex items-center sm:hidden">
                @auth
                    @if(Auth::user()->role !== 'citizen')
                        <button @click="open = !open"
                                class="inline-flex items-center justify-center p-2 rounded-lg text-white/60 hover:text-white hover:bg-muni-darker focus:outline-none transition-colors"
                                aria-label="Toggle menu">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @else
                        {{-- Citizens: show user avatar button instead --}}
                        <div x-data="{ userOpen: false }" class="relative">
                            <button @click="userOpen = !userOpen"
                                    class="w-9 h-9 bg-muni-mid rounded-full flex items-center justify-center text-white text-sm font-bold focus:outline-none">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </button>
                            <div x-show="userOpen" x-cloak @click.outside="userOpen = false"
                                 class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-xs text-gray-500">Signed in as</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
                @guest
                    <button @click="open = !open"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-white/60 hover:text-white hover:bg-muni-darker focus:outline-none transition-colors"
                            aria-label="Toggle menu">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endguest
            </div>
        </div>
    </div>

    {{-- Responsive Menu (staff/admin/guests only — citizens use bottom tab nav) --}}
    @auth
        @if(Auth::user()->role !== 'citizen')
            <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-muni-darker">
                <div class="pt-2 pb-3 space-y-1 px-3">
                    @if(Auth::user()->role === 'staff')
                        <a href="{{ route('staff.dashboard') }}"
                           class="{{ request()->routeIs('staff.dashboard') ? 'bg-muni-darker text-white' : 'text-white/70' }} block px-3 py-2.5 rounded-lg text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('staff.complaints.index') }}"
                           class="{{ request()->routeIs('staff.complaints.*') ? 'bg-muni-darker text-white' : 'text-white/70' }} block px-3 py-2.5 rounded-lg text-sm font-medium">
                            Complaints
                        </a>
                    @elseif(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                           class="{{ request()->routeIs('admin.dashboard') ? 'bg-muni-darker text-white' : 'text-white/70' }} block px-3 py-2.5 rounded-lg text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                           class="{{ request()->routeIs('admin.users.*') ? 'bg-muni-darker text-white' : 'text-white/70' }} block px-3 py-2.5 rounded-lg text-sm font-medium">
                            Users
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                           class="{{ request()->routeIs('admin.categories.*') ? 'bg-muni-darker text-white' : 'text-white/70' }} block px-3 py-2.5 rounded-lg text-sm font-medium">
                            Categories
                        </a>
                    @endif
                </div>
                <div class="pt-4 pb-3 border-t border-muni-darker">
                    <div class="flex items-center gap-3 px-4 mb-3">
                        <div class="w-9 h-9 bg-muni-mid rounded-full flex items-center justify-center text-white font-bold shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-sm text-white">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-white/50">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="space-y-1 px-3">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-white/70 hover:text-white hover:bg-muni-darker transition-colors">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium text-white/70 hover:text-white hover:bg-muni-darker transition-colors">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth
    @guest
        <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-muni-darker">
            <div class="py-3 px-3 space-y-1">
                <a href="{{ route('login') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-white/70 hover:text-white hover:bg-muni-darker transition-colors">Sign In</a>
                <a href="{{ route('register') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-white/70 hover:text-white hover:bg-muni-darker transition-colors">Register</a>
            </div>
        </div>
    @endguest
</nav>

{{-- ===== Citizen Mobile Bottom Tab Nav ===== --}}
@auth
    @if(Auth::user()->role === 'citizen')
        @php
            $navUnread = Auth::user()->notifications()->where('is_read', false)->count();
        @endphp
        <nav class="fixed bottom-0 left-0 right-0 z-50 sm:hidden bg-muni-dark border-t border-muni-darker"
             style="padding-bottom: env(safe-area-inset-bottom);"
             aria-label="Mobile navigation">
            <div class="flex items-stretch h-16">

                {{-- Home --}}
                <a href="{{ route('citizen.dashboard') }}"
                   class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                          {{ request()->routeIs('citizen.dashboard') ? 'text-muni-light' : 'text-white/50 hover:text-white' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-[10px] font-semibold leading-none">Home</span>
                </a>

                {{-- My Complaints --}}
                <a href="{{ route('citizen.complaints.index') }}"
                   class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                          {{ request()->routeIs('citizen.complaints.index') ? 'text-muni-light' : 'text-white/50 hover:text-white' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-[10px] font-semibold leading-none">Complaints</span>
                </a>

                {{-- Submit — prominent centre button --}}
                <a href="{{ route('citizen.complaints.create') }}"
                   class="flex-1 flex flex-col items-center justify-center gap-1 -mt-5">
                    <div class="w-14 h-14 bg-muni-light rounded-2xl flex items-center justify-center shadow-lg
                                {{ request()->routeIs('citizen.complaints.create') ? 'ring-4 ring-white/30' : '' }}">
                        <svg class="w-7 h-7 text-muni-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-semibold leading-none text-muni-light mt-1">Submit</span>
                </a>

                {{-- Notifications --}}
                <a href="{{ route('citizen.notifications.index') }}"
                   class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors relative
                          {{ request()->routeIs('citizen.notifications.*') ? 'text-muni-light' : 'text-white/50 hover:text-white' }}">
                    <div class="relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($navUnread > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] rounded-full h-4 w-4 flex items-center justify-center font-bold">
                                {{ $navUnread > 9 ? '9+' : $navUnread }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] font-semibold leading-none">Alerts</span>
                </a>

                {{-- Profile --}}
                <a href="{{ route('profile.edit') }}"
                   class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                          {{ request()->routeIs('profile.*') ? 'text-muni-light' : 'text-white/50 hover:text-white' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-[10px] font-semibold leading-none">Profile</span>
                </a>

            </div>
        </nav>
    @endif
@endauth
