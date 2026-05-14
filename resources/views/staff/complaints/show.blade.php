<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Review Complaint #{{ $complaint->id }}</h2>
            <a href="{{ route('staff.complaints.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to list</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left column: details + history --}}
                <div class="lg:col-span-2 space-y-6">

                    @if(session('success'))
                        <div class="rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="rounded-md bg-red-50 p-4 text-sm text-red-800 border border-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Complaint details card --}}
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-200 flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $complaint->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Submitted by <span class="font-medium text-gray-700">{{ $complaint->user->name }}</span>
                                    ({{ $complaint->user->email }})
                                    &middot; {{ $complaint->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <x-status-badge :status="$complaint->status" />
                                @php
                                    $pColors = ['low'=>'bg-gray-100 text-gray-600','medium'=>'bg-yellow-100 text-yellow-700','high'=>'bg-red-100 text-red-700'];
                                @endphp
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $pColors[$complaint->priority] ?? '' }}">
                                    {{ ucfirst($complaint->priority) }} priority
                                </span>
                            </div>
                        </div>

                        <div class="px-6 py-5 space-y-4 text-sm">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Category</p>
                                    <p class="text-gray-700">{{ $complaint->category->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Location</p>
                                    <p class="text-gray-700">{{ $complaint->location }}</p>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Description</p>
                                <p class="text-gray-700 whitespace-pre-line">{{ $complaint->description }}</p>
                            </div>

                            @if($complaint->image_path)
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Attached Image</p>
                                    <img src="{{ asset('storage/' . $complaint->image_path) }}"
                                         alt="Complaint image"
                                         class="rounded-lg max-h-72 object-cover border border-gray-200">
                                </div>
                            @endif

                            @if($complaint->status === 'rejected' && $complaint->rejection_reason)
                                <div class="rounded-md bg-red-50 border border-red-200 p-4">
                                    <p class="text-xs font-medium text-red-700 uppercase tracking-wide mb-1">Rejection Reason</p>
                                    <p class="text-red-800">{{ $complaint->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Status update form --}}
                    @if(!empty($allowedTransitions))
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-base font-semibold text-gray-800">Update Status</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Current: <x-status-badge :status="$complaint->status" />
                                    &rarr; Allowed next:
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
                                        <label class="block text-sm font-medium text-gray-700 mb-1">New Status <span class="text-red-500">*</span></label>
                                        <select name="new_status" x-model="selectedStatus" required
                                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-- Select --</option>
                                            @foreach($allowedTransitions as $transition)
                                                <option value="{{ $transition }}">{{ ucfirst(str_replace('_', ' ', $transition)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Comment <span class="text-gray-400 font-normal">(optional)</span></label>
                                        <input type="text" name="comment" value="{{ old('comment') }}"
                                               class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="Internal note…">
                                    </div>
                                </div>

                                {{-- Rejection reason — shown only when rejected is selected --}}
                                <div x-show="selectedStatus === 'rejected'" x-cloak>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason <span class="text-red-500">*</span></label>
                                    <textarea name="rejection_reason" rows="3"
                                              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Explain why this complaint is being rejected…">{{ old('rejection_reason') }}</textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit"
                                            class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                                        Update Status
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg px-6 py-4 text-sm text-gray-500">
                            This complaint is in a final state (<x-status-badge :status="$complaint->status" />) and cannot be updated further.
                        </div>
                    @endif

                    {{-- Status history --}}
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-base font-semibold text-gray-800">Status History</h3>
                        </div>
                        <div class="px-6 py-5">
                            <ol class="relative border-l border-gray-200 space-y-6 ml-3">
                                @foreach($complaint->statusHistories->sortByDesc('created_at') as $history)
                                    <li class="ml-6">
                                        <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 ring-4 ring-white">
                                            <svg class="h-3 w-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            @if($history->old_status)
                                                <x-status-badge :status="$history->old_status" />
                                                <span class="text-gray-400 text-xs">&rarr;</span>
                                            @endif
                                            <x-status-badge :status="$history->new_status" />
                                            <span class="text-xs text-gray-400">{{ $history->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        @if($history->comment)
                                            <p class="text-sm text-gray-600">{{ $history->comment }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-0.5">by {{ $history->changedBy->name }}</p>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>

                </div>

                {{-- Right column: possible duplicates --}}
                <div class="space-y-6">
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-800">Possible Duplicates</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Other active complaints in the same category</p>
                        </div>

                        @if($duplicates->isEmpty())
                            <div class="px-5 py-6 text-center text-xs text-gray-400">No similar complaints found.</div>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach($duplicates as $dup)
                                    <li class="px-5 py-3">
                                        <a href="{{ route('staff.complaints.show', $dup) }}"
                                           class="block hover:text-blue-600 transition">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $dup->title }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $dup->location }}</p>
                                            <div class="flex items-center gap-2 mt-1">
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
                    <div class="bg-white shadow rounded-lg px-5 py-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Citizen Info</h3>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide">Name</dt>
                                <dd class="text-gray-700 font-medium">{{ $complaint->user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide">Email</dt>
                                <dd class="text-gray-700">{{ $complaint->user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400 uppercase tracking-wide">Total Complaints</dt>
                                <dd class="text-gray-700">{{ $complaint->user->complaints()->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
