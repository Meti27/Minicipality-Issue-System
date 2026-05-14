<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Complaint Details</h2>
            <a href="{{ route('citizen.complaints.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to list</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Complaint card --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $complaint->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $complaint->category->name }} &middot; Submitted {{ $complaint->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <x-status-badge :status="$complaint->status" />
                </div>

                <div class="px-6 py-5 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Location</p>
                        <p class="text-sm text-gray-700">{{ $complaint->location }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Description</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $complaint->description }}</p>
                    </div>

                    @if($complaint->image_path)
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Attached Image</p>
                            <img src="{{ asset('storage/' . $complaint->image_path) }}"
                                 alt="Complaint image"
                                 class="rounded-lg max-h-64 object-cover border border-gray-200">
                        </div>
                    @endif

                    @if($complaint->status === 'rejected' && $complaint->rejection_reason)
                        <div class="rounded-md bg-red-50 border border-red-200 p-4">
                            <p class="text-xs font-medium text-red-700 uppercase tracking-wide mb-1">Rejection Reason</p>
                            <p class="text-sm text-red-800">{{ $complaint->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Status timeline --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800">Status History</h3>
                </div>
                <div class="px-6 py-5">
                    <ol class="relative border-l border-gray-200 space-y-6 ml-3">
                        @foreach($complaint->statusHistories->sortByDesc('created_at') as $history)
                            <li class="mb-2 ml-6">
                                <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 ring-4 ring-white">
                                    <svg class="h-3 w-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <div class="flex items-center gap-2 mb-1">
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
    </div>
</x-app-layout>
