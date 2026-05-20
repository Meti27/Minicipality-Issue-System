<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Staff Portal</p>
                <h2 class="text-2xl font-black text-gray-900">Staff Dashboard</h2>
            </div>
            <a href="{{ route('staff.complaints.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-muni-dark text-white text-sm font-bold rounded-xl hover:bg-muni-darker transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-muni-dark focus:ring-offset-2">
                View All Complaints
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stat cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
                @foreach([
                    [
                        'label' => 'Total',
                        'value' => $stats['total'],
                        'bg'    => 'bg-muni-dark',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
                    ],
                    [
                        'label' => 'Submitted',
                        'value' => $stats['submitted'],
                        'bg'    => 'bg-muni-mid',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
                    ],
                    [
                        'label' => 'Pending',
                        'value' => $stats['pending_review'],
                        'bg'    => 'bg-yellow-500',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
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

            {{-- Quick action filters --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('staff.complaints.index', ['status' => 'submitted']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-slate-50 hover:border-gray-300 transition-colors shadow-sm">
                    <span class="w-2 h-2 bg-blue-600 rounded-full shrink-0" aria-hidden="true"></span>
                    New Submissions
                    @if($stats['submitted'] > 0)
                        <span class="bg-blue-100 text-blue-700 text-xs font-black px-2 py-0.5 rounded-full tabular-nums">{{ $stats['submitted'] }}</span>
                    @endif
                </a>
                <a href="{{ route('staff.complaints.index', ['status' => 'pending_review']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-slate-50 hover:border-gray-300 transition-colors shadow-sm">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full shrink-0" aria-hidden="true"></span>
                    Pending Review
                    @if($stats['pending_review'] > 0)
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-black px-2 py-0.5 rounded-full tabular-nums">{{ $stats['pending_review'] }}</span>
                    @endif
                </a>
            </div>

            {{-- Pending complaints table --}}
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-gray-900">Complaints Awaiting Action</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Review and update complaint statuses</p>
                    </div>
                    @if(!$pendingComplaints->isEmpty())
                        <span class="text-xs bg-yellow-100 text-yellow-800 font-black px-3 py-1 rounded-full tabular-nums">
                            {{ $pendingComplaints->count() }} pending
                        </span>
                    @endif
                </div>

                @if($pendingComplaints->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-base font-bold text-gray-900 mb-1">All clear!</p>
                        <p class="text-sm text-gray-400">No complaints awaiting action right now.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Citizen</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3.5"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @foreach($pendingComplaints as $complaint)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 max-w-xs truncate">{{ $complaint->title }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->category->name }}</td>
                                        <td class="px-6 py-4"><x-status-badge :status="$complaint->status" /></td>
                                        <td class="px-6 py-4 text-sm text-gray-400 whitespace-nowrap">{{ $complaint->created_at->diffForHumans() }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('staff.complaints.show', $complaint) }}"
                                               class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800 font-bold transition-colors">
                                                Review
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
