<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Management</h2>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-muni-dark text-white text-sm font-semibold rounded-xl hover:bg-muni-darker transition shadow-sm focus:outline-none focus:ring-2 focus:ring-muni-dark focus:ring-offset-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Account
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

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
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.users.index') }}"
                  class="bg-white shadow-sm rounded-xl border border-gray-100 px-5 py-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5" for="user-search">Search</label>
                    <input type="text" id="user-search" name="search" value="{{ $search }}"
                           placeholder="Name or email…"
                           class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-muni-dark focus:ring-muni-dark w-56">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5" for="role-filter">Role</label>
                    <select id="role-filter" name="role" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-muni-dark focus:ring-muni-dark">
                        <option value="">All roles</option>
                        <option value="citizen" {{ $role === 'citizen' ? 'selected' : '' }}>Citizen</option>
                        <option value="staff"   {{ $role === 'staff'   ? 'selected' : '' }}>Staff</option>
                        <option value="admin"   {{ $role === 'admin'   ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-muni-dark text-white text-sm font-semibold rounded-xl hover:bg-muni-darker transition focus:outline-none focus:ring-2 focus:ring-muni-dark focus:ring-offset-2">
                        Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </a>
                </div>
            </form>

            {{-- Table --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                @if($users->isEmpty())
                    <div class="px-6 py-14 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700 mb-1">No users found</p>
                        <p class="text-xs text-gray-400">Try adjusting your filters.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Complaints</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0
                                                    {{ $user->role === 'admin' ? 'bg-purple-600' : ($user->role === 'staff' ? 'bg-indigo-600' : 'bg-muni-mid') }}"
                                                     aria-hidden="true">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold
                                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'staff' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->is_active)
                                                <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-green-50 text-green-700 ring-1 ring-green-200 font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500" aria-hidden="true"></span>Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-red-50 text-red-600 ring-1 ring-red-200 font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500" aria-hidden="true"></span>Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $user->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 tabular-nums">{{ $user->complaints_count }}</td>
                                        <td class="px-6 py-4 text-right">
                                            @if($user->id !== auth()->id() && $user->role !== 'admin')
                                                <form method="POST"
                                                      action="{{ route('admin.users.toggleActive', $user) }}"
                                                      class="inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                            class="text-sm font-semibold transition {{ $user->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}">
                                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
