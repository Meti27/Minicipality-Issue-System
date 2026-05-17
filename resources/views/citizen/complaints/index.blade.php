<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Complaints</h2>
            <a href="{{ route('citizen.complaints.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                + Submit New
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

           {{-- Filter tabs --}}
<div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden" x-data="{ tab: '{{ request()->hasAny(['status']) ? 'status' : (request()->hasAny(['category_id']) ? 'category' : (request()->hasAny(['date_from','date_to']) ? 'date' : 'status')) }}' }">

    {{-- Tab buttons --}}
    <div class="flex border-b border-gray-100">
        <button @click="tab = 'status'"
                :class="tab === 'status' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-6 py-3 text-sm transition-colors">
            By Status
        </button>
        <button @click="tab = 'category'"
                :class="tab === 'category' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-6 py-3 text-sm transition-colors">
            By Category
        </button>
        <button @click="tab = 'date'"
                :class="tab === 'date' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-6 py-3 text-sm transition-colors">
            By Date
        </button>

        @if(request()->hasAny(['status', 'category_id', 'date_from', 'date_to']))
            <div class="ml-auto flex items-center pr-4">
                <a href="{{ route('citizen.complaints.index') }}"
                   class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                    Clear filters
                </a>
            </div>
        @endif
    </div>

    {{-- By Status --}}
    <div x-show="tab === 'status'" x-cloak class="px-6 py-4">
        <form method="GET" action="{{ route('citizen.complaints.index') }}" id="status-form">
           <select name="status" onchange="document.getElementById('status-form').submit()"
        class="text-sm border border-gray-200 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All statuses</option>
                @foreach(['submitted' => 'Submitted', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'rejected' => 'Rejected'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- By Category --}}
    <div x-show="tab === 'category'" x-cloak class="px-6 py-4">
        <form method="GET" action="{{ route('citizen.complaints.index') }}" id="category-form">
            <select name="category_id" onchange="document.getElementById('category-form').submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- By Date --}}
    <div x-show="tab === 'date'" x-cloak class="px-6 py-4">
        <form method="GET" action="{{ route('citizen.complaints.index') }}" id="date-form"
              class="flex flex-wrap gap-3 items-end">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       onchange="document.getElementById('date-form').submit()"
                       class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       onchange="document.getElementById('date-form').submit()"
                       class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </form>
    </div>

</div>
            {{-- Complaints table --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                @if($complaints->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 mb-3">No complaints found.</p>
                        <a href="{{ route('citizen.complaints.create') }}"
                           class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:underline font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Submit your first complaint
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @foreach($complaints as $complaint)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-semibold text-gray-900">{{ $complaint->title }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $complaint->location }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->category->name }}</td>
                                        <td class="px-6 py-4"><x-status-badge :status="$complaint->status" /></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $complaint->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('citizen.complaints.show', $complaint) }}"
                                               class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                                                View
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

                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $complaints->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>