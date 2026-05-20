<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-purple-600 uppercase tracking-widest mb-1">Admin Portal</p>
                <h2 class="text-2xl font-black text-gray-900">System Dashboard</h2>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-muni-dark text-white text-sm font-bold rounded-xl hover:bg-muni-darker transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-muni-dark focus:ring-offset-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Create Staff
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- User stats --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Users</h3>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach([
                        [
                            'label' => 'Total Users',
                            'value' => $userStats['total'],
                            'href'  => route('admin.users.index'),
                            'bg'    => 'bg-muni-dark',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        ],
                        [
                            'label' => 'Citizens',
                            'value' => $userStats['citizens'],
                            'href'  => route('admin.users.index', ['role' => 'citizen']),
                            'bg'    => 'bg-muni-mid',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                        ],
                        [
                            'label' => 'Staff Members',
                            'value' => $userStats['staff'],
                            'href'  => route('admin.users.index', ['role' => 'staff']),
                            'bg'    => 'bg-indigo-600',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>',
                        ],
                        [
                            'label' => 'Inactive',
                            'value' => $userStats['inactive'],
                            'href'  => route('admin.users.index'),
                            'bg'    => 'bg-red-600',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>',
                        ],
                    ] as $stat)
                        <a href="{{ $stat['href'] }}"
                           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md hover:-translate-y-0.5 transition-all group">
                            <div class="w-12 h-12 {{ $stat['bg'] }} rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $stat['icon'] !!}
                                </svg>
                            </div>
                            <p class="text-4xl font-black text-gray-900 tabular-nums leading-none mb-2">{{ $stat['value'] }}</p>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Complaint stats --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Complaints</h3>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
                    @foreach([
                        [
                            'label' => 'Total',
                            'value' => $complaintStats['total'],
                            'bg'    => 'bg-muni-dark',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
                        ],
                        [
                            'label' => 'Submitted',
                            'value' => $complaintStats['submitted'],
                            'bg'    => 'bg-muni-mid',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
                        ],
                        [
                            'label' => 'Pending',
                            'value' => $complaintStats['pending_review'],
                            'bg'    => 'bg-yellow-500',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        [
                            'label' => 'In Progress',
                            'value' => $complaintStats['in_progress'],
                            'bg'    => 'bg-orange-500',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>',
                        ],
                        [
                            'label' => 'Resolved',
                            'value' => $complaintStats['resolved'],
                            'bg'    => 'bg-green-600',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        [
                            'label' => 'Rejected',
                            'value' => $complaintStats['rejected'],
                            'bg'    => 'bg-red-600',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                    ] as $stat)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                            <div class="w-11 h-11 {{ $stat['bg'] }} rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    {!! $stat['icon'] !!}
                                </svg>
                            </div>
                            <p class="text-3xl font-black text-gray-900 tabular-nums leading-none mb-2">{{ $stat['value'] }}</p>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest truncate">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Two-column: recent complaints + recent users --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-base font-black text-gray-900">Recent Complaints</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Latest activity in the system</p>
                    </div>
                    @if($recentComplaints->isEmpty())
                        <div class="px-6 py-10 text-center text-gray-400 text-sm">No complaints yet.</div>
                    @else
                        <ul class="divide-y divide-gray-50" role="list">
                            @foreach($recentComplaints as $complaint)
                                <li class="px-6 py-4 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $complaint->title }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $complaint->user->name }} &middot; {{ $complaint->category->name }}</p>
                                        </div>
                                    </div>
                                    <x-status-badge :status="$complaint->status" />
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-black text-gray-900">Recent Registrations</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Newly joined users</p>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-muni-dark hover:text-muni-darker font-bold transition-colors">View all &rarr;</a>
                    </div>
                    <ul class="divide-y divide-gray-50" role="list">
                        @foreach($recentUsers as $user)
                            <li class="px-6 py-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-black shrink-0
                                        {{ $user->role === 'admin' ? 'bg-purple-600' : ($user->role === 'staff' ? 'bg-indigo-600' : 'bg-blue-600') }}"
                                         aria-hidden="true">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs px-2.5 py-1 rounded-lg font-bold
                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'staff' ? 'bg-indigo-100 text-indigo-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    @if(!$user->is_active)
                                        <span class="text-xs px-2 py-0.5 rounded-lg bg-red-100 text-red-600 font-semibold">Inactive</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
