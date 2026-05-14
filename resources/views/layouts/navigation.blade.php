<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex items-center">
                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="text-blue-700 font-bold text-base hidden sm:block">Municipality IS</span>
                </a>

                {{-- Navigation Links --}}
                <div class="hidden space-x-1 sm:ms-8 sm:flex">
                    @auth
                        @if(Auth::user()->role === 'citizen')
                            <x-nav-link :href="route('citizen.dashboard')" :active="request()->routeIs('citizen.dashboard')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('citizen.complaints.index')" :active="request()->routeIs('citizen.complaints.*')">My Complaints</x-nav-link>
                            <x-nav-link :href="route('citizen.complaints.create')" :active="request()->routeIs('citizen.complaints.create')">
                                <svg class="w-4 h-4 mr-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Submit
                            </x-nav-link>
                        @elseif(Auth::user()->role === 'staff')
                            <x-nav-link :href="route('staff.dashboard')" :active="request()->routeIs('staff.dashboard')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('staff.complaints.index')" :active="request()->routeIs('staff.complaints.*')">Complaints</x-nav-link>
                        @elseif(Auth::user()->role === 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">Users</x-nav-link>
                            <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">Categories</x-nav-link>
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
                                    class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg focus:outline-none transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($unreadCount > 0)
                                    <span class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center font-bold leading-none text-[10px]">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="notifOpen"
                                 x-cloak
                                 @click.outside="notifOpen = false"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">

                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                                    @if($unreadCount > 0)
                                        <form method="POST" action="{{ route('citizen.notifications.markAllRead') }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-blue-600 hover:underline">Mark all read</button>
                                        </form>
                                    @endif
                                </div>

                                @if($recentNotifs->isEmpty())
                                    <div class="px-4 py-8 text-center">
                                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <p class="text-sm text-gray-400">No notifications yet.</p>
                                    </div>
                                @else
                                    <ul class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                                        @foreach($recentNotifs as $notif)
                                            <li class="{{ !$notif->is_read ? 'bg-blue-50' : '' }}">
                                                <a href="{{ route('citizen.notifications.read', $notif) }}"
                                                   class="block px-4 py-3 hover:bg-gray-50 transition">
                                                    <p class="text-sm text-gray-800 leading-snug">{{ $notif->message }}</p>
                                                    <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                                    <a href="{{ route('citizen.notifications.index') }}"
                                       class="text-xs text-blue-600 hover:underline font-medium">View all notifications</a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Role badge --}}
                    @php
                        $roleBadge = match(Auth::user()->role) {
                            'admin' => 'bg-purple-100 text-purple-700',
                            'staff' => 'bg-amber-100 text-amber-700',
                            default => 'bg-blue-100 text-blue-700',
                        };
                    @endphp

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-gray-200 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                                <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:block max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <span class="text-xs px-1.5 py-0.5 rounded font-medium {{ $roleBadge }}">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
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
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            {{-- Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Responsive Menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1 px-3">
            @auth
                @if(Auth::user()->role === 'citizen')
                    <x-responsive-nav-link :href="route('citizen.dashboard')" :active="request()->routeIs('citizen.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('citizen.complaints.index')" :active="request()->routeIs('citizen.complaints.*')">My Complaints</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('citizen.complaints.create')">Submit Complaint</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('citizen.notifications.index')" :active="request()->routeIs('citizen.notifications.*')">
                        Notifications @if($unreadCount > 0) <span class="ml-1 bg-red-100 text-red-600 text-xs px-1.5 py-0.5 rounded-full font-semibold">{{ $unreadCount }}</span> @endif
                    </x-responsive-nav-link>
                @elseif(Auth::user()->role === 'staff')
                    <x-responsive-nav-link :href="route('staff.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('staff.complaints.index')">Complaints</x-responsive-nav-link>
                @elseif(Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')">Users</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.categories.index')">Categories</x-responsive-nav-link>
                @endif
            @endauth
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200 bg-gray-50">
            @auth
                <div class="flex items-center gap-3 px-4 mb-3">
                    <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-sm text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="space-y-1 px-3">
                    <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">Sign Out</x-responsive-nav-link>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>
