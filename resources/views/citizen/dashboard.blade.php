<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">Citizen Portal</p>
                <h2 class="text-2xl font-black text-gray-900">Welcome back, {{ Auth::user()->name }}</h2>
            </div>
            <a href="{{ route('citizen.complaints.create') }}"
               class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-muni-dark text-white text-sm font-bold rounded-xl hover:bg-muni-darker transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-muni-dark focus:ring-offset-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New Complaint
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stat cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach([
                    [
                        'label' => 'Total',
                        'value' => $stats['total'],
                        'bg'    => 'bg-muni-dark',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    ],
                    [
                        'label' => 'Submitted',
                        'value' => $stats['submitted'],
                        'bg'    => 'bg-muni-mid',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
                    ],
                    [
                        'label' => 'In Progress',
                        'value' => $stats['in_progress'],
                        'bg'    => 'bg-orange-500',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>',
                    ],
                    [
                        'label' => 'Resolved',
                        'value' => $stats['resolved'],
                        'bg'    => 'bg-green-600',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                    [
                        'label' => 'Rejected',
                        'value' => $stats['rejected'],
                        'bg'    => 'bg-red-600',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                ] as $stat)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="w-12 h-12 {{ $stat['bg'] }} rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                {!! $stat['icon'] !!}
                            </svg>
                        </div>
                        <p class="text-4xl font-black text-gray-900 tabular-nums leading-none mb-2">{{ $stat['value'] }}</p>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Recent complaints --}}
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-gray-900">Recent Complaints</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Your latest submitted issues</p>
                    </div>
                    <a href="{{ route('citizen.complaints.index') }}"
                       class="text-sm text-muni-dark hover:text-muni-darker font-bold transition-colors">
                        View all &rarr;
                    </a>
                </div>

                @if($recentComplaints->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-base font-bold text-gray-900 mb-2">No complaints yet</p>
                        <p class="text-sm text-gray-400 mb-6 max-w-sm mx-auto">Report your first infrastructure issue and we'll get it to the right team.</p>
                        <a href="{{ route('citizen.complaints.create') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-muni-dark text-white text-sm font-bold rounded-xl hover:bg-muni-darker transition-colors focus:outline-none focus:ring-2 focus:ring-muni-dark focus:ring-offset-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            Submit your first complaint
                        </a>
                    </div>
                @else
                    <ul class="divide-y divide-gray-50" role="list">
                        @foreach($recentComplaints as $complaint)
                            <li>
                                <a href="{{ route('citizen.complaints.show', $complaint) }}"
                                   class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-slate-50 transition-colors group">
                                    <div class="flex items-center gap-4 min-w-0 flex-1">
                                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-blue-50 transition-colors">
                                            <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                                                {{ $complaint->title }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $complaint->category->name }}
                                                <span class="mx-1.5 text-gray-300">&bull;</span>
                                                {{ $complaint->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-3">
                                        <x-status-badge :status="$complaint->status" />
                                        <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
