<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-muni-dark uppercase tracking-widest mb-1">My Complaints</p>
                <h2 class="text-xl font-black text-gray-900">Issue History</h2>
            </div>
            <a href="{{ route('citizen.complaints.create') }}"
               class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 bg-muni-dark text-white text-sm font-semibold rounded-xl hover:bg-muni-darker transition shadow-sm focus:outline-none focus:ring-2 focus:ring-muni-dark focus:ring-offset-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Submit New
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="rounded-xl bg-green-50 p-4 text-sm text-green-800 border border-green-200 flex items-start gap-3" role="alert">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter tabs --}}
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden"
                 x-data="{ tab: '{{ request()->hasAny(['status']) ? 'status' : (request()->hasAny(['category_id']) ? 'category' : (request()->hasAny(['date_from','date_to']) ? 'date' : 'status')) }}' }">

                <div class="flex border-b border-gray-100 overflow-x-auto scrollbar-none">
                    <button @click="tab = 'status'"
                            :class="tab === 'status' ? 'border-b-2 border-muni-dark text-muni-dark font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        By Status
                    </button>
                    <button @click="tab = 'category'"
                            :class="tab === 'category' ? 'border-b-2 border-muni-dark text-muni-dark font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        By Category
                    </button>
                    <button @click="tab = 'date'"
                            :class="tab === 'date' ? 'border-b-2 border-muni-dark text-muni-dark font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 text-sm transition-colors focus:outline-none whitespace-nowrap">
                        By Date
                    </button>

                    @if(request()->hasAny(['status', 'category_id', 'date_from', 'date_to']))
                        <div class="ml-auto flex items-center pr-4 shrink-0">
                            <a href="{{ route('citizen.complaints.index') }}"
                               class="text-xs text-gray-400 hover:text-red-500 transition-colors font-medium">
                                Clear
                            </a>
                        </div>
                    @endif
                </div>

                <div x-show="tab === 'status'" x-cloak class="px-4 py-3">
                    <form method="GET" action="{{ route('citizen.complaints.index') }}" id="status-form">
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5" for="status-select">Status</label>
                        <select id="status-select" name="status" onchange="document.getElementById('status-form').submit()"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-muni-dark bg-white">
                            <option value="">All statuses</option>
                            @foreach(['submitted' => 'Submitted', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'rejected' => 'Rejected'] as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div x-show="tab === 'category'" x-cloak class="px-4 py-3">
                    <form method="GET" action="{{ route('citizen.complaints.index') }}" id="category-form">
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5" for="category-select">Category</label>
                        <select id="category-select" name="category_id" onchange="document.getElementById('category-form').submit()"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-muni-dark bg-white">
                            <option value="">All categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div x-show="tab === 'date'" x-cloak class="px-4 py-3">
                    <form method="GET" action="{{ route('citizen.complaints.index') }}" id="date-form"
                          class="flex flex-col sm:flex-row gap-3">
                        <div class="flex flex-col gap-1.5 flex-1">
                            <label class="text-xs font-semibold text-gray-500" for="date-from">From</label>
                            <input type="date" id="date-from" name="date_from" value="{{ request('date_from') }}"
                                   onchange="document.getElementById('date-form').submit()"
                                   class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-muni-dark">
                        </div>
                        <div class="flex flex-col gap-1.5 flex-1">
                            <label class="text-xs font-semibold text-gray-500" for="date-to">To</label>
                            <input type="date" id="date-to" name="date_to" value="{{ request('date_to') }}"
                                   onchange="document.getElementById('date-form').submit()"
                                   class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-muni-dark">
                        </div>
                    </form>
                </div>

            </div>

            {{-- Empty state --}}
            @if($complaints->isEmpty())
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 px-6 py-16 text-center">
                    <div class="w-14 h-14 bg-muni-cream rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-muni-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mb-1">No complaints found</p>
                    <p class="text-xs text-gray-400 mb-4">Try adjusting your filters, or submit a new complaint.</p>
                    <a href="{{ route('citizen.complaints.create') }}"
                       class="inline-flex items-center gap-1.5 text-sm text-muni-dark hover:underline font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Submit your first complaint
                    </a>
                </div>

            @else

                {{-- ===== MOBILE CARD LIST (hidden on sm+) ===== --}}
                <div class="sm:hidden space-y-3">
                    @foreach($complaints as $complaint)
                        <a href="{{ route('citizen.complaints.show', $complaint) }}"
                           class="block bg-white rounded-2xl border border-gray-100 shadow-sm p-4 active:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <p class="text-sm font-bold text-gray-900 leading-snug flex-1">{{ $complaint->title }}</p>
                                <x-status-badge :status="$complaint->status" />
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ $complaint->category->name }}
                                </span>
                                <span class="text-gray-200">&bull;</span>
                                <span>{{ $complaint->created_at->format('d M Y') }}</span>
                            </div>
                            @if($complaint->location)
                                <p class="text-xs text-gray-400 mt-1 truncate">
                                    <svg class="w-3.5 h-3.5 inline mr-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $complaint->location }}
                                </p>
                            @endif
                            <div class="flex justify-end mt-2">
                                <span class="text-xs text-muni-dark font-semibold flex items-center gap-1">
                                    View details
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach

                    <div class="py-2">
                        {{ $complaints->links() }}
                    </div>
                </div>

                {{-- ===== DESKTOP TABLE (hidden on mobile) ===== --}}
                <div class="hidden sm:block bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-muni-cream">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-muni-dark uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-muni-dark uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-muni-dark uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-muni-dark uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @foreach($complaints as $complaint)
                                    <tr class="hover:bg-muni-cream/40 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-semibold text-gray-900">{{ $complaint->title }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $complaint->location }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $complaint->category->name }}</td>
                                        <td class="px-6 py-4"><x-status-badge :status="$complaint->status" /></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $complaint->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('citizen.complaints.show', $complaint) }}"
                                               class="inline-flex items-center gap-1 text-sm text-muni-dark hover:text-muni-darker font-semibold transition-colors">
                                                View
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
                </div>

            @endif

        </div>
    </div>
</x-app-layout>
