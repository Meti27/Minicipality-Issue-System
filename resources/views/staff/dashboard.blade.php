<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Staff Dashboard</h2>
                <p class="text-sm text-gray-500 mt-0.5">Overview of all complaints in the system</p>
            </div>
            <a href="{{ route('staff.complaints.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                View All Complaints
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
                @foreach([
                    [
                        'label' => 'Total',
                        'value' => $stats['total'],
                        'icon_bg' => 'bg-slate-100', 'icon_color' => 'text-slate-600',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
                    ],
                    [
                        'label' => 'Submitted',
                        'value' => $stats['submitted'],
                        'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-600',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
                    ],
                    [
                        'label' => 'Pending Review',
                        'value' => $stats['pending_review'],
                        'icon_bg' => 'bg-yellow-100', 'icon_color' => 'text-yellow-600',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
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
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-3 hover:shadow-md transition-shadow">
                        <div class="w-10 h-10 {{ $stat['icon_bg'] }} rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 {{ $stat['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $stat['icon'] !!}
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xl font-bold text-gray-900 leading-none">{{ $stat['value'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Quick action buttons --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('staff.complaints.index', ['status' => 'submitted']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    New Submissions
                    @if($stats['submitted'] > 0)
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['submitted'] }}</span>
                    @endif
                </a>
                <a href="{{ route('staff.complaints.index', ['status' => 'pending_review']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                    Pending Review
                    @if($stats['pending_review'] > 0)
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['pending_review'] }}</span>
                    @endif
                </a>
            </div>

            {{-- Pending complaints table --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">Complaints Awaiting Action</h3>
                    @if(!$pendingComplaints->isEmpty())
                        <span class="text-xs bg-yellow-100 text-yellow-700 font-semibold px-2.5 py-0.5 rounded-full">
                            {{ $pendingComplaints->count() }} pending
                        </span>
                    @endif
                </div>

                @if($pendingComplaints->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">No complaints awaiting action. All clear!</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Citizen</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @foreach($pendingComplaints as $complaint)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs truncate">{{ $complaint->title }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->category->name }}</td>
                                        <td class="px-6 py-4"><x-status-badge :status="$complaint->status" /></td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $complaint->created_at->diffForHumans() }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('staff.complaints.show', $complaint) }}"
                                               class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                                                Review
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
