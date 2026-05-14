<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Complaints</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Filters --}}
            <form method="GET" action="{{ route('staff.complaints.index') }}"
                  class="bg-white shadow rounded-lg px-5 py-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Title, location, citizen…"
                           class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 w-56">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                        Filter
                    </button>
                    <a href="{{ route('staff.complaints.index') }}"
                       class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 transition">
                        Clear
                    </a>
                </div>
            </form>

            {{-- Results --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                @if($complaints->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">No complaints match your filters.</div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Citizen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($complaints as $complaint)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-400">{{ $complaint->id }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs">
                                        <span class="block truncate">{{ $complaint->title }}</span>
                                        <span class="block text-xs text-gray-400 truncate">{{ $complaint->location }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->category->name }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $pColors = ['low' => 'text-gray-500', 'medium' => 'text-yellow-600', 'high' => 'text-red-600'];
                                        @endphp
                                        <span class="font-medium {{ $pColors[$complaint->priority] ?? '' }}">
                                            {{ ucfirst($complaint->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><x-status-badge :status="$complaint->status" /></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $complaint->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('staff.complaints.show', $complaint) }}"
                                           class="text-sm text-blue-600 hover:underline font-medium">Review</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $complaints->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
