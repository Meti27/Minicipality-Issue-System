<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Management</h2>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                + Create Account
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-800 border border-red-200">{{ $errors->first() }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.users.index') }}"
                  class="bg-white shadow rounded-lg px-5 py-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Name or email…"
                           class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 w-56">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                    <select name="role" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All roles</option>
                        <option value="citizen" {{ $role === 'citizen' ? 'selected' : '' }}>Citizen</option>
                        <option value="staff"   {{ $role === 'staff'   ? 'selected' : '' }}>Staff</option>
                        <option value="admin"   {{ $role === 'admin'   ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">Filter</button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 transition">Clear</a>
                </div>
            </form>

            {{-- Table --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                @if($users->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">No users match your filters.</div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Complaints</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'staff' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->is_active)
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">Active</span>
                                        @else
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-medium">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->complaints_count }}</td>
                                    <td class="px-6 py-4 text-right">
                                        @if($user->id !== auth()->id() && $user->role !== 'admin')
                                            <form method="POST"
                                                  action="{{ route('admin.users.toggleActive', $user) }}"
                                                  class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="text-sm font-medium {{ $user->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
