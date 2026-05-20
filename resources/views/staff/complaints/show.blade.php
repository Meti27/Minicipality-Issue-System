<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('staff.complaints.index') }}"
                   class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"
                   aria-label="Back to complaints">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Review Complaint #{{ $complaint->id }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $complaint->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <x-status-badge :status="$complaint->status" />
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left column: details + history --}}
                <div class="lg:col-span-2 space-y-6">

                    @if(session('success'))
                        <div class="rounded-xl bg-green-50 p-4 text-sm text-green-800 border border-green-200 flex items-start gap-3" role="alert">
                            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="rounded-xl bg-red-50 p-4 text-sm text-red-800 border border-red-200" role="alert">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Complaint details card --}}
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $complaint->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Submitted by <span class="font-medium text-gray-700">{{ $complaint->user->name }}</span>
                                        ({{ $complaint->user->email }})
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-1.5 shrink-0">
                                    @php
                                        $pColors = ['low'=>'bg-gray-100 text-gray-600','medium'=>'bg-yellow-100 text-yellow-700','high'=>'bg-red-100 text-red-700'];
                                    @endphp
                                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $pColors[$complaint->priority] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($complaint->priority) }} priority
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-5 space-y-5 text-sm">
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Category</p>
                                    <p class="text-gray-700">{{ $complaint->category->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Location</p>
                                    <p class="text-gray-700">{{ $complaint->location }}</p>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Description</p>
                                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $complaint->description }}</p>
                            </div>

                            @if($complaint->image_path)
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Attached Photo</p>
                                    <img src="{{ asset('storage/' . $complaint->image_path) }}"
                                         alt="Complaint photo"
                                         class="rounded-xl max-h-72 object-cover border border-gray-200 shadow-sm">
                                </div>
                            @endif

                            @if($complaint->status === 'rejected' && $complaint->rejection_reason)
                                <div class="rounded-xl bg-red-50 border border-red-200 p-4" role="alert">
                                    <p class="text-xs font-semibold text-red-700 uppercase tracking-wider mb-1.5">Rejection Reason</p>
                                    <p class="text-red-800">{{ $complaint->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Status update form --}}
                    @if(!empty($allowedTransitions))
                        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-base font-semibold text-gray-800">Update Status</h3>
                                <p class="text-xs text-gray-500 mt-1 flex flex-wrap items-center gap-1.5">
                                    Current: <x-status-badge :status="$complaint->status" />
                                    <span class="text-gray-300">&rarr;</span>
                                    Allowed next:
                                    @foreach($allowedTransitions as $t)
                                        <x-status-badge :status="$t" />
                                    @endforeach
                                </p>
                            </div>

                            <form method="POST"
                                  action="{{ route('staff.complaints.updateStatus', $complaint) }}"
                                  class="px-6 py-5 space-y-4"
                                  x-data="{ selectedStatus: '' }">
                                @csrf
                                @method('PATCH')

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="new_status">
                                            New Status <span class="text-red-500">*</span>
                                        </label>
                                        <select id="new_status" name="new_status" x-model="selectedStatus" required
                                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Select --</option>
                                            @foreach($allowedTransitions as $transition)
                                                <option value="{{ $transition }}">{{ ucfirst(str_replace('_', ' ', $transition)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="comment">
                                            Comment <span class="text-xs text-gray-400 font-normal">(optional)</span>
                                        </label>
                                        <input type="text" id="comment" name="comment" value="{{ old('comment') }}"
                                               class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="Internal note…">
                                    </div>
                                </div>

                                {{-- Rejection reason — shown only when rejected is selected --}}
                                <div x-show="selectedStatus === 'rejected'" x-cloak>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="rejection_reason">
                                        Rejection Reason <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="rejection_reason" name="rejection_reason" rows="3"
                                              class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Explain why this complaint is being rejected…">{{ old('rejection_reason') }}</textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit"
                                            class="px-5 py-2.5 bg-muni-dark text-white text-sm font-semibold rounded-xl hover:bg-muni-darker transition focus:outline-none focus:ring-2 focus:ring-muni-dark focus:ring-offset-2">
                                        Update Status
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-xl px-6 py-4 text-sm text-gray-500 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            This complaint is in a final state (<x-status-badge :status="$complaint->status" />) and cannot be updated further.
                        </div>
                    @endif

                    {{-- Status history --}}
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-800">Status History</h3>
                        </div>
                        <div class="px-6 py-5">
                            <ol class="relative border-l-2 border-gray-100 space-y-6 ml-3" role="list">
                                @foreach($complaint->statusHistories->sortByDesc('created_at') as $history)
                                    <li class="ml-6">
                                        <span class="absolute -left-3.5 flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 ring-4 ring-white">
                                            <svg class="h-3.5 w-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            @if($history->old_status)
                                                <x-status-badge :status="$history->old_status" />
                                                <span class="text-gray-300 text-xs" aria-hidden="true">&rarr;</span>
                                            @endif
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
                            </ol>
                        </div>
                    </div>

                </div>

                {{-- Right column: possible duplicates + citizen info --}}
                <div class="space-y-6">
                    {{-- Possible duplicates --}}
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-800">Possible Duplicates</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Other active complaints in the same category</p>
                        </div>

                        @if($duplicates->isEmpty())
                            <div class="px-5 py-8 text-center">
                                <p class="text-xs text-gray-400">No similar complaints found.</p>
                            </div>
                        @else
                            <ul class="divide-y divide-gray-50">
                                @foreach($duplicates as $dup)
                                    <li class="px-5 py-3 hover:bg-gray-50 transition-colors">
                                        <a href="{{ route('staff.complaints.show', $dup) }}"
                                           class="block">
                                            <p class="text-sm font-medium text-gray-800 truncate hover:text-blue-600 transition-colors">{{ $dup->title }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $dup->location }}</p>
                                            <div class="flex items-center gap-2 mt-1.5">
                                                <x-status-badge :status="$dup->status" />
                                                <span class="text-xs text-gray-400">{{ $dup->user->name }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Citizen info card --}}
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 px-5 py-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Citizen Info</h3>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0" aria-hidden="true">
                                {{ strtoupper(substr($complaint->user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $complaint->user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $complaint->user->email }}</p>
                            </div>
                        </div>
                        <dl class="space-y-2.5 text-sm border-t border-gray-100 pt-3">
                            <div class="flex items-center justify-between">
                                <dt class="text-xs text-gray-400">Total Complaints</dt>
                                <dd class="text-sm font-semibold text-gray-700 tabular-nums">{{ $complaint->user->complaints()->count() }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-xs text-gray-400">Member Since</dt>
                                <dd class="text-sm text-gray-700">{{ $complaint->user->created_at->format('M Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
