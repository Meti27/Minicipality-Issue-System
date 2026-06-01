<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
        <p class="text-sm text-gray-500 mt-1">Sign in to your municipality account</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-muni-dark hover:text-muni-darker hover:underline" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-muni-dark shadow-sm focus:ring-muni-mid" name="remember">
            <label for="remember_me" class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</label>
        </div>

        <x-primary-button class="w-full justify-center py-2.5">
            {{ __('Sign In') }}
        </x-primary-button>

        <p class="text-center text-sm text-gray-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-muni-dark font-medium hover:underline">Register here</a>
        </p>
    </form>
</x-guest-layout>
