<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('citizen.dashboard') }}"
               class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Submit a Complaint</h2>
                <p class="text-sm text-gray-500 mt-0.5">Describe the issue and we'll get it to the right team</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <p class="text-xs text-gray-500 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Fields marked with <span class="text-red-500 font-semibold">*</span> are required
                    </p>
                </div>

                <form method="POST" action="{{ route('citizen.complaints.store') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('title') border-red-400 @enderror"
                               placeholder="Brief description of the issue, e.g. Large pothole on Main Street">
                        @error('title')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select id="category_id" name="category_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('category_id') border-red-400 @enderror">
                                <option value="">— Select a category —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="location" name="location" value="{{ old('location') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('location') border-red-400 @enderror"
                                   placeholder="Street address or area">
                            @error('location')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @error('description') border-red-400 @enderror"
                                  placeholder="Describe the issue in detail — how severe it is, how long it's been there, any safety hazards...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
<div>
    <label for="image" class="block text-sm font-semibold text-gray-700 mb-1.5">
        Photo <span class="text-red-500">*</span>
    </label>

    <div class="mt-1 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
        
        <input
            id="image"
            name="image"
            type="file"
            accept="image/*"
            class="block w-full text-sm text-gray-500
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-md file:border-0
                   file:text-sm file:font-semibold
                   file:bg-blue-50 file:text-blue-700
                   hover:file:bg-blue-100"
        >

        <p class="mt-2 text-xs text-gray-400">
            JPG, PNG or GIF — max 2 MB
        </p>

        {{-- Image Preview --}}
        <div class="mt-4 flex justify-center">
            <img id="preview"
                 src="#"
                 alt="Preview"
                 class="hidden max-h-64 rounded-lg shadow-md border border-gray-200">
        </div>
    </div>

    @error('image')
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
    document.getElementById('image').addEventListener('change', function (event) {
        const preview = document.getElementById('preview');
        const file = event.target.files[0];

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        }
    });
</script>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <a href="{{ route('citizen.dashboard') }}"
                           class="text-sm text-gray-500 hover:text-gray-700 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Submit Complaint
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
