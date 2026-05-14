<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Category Management</h2>
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                + New Category
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                @if($categories->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">No categories found.</div>
                @else
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Complaints</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @foreach($categories as $category)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $category->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $category->description ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $category->complaints_count }}</td>
                                    <td class="px-6 py-4">
                                        @if($category->is_active)
                                            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-green-50 text-green-700 ring-1 ring-green-200 font-medium">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-red-50 text-red-600 ring-1 ring-red-200 font-medium">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="text-sm text-blue-600 hover:underline font-medium">Edit</a>

                                        <form method="POST"
                                              action="{{ route('admin.categories.toggleActive', $category) }}"
                                              class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="text-sm font-medium {{ $category->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}">
                                                {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
