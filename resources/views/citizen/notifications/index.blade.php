<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notifications</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">

                @if($notifications->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">You have no notifications yet.</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-50">
                        @foreach($notifications as $notif)
                            <li class="px-6 py-4 {{ !$notif->is_read ? 'bg-blue-50 border-l-2 border-blue-500' : '' }} transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="w-8 h-8 rounded-full {{ !$notif->is_read ? 'bg-blue-100' : 'bg-gray-100' }} flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 {{ !$notif->is_read ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-800 {{ !$notif->is_read ? 'font-medium' : '' }}">{{ $notif->message }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->format('d M Y, H:i') }} &middot; {{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    @if($notif->complaint_id)
                                        <a href="{{ route('citizen.complaints.show', $notif->complaint_id) }}"
                                           class="shrink-0 inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap">
                                            View
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $notifications->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
