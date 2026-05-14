<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">Here's an overview of your complaints</p>
            </div>
            <a href="{{ route('citizen.complaints.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
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
                        'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-600',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    ],
                    [
                        'label' => 'Submitted',
                        'value' => $stats['submitted'],
                        'icon_bg' => 'bg-sky-100', 'icon_color' => 'text-sky-600',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
                    ],
                    [
                        'label' => 'In Progress',
                        'value' => $stats['in_progress'],
                        'icon_bg' => 'bg-orange-100', 'icon_color' => 'text-orange-600',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>',
                    ],
                    [
                        'label' => 'Resolved',
                        'value' => $stats['resolved'],
                        'icon_bg' => 'bg-green-100', 'icon_color' => 'text-green-600',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                    [
                        'label' => 'Rejected',
                        'value' => $stats['rejected'],
                        'icon_bg' => 'bg-red-100', 'icon_color' => 'text-red-600',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ],
                ] as $stat)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <div class="w-11 h-11 {{ $stat['icon_bg'] }} rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 {{ $stat['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $stat['icon'] !!}
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-2xl font-bold text-gray-900 leading-none">{{ $stat['value'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Recent complaints --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">Recent Complaints</h3>
                    <a href="{{ route('citizen.complaints.index') }}"
                       class="text-xs text-blue-600 hover:underline font-medium">View all</a>
                </div>

                @if($recentComplaints->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 mb-2">No complaints submitted yet.</p>
                        <a href="{{ route('citizen.complaints.create') }}"
                           class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:underline font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Submit your first complaint
                        </a>
                    </div>
                @else
                    <ul class="divide-y divide-gray-50">
                        @foreach($recentComplaints as $complaint)
                            <li class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-gray-50 transition-colors">
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('citizen.complaints.show', $complaint) }}"
                                       class="text-sm font-semibold text-gray-900 hover:text-blue-600 truncate block transition-colors">
                                        {{ $complaint->title }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $complaint->category->name }} &middot; {{ $complaint->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <x-status-badge :status="$complaint->status" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
