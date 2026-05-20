<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Complaints</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Filters --}}
            <form method="GET" action="{{ route('staff.complaints.index') }}"
                  class="bg-white shadow-sm rounded-xl border border-gray-100 px-5 py-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5" for="search">Search</label>
                    <input type="text" id="search" name="search" value="{{ $search }}"
                           placeholder="Title, location, citizen…"
                           class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 w-56">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5" for="status-filter">Status</label>
                    <select id="status-filter" name="status" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All statuses</option>
                        @foreach(['submitted','pending_review','validated','in_progress','resolved','closed','rejected'] as $s)
                            <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-muni-dark text-white text-sm font-semibold rounded-xl hover:bg-muni-darker transition focus:outline-none focus:ring-2 focus:ring-muni-dark focus:ring-offset-2">
                        Filter
                    </button>
                    <a href="{{ route('staff.complaints.index') }}"
                       class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </a>
                </div>
            </form>

            {{-- Results --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                @if($complaints->isEmpty())
                    <div class="px-6 py-14 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700 mb-1">No complaints found</p>
                        <p class="text-xs text-gray-400">Try adjusting your filters.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Citizen</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @foreach($complaints as $complaint)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-400 tabular-nums">{{ $complaint->id }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs">
                                            <span class="block truncate">{{ $complaint->title }}</span>
                                            <span class="block text-xs text-gray-400 truncate mt-0.5">{{ $complaint->location }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->category->name }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @php
                                                $pColors = ['low' => 'text-gray-500', 'medium' => 'text-yellow-600 font-medium', 'high' => 'text-red-600 font-medium'];
                                            @endphp
                                            <span class="{{ $pColors[$complaint->priority] ?? 'text-gray-500' }}">
                                                {{ ucfirst($complaint->priority) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4"><x-status-badge :status="$complaint->status" /></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $complaint->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('staff.complaints.show', $complaint) }}"
                                               class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-semibold transition-colors">
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

                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $complaints->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
