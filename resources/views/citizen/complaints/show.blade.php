<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('citizen.complaints.index') }}"
                   class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"
                   aria-label="Back to complaints">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Complaint Details</h2>
            </div>
            <span id="status-badge-container"><x-status-badge :status="$complaint->status" /></span>
        </div>
    </x-slot>

    {{-- Real-time toast notification --}}
    <div id="ws-toast"
         class="hidden fixed top-4 right-4 z-50 flex items-center gap-3 bg-white border border-gray-200 shadow-lg rounded-xl px-4 py-3 text-sm text-gray-800 max-w-sm transition-all duration-300 opacity-0 translate-y-2"
         role="status" aria-live="polite">
        <span class="flex-shrink-0 w-2 h-2 rounded-full bg-green-500"></span>
        <span id="ws-toast-message"></span>
        <button onclick="document.getElementById('ws-toast').classList.add('hidden')"
                class="ml-auto text-gray-400 hover:text-gray-600 transition" aria-label="Dismiss">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-xl bg-green-50 p-4 text-sm text-green-800 border border-green-200 flex items-start gap-3" role="alert">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Complaint card --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $complaint->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $complaint->category->name }}
                                <span class="mx-1">&middot;</span>
                                Submitted {{ $complaint->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Location</p>
                            <p class="text-sm text-gray-700">{{ $complaint->location }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Category</p>
                            <p class="text-sm text-gray-700">{{ $complaint->category->name }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Description</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $complaint->description }}</p>
                    </div>

                    @if($complaint->image_path)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Attached Photo</p>
                            <img src="{{ asset('storage/' . $complaint->image_path) }}"
                                 alt="Complaint photo"
                                 class="rounded-xl max-h-72 object-cover border border-gray-200 shadow-sm">
                        </div>
                    @endif

                    <div id="rejection-reason-box"
                         class="rounded-xl bg-red-50 border border-red-200 p-4 {{ ($complaint->status === 'rejected' && $complaint->rejection_reason) ? '' : 'hidden' }}"
                         role="alert">
                        <p class="text-xs font-semibold text-red-700 uppercase tracking-wider mb-1.5">Rejection Reason</p>
                        <p id="rejection-reason-text" class="text-sm text-red-800">{{ $complaint->rejection_reason }}</p>
                    </div>
                </div>
            </div>

            {{-- Status timeline --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">Status History</h3>
                </div>
                <div class="px-6 py-5">
                    <ol id="status-history-list" class="relative border-l-2 border-gray-100 space-y-6 ml-3" role="list">
                        @if($complaint->statusHistories->isEmpty())
                            <li id="status-history-empty" class="list-none">
                                <p class="text-sm text-gray-400 text-center py-4">No status updates yet.</p>
                            </li>
                        @else
                            @foreach($complaint->statusHistories->sortByDesc('created_at') as $history)
                                <li class="ml-6">
                                    <span class="absolute -left-3.5 flex h-7 w-7 items-center justify-center rounded-full bg-muni-light/40 ring-4 ring-white">
                                        <svg class="h-3.5 w-3.5 text-muni-dark" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <x-status-badge :status="$history->new_status" />
                                        <span class="text-xs text-gray-400">{{ $history->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    @if($history->comment)
                                        <p class="text-sm text-gray-600 mt-1">{{ $history->comment }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        by {{ $history->changedBy?->name ?? 'System' }}
                                    </p>
                                </li>
                            @endforeach
                        @endif
                    </ol>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        const statusConfigs = {
            'submitted':      { bg: 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',       dot: 'bg-blue-500' },
            'pending_review': { bg: 'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200', dot: 'bg-yellow-500' },
            'validated':      { bg: 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200', dot: 'bg-indigo-500' },
            'in_progress':    { bg: 'bg-orange-50 text-orange-700 ring-1 ring-orange-200', dot: 'bg-orange-500' },
            'resolved':       { bg: 'bg-green-50 text-green-700 ring-1 ring-green-200',    dot: 'bg-green-500' },
            'closed':         { bg: 'bg-gray-100 text-gray-600 ring-1 ring-gray-200',      dot: 'bg-gray-400' },
            'rejected':       { bg: 'bg-red-50 text-red-700 ring-1 ring-red-200',          dot: 'bg-red-500' },
        };

        function statusLabel(status) {
            return status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        }

        function renderBadge(status) {
            const cfg = statusConfigs[status] ?? { bg: 'bg-gray-100 text-gray-600 ring-1 ring-gray-200', dot: 'bg-gray-400' };
            return `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium ${cfg.bg}">
                <span class="w-1.5 h-1.5 rounded-full ${cfg.dot} shrink-0"></span>
                ${statusLabel(status)}
            </span>`;
        }

        function showToast(message) {
            const toast = document.getElementById('ws-toast');
            const msg   = document.getElementById('ws-toast-message');
            if (!toast || !msg) return;
            msg.textContent = message;
            toast.classList.remove('hidden', 'opacity-0', 'translate-y-2');
            toast.classList.add('opacity-100', 'translate-y-0');
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 5000);
        }

        function prependHistoryEntry(event) {
            const timeline   = document.getElementById('status-history-list');
            const emptyState = document.getElementById('status-history-empty');
            if (!timeline) return;

            if (emptyState) emptyState.remove();

            const cfg = statusConfigs[event.new_status] ?? { bg: 'bg-gray-100 text-gray-600 ring-1 ring-gray-200', dot: 'bg-gray-400' };
            const commentHtml = event.comment
                ? `<p class="text-sm text-gray-600 mt-1">${event.comment}</p>`
                : '';

            const li = document.createElement('li');
            li.className = 'ml-6';
            li.innerHTML = `
                <span class="absolute -left-3.5 flex h-7 w-7 items-center justify-center rounded-full bg-muni-light/40 ring-4 ring-white">
                    <svg class="h-3.5 w-3.5 text-muni-dark" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                </span>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium ${cfg.bg}">
                        <span class="w-1.5 h-1.5 rounded-full ${cfg.dot} shrink-0"></span>
                        ${statusLabel(event.new_status)}
                    </span>
                    <span class="text-xs text-gray-400">${event.timestamp}</span>
                </div>
                ${commentHtml}
                <p class="text-xs text-gray-400 mt-0.5">by ${event.changed_by_name}</p>
            `;

            timeline.insertBefore(li, timeline.firstChild);
        }

        window.Echo.private('complaint.{{ $complaint->id }}')
            .listen('.status.updated', (event) => {
                // Update header badge
                const badgeContainer = document.getElementById('status-badge-container');
                if (badgeContainer) badgeContainer.innerHTML = renderBadge(event.new_status);

                // Show/update rejection reason
                const rejectionBox  = document.getElementById('rejection-reason-box');
                const rejectionText = document.getElementById('rejection-reason-text');
                if (rejectionBox && rejectionText) {
                    if (event.new_status === 'rejected' && event.rejection_reason) {
                        rejectionText.textContent = event.rejection_reason;
                        rejectionBox.classList.remove('hidden');
                    } else {
                        rejectionBox.classList.add('hidden');
                    }
                }

                // Prepend history entry
                prependHistoryEntry(event);

                // Toast
                showToast('Status updated to: ' + statusLabel(event.new_status));
            });
    })();
    </script>
    @endpush
</x-app-layout>
