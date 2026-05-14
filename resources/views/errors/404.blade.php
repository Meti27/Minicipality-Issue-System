<x-app-layout>
    <div class="min-h-[60vh] flex items-center justify-center py-16">
        <div class="text-center">
            <p class="text-6xl font-bold text-blue-600">404</p>
            <h1 class="mt-4 text-2xl font-semibold text-gray-900">Page Not Found</h1>
            <p class="mt-2 text-gray-500">The page you're looking for doesn't exist or has been moved.</p>
            <a href="{{ route('dashboard') }}"
               class="mt-6 inline-flex items-center px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </div>
</x-app-layout>
